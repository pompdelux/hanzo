<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface
    ;

use Propel,
    PDO
    ;

use Hanzo\Model\Customers,
    Hanzo\Model\CustomersPeer,
    Hanzo\Model\Addresses,
    Hanzo\Model\AddressesPeer,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer
    ;
class ImportCommand extends ContainerAwareCommand
{
    static $oscomIdToCountryMap = array(
        1 => "Afghanistan",
        2 => "Albania",
        3 => "Algeria",
        4 => "American Samoa",
        5 => "Andorra",
        6 => "Angola",
        7 => "Anguilla",
        8 => "Antarctica",
        9 => "Antigua and Barbuda",
        10 => "Argentina",
        11 => "Armenia",
        12 => "Aruba",
        13 => "Australia",
        14 => "Austria",
        15 => "Azerbaijan",
        16 => "Bahamas",
        17 => "Bahrain",
        18 => "Bangladesh",
        19 => "Barbados",
        20 => "Belarus",
        21 => "Belgium",
        22 => "Belize",
        23 => "Benin",
        24 => "Bermuda",
        25 => "Bhutan",
        26 => "Bolivia",
        27 => "Bosnia and Herzegowina",
        28 => "Botswana",
        29 => "Bouvet Island",
        30 => "Brazil",
        31 => "British Indian Ocean Territory",
        32 => "Brunei Darussalam",
        33 => "Bulgaria",
        34 => "Burkina Faso",
        35 => "Burundi",
        36 => "Cambodia",
        37 => "Cameroon",
        38 => "Canada",
        39 => "Cape Verde",
        40 => "Cayman Islands",
        41 => "Central African Republic",
        42 => "Chad",
        43 => "Chile",
        44 => "China",
        45 => "Christmas Island",
        46 => "Cocos (Keeling) Islands",
        47 => "Colombia",
        48 => "Comoros",
        49 => "Congo",
        50 => "Cook Islands",
        51 => "Costa Rica",
        52 => "Cote D'Ivoire",
        53 => "Croatia",
        54 => "Cuba",
        55 => "Cyprus",
        56 => "Czech Republic",
        57 => "Denmark",
        58 => "Djibouti",
        59 => "Dominica",
        60 => "Dominican Republic",
        61 => "East Timor",
        62 => "Ecuador",
        63 => "Egypt",
        64 => "El Salvador",
        65 => "Equatorial Guinea",
        66 => "Eritrea",
        67 => "Estonia",
        68 => "Ethiopia",
        69 => "Falkland Islands (Malvinas)",
        70 => "Faroe Islands",
        71 => "Fiji",
        72 => "Finland",
        73 => "France",
        78 => "Gabon",
        79 => "Gambia",
        80 => "Georgia",
        81 => "Germany",
        82 => "Ghana",
        83 => "Gibraltar",
        84 => "Greece",
        85 => "Greenland",
        86 => "Grenada",
        87 => "Guadeloupe",
        88 => "Guam",
        89 => "Guatemala",
        90 => "Guinea",
        91 => "Guinea-bissau",
        92 => "Guyana",
        93 => "Haiti",
        94 => "Heard and Mc Donald Islands",
        95 => "Honduras",
        96 => "Hong Kong",
        97 => "Hungary",
        98 => "Iceland",
        99 => "India",
        100 => "Indonesia",
        101 => "Iran (Islamic Republic of)",
        102 => "Iraq",
        103 => "Ireland",
        104 => "Israel",
        105 => "Italy",
        106 => "Jamaica",
        107 => "Japan",
        108 => "Jordan",
        109 => "Kazakhstan",
        110 => "Kenya",
        111 => "Kiribati",
        112 => "Korea, Democratic People's Republic of",
        113 => "Korea, Republic of",
        114 => "Kuwait",
        115 => "Kyrgyzstan",
        116 => "Lao People's Democratic Republic",
        117 => "Latvia",
        118 => "Lebanon",
        119 => "Lesotho",
        120 => "Liberia",
        121 => "Libyan Arab Jamahiriya",
        122 => "Liechtenstein",
        123 => "Lithuania",
        124 => "Luxembourg",
        125 => "Macau",
        126 => "Macedonia, The Former Yugoslav Republic of",
        127 => "Madagascar",
        128 => "Malawi",
        129 => "Malaysia",
        130 => "Maldives",
        131 => "Mali",
        132 => "Malta",
        133 => "Marshall Islands",
        134 => "Martinique",
        135 => "Mauritania",
        136 => "Mauritius",
        137 => "Mayotte",
        138 => "Mexico",
        139 => "Micronesia, Federated States of",
        140 => "Moldova, Republic of",
        141 => "Monaco",
        142 => "Mongolia",
        143 => "Montserrat",
        144 => "Morocco",
        145 => "Mozambique",
        146 => "Myanmar",
        147 => "Namibia",
        148 => "Nauru",
        149 => "Nepal",
        150 => "Netherlands",
        151 => "Netherlands Antilles",
        152 => "New Caledonia",
        153 => "New Zealand",
        154 => "Nicaragua",
        155 => "Niger",
        156 => "Nigeria",
        157 => "Niue",
        158 => "Norfolk Island",
        159 => "Northern Mariana Islands",
        161 => "Oman",
        162 => "Pakistan",
        163 => "Palau",
        164 => "Panama",
        165 => "Papua New Guinea",
        166 => "Paraguay",
        167 => "Peru",
        168 => "Philippines",
        169 => "Pitcairn",
        170 => "Poland",
        171 => "Portugal",
        172 => "Puerto Rico",
        173 => "Qatar",
        174 => "Reunion",
        175 => "Romania",
        176 => "Russian Federation",
        177 => "Rwanda",
        178 => "Saint Kitts and Nevis",
        179 => "Saint Lucia",
        180 => "Saint Vincent and the Grenadines",
        181 => "Samoa",
        182 => "San Marino",
        183 => "Sao Tome and Principe",
        184 => "Saudi Arabia",
        185 => "Senegal",
        186 => "Seychelles",
        187 => "Sierra Leone",
        188 => "Singapore",
        189 => "Slovakia (Slovak Republic)",
        190 => "Slovenia",
        191 => "Solomon Islands",
        192 => "Somalia",
        193 => "South Africa",
        194 => "South Georgia and the South Sandwich Islands",
        195 => "Spain",
        196 => "Sri Lanka",
        197 => "St. Helena",
        198 => "St. Pierre and Miquelon",
        199 => "Sudan",
        200 => "Suriname",
        201 => "Svalbard and Jan Mayen Islands",
        202 => "Swaziland",
        204 => "Switzerland",
        205 => "Syrian Arab Republic",
        206 => "Taiwan",
        207 => "Tajikistan",
        208 => "Tanzania, United Republic of",
        209 => "Thailand",
        210 => "Togo",
        211 => "Tokelau",
        212 => "Tonga",
        213 => "Trinidad and Tobago",
        214 => "Tunisia",
        215 => "Turkey",
        216 => "Turkmenistan",
        217 => "Turks and Caicos Islands",
        218 => "Tuvalu",
        219 => "Uganda",
        220 => "Ukraine",
        221 => "United Arab Emirates",
        222 => "United Kingdom",
        223 => "United States",
        224 => "United States Minor Outlying Islands",
        225 => "Uruguay",
        226 => "Uzbekistan",
        227 => "Vanuatu",
        228 => "Vatican City State (Holy See)",
        229 => "Venezuela",
        230 => "Viet Nam",
        231 => "Virgin Islands (British)",
        232 => "Virgin Islands (U.S.)",
        233 => "Wallis and Futuna Islands",
        234 => "Western Sahara",
        235 => "Yemen",
        236 => "Yugoslavia",
        237 => "Zaire",
        238 => "Zambia",
        239 => "Zimbabwe",
    );

