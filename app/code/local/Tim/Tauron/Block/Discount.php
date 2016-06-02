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
 * Collect products with special price
 *
 * Class Tim_Tauron_Block_Discount
 */
class Tim_Tauron_Block_Discount extends Mage_Catalog_Block_Product_List/*Mage_Core_Block_Template*/
{
    /**
     * Returns collection with products having discount
     *
     * @return object Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getDiscountProducts()
    {
        $_productCollection = Mage::getModel('catalog/product')->getCollection();
        $_productCollection->addAttributeToSelect(array(
            'image',
            'name',
            'short_description'
            ))
            ->addFieldToFilter('visibility', array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
            )) //showing just products visible in catalog or both search and catalog
            ->addFinalPrice()
//            ->addAttributeToSort('price', 'asc') //in case we would like to sort products by price
            ->getSelect()
            ->where('price_index.final_price < price_index.price')
//            ->limit(30) //we can specify how many products we want to show on this page
//            ->order(new Zend_Db_Expr('RAND()')) //in case we would like to sort products randomly
            ;
        Mage::getModel('review/review')->appendSummary($_productCollection);

        return $_productCollection;
    }
}