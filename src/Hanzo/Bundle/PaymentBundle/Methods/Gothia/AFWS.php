<?php /* vim: set sw=4: */

// Ange sökvägen till nusoap biblioteket
require_once(dirname(__FILE__).'/nusoap/nusoap.php');

// Funktion för att initiera WSDL klienten
function AFSWS_Init($mode = 'live')
{
	// Ange sökvägen till webservicen
	$wsdl = 'http://clienttesthorizon.gothiagroup.com/AFSServices/AFSService.svc?wsdl';

    if ($mode != 'test') {
	    $wsdl = 'https://horizonws.gothiagroup.com/AFSServices/AFSService.svc?wsdl';
    }

	// Skapa en klient för den angiva webservicen
	$client = new nusoap_client($wsdl, 'wsdl');

	// Ange namespaces som ska användas
	$client->namespaces = array(
		'SOAP-ENV' => 'http://schemas.xmlsoap.org/soap/envelope/',
		'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
		'xsd' => 'http://www.w3.org/2001/XMLSchema',
		'akt' => 'http://Horizon.ExternalServices',
		'akt1' => 'http://Horizon.ExternalServices.AFS',
		'tem' => 'http://tempuri.org/'
	);

	// Kolla om fel uppstod, i så fall skriv ut dessa
	$err = $client->getError();
	if ($err) {
		echo '<h2>Constructor error</h2><pre>'.$err.'</pre>';
	}

	return $client;
}

// Funktioner för att förenkla skapandet av taggar
function AFSWS_Tag($tagName, $value, $namespace = '')
{
	if (is_null($value)) return '';
	return empty($value) ? AFSWS_ClosedTag($tagName, $namespace) : AFSWS_StartTag($tagName, $namespace).$value.AFSWS_EndTag($tagName, $namespace);
}

function AFSWS_StartTag($tagName, $namespace = '')
{
	if (!empty($namespace)) $tagName = $namespace.':'.$tagName;
	return '<'.$tagName.'>';
}

function AFSWS_EndTag($tagName, $namespace = '')
{
	if (!empty($namespace)) $tagName = $namespace.':'.$tagName;
	return '</'.$tagName.'>';
}

function AFSWS_ClosedTag($tagName, $namespace = '')
{
	if (!empty($namespace)) $tagName = $namespace.':'.$tagName;
	return '<'.$tagName.'/>';
}

// Funktion för att skapa ett användarobjekt
function AFSWS_User($username, $password, $clientID)
{
	$ns = 'akt';

	$userData = AFSWS_Tag('Username', $username, $ns);
	$userData = $userData.AFSWS_Tag('Password', $password, $ns);
	$userData = $userData.AFSWS_Tag('ClientID', $clientID, $ns);

	return AFSWS_Tag('user', $userData);
}

// Funktion för att hämta felmeddelanden ur ett svar på en förfrågan
function AFSWS_GetErrors($response)
{
	$errorMessages = array();

	if (!is_null($response) && is_array($response) && array_key_exists('Errors', $response) && !is_null($response['Errors'])) {
		if (array_key_exists('ID', $response['Errors']['ResponseMessageBase'])) {
			$error = $response['Errors']['ResponseMessageBase'];
			$errorMessages[] = array('ID' => $error['ID'], 'Message' => $error['Message']);
		} else {
			foreach ($response['Errors']['ResponseMessageBase'] as $error) {
				$errorMessages[] = array('ID' => $error['ID'], 'Message' => $error['Message']);
			}
		}
	}

	return $errorMessages;
}

// Funktion för att skapa hämta en kund
function AFSWS_GetCustomer($user, $search)
{
	return '<GetCustomer xmlns="http://tempuri.org/">'.$user.$search.'</GetCustomer>';
}

