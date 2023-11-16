<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Tereshkov\Demo\Api\Data\CustomerInterface;
use Tereshkov\Demo\Model\Source\Customer\Hobby;

class CreateCustomerHobbyAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
    /**
     * Add eav attributes
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->addAttribute(
            Customer::ENTITY,
            CustomerInterface::HOBBY,
            [
                'type' => 'varchar',
                'label' => 'Hobby',
                'input' => 'select',
                'source' => Hobby::class,
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 999,
                'system' => 0,
            ]
        );

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $entityAttributes = [
            Customer::ENTITY => [
                CustomerInterface::HOBBY => [
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer', 'customer_account_hobby_edit'],
                ]
            ]
        ];
        $customerSetup->upgradeAttributes($entityAttributes);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer::ENTITY, CustomerInterface::HOBBY);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get Aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
