<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Model\Customer\Hobby;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface as MagentoCustomerInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tereshkov\Demo\Api\Data\CustomerInterface;
use Tereshkov\Demo\Model\Source\Customer\Hobby as HobbySource;

class AttributeProvider
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var HobbySource
     */
    private $hobbySource;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        HobbySource $hobbySource
    ) {
        $this->customerRepository = $customerRepository;
        $this->hobbySource = $hobbySource;
    }

    /**
     * Retrieve Hobby Attribute
     *
     * @param int $customerId
     * @return AttributeInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getHobbyAttribute(int $customerId): AttributeInterface
    {
        return $this->getCustomer($customerId)->getCustomAttribute(CustomerInterface::HOBBY);
    }

    /**
     * Retrieve Hobby Attribute Label
     *
     * @param int $customerId
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getHobbyAttributeLabel(int $customerId): string
    {
        $hobbyAttribute = $this->getHobbyAttribute($customerId);

        return $this->hobbySource->getLabelByValue((string)$hobbyAttribute->getValue());
    }

    /**
     * Retrieve Hobby Attribute Label
     *
     * @param int $customerId
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getHobbyAttributeValue(int $customerId, string $label): string
    {
        return $this->hobbySource->getValueByLabel((string)$label);
    }

    /**
     * Retrieve Customer by id
     *
     * @param int $customerId
     * @return MagentoCustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCustomer(int $customerId): MagentoCustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }
}
