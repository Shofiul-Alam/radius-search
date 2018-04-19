<?php

/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 */

namespace XLite\Module\Shofi\AdvanceSearch\Model\Repo;

use XLite\Module\Shofi\AdvanceSearch\Model\Extension\Query\Acos;
use XLite\Module\Shofi\AdvanceSearch\Model\Extension\Query\Cos;
use XLite\Module\Shofi\AdvanceSearch\Model\Extension\Query\Radians;
use XLite\Module\Shofi\AdvanceSearch\Model\Extension\Query\Sin;

class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator {

    //My Editing Part
    const P_BY_SUBERB         = 'suburb';
    const P_BY_DISTANCE       = 'distance';
    //End Editing Part-----------


    /**
    /**
     * Prepare vendor search condition Suberb
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed product location Suberb
     *
     * @return void
     */
    protected function prepareCndSuburb(\Doctrine\ORM\QueryBuilder $queryBuilder, $suberb)
    {



        if ($suberb) {

            //->innerJoin('p.productLocation', 'i')

            $em = $queryBuilder->getEntityManager();
            $config = $em->getConfiguration();
            $config->addCustomNumericFunction('acos', Acos::class);
            $config->addCustomNumericFunction('cos', Cos::class);
            $config->addCustomNumericFunction('radians', Radians::class);
            $config->addCustomNumericFunction('sin', Sin::class);

            $cnd = \XLite\Core\Session::getInstance()->XLiteViewItemsListProductCustomerSearch_search;


            if (strpos($suberb, '|') != null && count(explode('|', $suberb) == 3)) {
                $geoCode = explode('|', $suberb);
                $latitude = $geoCode[0];
                $longitude = $geoCode[1];
            } else {
                $codeString = $this->getGeocode($suberb);
                $geoCode = explode('|', $codeString);
                $latitude = $geoCode[0];
                $longitude = $geoCode[1];
            }

            if($cnd['distance'] == null) {
                $distance = "4";
            } else {
                $distance = $cnd['distance'];
            }





            $condition = $queryBuilder->expr()->orX();
            $condition->add($queryBuilder->expr()->lt(
                "(3959 * acos(cos
                        (radians('".$latitude."')) * cos(radians(pl.lat)) * cos(radians
                            (pl.lang) - radians('".$longitude."')) + sin(radians('".$latitude."')) * sin
                        (radians(pl.lat))))", $distance));


            $queryBuilder
                ->innerJoin('p.productLocation', 'pl', 'WITH', 'pl.lat IS NOT NULL')
                ->andWhere($condition);
        }

    }




    public function getGeocode($string){


        $suberb = "";
        $string = str_replace (" ", "+", urlencode($string));
//        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false&key=AIzaSyD4yOpck0mwuk4nJ8HFv7DCl3aH71Etjd4";
        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false&key=AIzaSyDT2_DufkC6x7tpop9YPliZ3FQBiw7WalA";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);


        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] != 'OK') {
            return null;
        }


        $geometry = $response['results'][0]['geometry'];


        $latitude  = $geometry['location']['lat'];
        $longitude = $geometry['location']['lng'];

        $components = $response['results'][0]['address_components'];

        foreach($components as $component) {
            if($component['types'][0] == 'locality') {
                $suberb = $component['long_name'];
            }
        }

        $geoCode = $latitude. "|" . $longitude;




        return $geoCode;

    }


    /**
     * Prepare vendor search condition Product Condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed product Additional Info New
     *
     * @return void
     */
    protected function prepareCndCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $condition)
    {


        if ($condition) {

            $queryBuilder
                ->innerJoin('p.productDetails', 'pd');

            foreach ($condition as $key=>$cond) {
                if($cond === "on") {
                    $queryBuilder->andWhere('pd.toolCondition = :condition')
                                ->setParameter('condition', $key);
                }

            }
        }

    }


    /**
     * Prepare vendor search condition Product Condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed product Additional Info Brands
     *
     * @return void
     */
    protected function prepareCndBrand(\Doctrine\ORM\QueryBuilder $queryBuilder, $brands)
    {


        if ($brands) {
            $queryBuilder
                ->innerJoin('p.productDetails', 'pd');

            foreach ($brands as $key=>$brand) {
                if($brand === "on") {
                    $queryBuilder->andWhere('pd.brand = :condition')
                        ->setParameter('condition', $key);
                }

            }
        }

    }


    /**
     * Prepare vendor search condition Product Condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed product Additional Info Brands
     *
     * @return void
     */
    protected function prepareCndPower(\Doctrine\ORM\QueryBuilder $queryBuilder, $powers)
    {


        if ($powers) {
            $queryBuilder
                ->innerJoin('p.productDetails', 'pd');

            foreach ($powers as $key=>$brand) {
                if($brand === "on") {
                    $queryBuilder->andWhere('pd.powerSource = :condition')
                        ->setParameter('condition', $key);
                }

            }
        }

    }

    /**
     * Create a new QueryBuilder instance that is pre-populated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from. OPTIONAL
     * @param string $code    Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy, $code);


        $queryBuilder->andWhere('p.product_id NOT IN (61, 62, 63)');


        return $queryBuilder;
    }




}

?>