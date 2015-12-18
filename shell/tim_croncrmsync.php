<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
require '../app/Mage.php';
ini_set('display_errors', 1);
Mage::app();
$croncrmsync = Mage::getModel('croncrmsync/croncrmsync');
$croncrmsync->crmsynchronization();
exit;