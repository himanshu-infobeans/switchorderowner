<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */
namespace Infobeans\Switchorderowner\Model;

use Magento\Framework\DataObject\IdentityInterface;


class Detail extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
     /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Infobeans\Switchorderowner\Model\ResourceModel\Detail');
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

}