    static $hanzoNameToIdMap = array(
        "Afghanistan" => 1,
        "Albania" => 2,
        "Algeria" => 3,
        "American Samoa" => 4,
        "Andorra" => 5,
        "Angola" => 6,
        "Anguilla" => 7,
        "Antarctica" => 8,
        "Antigua and Barbuda" => 9,
        "Argentina" => 10,
        "Armenia" => 11,
        "Aruba" => 12,
        "Australia" => 13,
        "Austria" => 14,
        "Azerbaijan" => 15,
        "Bahamas" => 16,
        "Bahrain" => 17,
        "Bangladesh" => 18,
        "Barbados" => 19,
        "Belarus" => 20,
        "Belgium" => 21,
        "Belize" => 22,
        "Benin" => 23,
        "Bermuda" => 24,
        "Bhutan" => 25,
        "Bolivia" => 26,
        "Bosnia and Herzegovina" => 27,
        "Botswana" => 28,
        "Bouvet Island" => 29,
        "Brazil" => 30,
        "British Indian Ocean Territory" => 31,
        "Brunei" => 32,
        "Bulgaria" => 33,
        "Burkina Faso" => 34,
        "Burundi" => 35,
        "Cambodia" => 36,
        "Cameroon" => 37,
        "Canada" => 38,
        "Cape Verde" => 39,
        "Cayman Islands" => 40,
        "Central African Republic" => 41,
        "Chad" => 42,
        "Chile" => 43,
        "China" => 44,
        "Christmas Island" => 45,
        "Cocos (Keeling) Islands" => 46,
        "Colombia" => 47,
        "Comoros" => 48,
        "Congo (Brazzaville)" => 49,
        "Congo (Kinshasa)" => 50,
        "Cook Islands" => 51,
        "Costa Rica" => 52,
        "Ivory Coast" => 53,
        "Croatia" => 54,
        "Cuba" => 55,
        "Cyprus" => 56,
        "Czech Republic" => 57,
        "Denmark" => 58,
        "Djibouti" => 59,
        "Dominica" => 60,
        "Dominican Republic" => 61,
        "Ecuador" => 62,
        "Egypt" => 63,
        "El Salvador" => 64,
        "Equatorial Guinea" => 65,
        "Eritrea" => 66,
        "Estonia" => 67,
        "Ethiopia" => 68,
        "Falkland Islands" => 69,
        "Faroe Islands" => 70,
        "Fiji" => 71,
        "Finland" => 72,
        "France" => 73,
        "French Guiana" => 74,
        "French Polynesia" => 75,
        "French Southern Territories" => 76,
        "Gabon" => 77,
        "Gambia" => 78,
        "Georgia" => 79,
        "Germany" => 80,
        "Ghana" => 81,
        "Gibraltar" => 82,
        "Greece" => 83,
        "Greenland" => 84,
        "Grenada" => 85,
        "Guadeloupe" => 86,
        "Guam" => 87,
        "Guatemala" => 88,
        "Guinea" => 89,
        "Guinea-Bissau" => 90,
        "Guyana" => 91,
        "Haiti" => 92,
        "Heard Island and McDonald Islands" => 93,
        "Vatican" => 94,
        "Honduras" => 95,
        "Hong Kong S.A.R., China" => 96,
        "Hungary" => 97,
        "Iceland" => 98,
        "India" => 99,
        "Indonesia" => 100,
        "Iran" => 101,
        "Iraq" => 102,
        "Ireland" => 103,
        "Israel" => 104,
        "Italy" => 105,
        "Jamaica" => 106,
        "Japan" => 107,
        "Jordan" => 108,
        "Kazakhstan" => 109,
        "Kenya" => 110,
        "Kiribati" => 111,
        "North Korea" => 112,
        "South Korea" => 113,
        "Kuwait" => 114,
        "Kyrgyzstan" => 115,
        "Laos" => 116,
        "Latvia" => 117,
        "Lebanon" => 118,
        "Lesotho" => 119,
        "Liberia" => 120,
        "Libya" => 121,
        "Liechtenstein" => 122,
        "Lithuania" => 123,
        "Luxembourg" => 124,
        "Macao S.A.R., China" => 125,
        "Macedonia" => 126,
        "Madagascar" => 127,
        "Malawi" => 128,
        "Malaysia" => 129,
        "Maldives" => 130,
        "Mali" => 131,
        "Malta" => 132,
        "Marshall Islands" => 133,
        "Martinique" => 134,
        "Mauritania" => 135,
        "Mauritius" => 136,
        "Mayotte" => 137,
        "Mexico" => 138,
        "Micronesia" => 139,
        "Moldova" => 140,
        "Monaco" => 141,
        "Mongolia" => 142,
        "Montenegro" => 143,
        "Montserrat" => 144,
        "Morocco" => 145,
        "Mozambique" => 146,
        "Myanmar" => 147,
        "Namibia" => 148,
        "Nauru" => 149,
        "Nepal" => 150,
        "Netherlands" => 151,
        "Netherlands Antilles" => 152,
        "New Caledonia" => 153,
        "New Zealand" => 154,
        "Nicaragua" => 155,
        "Niger" => 156,
        "Nigeria" => 157,
        "Niue" => 158,
        "Norfolk Island" => 159,
        "Northern Mariana Islands" => 160,
        "Norway" => 161,
        "Oman" => 162,
        "Pakistan" => 163,
        "Palau" => 164,
        "Palestinian Territory" => 165,
        "Panama" => 166,
        "Papua New Guinea" => 167,
        "Paraguay" => 168,
        "Peru" => 169,
        "Philippines" => 170,
        "Pitcairn" => 171,
        "Poland" => 172,
        "Portugal" => 173,
        "Puerto Rico" => 174,
        "Qatar" => 175,
        "Reunion" => 176,
        "Romania" => 177,
        "Russia" => 178,
        "Rwanda" => 179,
        "Saint Helena" => 180,
        "Saint Kitts and Nevis" => 181,
        "Saint Lucia" => 182,
        "Saint Pierre and Miquelon" => 183,
        "Saint Vincent and the Grenadines" => 184,
        "Samoa" => 185,
        "San Marino" => 186,
        "Sao Tome and Principe" => 187,
        "Saudi Arabia" => 188,
        "Senegal" => 189,
        "Serbia" => 190,
        "Seychelles" => 191,
        "Sierra Leone" => 192,
        "Singapore" => 193,
        "Slovakia" => 194,
        "Slovenia" => 195,
        "Solomon Islands" => 196,
        "Somalia" => 197,
        "South Africa" => 198,
        "South Georgia and the South Sandwich Islands" => 199,
        "Spain" => 200,
        "Sri Lanka" => 201,
        "South Sudan" => 202,
        "Sudan" => 203,
        "Suriname" => 204,
        "Svalbard and Jan Mayen" => 205,
        "Swaziland" => 206,
        "Sweden" => 207,
        "Switzerland" => 208,
        "Syria" => 209,
        "Taiwan" => 210,
        "Tajikistan" => 211,
        "Tanzania" => 212,
        "Thailand" => 213,
        "Timor-Leste" => 214,
        "Togo" => 215,
        "Tokelau" => 216,
        "Tonga" => 217,
        "Trinidad and Tobago" => 218,
        "Tunisia" => 219,
        "Turkey" => 220,
        "Turkmenistan" => 221,
        "Turks and Caicos Islands" => 222,
        "Tuvalu" => 223,
        "Uganda" => 224,
        "Ukraine" => 225,
        "United Arab Emirates" => 226,
        "United States" => 227,
        "United States Minor Outlying Islands" => 228,
        "Uruguay" => 229,
        "Uzbekistan" => 230,
        "Vanuatu" => 231,
        "Venezuela" => 232,
        "Vietnam" => 233,
        "British Virgin Islands" => 234,
        "U.S. Virgin Islands" => 235,
        "Wallis and Futuna" => 236,
        "Western Sahara" => 237,
        "Yemen" => 238,
        "Zambia" => 239,
        "Zimbabwe" => 240,
        "Aland Islands" => 241,
        "Guernsey" => 242,
        "Isle of Man" => 243,
        "Jersey" => 244,
        "Saint BarthÃ©lemy" => 245,
        "Saint Martin (French part)" => 246,
        "United Kingdom" => 247,
        "CuraÃ§ao" => 248,
        "Sint Maarten (Dutch part)" => 249,
        "Bonaire, Sint Eustatius and Saba" => 250,
    );

