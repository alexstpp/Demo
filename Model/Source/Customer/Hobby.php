<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Model\Source\Customer;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\Collection as DataCollection;

class Hobby extends AbstractSource
{
    public const YOGA = 'yoga';
    public const TRAVELING = 'traveling';
    public const HIKING = 'hiking';

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $simpleOptions;

    public function getAllOptions(): array
    {
        if (null === $this->options) {
            $this->options = [
                ['value' => '', 'label' => __(__('Please Select'))],
                ['value' => self::YOGA, 'label' => __('Yoga')],
                ['value' => self::TRAVELING, 'label' => __('Traveling')],
                ['value' => self::HIKING, 'label' => __('Hiking')]
            ];
        }

        return $this->options;
    }

    public function getSimpleOptionsArray(): array
    {
        if (null === $this->simpleOptions) {
            foreach ($this->getAllOptions() as $option) {
                $this->simpleOptions[$option['value']] = $option['label'];
            }
        }

        return $this->simpleOptions;
    }

    public function getLabelByValue(string $value): string
    {
        $simpleOptions = $this->getSimpleOptionsArray();

        return isset($simpleOptions[$value]) && !empty($value) ? (string)$simpleOptions[$value] : '';
    }

    public function getValueByLabel(string $label): string
    {
        $value = '';
        $simpleOptions = $this->getSimpleOptionsArray();
        foreach ($simpleOptions as $optValue => $optLabel) {
            if ($label == $optLabel) {
                $value = $optValue;
                break;
            }
        }

        return $value;
    }

    public function addValueSortToCollection($collection, $dir = DataCollection::SORT_ORDER_DESC): self
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();
        $linkField = $this->getAttribute()->getEntity()->getLinkField();

        $defaultValueTable = $attributeCode . '_default';
        $storeValueTable = $attributeCode . '_store';
        $collection->getSelect()
            ->joinLeft(
                [$defaultValueTable => $attributeTable],
                'e.' . $linkField . '=' . $defaultValueTable . '.' . $linkField .
                ' AND ' . $defaultValueTable . '.attribute_id=\'' . $attributeId . '\''.
                ' AND ' . $defaultValueTable . '.store_id=\'0\'',
                []
            )
            ->joinLeft(
                [$storeValueTable => $attributeTable],
                'e.' . $linkField . '=' . $storeValueTable . '.' . $linkField .
                ' AND ' . $storeValueTable . '.attribute_id=\'' . $attributeId . '\'' .
                ' AND ' . $storeValueTable . '.store_id=\'' . $collection->getStoreId() . '\'',
                []
            );
        $valueExpr = $collection->getConnection()
            ->getCheckSql(
                $storeValueTable . '.value_id > 0',
                $storeValueTable . '.value',
                $defaultValueTable . '.value'
            );

        $collection->getSelect()->order($valueExpr . ' ' . $dir);

        return $this;
    }
}
