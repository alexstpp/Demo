<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Plugin\CustomerData;

use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Psr\Log\LoggerInterface;
use Tereshkov\Demo\Model\Customer\Hobby\AttributeProvider;

class AddHobby
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Tereshkov\Demo\Model\Customer\Hobby\AttributeProvider
     */
    private $attributeProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CurrentCustomer $currentCustomer,
        AttributeProvider $attributeProvider,
        LoggerInterface $logger
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->attributeProvider = $attributeProvider;
        $this->logger = $logger;
    }

    public function afterGetSectionData(Customer $subject, array $result): array
    {
        try {
            $customerId = (int)$this->currentCustomer->getCustomerId();
            if ($customerId) {
                if ($hobby = $this->attributeProvider->getHobbyAttributeLabel($customerId)) {
                    $result['hobby'] = $hobby;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $result;
    }
}
