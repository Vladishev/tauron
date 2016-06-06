<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 */

/**
 * Class Tim_Tauron_Block_Onepage_Shipping_Method_Available. Rewrites block.
 *
 * @category  Tim
 * @package   Tim_Tauron
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    const CART_GRAND_TOTAL = 99;
    /*
     * Rewrite method for choosing only one shipping method
     * @return array
     */
    public function getShippingRates()
    {
        $rates = parent::getShippingRates();
        $quoteGrandTotal = Mage::getModel('checkout/session')->getQuote()->getGrandTotal();
        if ($quoteGrandTotal < self::CART_GRAND_TOTAL) {
            if (array_key_exists('flatrate', $rates)) {
                $rates = array('flatrate' => $rates['flatrate']);
            }
        } else {
            if (array_key_exists('freeshipping', $rates)) {
                $rates = array('freeshipping' => $rates['freeshipping']);
            }
        }

        return $rates;
    }
}