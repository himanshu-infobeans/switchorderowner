<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App as App;

class Data extends AbstractHelper
{
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        App\Helper\Context $context
    ) {
        parent::__construct(
            $context
        );
    }
    
    /**
     * @return bool
     */
    public function configNotificationEnabled()
    {
        return $this->scopeConfig->getValue(
            'switchorderowner/notification/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Allowed Action
     *
     * @return boolean
     */
//    public function isAllowed()
//    {
//        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/switchorderowner');
//    }

    /**
     * Retrieves is Enabled
     *
     * @return boolean
     */
//    public function extEnabled()
//    {
//        return !Mage::getStoreConfig('advanced/modules_disable_output/Infobeans_Switchorderowner');
//    }
}
