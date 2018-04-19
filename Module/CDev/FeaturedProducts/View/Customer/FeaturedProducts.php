<?php
/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 */

namespace XLite\Module\Shofi\AdvanceSearch\Module\CDev\FeaturedProducts\View\Customer;


class FeaturedProducts extends \XLite\Module\CDev\FeaturedProducts\View\Customer\FeaturedProducts implements \XLite\Base\IDecorator {

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $repository = $this->getRepository();
        $featuredProduct = $repository ? $repository->search($cnd, $countOnly) : 0;
        if($countOnly) {
            return $featuredProduct;

        } else {
            shuffle($featuredProduct);

            return $featuredProduct;
        }

    }
}

?>