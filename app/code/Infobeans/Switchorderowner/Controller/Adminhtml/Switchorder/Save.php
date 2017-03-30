<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Controller\Adminhtml\Switchorder;

use Magento\Backend\App\Action\Context;

class Save extends \Magento\Backend\App\Action {
    
    protected $_helperOrderFactory;
    
    protected $_customerFactory;
    
    protected $_storeFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * 
     * @param Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
            \Magento\Store\Model\StoreFactory $storeFactory,
            \Magento\Customer\Model\CustomerFactory $customerFactory,
            \Infobeans\Switchorderowner\Helper\Order $helperOrderFactory,
            Context $context,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
         
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->_helperOrderFactory = $helperOrderFactory;
        $this->_customerFactory = $customerFactory;
        $this->_storeFactory = $storeFactory;
    }

    /**
     * 
     * @return void
     */
    public function execute()
    {
        $params = $this->_request->getParams();
        $customerId = (isset($params['customer_id']) && $params['customer_id']) ? $params['customer_id'] : '';
        $sendEmail = (isset($params['send_email']) && $params['send_email']) ? 1 : 0;
        
        $orderIds = (isset($params['order_ids']) && $params['order_ids']) ? $params['order_ids'] : 0;
        $orderIds = explode(",", $orderIds);
        $overwriteName = $this->_scopeConfig->getValue('switchorderowner/address/override_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $overwriteAddress = $this->_scopeConfig->getValue('switchorderowner/address/override_billing_shipping', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $success = 0;
        $error = 0;
        $websiteError = 0;
        $websiteErrorMsg = "";
        $orderStatusError = 0;
        $orderStatusErrorMsg = "";
        $acountSharingOption = $this->_scopeConfig->getValue('customer/account_share/scope', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $state = explode(",", $this->_scopeConfig->getValue('switchorderowner/order_setting/orderstate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        
        $_orderHelper = $this->_helperOrderFactory;
        if ($customerId) {
            foreach ($orderIds as $orderId) {
                $canOwnerSwitch = 1;
                if ($customerId && $orderId) {
                    $order = $_orderHelper->getOrder($orderId);
                    $orderState = $order->getStatus();
                    $customer = $this->_customerFactory->create()->load($customerId);
                    $newCustomerWebsiteId = $customer->getWebsiteId();
                    $oldCustomerWebsiteId = $this->_storeFactory->create()->load($order->getStoreId())->getWebsiteId();
                    if ($acountSharingOption == 1 && $newCustomerWebsiteId != $oldCustomerWebsiteId) {
                        $canOwnerSwitch = 0;
                    }
                    if (!in_array($orderState, $state) && $canOwnerSwitch == 1) {
                        
                        $order->switchOrderOwner($customerId, $overwriteName, $sendEmail, $overwriteAddress);
                        $success++;
                    } else if ($canOwnerSwitch == 0) {
                        $websiteError++;
                        $websiteErrorMsg = $websiteErrorMsg . $order->getIncrementId() . ", ";
                    } else if (in_array($orderState, $state)) {
                        $orderStatusError++;
                        $orderStatusErrorMsg = $orderStatusErrorMsg . $order->getIncrementId() . ", ";
                    } else {
                        $error++;
                    }
                } else {
                    $error++;
                }
            }
            if (count($orderIds) > 1) {
                if ($success) {
                    $this->messageManager->addSuccess(__("%s Order owner were successfully switched.", $success));
                }
                if ($websiteError) {
                    $this->messageManager->addError(__("Order owner can not be switched for %s as the selected customer belongs to the different website.", rtrim($websiteErrorMsg, ", ")));
                }
                if ($orderStatusError) {
                    $this->messageManager->addError(__("Order owner can not be switched for %s as it can not be processed further.", rtrim($orderStatusErrorMsg, ", ")));
                }
                if ($error) {
                    $this->messageManager->addError(__("%s Order were not be updated due to some error.", $error));
                }

                $this->_redirect('sales/order');
            } else {
                if ($success) {
                    $this->messageManager->addSuccess(__("Order owner was successfully switched."));
                }
                if ($websiteError) {
                    $this->messageManager->addError(__("Order %s owner can not be switched as the selected customer belongs to the different website.", rtrim($websiteErrorMsg, ", ")));
                }
                if ($orderStatusError) {
                    $this->messageManager->addError(__("Order %s owner can not be switched as it can not be processed further.", rtrim($orderStatusErrorMsg, ", ")));
                }
                if ($error) {
                    $this->messageManager->addError(__("Order was not be updated due to some error."));
                }
                $this->_redirect('sales/order/index');
            }
        } else {
            $this->messageManager->addError(__("Some data was missed or your session was expired. Please try again."));
            if ($orderId = $this->_request->getParams('order_id')) {
                $this->_redirect('sales/order/view', array('order_id' => $orderId));
            } else {
                $this->_redirect('sales/order/index');
            }
        }
        return;
    }
    
    protected function _isAllowed() {
    return true;
    }
}
