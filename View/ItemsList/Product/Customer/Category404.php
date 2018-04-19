<?php
namespace XLite\Module\Shofi\AdvanceSearch\View\ItemsList\Product\Customer;

//
///**
// * @ListChild (list="itemsList.product.cart", zone="customer")
// */
class Category404 extends \XLite\View\AView
{

    public static function getAllowedTargets() {
        $list = parent::getAllowedTargets();
        $list [] = 'search';

        return $list;

    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {

        return 'modules/Shofi/AdvanceSearch/item_list/product/cart_tray/category404.twig';
    }


}

?>