<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Model;

class Order extends \Magento\Sales\Model\Order {

    
    /**
     *
     * @var \Infobeans\Switchorderowner\Model\History 
     */
    protected $_ownerswitchHistoryCollection = null;
    
    protected $_historyFactory;
    
    public function __construct(
            \Infobeans\Switchorderowner\Model\HistoryFactory $historyFactory,
            \Magento\Framework\Model\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
            \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Sales\Model\Order\Config $orderConfig,
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
            \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
            \Magento\Catalog\Model\Product\Visibility $productVisibility,
            \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
            \Magento\Directory\Model\CurrencyFactory $currencyFactory,
            \Magento\Eav\Model\Config $eavConfig,
            \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
            \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
            \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
            \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
            \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
            \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
            \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
            \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
            \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
            \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
            array $data = array()            
            ) {
        $this->_historyFactory = $historyFactory;
        parent::__construct(
                $context,
                $registry,
                $extensionFactory,
                $customAttributeFactory,
                $timezone,
                $storeManager,
                $orderConfig,
                $productRepository,
                $orderItemCollectionFactory,
                $productVisibility,
                $invoiceManagement,
                $currencyFactory,
                $eavConfig,
                $orderHistoryFactory,
                $addressCollectionFactory,
                $paymentCollectionFactory,
                $historyCollectionFactory,
                $invoiceCollectionFactory,
                $shipmentCollectionFactory,
                $memoCollectionFactory,
                $trackCollectionFactory,
                $salesOrderCollectionFactory,
                $priceCurrency,
                $productListFactory,
                $resource,
                $resourceCollection,
                $data);
          
    }
    
    /**
     * Customer
     *
     * @param $customerId
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer($customerId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('\Magento\Customer\Model\Customer')->load($customerId);
        return $customer;
    }

    /**
     * 
     * @return array
     */
    public function getNameParts() {
        return array(
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
        );
    }

    /**
     * 
     * @return boolean
     */
    public function getPreviousCustomerName() {
        $nameParts = array();
        /** @var Infobeans_Switchorderowner_Model_History $lastItem */
        $lastItem = $this->getOwnerSwitchHistory()->getLastItem();
        if ($lastItem && $lastItem->hasDetails()) {
            
            $from = $lastItem->getFromData();

            foreach ($this->getNameParts() as $key) {
                $fromKey = 'customer_' . $key;
                if (isset($from[$fromKey]) && $from[$fromKey]) {
                    $nameParts[] = $from[$fromKey];
                }
            }

            return implode(" ", $nameParts);
        }
        return false;
    }

