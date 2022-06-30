<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 * @license   See LICENSE_WALKTHECHAT.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Config\Source;

/**
 * Class DescriptionAttributes
 *
 * @package Walkthechat\Walkthechat\Model\Config\Source
 */
class DescriptionAttributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory
     */
    protected $eavTypeFactory;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory
     * @param TypeFactory $typeFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory
    ) 
    {
        $this->attributeFactory = $attributeFactory;
        $this->eavTypeFactory = $typeFactory;
    }

    protected function _getAttributes()
    {
        $entityType = $this->eavTypeFactory->create()->loadByCode('catalog_product');
        
        $collection = $this->attributeFactory->create()->getCollection();
        $collection->addFieldToFilter('entity_type_id', $entityType->getId());
        $collection->addFieldToFilter('frontend_input', ['in' => ['text', 'textarea']]);
        $collection->setOrder('frontend_label', 'ASC');
        
        return $collection;
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = [];
        
        foreach ($this->_getAttributes() as $attribute) {
            $attributes[] = [
                'value' => $attribute->getAttributeId(),
                'label' => $attribute->getFrontendLabel(),
            ];
        }
        
        return $attributes;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        $attributes = [];
        
        foreach ($this->_getAttributes() as $attribute) {
            $attributes[$attribute->getAttributeId()] = $attribute->getFrontendLabel();
        }

        return $attributes;
    }
}
