<?php
namespace XLite\Module\Shofi\AdvanceSearch\View\ItemsList\Product\Customer;


class Search extends \XLite\View\ItemsList\Product\Customer\Search implements \XLite\Base\IDecorator {

    //My Edditing part
    const PARAM_BY_SUBERB         = 'suburb';
    const PARAM_BY_DISTANCE       = 'distance';
    //End Editing ----------



    /**
     * Return search parameters.
     * :TODO: refactor
     *
     * @return array
     */
    public static function getSearchParams()
    {

        return array_merge(parent::getSearchParams(), array(
            //My Editing Part
            \XLite\Model\Repo\Product::P_BY_SUBERB      => self::PARAM_BY_SUBERB,
            \XLite\Model\Repo\Product::P_BY_DISTANCE    => self::PARAM_BY_DISTANCE,

            //End Edit ----------
        ));


    }

    protected function prepareCnd(\XLite\Core\CommonCell $cnd)
    {
        $cnd = parent::prepareCnd($cnd);

        if($this->getTarget() == 'search') {

            $cnd1 = new \XLite\Core\CommonCell(\XLite\Core\Session::getInstance()->XLiteViewItemsListProductCustomerSearch_search);
            $cnd->{\XLite\Model\Repo\Product::P_SUBSTRING} = $cnd1->substring;
            $cnd->{\XLite\Model\Repo\Product::P_INCLUDING} = '';
            $cnd->{\XLite\Model\Repo\Product::P_BY_SUBERB} = $cnd1->suburb;
            $cnd->{\XLite\Model\Repo\Product::P_BY_DISTANCE} = $cnd1->distance;


        } else if($this->getTarget() == 'category') {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategory()->getCategoryId();
        }


        return $cnd;
    }




}

?>