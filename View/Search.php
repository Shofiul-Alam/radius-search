<?php

namespace XLite\Module\Shofi\AdvanceSearch\View;

class search extends \XLite\View\Search implements \XLite\Base\IDecorator {
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/Shofi/AdvanceSearch/product/search';
    }

    public function hasProducts() {
        $count = \XLite\View\ItemsList\Product\Customer\Search::getInstance()->hasResultsPublic();
        $result = false;

        if($count) {
            $result = true;
        }
        return $result;

    }
}