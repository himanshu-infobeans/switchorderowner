<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */
namespace Infobeans\Switchorderowner\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Order extends AbstractHelper
{
    protected $ownerOrderFactory;
    
    /**
     * @param \Infobeans\Switchorderowner\Model\OrderFactory $ownerOrderFactory
     * @param \Magento\Framework\App\Context $context
     */
    public function __construct(
        \Infobeans\Switchorderowner\Model\OrderFactory $ownerOrderFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->ownerOrderFactory = $ownerOrderFactory;
    }
    
    /**
     * @param type $orderId
     * @return type
     */
    public function getOrder($orderId = null)
    {
        /* if (!$orderId) {
            $orderId = Mage::app()->getRequest()->getParam('order_id');
        } */
        
        $order = $this->ownerOrderFactory->create()->load($orderId);
        if ($order->getId()) {
            return $order;
        } else {
            return null;
        }
    }
}