// Funktion för att söka efter en kund via många olika sökparametrar
function AFSWS_GetCustomerInfo($customerNo = null, $orgno = null, $firstname = null, $lastname = null, $ssn = null, $bornDate = null, $address = null,
	$postalCode = null, $postalPlace = null, $countryCode = null)
{
	$customerData = AFSWS_Tag('Address', $address);
	$customerData = $customerData.AFSWS_Tag('customerNo', $customerNo);
	$customerData = $customerData.AFSWS_Tag('orgno', $orgno);
	$customerData = $customerData.AFSWS_Tag('firstname', $firstname);
	$customerData = $customerData.AFSWS_Tag('lastname', $lastname);
	$customerData = $customerData.AFSWS_Tag('ssn', $ssn);
	$customerData = $customerData.AFSWS_Tag('bornDate', $bornDate);
	$customerData = $customerData.AFSWS_Tag('address', $address);
	$customerData = $customerData.AFSWS_Tag('postalCode', $postalCode);
	$customerData = $customerData.AFSWS_Tag('postalPlace', $postalPlace);
	$customerData = $customerData.AFSWS_Tag('countryCode', $countryCode);

	return $customerData;
}

// Funktion för att hämta kundinformation
function AFSWS_CheckCustomer($user, $customer)
{
	return '<CheckCustomer xmlns="http://tempuri.org/">'.$user.$customer.'</CheckCustomer>';
}

// Funktion för att skapa ett kundobjekt
function AFSWS_Customer($address = null, $countryCode = null, $currencyCode = null, $customerNo = null, $customerCategory = null, $directPhone = null,
	$distributionBy = null, $distributionType = null, $email = null, $fax = null, $firstName = null, $lastName = null, $mobilePhone = null, $orgNoSSN = null,
	$phone = null, $postalCode = null, $postalPlace = null, $statCodeAlphaNum = null, $statCodeNum = null
) {
	$ns = 'akt1';

	$customerData = AFSWS_Tag('Address', $address, $ns);
	$customerData = $customerData.AFSWS_Tag('CountryCode', $countryCode, $ns);
	$customerData = $customerData.AFSWS_Tag('CurrencyCode', $currencyCode, $ns);
	$customerData = $customerData.AFSWS_Tag('CustNo', $customerNo, $ns);
	$customerData = $customerData.AFSWS_Tag('CustomerCategory', $customerCategory, $ns);
	$customerData = $customerData.AFSWS_Tag('DirectPhone', $directPhone, $ns);
	$customerData = $customerData.AFSWS_Tag('DistributionBy', $distributionBy, $ns);
	$customerData = $customerData.AFSWS_Tag('DistributionType', $distributionType, $ns);
	$customerData = $customerData.AFSWS_Tag('Email', $email, $ns);
	$customerData = $customerData.AFSWS_Tag('Fax', $fax, $ns);
	$customerData = $customerData.AFSWS_Tag('FirstName', $firstName, $ns);
	$customerData = $customerData.AFSWS_Tag('LastName', $lastName, $ns);
	$customerData = $customerData.AFSWS_Tag('MobilePhone', $mobilePhone, $ns);
	$customerData = $customerData.AFSWS_Tag('Organization_PersonalNo', $orgNoSSN, $ns);
	$customerData = $customerData.AFSWS_Tag('Phone', $phone, $ns);
	$customerData = $customerData.AFSWS_Tag('PostalCode', $postalCode, $ns);
	$customerData = $customerData.AFSWS_Tag('PostalPlace', $postalPlace, $ns);
	$customerData = $customerData.AFSWS_Tag('StatCodeAlphaNumeric', $statCodeAlphaNum, $ns);
	$customerData = $customerData.AFSWS_Tag('StatCodeNumeric', $statCodeNum, $ns);

	return AFSWS_Tag('customer', $customerData);
}

