<?php
/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */
namespace Infobeans\Switchorderowner\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_last = null;
    
    /**
     * @var string
     */
    protected $_idFieldName = 'history_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Infobeans\Switchorderowner\Model\History', 'Infobeans\Switchorderowner\Model\ResourceModel\History');
    }
    
    /**
     * 
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->getItems() as $item) {
            $this->_last = $item;
        }
    }

    /**
     * Last Item
     *
     * @return Infobeans_Switchorderowner_Model_History
     */
    public function getLastItem()
    {
        if (!$this->isLoaded()) {
            $this->load();
        }
        return $this->_last;
    }

}
