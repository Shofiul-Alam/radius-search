<?php
/**
 * Created by PhpStorm.
 * User: Shofiul
 * Date: 5/04/2017
 * Time: 1:44 PM
 */

namespace XLite\Module\Shofi\AdvanceSearch\View\Model\Product\Admin;


class Search extends \XLite\View\ItemsList\Model\Product\Admin\Search implements \XLite\Base\IDecorator {

    protected function defineColumns()
    {
        $columns = parent::defineColumns();
        
        if(!(\XLite\Core\Auth::getInstance()->hasRootAccess())) {
            unset($columns['price']);
        }


        $columns['daily_price'] = array (
            static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Daily Price'),
            static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Price',
            static::COLUMN_PARAMS  => array('min' => 0),
            static::COLUMN_SORT    => static::SORT_BY_MODE_PRICE,
            static::COLUMN_ORDERBY => 410,
        );

        return $columns;
    }
}