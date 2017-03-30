<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */
namespace Infobeans\Switchorderowner\Model;

use Magento\Framework\DataObject\IdentityInterface;

class History extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    protected $_order = null;
   
    
    protected $_details = null;
    
     /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Infobeans\Switchorderowner\Model\ResourceModel\History');
    }
    
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    
    /**
     * 
     * @return boolean | \Infobeans\Switchorderowner\Model\Order
     * 
     */
    public function getOrder()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!$this->_order) {
            if ($orderId = $this->getOrderId()) {

                $order = $objectManager->create('\Infobeans\Switchorderowner\Model\Order')->load($orderId);
                $this->_order = $order;

            } else {
                return false;
            }
        }
        return $this->_order;
    }

    /**
     * 
     * @param \Magento\Sales\Model\Order $order
     * @param type $sendEmail
     * @return \Infobeans\Switchorderowner\Model\History
     */
    public function applyOrder(\Magento\Sales\Model\Order $order, $sendEmail = false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dateTime = $objectManager->create('\Magento\Framework\Stdlib\DateTime\DateTime');
        $this->_order = $order;
        $timestamp = $dateTime->gmtTimestamp();
        $this->setOrderId($order->getId())
            ->setIsNotified($sendEmail ? 1 : 0)
            ->setAssignTime($dateTime->date('',$timestamp))
            ->save();

        return $this;
    }
    
    /**
     * 
     * @param type $key
     * @param type $from
     * @param type $to
     * @return \Infobeans\Switchorderowner\Model\History
     */
    public function addDetails($key, $from = null, $to = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $detail = $objectManager->create('\Infobeans\Switchorderowner\Model\Detail');
        
        $detail->setHistoryId($this->getId())
            ->setDataKey($key)
            ->setFrom($from)
            ->setTo($to)
            ->save();

        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function getDetails()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        if (!$this->_details) {
            /** @var $collection  Infobeans_Switchorderowner_Model_Mysql4_Detail_Collection */
            $collection = $objectManager->create('\Infobeans\Switchorderowner\Model\Detail')->getCollection();
            $collection->addFieldToFilter('history_id', $this->getId());
            $this->_details = $collection;
        }
        return $this->_details;
    }
    
    /**
     * 
     * @return type
     */
    public function hasDetails()
    {
        return !!$this->getDetails()->getSize();
    }
    
    /**
     * 
     * @return string
     */
    public function getAssignTime()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dateTime = $objectManager->create('\Magento\Framework\Stdlib\DateTime\DateTime');
        return $dateTime->date('dM, y H:i:s',$this->getData('assign_time'));
    }
    
    /**
     * 
     * @return string | url
     */
    public function getCustomerUrl()
    {
//        echo $this->getUrl('customer/index/edit', array('id' => $this->getCustomer()->getId()));exit;
        return $this->getUrl('customer/index/edit', array('id' => $this->getCustomer()->getId()));
    }
    
    /**
     * 
     * @return \Infobeans\Switchorderowner\Model\Varien_Object
     */
    public function getCustomer()
    {
        $customerId = null;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($this->getDetails() as $detail) {
            if ($detail->getDataKey() == 'customer_id') {
                $customerId = $detail->getTo();
                break;
            }
        }
        if ($customerId) {
            $customer = $objectManager->create('\Magento\Customer\Model\Customer')->load($customerId);
            return $customer;
        }
        return new Varien_Object();
    }
    
    /**
     * 
     * @return string | url
     */
    public function getAdminUrl()
    {
        return $this->getUrl('adminhtml/permissions_user/edit', array('user_id' => $this->getAdmin()->getUserId()));
    }
    
    /**
     * 
     * @return \Infobeans\Switchorderowner\Model\Varien_Object
     */
    public function getAdmin()
    {
        $adminId = null;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($this->getDetails() as $detail) {
            if ($detail->getDataKey() == 'assignor') {
                $adminId = $detail->getTo();
                break;
            }
        }
        if ($adminId) {
            $adminData = $objectManager->create('Magento\User\Model\User')->load($adminId);
            return $adminData;
        }
        return new Varien_Object();
    }
    
    /**
     * 
     * @return type
     */
    public function getFromData()
    {
        $data = array();

        foreach ($this->getDetails() as $detail){
            $data[$detail->getDataKey()] = $detail->getFrom();
        }

        return $data;
    }
}