// Funktion för att skapa ett orderobjekt
function AFSWS_Order($allowPartlyShipment = null, $comments = null, $currencyCode = null, $customerNo = null, $deliveryAddress = null,
	$deliveryCountryCode = null, $deliveryPostalCode = null, $deliveryPostalPlace = null, $discountProfileNo = null, $estimatedShipDate = null,
	$exchangeRate = null, $invoiceLayoutNo = null, $invoiceProfileNo = null, $orderDate = null, $orderLines = null, $orderNo = null, $ourRef = null,
	$statCodeAlphaNum = null, $statCodeNum = null, $yourRef = null
) {
	$ns = 'akt1';

	$orderData = AFSWS_Tag('AllowPartlyShipment', $allowPartlyShipment, $ns);
	$orderData = $orderData.AFSWS_Tag('Comments', $comments, $ns);
	$orderData = $orderData.AFSWS_Tag('CurrencyCode', $currencyCode, $ns);
	$orderData = $orderData.AFSWS_Tag('CustomerNo', $customerNo, $ns);
	$orderData = $orderData.AFSWS_Tag('DeliveryAddress', $deliveryAddress, $ns);
	$orderData = $orderData.AFSWS_Tag('DeliveryCountryCode', $deliveryCountryCode, $ns);
	$orderData = $orderData.AFSWS_Tag('DeliveryPostalCode', $deliveryPostalCode, $ns);
	$orderData = $orderData.AFSWS_Tag('DeliveryPostalPlace', $deliveryPostalPlace, $ns);
	$orderData = $orderData.AFSWS_Tag('DiscountProfileNo', $discountProfileNo, $ns);
	$orderData = $orderData.AFSWS_Tag('EstimatedShipDate', $estimatedShipDate, $ns);
	$orderData = $orderData.AFSWS_Tag('ExchangeRate', $exchangeRate, $ns);
	$orderData = $orderData.AFSWS_Tag('InvoiceLayoutNo', $invoiceLayoutNo, $ns);
	$orderData = $orderData.AFSWS_Tag('InvoiceProfileNo', $invoiceProfileNo, $ns);
	$orderData = $orderData.AFSWS_Tag('OrderDate', $orderDate, $ns);
	$orderData = $orderData.AFSWS_Tag('OrderLines', $orderLines, $ns);
	$orderData = $orderData.AFSWS_Tag('OrderNo', $orderNo, $ns);
	$orderData = $orderData.AFSWS_Tag('OurRef', $ourRef, $ns);
	$orderData = $orderData.AFSWS_Tag('StatCodeAlphaNum', $statCodeAlphaNum, $ns);
	$orderData = $orderData.AFSWS_Tag('StatCodeNum', $statCodeNum, $ns);
	$orderData = $orderData.AFSWS_Tag('YourRef', $yourRef, $ns);

	return AFSWS_Tag('order', $orderData);
}

// Funktion för att lägga till en orderrad
function AFSWS_OrderLine($itemDescription, $itemID, $orderLineNo, $quantity, $taxPercent, $unitCode, $unitPrice)
{
	$ns = 'akt1';

	$orderLineData = AFSWS_Tag('ItemDescription', $itemDescription, $ns);
	$orderLineData = $orderLineData.AFSWS_Tag('ItemID', $itemID, $ns);
	$orderLineData = $orderLineData.AFSWS_Tag('OrderLineNo', $orderLineNo, $ns);
	$orderLineData = $orderLineData.AFSWS_Tag('Quantity', $quantity, $ns);
	$orderLineData = $orderLineData.AFSWS_Tag('TaxPercent', $taxPercent, $ns);
	$orderLineData = $orderLineData.AFSWS_Tag('UnitCode', $unitCode, $ns);
	$orderLineData = $orderLineData.AFSWS_Tag('UnitPrice', $unitPrice, $ns);

	return AFSWS_Tag('AFSOrderLine', $orderLineData, $ns);
}

// Funktion för att lägga till alternativ för aktiveringstyp på fakturan
function AFSWS_OrderOptions($accountOfferType = null, $noInvoicePurchase = null, $orderActivationType = null)
{
	$ns = 'akt1';

	$orderOptions = AFSWS_Tag('AccountOfferType', $accountOfferType, $ns);
	$orderOptions = $orderOptions.AFSWS_Tag('NoInvoicePurchase', $noInvoicePurchase, $ns);
	$orderOptions = $orderOptions.AFSWS_Tag('OrderActivationType', $orderActivationType, $ns);

	return AFSWS_Tag('orderOptions', $orderOptions);
}

// Funktion för att skapa en ny order
function AFSWS_InsertOrder($user, $order)
{
	return '<InsertOrder xmlns="http://tempuri.org/">'.$user.$order.'</InsertOrder>';
}

// Funktion för att skapa en ny order med avancerade alternativ
function AFSWS_InsertOrderAdv($user, $order, $orderOptions)
{
	return '<InsertOrderAdv xmlns="http://tempuri.org/">'.$user.$order.$orderOptions.'</InsertOrderAdv>';
}

