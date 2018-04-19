<?php
namespace XLite\Module\Shofi\AdvanceSearch\View;


/**
 *
 *
 */

class SearchForm extends \XLite\View\AView {

    public static function  getAllowedTargets() {
        $result[] = 'search';
        $result[] = 'category';
        $result[] = 'main';
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
        return 'modules/Shofi/AdvanceSearch/searchForm.twig';
    }

    public function getCondition() {
//
//        if($this->getTarget() == "category") {
//            $cnd = [];
//        } else {
            $cnd = new \XLite\Core\CommonCell(\XLite\Core\Session::getInstance()->XLiteViewItemsListProductCustomerSearch_search);

//        }

        return $cnd;
    }

}

?>