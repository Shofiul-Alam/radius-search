<?php
/**
 * Created by PhpStorm.
 * User: Shofiul
 * Date: 5/04/2017
 * Time: 2:19 PM
 */

namespace XLite\Module\Shofi\AdvanceSearch\View\FormField\Inline;

class AInline extends \XLite\View\FormField\Inline\AInline implements \XLite\Base\IDecorator {

    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        return parent::defineFields();
    }

    public function getViewValueDaily_price($field) {
        if( $this->getEntity()->getAPrice()) {
            return $this->getEntity()->getAPrice()->getDailyPrice();
        } else {
            $value = $field['widget']->getValue();
            $result = ('' === (string) $value) ? $this->getEmptyValue($field) : $value;

            return $result;
        }

    }
}

