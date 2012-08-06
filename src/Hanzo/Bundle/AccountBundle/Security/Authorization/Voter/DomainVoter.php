<?php

namespace Hanzo\Bundle\AccountBundle\Security\Authorization\Voter;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Security\Core\Authorization\Voter\VoterInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\User\UserInterface
    ;

use Hanzo\Model\Customers,
    Hanzo\Model\CustomersQuery;

class DomainVoter implements VoterInterface
{
    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    public function supportsAttribute($attribute)
    {
        return true;
    }

    public function supportsClass($class)
    {
        return true;
    }

    function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = self::ACCESS_ABSTAIN;

        if (!($object instanceof Request)) 
        {
            return $result;
        }

        $user = $token->getUser();
        if (!($user instanceof UserInterface)) 
        {
            return $result;
        }

        $customer = CustomersQuery::create()->findOneByEmail($user->getUserName());
        $addresses = $customer->getAddressess();

        $paymentAddress = null;
        foreach ($addresses as $address) 
        {
            if ( $address->getType() == 'payment' )
            {
                $paymentAddress = $address;
                break;
            }
        }

        // No payment address... wtf?
        if ( is_null($paymentAddress) )
        {
            error_log(__LINE__.':'.__FILE__.' '); // hf@bellcom.dk debugging
        }

        // Hardcoded lookup table
        $country = $paymentAddress->getCountry();
        $locale  = $this->container->get('session')->getLocale();

        $countryToLocaleMap = array(
            'Denmark'     => array( 'da_DK' ),
            'Norway'      => array( 'nb_NO' ),
            'Netherlands' => array( 'nl_NL' ),
            'Sweden'      => array( 'sv_SE' ),
            // TODO: FI
            );

        // Other countries have to run en_GB: 
        if ( !isset($countryToLocaleMap[$country]) && $locale != 'en_GB' )
        {
            $this->container->get('session')->setFlash('warning', 'Nein!');
            return VoterInterface::ACCESS_DENIED;
        }

        // If the country is not set in the mapping and the local does not match
        if ( !( isset($countryToLocaleMap[$country]) && in_array($locale,$countryToLocaleMap[$country]) ) )
        {
            $this->container->get('session')->setFlash('warning', 'Nein!');
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
