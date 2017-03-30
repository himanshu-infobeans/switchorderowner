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
    protected $_ownerOrderFactory;
    
    public function __construct(
            \Infobeans\Switchorderowner\Model\Order $ownerOrderFactory,
            Context $context
            )
    {
        parent::__construct($context);
        $this->_ownerOrderFactory = $ownerOrderFactory;
    }
    
    /**
     * 
     * @param type $orderId
     * @return type
     */
    public function getOrder($orderId = null)
    {
        /* if (!$orderId) {
            $orderId = Mage::app()->getRequest()->getParam('order_id');
        } */
        
        $order = $this->_ownerOrderFactory->create()->load($orderId);
        if ($order->getId()) {
            return $order;
        } else {
            return null;
        }
    }
}
