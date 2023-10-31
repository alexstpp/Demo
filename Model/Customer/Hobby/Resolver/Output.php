<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Model\Customer\Hobby\Resolver;

use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Tereshkov\Demo\Model\Customer\Hobby\AttributeProvider;

class Output implements ResolverInterface
{
    /**
     * @var AttributeProvider
     */
    private $attributeProvider;

    /**
     * @var GetCustomer
     */
    private $getCustomer;

    public function __construct(
        AttributeProvider $attributeProvider,
        GetCustomer $getCustomer
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->getCustomer = $getCustomer;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $customer = $this->getCustomer->execute($context);

        return $this->attributeProvider->getHobbyAttributeLabel((int)$customer->getId());
    }
}
