<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Gothia;

/**
 * Class GothiaApiCallResponse
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\Gothia
 */
class GothiaApiCallResponse
{
    /**
     * undocumented class variable
     *
     * @var string
     **/
    public $data;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $isError = false;

    /**
     * undocumented class variable
     *
     * @var array
     **/
    public $errors;

    /**
     * Transaction id returned from Gothia, so the call can be matched to the webservice log at Gothia
     *
     * @var string
     **/
    public $transactionId = null;

    public $prettyErrors = [
        5000  => 'We are unable to connect to Gothia Invoice service, please try again later.', // System error occured, contact service supplier or try again later
        10004 => 'We couldn\'t find you in Gothia Invoice Service. Please be sure that all your details are correct on your profile page.', // Customer not found
        10006 => 'We couldn\'t find you in Gothia Invoice Service. Please be sure that all your details are correct on your profile page.', // Customer not found in external DB
        10036 => 'The reservation was not approved at Gothia Invoice Service. You may have exceeded the limit of reservations at Gothia.', // Reservation is not approved
        10041 => 'Your account information is incorrect. Please check that you have entered the correct bank code and account number.', // InvalidPaymentInfo
    ];


    /**
     * @param array  $rawResponse
     * @param string $function
     */
    public function __construct($rawResponse, $function)
    {
        $this->parse($rawResponse, $function);
        $this->checkResponseForErrors($rawResponse);
    }


    /**
     * @return string
     */
    public function isError()
    {
        return $this->isError;
    }

    /**
     * Set error to true
     */
    public function setIsError()
    {
        $this->isError = true;
    }


    /**
     * @param array  $rawResponse
     * @param string $function
     */
    protected function parse($rawResponse, $function)
    {
        switch ($function) {
            case 'CheckCustomer':
                if (isset($rawResponse['CheckCustomerResult']['TransactionID'])) {
                    $this->transactionId = $rawResponse['CheckCustomerResult']['TransactionID'];
                }

                if (!isset($rawResponse['CheckCustomerResult']['Success'])) {
                    $this->setIsError();
                } else {
                    if ($rawResponse['CheckCustomerResult']['Success'] !== 'true') {
                        $this->setIsError();
                    }
                }

                if (!isset($rawResponse['CheckCustomerResult']['Customer'])) {
                    $this->setIsError();
                }

                if (!$this->isError()) {
                    foreach ($rawResponse['CheckCustomerResult']['Customer'] as $key => $value) {
                        $this->data[$key] = $value;
                    }
                }

                if (isset($this->data['PurchaseStop']) && ($this->data['PurchaseStop'] === 'true')) {
                    $this->isError = true;
                }

                break;
            case 'CheckCustomerAndPlaceReservation':
                if (isset($rawResponse['CheckCustomerAndPlaceReservationResult']['TransactionID'])) {
                    $this->transactionId = $rawResponse['CheckCustomerAndPlaceReservationResult']['TransactionID'];
                }

                if (!isset($rawResponse['CheckCustomerAndPlaceReservationResult']['Success'])) {
                    $this->setIsError();
                } else {
                    if ($rawResponse['CheckCustomerAndPlaceReservationResult']['Success'] !== 'true') {
                        $this->setIsError();
                    }
                }

                if (!isset($rawResponse['CheckCustomerAndPlaceReservationResult']['Customer'])) {
                    $this->setIsError();
                }

                if (!isset($rawResponse['CheckCustomerAndPlaceReservationResult']['ReservationApproved']) || ($rawResponse['CheckCustomerAndPlaceReservationResult']['ReservationApproved'] !== 'true')) {
                    $this->setIsError();
                }

                if (!$this->isError()) {
                    foreach ($rawResponse['CheckCustomerAndPlaceReservationResult']['Customer'] as $key => $value) {
                        $this->data[$key] = $value;
                    }
                    foreach ($rawResponse['CheckCustomerAndPlaceReservationResult']['Reservation'] as $key => $value) {
                        $this->data[$key] = $value;
                    }
                }

                if (isset($this->data['PurchaseStop']) && ($this->data['PurchaseStop'] === 'true')) {
                    $this->isError = true;
                }

                break;

            case 'CancelReservation':
                if (isset($rawResponse['CancelReservationResult']['TransactionID'])) {
                    $this->transactionId =  $rawResponse['CancelReservationResult']['TransactionID'];
                }

                if (!isset($rawResponse['CancelReservationResult']['Success']) || ($rawResponse['CancelReservationResult']['Success'] !== 'true')) {
                    $this->setIsError();
                }

                if (!$this->isError()) {
                    foreach ($rawResponse['CancelReservationResult'] as $key => $value) {
                        $this->data[$key] = $value;
                    }
                }
                break;

            case 'PlaceReservation':
                if (isset($rawResponse['PlaceReservationResult']['TransactionID'])) {
                    $this->transactionId =  $rawResponse['PlaceReservationResult']['TransactionID'];
                }

                if (!isset($rawResponse['PlaceReservationResult']['ReservationApproved']) || ($rawResponse['PlaceReservationResult']['ReservationApproved'] !== 'true')) {
                    $this->setIsError();
                }

                if (!$this->isError()) {
                    foreach ($rawResponse['PlaceReservationResult']['Reservation'] as $key => $value) {
                        $this->data[$key] = $value;
                    }
                }
                break;
        }
    }


    /**
     * @param array $rawResponse
     */
    private function checkResponseForErrors($rawResponse)
    {

        if (is_array($rawResponse)) {
            foreach ($rawResponse as $key => $data) {
                if (isset($data['Errors']) && !empty($data['Errors']) && is_array($data['Errors'])) {
                    foreach ($data['Errors'] as $errorKey => $errorData) {
                        if (!empty($errorData)) {
                            if (!isset($errorData['ID']) && isset($errorData[0]['ID'])) {
                                foreach ($errorData as $subError) {
                                    $this->errors[] = (isset( $this->prettyErrors[$subError['ID']] )) ? $this->prettyErrors[$subError['ID']] : $subError['Message'];
                                }
                            } else {
                                $this->errors[] = (isset( $this->prettyErrors[$errorData['ID']] )) ? $this->prettyErrors[$errorData['ID']] : $errorData['Message'];
                            }
                        }
                    }
                }

                if (isset($data['TemporaryExternalProblem']) && ($data['TemporaryExternalProblem'] !== 'false')) {
                    $this->errors[] = 'We are unable to connect to Gothia Invoice service, please try again later.';
                }
            }
        }

        if (!empty($this->errors)) {
            $this->setIsError();
        }
    }
}