    protected function configure()
    {
        $this
            ->setName('dataio:import')
            ->setDescription('Imports stuff')
            ->addArgument('import_type', InputArgument::REQUIRED, 'What to import')
            ->addArgument('database', InputArgument::REQUIRED, 'Which database to import from')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $importType = $input->getArgument('import_type');
        $database   = $input->getArgument('database');

        //TODO: handle .se/.no/.nl/.com
        switch ($database) 
        {
            case 'dk':
                $this->connection = Propel::getConnection( 'pdlfront_dk' );
                break;

            default:
                $this->output->writeln('<error>Unknown database "'.$database.'"</error>');
                exit;
                break;
        }

        $this->connection->exec("SET NAMES latin1");

        switch ($importType) 
        {
            case 'customers':
                $this->customerImport();
                break;

            default:
                $this->output->writeln('<error>Unknown import type "'.$importType.'"</error>');
                exit;
                break;
        }

        $this->output->writeln('<info>Import completed</info>');
    }

    /**
     * customerImport
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function customerImport()
    {
        $this->output->writeln('<info>Starting customer import</info>');

        //$sql = "SELECT * FROM osc_customers LEFT JOIN osc_address_book ON osc_customers.customers_id = osc_address_book.customers_id";
        $sql = "SELECT * FROM osc_customers";
        foreach ( $this->connection->query($sql) as $row ) 
        {
            $customer = CustomersPeer::getByEmail( $row['customers_email_address'] );

            if ( is_null($customer) )
            {
                $this->output->writeln('Creating: '. $row['customers_email_address']);
                $customer = new Customers();
                $customer->setFirstname( $row['customers_firstname'] )
                    ->setLastname($row['customers_lastname'])
                    ->setPassword( sha1( $row[ 'customers_password_cleartext' ] ) )
                    ->setEmail($row['customers_email_address'])
                    ->setPhone($row['customers_telephone'])
                    ->setPasswordClear( $row[ 'customers_password_cleartext' ] )
                    ->setDiscount( $row['customers_discount'] )
                    ->setIsActive( $row['customers_status'] )
                    ;

                if ( !empty( $row['customers_initials'] ) )
                {
                    $customer->setInitials($row['customers_initials']);
                }

                $addresses = array();
                $addresses = $this->getAddresses( $row );

                foreach ($addresses as $address) 
                {
                    $customer->addAddresses( $address );
                }

                $customer->save();
                $customerId = $customer->getId();

                $this->createOrders($row, $customer);
                unset($customer);
            }

            //break;
        }
    }

    /**
     * createOrders
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function createOrders( $customerRow, $customer )
    {
        $sql = "SELECT * 
            FROM 
              osc_orders 
            LEFT JOIN 
              osc_orders_attributes ON osc_orders.orders_id = osc_orders_attributes.orders_id 
            WHERE 
              customers_id = ". $customerRow['customers_id'];
        foreach ( $this->connection->query($sql) as $row )
        {
            if ( empty($row['customers_name']) )
            {
                $this->output->writeln('<error>Order not valid</error>');
            }

            $order = new Orders();
            $order->setPaymentGatewayId( (!empty($row['payment_gateway_id'])) ? $row['payment_gateway_id'] : NULL ) 
                ->setSessionId( uniqid().uniqid() )
                ->setState( $this->mapStatusToState( $row['orders_status'] ) )
                ->setCustomersId( $customer->getId() )
                ->setFirstName( $customer->getFirstName() )
                ->setLastName( $customer->getLastName() )
                ->setEmail( $customer->getEmail() )
                ->setPhone( $customer->getPhone() )
                ->setBillingFirstName( $customer->getFirstName() ) // The name from the order table is ignored
                ->setBillingLastName( $customer->getLastName() ) // The name from the order table is ignored
                ->setBillingCompanyName( $row['billing_company'] )
                ->setBillingAddressLine1( $row['billing_street_address'] )
                ->setBillingPostalCode( $row['billing_postcode'] )
                ->setBillingCity( $row['billing_city'] )
                ->setBillingCountry( $row['billing_country'] )
                ->setBillingCountriesId( $this->mapCountryName( $row['billing_country'] ) )
                ->setBillingMethod( $row['payment_method'] )
                ->setDeliveryFirstName( $customer->getFirstName() ) // The name from the order table is ignored
                ->setDeliveryLastName( $customer->getLastName() ) // The name from the order table is ignored
                ->setDeliveryCompanyName( $row['delivery_company'] )
                ->setDeliveryAddressLine1( $row['delivery_street_address'] )
                ->setDeliveryAddressLine2( $row['delivery_suburb'] )
                ->setDeliveryPostalCode( $row['delivery_postcode'] )
                ->setDeliveryCity( $row['delivery_city'] )
                ->setDeliveryCountry( $row['delivery_country'] )
                ->setDeliveryCountriesId( $this->mapCountryName( $row['delivery_country'] ) )
                ->setDeliveryMethod( 'hest' ) // TODO
                ;
            $order->save();
        }
    }

    /**
     * getAddresses
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function getAddresses( $customerRow )
    {
        $addresses = array();

        $orderAddressRow = array();
        foreach ( $this->connection->query("SELECT * FROM osc_orders WHERE customers_id = ".$customerRow['customers_id']." ORDER BY orders_id DESC LIMIT 1") as $row )
        {
            $orderAddressRow = $row;
        }

        if ( empty( $orderAddressRow ) )
        {
            return $addresses;
        }

        // Try to identify some which addressbook entry matches the latest order
        $shippingAddress = array();
        $shippingAddress['latitude']  = null;
        $shippingAddress['longitude'] = null;
        $shippingAddress['firstname'] = $customerRow['customers_firstname'];
        $shippingAddress['lastname']  = $customerRow['customers_lastname'];
        $shippingAddress['entry_country_id'] = 57; // Defaults to DK

        $paymentAddress = array();
        $paymentAddress['latitude']  = null;
        $paymentAddress['longitude'] = null;
        $paymentAddress['firstname'] = $customerRow['customers_firstname'];
        $paymentAddress['lastname']  = $customerRow['customers_lastname'];
        $paymentAddress['entry_country_id'] = 57; // Defaults to DK

        foreach ( $this->connection->query("SELECT * FROM osc_address_book WHERE customers_id = ".$customerRow['customers_id']) as $addressBookRow )
        {
            if ( 
                $addressBookRow['entry_firstname'].' '.$addressBookRow['entry_lastname'] == $orderAddressRow['delivery_name']
                &&
                $addressBookRow['entry_street_address'] == $orderAddressRow['delivery_street_address']
                &&
                $addressBookRow['entry_postcode'] == $orderAddressRow['delivery_postcode']
            )
            {
                $shippingAddress['latitude']  = $addressBookRow['latitude'];
                $shippingAddress['longitude'] = $addressBookRow['longitude'];
                $shippingAddress['firstname'] = $addressBookRow['entry_firstname'];
                $shippingAddress['lastname']  = $addressBookRow['entry_lastname'];
                $shippingAddress['entry_country_id'] = $addressBookRow['entry_country_id'];

                //echo 'Shipping address hit'.PHP_EOL;
                //print_r($addressBookRow);
            }

            if ( 
                $addressBookRow['entry_firstname'].' '.$addressBookRow['entry_lastname'] == $orderAddressRow['billing_name']
                &&
                $addressBookRow['entry_street_address'] == $orderAddressRow['billing_street_address']
                &&
                $addressBookRow['entry_postcode'] == $orderAddressRow['billing_postcode']
            )
            {
                $paymentAddress['latitude']  = $addressBookRow['latitude'];
                $paymentAddress['longitude'] = $addressBookRow['longitude'];
                $paymentAddress['firstname'] = $addressBookRow['entry_firstname'];
                $paymentAddress['lastname']  = $addressBookRow['entry_lastname'];
                $paymentAddress['entry_country_id'] = $addressBookRow['entry_country_id'];

                //echo 'Payment address hit'.PHP_EOL;
                //print_r($addressBookRow);
            }

            // TODO: Døgnpost
        }

        $address = new Addresses();
        $address->setAddressLine1($orderAddressRow['delivery_street_address'])
            ->setAddressLine2($orderAddressRow['delivery_suburb'])
            ->setType('shipping')
            ->setPostalCode($orderAddressRow['delivery_postcode'])
            ->setCity($orderAddressRow['delivery_city'])
            ->setFirstName( $shippingAddress['firstname'] )
            ->setLastName( $shippingAddress['lastname'] )
            ->setCountry( $orderAddressRow['delivery_country'] )
            ->setCountriesId( $this->mapCountryId( $shippingAddress['entry_country_id'] ) )
            ->setStateProvince( $orderAddressRow['delivery_state'] )
            ->setCompanyName($orderAddressRow['delivery_company'])
            ->setLatitude( $shippingAddress['latitude'] )
            ->setLongitude( $shippingAddress['longitude'] )
            ;

        // entry_state

        $addresses[] = $address;

        $address = new Addresses();
        $address->setAddressLine1($orderAddressRow['billing_street_address'])
            ->setAddressLine2($orderAddressRow['billing_suburb'])
            ->setType('payment')
            ->setPostalCode($orderAddressRow['billing_postcode'])
            ->setCity($orderAddressRow['billing_city'])
            ->setFirstName( $paymentAddress['firstname'] )
            ->setLastName( $paymentAddress['lastname'] )
            ->setCountry( $orderAddressRow['billing_country'] )
            ->setCountriesId( $this->mapCountryId( $paymentAddress['entry_country_id'] ) )
            ->setStateProvince( $orderAddressRow['billing_state'] )
            ->setCompanyName($orderAddressRow['billing_company'])
            ->setLatitude( $paymentAddress['latitude'] )
            ->setLongitude( $paymentAddress['longitude'] )
            ;

        $addresses[] = $address;

        return $addresses;
    }

    /**
     * mapStatusToState
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function mapStatusToState( $status )
    {
        static $map = array(
            1 => Orders::STATE_PENDING, // Afventer i oscom
            2 => 9999, // NOTE: In hanzo there is no editing state 
            3 => Orders::STATE_BEING_PROCESSED, // Under behandling
            4 => Orders::STATE_SHIPPED, // Faktureret
        );

        if ( !isset($map[$status]) )
        {
            $this->output->writeln('<error>Unknown order status "'.$status.'"</error>');
            return Orders::STATE_ERROR;
        }

        return $map[$status];
    }

    /**
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function mapCountryId( $id )
    {
        $hanzoId = 58; // Defaults to DK
        $error = true;

        if ( isset( self::$oscomIdToCountryMap[$id] ) )
        {
            $name = self::$oscomIdToCountryMap[$id];
            if ( isset( self::$hanzoNameToIdMap[$name] ) )
            {
                $hanzoId = self::$hanzoNameToIdMap[$name];
                $error = false;
            }
        }

        if ( $error )
        {
            $this->output->writeln('<error>Could not find a country, defaulting to DK (id was: "'.$id.'")</error>');
        }

        return $hanzoId;
    }

    /**
     * mapCountryName
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function mapCountryName( $name )
    {
        if ( !isset( self::$hanzoNameToIdMap[$name] ) )
        {
            $this->output->writeln('<error>Could not find a country, defaulting to DK (name was: "'.$name.'")</error>');
            return self::$hanzoNameToIdMap['Denmark'];
        }

        return self::$hanzoNameToIdMap[$name];
    }
}
