<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

/**
 * ShipAdvisorWS class
 *
 * ShipAdvisor project engine. Filters products for a shopping article.
 */
class ShipAdvisorWS extends SoapClient
{
    private static $classmap = array(
        'DropPointData' => 'DropPointData',
        'OpeningHours' => 'OpeningHours',
        'DayOfWeek' => 'DayOfWeek',
        'RatingData' => 'RatingData',
        'KeyValue' => 'KeyValue',
        'ValueType' => 'ValueType',
        'GetChODCarrierDropPointsOnMap' => 'GetChODCarrierDropPointsOnMap',
        'GetChODCarrierDropPointsOnMapResponse' => 'GetChODCarrierDropPointsOnMapResponse',
        'MasterDropPointWrapper' => 'MasterDropPointWrapper',
        'MasterDropPoint' => 'MasterDropPoint',
        'MasterBusinessHour' => 'MasterBusinessHour',
        'MDPXMPC' => 'MDPXMPC',
        'MasterPostCode' => 'MasterPostCode',
        'MasterDropPointXRating' => 'MasterDropPointXRating',
        'Rating' => 'Rating',
        'GetChODWebShopDropPointsOnMap' => 'GetChODWebShopDropPointsOnMap',
        'GetChODWebShopDropPointsOnMapResponse' => 'GetChODWebShopDropPointsOnMapResponse',
        'GetChODAllDropPointsOnMap' => 'GetChODAllDropPointsOnMap',
        'GetChODAllDropPointsOnMapResponse' => 'GetChODAllDropPointsOnMapResponse',
        'GetChODCarrierDropPointsWithinBounds' => 'GetChODCarrierDropPointsWithinBounds',
        'GetChODCarrierDropPointsWithinBoundsResponse' => 'GetChODCarrierDropPointsWithinBoundsResponse',
        'GetChODWebShopDropPointsWithinBounds' => 'GetChODWebShopDropPointsWithinBounds',
        'GetChODWebShopDropPointsWithinBoundsResponse' => 'GetChODWebShopDropPointsWithinBoundsResponse',
        'GetChODAllDropPointsWithinBounds' => 'GetChODAllDropPointsWithinBounds',
        'GetChODAllDropPointsWithinBoundsResponse' => 'GetChODAllDropPointsWithinBoundsResponse',
        'GetClosestDropPoint' => 'GetClosestDropPoint',
        'GetClosestDropPointResponse' => 'GetClosestDropPointResponse',
        'SearchForDropPoints' => 'SearchForDropPoints',
        'SearchForDropPointsResponse' => 'SearchForDropPointsResponse',
        'ServiceAuthenticationHeader' => 'ServiceAuthenticationHeader',
    );


    public function __construct($wsdl = "http://qa-ws01.facility.dir.dk/ShipAdvisor/Main.asmx?WSDL", $options = array()) {
        foreach(self::$classmap as $key => $value) {
            if(!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }

        parent::__construct($wsdl, $options);
    }

    /**
     *
     *
     * @param GetChODCarrierDropPointsOnMap $parameters
     * @return GetChODCarrierDropPointsOnMapResponse
     */
    public function GetChODCarrierDropPointsOnMap(GetChODCarrierDropPointsOnMap $parameters) {
        return $this->__soapCall('GetChODCarrierDropPointsOnMap', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }

    /**
     *
     *
     * @param GetChODWebShopDropPointsOnMap $parameters
     * @return GetChODWebShopDropPointsOnMapResponse
     */
    public function GetChODWebShopDropPointsOnMap(GetChODWebShopDropPointsOnMap $parameters) {
        return $this->__soapCall('GetChODWebShopDropPointsOnMap', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }

    /**
     *
     *
     * @param GetChODAllDropPointsOnMap $parameters
     * @return GetChODAllDropPointsOnMapResponse
     */
    public function GetChODAllDropPointsOnMap(GetChODAllDropPointsOnMap $parameters) {
        return $this->__soapCall('GetChODAllDropPointsOnMap', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }

    /**
     *
     *
     * @param GetChODCarrierDropPointsWithinBounds $parameters
     * @return GetChODCarrierDropPointsWithinBoundsResponse
     */
    public function GetChODCarrierDropPointsWithinBounds(GetChODCarrierDropPointsWithinBounds $parameters) {
        return $this->__soapCall('GetChODCarrierDropPointsWithinBounds', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }

    /**
     *
     *
     * @param GetChODWebShopDropPointsWithinBounds $parameters
     * @return GetChODWebShopDropPointsWithinBoundsResponse
     */
    public function GetChODWebShopDropPointsWithinBounds(GetChODWebShopDropPointsWithinBounds $parameters) {
        return $this->__soapCall('GetChODWebShopDropPointsWithinBounds', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }

    /**
     *
     *
     * @param GetChODAllDropPointsWithinBounds $parameters
     * @return GetChODAllDropPointsWithinBoundsResponse
     */
    public function GetChODAllDropPointsWithinBounds(GetChODAllDropPointsWithinBounds $parameters) {
        return $this->__soapCall('GetChODAllDropPointsWithinBounds', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }

    /**
     *
     *
     * @param GetClosestDropPoint $parameters
     * @return GetClosestDropPointResponse
     */
    public function GetClosestDropPoint(GetClosestDropPoint $parameters) {
        return $this->__soapCall('GetClosestDropPoint', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }

    /**
     *
     *
     * @param SearchForDropPoints $parameters
     * @return SearchForDropPointsResponse
     */
    public function SearchForDropPoints(SearchForDropPoints $parameters) {
        return $this->__soapCall('SearchForDropPoints', array($parameters), array(
            'uri' => 'http://ws.consignorsupport.no/ShipAdvisor',
            'soapaction' => ''
        ));
    }
}
