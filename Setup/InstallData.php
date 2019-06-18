<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Setup;

/**
 * Class InstallData
 *
 * @package WalktheChat\Walkthechat\Setup
 */
class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * Init
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface   $context
     *
     * @return void
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            \WalktheChat\Walkthechat\Helper\Data::ATTRIBUTE_CODE,
            [
                'type'                    => 'varchar',
                'label'                   => 'WalkTheChat ID',
                'input'                   => 'text',
                'required'                => false,
                'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'used_in_product_listing' => false,
                'unique'                  => true,
                'visible'                 => false,
                'backend'                 => '',
                'frontend'                => '',
                'class'                   => '',
                'source'                  => '',
                'user_defined'            => false,
                'default'                 => '',
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'apply_to'                => '',
            ]
        );
    }
}
