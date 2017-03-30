<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Block\Adminhtml\Order\View\Tab;

class Switchownerhistory extends \Magento\Backend\Block\Template implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/view/tab/switchownerhistory.phtml';
    
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    protected $_orderFactory;
    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Infobeans\Switchorderowner\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_orderFactory = $orderFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Switchowner History');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Switch Order History');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
    
    /**
     * Current Order
     *
     * @return object \Infobeans\Switchorderowner\Model\Order
     */
    protected function _getOrder()
    {
            $id = $this->_request->getParam('order_id');
            $orderFactory = $this->_orderFactory->create();
            $order = $orderFactory->load($id);
            return $order;
    }
    
    /**
     * Switch Order owner history
     *
     * @return oject \Infobeans\Switchorderowner\Model\Order
     */
    public function getHistory()
    {
        return $this->_getOrder()->getOwnerSwitchHistory();
    }
}
