<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Block\Adminhtml;

class Switchowner extends \Magento\Framework\View\Element\Template
{

    protected $_storeFactory;
    
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
    \Magento\Store\Model\System\StoreFactory $storeFactory,
    \Magento\Framework\View\Element\Template\Context $context,
    array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeFactory = $storeFactory;
    }
    
    /**
     * Fetch customer url
     * 
     * @return string
     */
    public function getCustomerDataUrl(){
        return $this->getUrl("switchorderowner/customer");
    }
    
    /**
     * Switch order owner save url
     * 
     * @return string
     */
    public function getSwitchOrderOwnerSaveUrl() {
        return $this->getUrl('switchorderowner/switchorder/save');
    }
    
     /**
      * Website and Store Dropdown Array
      * 
      * @param bool $isAll
      * @return array
      */
    public function getStoresStructure($isAll = false)
    {
        $model = $this->_storeFactory->create();
            
        $out = $model->getStoresStructure($isAll);
       
         $result = [];
        foreach($out as $data){
           $result[]=$data;
        }        
        $_mainWebsites = [];
        $_storeViews = [];
        $i=0;
        $j=1;
        $l=1;
        $e=1;
        foreach ($result as $k => $v) {
            $_mainWebsites[$i]['websites'][$l]= $result[$k]['label'] ;
            $stores = $result[$k]['children'];
            foreach($stores as $p => $q):
                $_mainWebsites[$i]['websites']['stores'][$j] = $stores[$j]['label'];
                $_storeViews = $stores[$j]['children'];
                foreach($_storeViews as $x => $y):
                    $_mainWebsites[$i]['websites']['stores'][$stores[$j]['label']][$e] = $_storeViews[$e]['label'];
                    $e++;
                endforeach;
                $j++;
            endforeach;
            $l++;
            $i++;
            
        }
        return $_mainWebsites;
    }
    
}
