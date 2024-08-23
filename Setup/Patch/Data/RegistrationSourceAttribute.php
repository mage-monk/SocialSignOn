<?php
declare(strict_types=1);

namespace MageMonk\SocialSignOn\Setup\Patch\Data;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use MageMonk\SocialSignOn\Model\Customer\Attribute\Source\RegistrationSourceType;
use MageMonk\SocialSignOn\Model\Customer\SessionSourceKey;

class RegistrationSourceAttribute implements DataPatchInterface
{
    /**
     * @var CustomerSetup
     */
    private CustomerSetup $customerSetup;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeResource $attributeResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetupFactory $customerSetupFactory,
        private readonly AttributeResource $attributeResource,
        private readonly LoggerInterface $logger
    ) {
        $this->customerSetup = $this->customerSetupFactory->create(['setup' => $moduleDataSetup]);
    }

    /**
     * @inheirtDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheirtDoc
     */
    public function getAliases() : array
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        try {
            $this->customerSetup->addAttribute(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                SessionSourceKey::REGISTRATION_SOURCE_KEY,
                [
                    'type' => 'int',
                    'input' => 'select',
                    'label' => 'Registration Source',
                    'visible' => true,
                    'system' => false,
                    'user_defined' => false,
                    'is_filterable_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'source' => RegistrationSourceType::class,
                    'position' => 152,
                    'is_used_in_grid' => false,
                    'required' => false,
                    'default' => RegistrationSourceType::MAGENTO
                ]
            );

            $this->customerSetup->addAttributeToSet(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
                null,
                SessionSourceKey::REGISTRATION_SOURCE_KEY
            );

            $attribute = $this->customerSetup->getEavConfig()
                ->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                    SessionSourceKey::REGISTRATION_SOURCE_KEY);

            $attribute->setData('used_in_forms', [
                'adminhtml_customer', 'customer_account_create'
            ]);

            $this->attributeResource->save($attribute);
        } catch (Exception $e) {
            $this->logger->err($e->getMessage());
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
