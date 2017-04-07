<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Model\ResourceModel\Detail;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'detail_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Infobeans\Switchorderowner\Model\Detail',
            'Infobeans\Switchorderowner\Model\ResourceModel\Detail'
        );
    }
}
