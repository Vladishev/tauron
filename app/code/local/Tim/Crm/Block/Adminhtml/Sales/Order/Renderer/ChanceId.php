<?php

class Tim_Crm_Block_Adminhtml_Sales_Order_Renderer_ChanceId extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
//        var_dump($row);die;
        if(!$row->getTimChanceId() && $row->getTimSentToCrm() === '0'){
            return '<center><span style="font-weight:bold;color:#d4ae0e;">'.Mage::helper('sales')->__('Prepare to send').'</span></center>';
        }
        
        if($row->getTimError()){
            $errorUrl = Mage::getModel('adminhtml/url')->getUrl('crm_admin/adminhtml_manual/readerrormessage', array( 'id' => $row->getId() ));
            $errorMessage = Mage::helper('sales')->__('Error');
            $orderInfo = unserialize($row->getTimInfo());
            
        $html = <<<HTML
                <center><a href="$errorUrl" target="_blank" style="font-weight:bold;color:red;" title="{$orderInfo['message']} ({$orderInfo['created_date']})">$errorMessage</a></center>
                <script type="text/javascript">
                    window.onload = function(){
                        var trs = document.getElementById('sales_order_grid_table').getElementsByTagName('tr');
                        for(var i in trs){
                            if(trs.hasOwnProperty(i)){
                                if(trs[i].title.search('/{$row->getId()}/') !== -1){
                                    trs[i].style.background = '#f2d4d4';
                                }
                            }
                        }
                    };
                </script>
                
HTML;
        
            return $html;
        }

        return $row->getTimChanceId();
    }
}


