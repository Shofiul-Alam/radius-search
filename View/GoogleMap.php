<?php
namespace XLite\Module\Shofi\AdvanceSearch\View;

/**
 * Class GoogleMap
 *
 *
 */

use \XLite\Module\XC\Geolocation\Logic;

class GoogleMap extends \XLite\View\ItemsList\Product\Customer\Search {

    public static function getAllowedTargets() {

        $result[] = 'search';
        $result[] = 'category';
        $result[] = 'profile';
        return $result;
    }
    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'modules/Shofi/AdvanceSearch/js/search.js'
            ]
        );
    }

    public function getDefaultTemplate() {
        return 'modules\Shofi\AdvanceSearch\google_map.twig';
    }

    public function getProductsAsJoson() {

        $allProducts = $this->getQueryProducts();
        $arrayObj = array();
        foreach($allProducts as $product) {
            if($product->getProductLocation() != null && $product->getCategoryId() != null) {
                $arrayObj[] = $this->Object_to_array($product);
            }

        }


        $userLoc = $this->prepareUserLoc($this->getUserObj());

        $arrayObj[] = $userLoc;


        return json_encode($arrayObj);
    }

    private function object_to_array($obj)
    {


        $result = array();

        if (is_object($obj))
        {

            $result['title'] = $obj->getName();
            if($obj->getImage() != null) {
                $result['image'] = $obj->getImage()->getUrl();
            }
            $result['category_id'] = $obj->getCategoryId() ;
            if($obj->getCategory()->getMapIcon() != null) {
                $result['markerIcon'] = $obj->getCategory()->getMapIcon()->getUrl();
            }

            $result['url'] = $obj->getURL();
            $result['mapX'] = $obj->getProductLocation()->lat;
            $result['mapY'] = $obj->getProductLocation()->lang;

            if($obj->getProcessedDescription() != null){
                $result['description'] = $this->limit_text($obj->getProcessedDescription(), 15);
            }
            $result['linkText'] = 'Show';
        }





        return $result;
    }

    private function getUserLocation() {

        $userGeoloc = $this->getUserObj();

        return json_encode($userGeoloc);

    }

    private function getUserObj() {

//        $addrArray = Logic\Geolocation::getInstance()->getLocation(new Logic\GeoInput\IpAddress);
        $addrArray = \XLite\Module\XC\Geolocation\Controller\Customer\LocationSelect::getInstance()->getAddress();

        $addressString = '';

        foreach($addrArray as $value) {
            $addressString .= $value . ' ';
        }

        return $this->getGeocode($addressString);
    }

    public function getGeocode($string){



        $string = str_replace (" ", "+", urlencode($string));
        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false&key=AIzaSyAf0e0NGsj0WgiyGhuXcc_qXG_yhVwWFoU";

        $response = $this->intGeoCurl($details_url);

        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] == 'OVER_QUERY_LIMIT' || $response['status'] == 'REQUEST_DENIED') {
            $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false&key=AIzaSyBtz_fTcsPh1Upqr5rGEEQ_JRDndjestFM";

            $response = $this->intGeoCurl($details_url);

            if($response['status'] == 'OVER_QUERY_LIMIT' || $response['status'] == 'REQUEST_DENIED') {
                $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $string . "&sensor=false&key=AIzaSyDDxIvKx_ezWfC5AcMfhc7hI-Hac3CjLFw";
                $response = $this->intGeoCurl($details_url);

                if ($response['status'] == 'OVER_QUERY_LIMIT' || $response['status'] == 'REQUEST_DENIED') {
                    return null;

                }
            }

        }


        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] != 'OK') {
            return null;
        }


        $components = $response['results'][0]['address_components'];

        foreach($components as $component) {
            if($component['types'][0] == 'locality') {
                $suberb = $component['long_name'];
            }
        }

        $geometry = $response['results'][0]['geometry'];


        $latitude  = $geometry['location']['lat'];
        $longitude = $geometry['location']['lng'];

        $array = array(
            'latitude' => $latitude,
            'longitude' => $longitude,
            'suberb' => $suberb,
            'location_type' => $geometry['location_type'],
        );



        return $array;

    }

    public function intGeoCurl($details_url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);

        return $response;
    }

    private function prepareUserLoc($obj) {


        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        if($profile) {
            $profileLink = $profile->getCleanURL();
            if($profile->getVendorImage()) {
                $profileImage = $profile->getVendorImage()->getURL();
            } else {
                $profileImage = 'https://upload.wikimedia.org/wikipedia/commons/d/d3/User_Circle.png';
            }
            $profileLinkText = 'Profile';
            if($profile->getVendorFirstName() && $profile->getVendorFirstName()!= 'Vendor') {
                $profileTitle = $profile->getVendorFirstName();
            } else {
                $profileTitle = $profile->getFirstName();
            }

        } else {
            $profileLink = '/?target=profile&mode=register';
            $profileImage = 'https://upload.wikimedia.org/wikipedia/commons/d/d3/User_Circle.png';
            $profileLinkText = 'Register';
            $profileTitle = 'Vistor';
        }

        $result = array();

        if (is_array($obj))
        {

            $result['title'] = "Hi! ". $profileTitle;

            $result['image'] = $profileImage;

            $result['category_id'] = "";
            $result['markerIcon'] = "https://toolmateshire.com.au/images/arrow.png";


            $result['url'] = $profileLink;
            $result['mapX'] = $obj['latitude'];
            $result['mapY'] = $obj['longitude'];
            $result['description'] = "This is your location";
            $result['linkText'] = $profileLinkText;

        }





        return $result;

    }



    private function limit_text($string, $limit) {

        $rawText = str_replace('"', '', strip_tags($string));
        $text = str_replace("'", '', strip_tags($rawText));

        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }

        return $text;
    }

    private function getQueryProducts(){

        if($this->getTarget() == 'search') {
            $cnd = new \XLite\Core\CommonCell(\XLite\Core\Session::getInstance()->XLiteViewItemsListProductCustomerSearch_search);
            $queryProducts = \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
                $this->prepareCnd($cnd)
            );
        } else if($this->getTarget() == 'category') {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategory()->getCategoryId();
            $queryProducts = \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
                $this->prepareCnd($cnd)
            );
        }


        return $queryProducts;
    }

}