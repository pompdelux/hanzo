<?php

namespace Hanzo\Bundle\AccountBundle\Security\Authorization\Voter;

use Hanzo\Core\Tools;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\User as CoreUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;

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

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $countryIdToLocaleMap = array(
            80  => array( 'de_DE' ),          // Germany
            58  => array( 'da_DK' ),          // Denmark
            72  => array( 'fi_FI', 'sv_FI' ), // Finland
            80  => array( 'de_DE' ),          // Germany
            151 => array( 'nl_NL' ),          // Netherlands
            161 => array( 'nb_NO' ),          // Norway
            207 => array( 'sv_SE' ),          // Sweden
            14  => array( 'de_AT' ),          // Austria
            208 => array( 'de_CH' ),          // Switzerland
        );

        if (!($object instanceof Request)) {
          return VoterInterface::ACCESS_ABSTAIN;
        }

        $request = $object;

        $user = $token->getUser();
        if (!($user instanceof UserInterface)) {
          return VoterInterface::ACCESS_ABSTAIN;
        }

        // This is here to allow us to have in-memory users for api access.
        if ($user instanceof CoreUser) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $customer  = CustomersQuery::create()->findOneByEmail($user->getUserName());
        $addresses = $customer->getAddressess();

        $paymentAddress = null;
        foreach ($addresses as $address) {
            if ( $address->getType() == 'payment' ) {
                $paymentAddress = $address;
                break;
            }
        }

        // No payment address... wtf?
        if ( is_null($paymentAddress) ) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // Hardcoded lookup table
        $country   = $paymentAddress->getCountry();
        $countryId = $paymentAddress->getCountriesId();
        $locale    = $request->getLocale();
        $translator = $this->container->get('translator');

        // Restrict access to login webshop to only customers.
        if (!in_array('ROLE_ADMIN', $user->getRoles()) &&
            !in_array('ROLE_SALES', $user->getRoles()) &&
            !in_array('ROLE_STATS', $user->getRoles()) &&
            !in_array('ROLE_DESIGN', $user->getRoles()) &&
            !in_array('ROLE_CUSTOMERS_SERVICE', $user->getRoles()) &&
            !in_array('ROLE_SUPPORT', $user->getRoles()) &&
            !in_array('ROLE_LOGISTICS', $user->getRoles()) &&
            ('webshop' === $this->container->get('kernel')->getStoreMode()) &&
            ($customer->getGroupsId() !== 1)
        ) {
            $useLocale = $countryIdToLocaleMap[$countryId][0]; // Use the first locale

            $msg = $translator->trans('login.restricted.only.customers',array( '%url%' => 'http://c.pompdelux.com/'.$useLocale.'/login', '%site_name%' => $country ),'account');
            $this->container->get('session')->getFlashBag()->add('error', $msg);

            return VoterInterface::ACCESS_DENIED;
        }

        // If the country is not set in the mapping it must run en_GB, so deny access if it doesn't
        if ( !isset($countryIdToLocaleMap[$countryId]) && $locale != 'en_GB' ) {
            $msg = $translator->trans('login.restricted.other_locale',array( '%url%' => $request->getBaseUrl().'/en_GB/login', '%site_name%' => 'International' ),'account');
            $this->container->get('session')->getFlashBag()->add('error', $msg);

            return VoterInterface::ACCESS_DENIED;
        }

        // If the country has an local shop it must use that
        if ( isset($countryIdToLocaleMap[$countryId]) && !( in_array($locale,$countryIdToLocaleMap[$countryId]) ) ) {
            $useLocale = $countryIdToLocaleMap[$countryId][0]; // Use the first locale

            $msg = $translator->trans('login.restricted.other_locale',array( '%url%' => $request->getBaseUrl().'/'.$useLocale.'/login', '%site_name%' => $country ),'account');
            $this->container->get('session')->getFlashBag()->add('error', $msg);

            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
