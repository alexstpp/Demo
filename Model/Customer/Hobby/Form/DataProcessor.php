<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Model\Customer\Hobby\Form;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer\Mapper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface;

class DataProcessor
{
    /**
     * @var CustomerExtractor
     */
    private $customerExtractor;

    /**
     * @var Mapper
     */
    private $customerMapper;

    public function __construct(
        CustomerExtractor $customerExtractor,
        Mapper $customerMapper
    ) {
        $this->customerExtractor = $customerExtractor;
        $this->customerMapper = $customerMapper;
    }

    public function process(RequestInterface $request, CustomerInterface $currentCustomer): CustomerInterface
    {
        $data = $this->customerMapper->toFlatArray($currentCustomer);
        $updatedCustomer = $this->customerExtractor->extract(
            'customer_account_hobby_edit',
            $request,
            $data
        );

        return $updatedCustomer;
    }
}