// Funktion för att skapa omvandla en order till faktura
function AFSWS_ShipOrder($user, $orderNo, $shipmentCost, $fee, $shipmentProvider, $shipmentPackageNo)
{
	$shipOrderData = AFSWS_Tag('orderNo', $orderNo);
	$shipOrderData = $shipOrderData.AFSWS_Tag('shipmentCost', $shipmentCost);
	$shipOrderData = $shipOrderData.AFSWS_Tag('fee', $fee);
	$shipOrderData = $shipOrderData.AFSWS_Tag('shipmentProvider', $shipmentProvider);
	$shipOrderData = $shipOrderData.AFSWS_Tag('shipmentPackageNo', $shipmentPackageNo);

	return '<ShipOrder xmlns="http://tempuri.org/">'.$user.$shipOrderData.'</ShipOrder>';
}

// hf@bellcom.dk, 17-aug-2011: added extra calls -->>
/**
 * AFSWS_PlaceReservation
 * @param string $user Output of AFSWS_Customer function
 * @param string $reservation Output of AFSWS_Reservation function
 * @return string
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function AFSWS_PlaceReservation( $user, $reservation )
{
  return '<PlaceReservation xmlns="http://tempuri.org/">'.$user.$reservation.'</PlaceReservation>';
}

/**
 * AFSWS_Reservation
 * @param string $accountOfferType
 * @param int $amount
 * @param string $currencyCode
 * @param string $customerNo
 * @param string $orderNo
 * @return string
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function AFSWS_Reservation($accountOfferType, $amount, $currencyCode, $customerNo, $orderNo)
{
	$ns = 'akt1';

    $reservationData  = AFSWS_Tag('AccountOfferType',$accountOfferType,$ns);
    $reservationData .= AFSWS_Tag('Amount',$amount,$ns);
    $reservationData .= AFSWS_Tag('CurrencyCode',$currencyCode,$ns);
    $reservationData .= AFSWS_Tag('CustomerNo',$customerNo,$ns);
    $reservationData .= AFSWS_Tag('OrderNo',$orderNo,$ns);

	return AFSWS_Tag('reservation', $reservationData);
}

/**
 * AFSWS_CancelReservation(
 * @param string $user
 * @param string $cancelReservation
 * @return string
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function AFSWS_CancelReservation($user, $cancelReservation)
{
  return '<CancelReservation xmlns="http://tempuri.org/">'.$user.$cancelReservation.'</CancelReservation>';
}

/**
 * AFSWS_CancelReservationObj
 * @param string $customerNo
 * @param string $orderNo
 * @param string $amount
 * @return string
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function AFSWS_CancelReservationObj($customerNo, $orderNo = null, $amount = '')
{
	$ns = 'akt1';

    $reservationData  = AFSWS_Tag('CustomerNo',$customerNo,$ns);
    $reservationData .= AFSWS_Tag('Amount',$amount,$ns);
    $reservationData .= AFSWS_Tag('OrderNo',$orderNo,$ns);

	return AFSWS_Tag('cancelReservation', $reservationData);
}

// <<-- hf@bellcom.dk, 17-aug-2011: added extra calls

// <<-- ab@bellcom.dk, 10-06-13: added check'n'place


// Include the additional Reservation Info block,
// ab@bellcom.dk
function AFSWS_AdditionalReservationInfo($bankAccount = NULL, $bankId = NULL, $PaymentMethod)
{
	$ns = 'akt1';

	$additionalInfos = AFSWS_Tag('DirectDebetBankAccount', $bankAccount, $ns);
	$additionalInfos .= AFSWS_Tag('DirectDebetBankID', $bankId, $ns);
	$additionalInfos .= AFSWS_Tag('PaymentMethod', $PaymentMethod, $ns);

	return AFSWS_Tag('additionalReservationInfo', $additionalInfos);
}
/**
 * AFSWS_PlaceReservation
 * @param string $user Output of AFSWS_Customer function
 * @param string $reservation Output of AFSWS_Reservation function
 * @return string
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function AFSWS_CheckCustomerAndPlaceReservation($user, $customer, $reservation, $additionalReservationInfo = '')
{
    return '<CheckCustomerAndPlaceReservation xmlns="http://tempuri.org/">'.$user.$customer.$reservation.$additionalReservationInfo.'</CheckCustomerAndPlaceReservation>';
}
