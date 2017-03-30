<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */
namespace Infobeans\Switchorderowner\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App as App;

class Notify extends AbstractHelper
{

    protected $_ownerHelperData;
    
    protected $_customerFactory;

    protected $_dataObject;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    
    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
            \Magento\Framework\DataObject $dataObject,
            \Magento\Customer\Model\Customer $customerFactory,
            \Infobeans\Switchorderowner\Helper\Data $ownerHelperData,
            App\Helper\Context $context,
            \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        parent::__construct(
                $context
        );
        $this->_transportBuilder = $transportBuilder;
        $this->_ownerHelperData = $ownerHelperData;
        $this->_customerFactory = $customerFactory;
        $this->_dataObject = $dataObject;
    }

    /**
     * Notify Customer
     *
     * @param \Infobeans\Switchorderowner\Model\Order $order
     * @param $customerId
     * @param $customerIsGuest
     * @return $this
     */
    public function notifyCustomer(\Infobeans\Switchorderowner\Model\Order $order, $customerId, $customerIsGuest) {
        if (!$this->_ownerHelperData->configNotificationEnabled()) {
            return $this;
        }

        $store = $order->getStore();
        $storeId = $order->getStoreId();
        $customer = $this->_customerFactory->create()->load($customerId);

        $customerPrevName = $order->getPreviousCustomerName();
        $customerIsGuest = $customerIsGuest && !$customerIsGuest;
        
        $postObject = $this->_dataObject;

        $vars = [
            'order' => $order,
            'customer' => $customer,
            'store' => $store,
            'is_guest' => $customerIsGuest ? 1 : 0,
            'is_customer' => $customerIsGuest ? 0 : 1,
            'old_customer_name' => $customerPrevName,
        ];
        
        $postObject->setData($vars);
        
        $template = $this->scopeConfig->getValue('switchorderowner/notification/template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $sender = $this->scopeConfig->getValue('switchorderowner/notification/identity', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $copyTo = $this->scopeConfig->getValue('switchorderowner/notification/copy_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $receivers = array($customer->getEmail());

        if ($copyTo) {
            $copyReceivers = explode(",", $copyTo);
            $receivers = array_merge($receivers, $copyReceivers);
        }

        foreach ($receivers as $receiver) {
            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                        ->setTemplateOptions(
                                ['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => $storeId]
                                )
                        ->setTemplateVars(
                                ['data' => $postObject]
                                )
                        ->setFrom(
                                $sender
                                )
                        ->addTo(
                                trim($receiver),$customer->getName()
                                )
                        ->getTransport();
                $transport->sendMessage();
            } catch (\Exception $e) {
                
            }
        }

        return $this;
    }

}
