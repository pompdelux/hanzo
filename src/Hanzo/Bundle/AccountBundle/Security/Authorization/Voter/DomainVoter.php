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
            error_log(__LINE__.':'.__FILE__.' DomainVoter: no payment address found, abstaining'); // hf@bellcom.dk debugging
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // Hardcoded lookup table
        $country   = $paymentAddress->getCountry();
        $countryId = $paymentAddress->getCountriesId();
        $locale    = $this->container->get('session')->getLocale();

        $countryIdToLocaleMap = array(
            58  => array( 'da_DK' ), // Denmark
            72  => array( 'fi_FI', 'sv_FI' ), // Finland
            151 => array( 'nl_NL' ), // Netherlands
            161 => array( 'nb_NO' ), // Norway
            207 => array( 'sv_SE' ), // Sweden
            );

        // Other countries have to run en_GB: 
        if ( !isset($countryIdToLocaleMap[$countryId]) && $locale != 'en_GB' )
        {
            $translator = $this->container->get('translator');

            $request = $this->container->get('request');

            $msg = $translator->trans('login.restricted.other_locale',array( '%url%' => $request->getBaseUrl().'/en_GB/login', '%site_name%' => 'International' ),'account');
            $this->container->get('session')->setFlash('error', $msg);
            return VoterInterface::ACCESS_DENIED;
        }

        // If the country is not set in the mapping and the local does not match
        if ( !( isset($countryIdToLocaleMap[$countryId]) && in_array($locale,$countryIdToLocaleMap[$countryId]) ) )
        {
            $translator = $this->container->get('translator');

            $request = $this->container->get('request');

            $useLocale = $countryIdToLocaleMap[$countryId][0]; // Use the first locale

            $msg = $translator->trans('login.restricted.other_locale',array( '%url%' => $request->getBaseUrl().'/'.$useLocale.'/login', '%site_name%' => $country ),'account');
            $this->container->get('session')->setFlash('error', $msg);
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
