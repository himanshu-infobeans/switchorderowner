<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Controller\Adminhtml\Customer;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action {

    protected $_customerFactory;
    /**
     * 
     * @param Context $context
     */
    public function __construct(
            \Magento\Customer\Model\CustomerFactory $customerFactory, 
            Context $context
            ) {
        parent::__construct($context);
        $this->_customerFactory = $customerFactory;
    }

    /**
     * 
     * Fetch customers based on store and webstie
     * 
     * @return string|json
     *  
     */
    public function execute() {
        
        $customerObj = $this->_customerFactory->create()->getCollection();

        $params = $this->_request->getParams();
        $storeId = (isset($params['store']) && $params['store']) ? $params['store'] : '';
        $websiteId = (isset($params['website']) && $params['website']) ? $params['website'] : '';



        $customerObj->addFieldToFilter('website_id', array('eq' => $storeId));
        $customerOptions = "<div class='control'><select style='width:350px;' tabindex='5' name='customer-list' id='customer-list' class='chosen-select validate-select' >"
                . "<option value=''>Select Customer</option>";

        foreach ($customerObj as $customerObjdata) {
            $customerData = $this->_customerFactory->create()->load($customerObjdata->getEntityId());
            $customerOptions = $customerOptions . '<option value="' . $customerData->getId() . '">' . $customerData->getEmail() . ' ( ' . $customerData->getFirstname() . ' ' . $customerData->getMiddlename() . ' ' . $customerData->getLastname() . ' )</option>';
        }
        $customerOptions = $customerOptions . "</select></div>";
        
        $_result = array('customerOptions' => $customerOptions);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($_result);
    }

}
