<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Model;

class Order extends \Magento\Sales\Model\Order
{
    /**
     * @var \Infobeans\Switchorderowner\Model\History
     */
    protected $ownerswitchHistoryCollection = null;
    
    protected $historyFactory;
    protected $customerFactory;
    protected $orderFactory;
    protected $authSession;
    protected $notifyFactory;
    protected $addressFactory;
    protected $orderAddressFactory;
    protected $linkPurchasedFactory;

    /**
     * @param \Infobeans\Switchorderowner\Model\HistoryFactory $historyFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Infobeans\Switchorderowner\Helper\NotifyFactory $notifyFactory,
        \Infobeans\Switchorderowner\Model\HistoryFactory $historyFactory,
        \Magento\Downloadable\Model\Link\PurchasedFactory $linkPurchasedFactory,
        \Magento\Sales\Model\Order\AddressFactory $orderAddressFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
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
        array $data = []
    ) {
        $this->historyFactory = $historyFactory;
        $this->customerFactory = $customerFactory;
        $this->orderFactory = $orderFactory;
        $this->authSession = $authSession;
        $this->notifyFactory = $notifyFactory;
        $this->addressFactory = $addressFactory;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->linkPurchasedFactory = $linkPurchasedFactory;
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
            $data
        );
    }
    
    /**
     * Customer
     *
     * @param $customerId
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer($customerId)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        return $customer;
    }

    /**
     * @return array
     */
    public function getNameParts()
    {
        return [
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
        ];
    }

    /**
     * @return boolean
     */
    public function getPreviousCustomerName()
    {
        $nameParts = [];
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
    public function switchOrderOwner($customerId, $overwriteName = 1, $sendEmail = true, $overwriteAddress = "")
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $customer = $this->_getCustomer($customerId);

        /** @var $history \Infobeans\Switchorderowner\Model\History */
        $history = $this->historyFactory->create();
        $history->applyOrder($this, $sendEmail);

        $prevIsGuest = $this->getCustomerIsGuest();
        $order = $this->orderFactory->create()->load($this->getId());
        $oldCustomerGroup = $order->getCustomerGroupId();
        $newCustomerGroup = $customer->getGroupId();

        $authSession = $this->authSession;
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
            $this->notifyFactory->create()->notifyCustomer($this, $customerId, $prevIsGuest);
        }

        if ($overwriteAddress == 1) {
            $customerBillingAddressId = $customer->getDefaultBilling();
            $defaultBillingAddress = $this->addressFactory->create()->load($customerBillingAddressId)->getData();
            $billingData = [
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
            ];

            $customerShippingAddressId = $customer->getDefaultShipping();
            $defaultShippingAddress = $this->addressFactory->create()->load($customerShippingAddressId)->getData();

            $shippingData = [
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
            ];

            try {
                $billingId = $order->getBillingAddressId();
                $entity_id = $order->getEntityId();
                if ($billingId) {
                    $billingAddress = $this->orderAddressFactory->create(['data' => $billingData])
                            ->load($billingId);
                    $billingAddress->setOrder($order);
                    $billingAddress->setData($billingData);
                    $billingAddress->save();
                }

                $shippingId = $order->getShippingAddressId();
                if ($shippingId) {
                    $shippingAddress = $this->orderAddressFactory->create(['data' => $shippingData])
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
                $downloadableLinks = $this->linkPurchasedFactory->create()
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
    public function getOwnerSwitchHistory()
    {
        if (!$this->ownerswitchHistoryCollection) {
            $collection = $this->historyFactory->create()->getCollection();
            $collection
                    ->addFieldToFilter('order_id', $this->getId())
                    ->setOrder('assign_time', 'asc');

            $this->ownerswitchHistoryCollection = $collection;
        }
        return $this->ownerswitchHistoryCollection;
    }
}