    /**
     * Switch Order Owner
     *
     * @param $customerId
     * @param bool $overwriteName
     * @param bool $sendEmail
     * @return Infobeans\Switchorderowner\Model\Order
     */
    public function switchOrderOwner($customerId, $overwriteName = 1, $sendEmail = true, $overwriteAddress = "") {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $customer = $this->_getCustomer($customerId);

        /** @var $history \Infobeans\Switchorderowner\Model\History */
        $history = $objectManager->create('\Infobeans\Switchorderowner\Model\History');
        $history->applyOrder($this, $sendEmail);

        $prevIsGuest = $this->getCustomerIsGuest();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($this->getId());
        $oldCustomerGroup = $order->getCustomerGroupId();
        $newCustomerGroup = $customer->getGroupId();

        $authSession = $objectManager->create('\Magento\Backend\Model\Auth\Session');
        $adminUserId = $authSession->getUser()->getUserId();


        $history->addDetails('customer_id', $this->getCustomerId(), $customerId)
                ->addDetails('customer_email', $this->getCustomerEmail(), $customer->getEmail())
                ->addDetails('customer_is_guest', $this->getCustomerIsGuest(), $customer->getIsGuest())
                ->addDetails('assignor', "", $adminUserId);

        $this->setCustomerId($customerId)
                ->setCustomerEmail($customer->getEmail())
                ->setCustomerIsGuest(0)
                ->setCustomerGroupId($newCustomerGroup);


        if ($overwriteName == 1) {
            $nameParts = $this->getNameParts();
            foreach ($nameParts as $nameKey) {
                $dataKey = 'customer_' . $nameKey;
                $history->addDetails($dataKey, $this->getData($dataKey), $customer->getData($nameKey));
                $this->setData($dataKey, $customer->getData($nameKey));
            }
        }

        if ($sendEmail) {
            $objectManager->create('\Infobeans\Switchorderowner\Helper\Notify')->notifyCustomer($this, $customerId, $prevIsGuest);
        }

        if ($overwriteAddress == 1) {
            $customerBillingAddressId = $customer->getDefaultBilling();
            $defaultBillingAddress = $objectManager->create('\Magento\Customer\Model\Address')->load($customerBillingAddressId)->getData();
            $billingData = array(
                'entity_id' => $order->getBillingAddressId(),
                'firstname' => isset($defaultBillingAddress['firstname']) ? $defaultBillingAddress['firstname'] : "",
                'middlename' => isset($defaultBillingAddress['middlename']) ? $defaultBillingAddress['middlename'] : "",
                'lastname' => isset($defaultBillingAddress['lastname']) ? $defaultBillingAddress['lastname'] : "",
                'suffix' => isset($defaultBillingAddress['suffix']) ? $defaultBillingAddress['suffix'] : "",
                'prefix' => isset($defaultBillingAddress['prefix']) ? $defaultBillingAddress['prefix'] : "",
                'company' => isset($defaultBillingAddress['company']) ? $defaultBillingAddress['company'] : "",
                'street' => isset($defaultBillingAddress['street']) ? $defaultBillingAddress['street'] : "",
                'city' => isset($defaultBillingAddress['city']) ? $defaultBillingAddress['city'] : "",
                'country_id' => isset($defaultBillingAddress['country_id']) ? $defaultBillingAddress['country_id'] : "",
                'region' => isset($defaultBillingAddress['region']) ? $defaultBillingAddress['region'] : "",
                'region_id' => isset($defaultBillingAddress['region_id']) ? $defaultBillingAddress['region_id'] : "",
                'postcode' => isset($defaultBillingAddress['postcode']) ? $defaultBillingAddress['postcode'] : "",
                'telephone' => isset($defaultBillingAddress['telephone']) ? $defaultBillingAddress['telephone'] : "",
                'fax' => isset($defaultBillingAddress['fax']) ? $defaultBillingAddress['fax'] : "",
                'email' => $customer->getEmail(),
                'address_type' => "billing",
            );

            $customerShippingAddressId = $customer->getDefaultShipping();
            $defaultShippingAddress = $objectManager->create('\Magento\Customer\Model\Address')->load($customerShippingAddressId)->getData();

            $shippingData = array(
                'entity_id' => $order->getShippingAddressId(),
                'firstname' => isset($defaultShippingAddress['firstname']) ? $defaultShippingAddress['firstname'] : "",
                'middlename' => isset($defaultShippingAddress['middlename']) ? $defaultShippingAddress['middlename'] : "",
                'lastname' => isset($defaultShippingAddress['lastname']) ? $defaultShippingAddress['lastname'] : "",
                'suffix' => isset($defaultShippingAddress['suffix']) ? $defaultShippingAddress['suffix'] : "",
                'prefix' => isset($defaultShippingAddress['prefix']) ? $defaultShippingAddress['prefix'] : "",
                'company' => isset($defaultShippingAddress['company']) ? $defaultShippingAddress['company'] : "",
                'street' => isset($defaultShippingAddress['street']) ? $defaultShippingAddress['street'] : "",
                'city' => isset($defaultShippingAddress['city']) ? $defaultShippingAddress['city'] : "",
                'country_id' => isset($defaultShippingAddress['country_id']) ? $defaultShippingAddress['country_id'] : "",
                'region' => isset($defaultShippingAddress['region']) ? $defaultShippingAddress['region'] : "",
                'region_id' => isset($defaultShippingAddress['region_id']) ? $defaultShippingAddress['region_id'] : "",
                'postcode' => isset($defaultShippingAddress['postcode']) ? $defaultShippingAddress['postcode'] : "",
                'telephone' => isset($defaultShippingAddress['telephone']) ? $defaultShippingAddress['telephone'] : "",
                'fax' => isset($defaultShippingAddress['fax']) ? $defaultShippingAddress['fax'] : "",
                'email' => $customer->getEmail(),
                'vat_id' => isset($defaultShippingAddress['vat_id']) ? $defaultShippingAddress['vat_id'] : "",
                'address_type' => "shipping",
            );

            try {
                $billingId = $order->getBillingAddressId();
                $entity_id = $order->getEntityId();
                if ($billingId) {
                    $billingAddress = $objectManager->create('\Magento\Sales\Model\Order\Address', ['data' => $billingData])
                            ->load($billingId);
                    $billingAddress->setOrder($order);
                    $billingAddress->setData($billingData);
                    $billingAddress->save();
                }

                $shippingId = $order->getShippingAddressId();
                if ($shippingId) {
                    $shippingAddress = $objectManager->create('\Magento\Sales\Model\Order\Address', ['data' => $shippingData])
                            ->load($shippingId);
                    $shippingAddress->setOrder($order);
                    $shippingAddress->setData($shippingData);
                    $shippingAddress->save();
                }
            } catch (Exception $e) {
//                Mage::log($e,null,"IBswitchowner.log");
//                Mage::getSingleton('core/session')->addError($e->getMessage());
//                exit;
            }
        }

        $this->save();

        $items = $order->getAllItems();
        foreach ($items as $item) {
            if ($item->getProductType() == 'downloadable') {
                $downloadableLinks = $objectManager->create('\Magento\Downloadable\Model\Link\Purchased')
                        ->getCollection()
                        ->addFieldToFilter('order_item_id', $item->getItemId());
                foreach ($downloadableLinks->getItems() as $link) {
                    $link->setCustomerId($customerId);
                    $link->save();
                }
            }
        }

        return $this;
    }

    /**
     * History of Owner Switch
     *
     */
    public function getOwnerSwitchHistory() {
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        echo 'in Model';exit;
        if (!$this->_ownerswitchHistoryCollection) {
//            $collection = $objectManager->create('\Infobeans\Switchorderowner\Model\History')->getCollection();
            $collection = $this->_historyFactory->create()->getCollection();
            $collection
                    ->addFieldToFilter('order_id', $this->getId())
                    ->setOrder('assign_time', 'asc');

            $this->_ownerswitchHistoryCollection = $collection;
        }
        return $this->_ownerswitchHistoryCollection;
    }

}
