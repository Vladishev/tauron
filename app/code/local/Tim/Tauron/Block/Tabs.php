<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

/**
 * Class Tim_Tauron_Block_Tabs
 */
class Tim_Tauron_Block_Tabs extends Mage_Core_Block_Template
{
    /**
     * Returns array 'Tab title' => 'statick block id'
     *
     * @return array
     */
    public function getTabsTitles()
    {
        return array(
            'Rewolucja LED' => 'tim_tabs1',
            'E14 - parametry' => 'tim_tabs2',
            'E27 - parametry' => 'tim_tabs3',
            'Mity dotyczące żarówek LED' => 'tim_tabs4',
            'Zobacz film' => 'tim_tabs5',
            );
    }
}