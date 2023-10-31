<?php
declare(strict_types=1);

namespace Tereshkov\Demo\ViewModel\Customer\Hobby;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tereshkov\Demo\Model\Customer\Hobby\AttributeProvider;
use Tereshkov\Demo\Model\Source\Customer\Hobby as HobbySource;

class Form implements ArgumentInterface
{
    public const PERSISTOR_DATA_FORM_KEY = 'tereshkov_demo_hobby_persistor_form';

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var AttributeProvider
     */
    private $attributeProvider;

    /**
     * @var HobbySource
     */
    private $hobbySource;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        AttributeProvider $attributeProvider,
        CustomerSession $customerSession,
        HobbySource $hobbySource,
        UrlInterface $url,
        DataPersistorInterface $dataPersistor
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->customerSession = $customerSession;
        $this->hobbySource = $hobbySource;
        $this->dataPersistor = $dataPersistor;
        $this->url = $url;
    }

    public function getSource(): array
    {
        return $this->hobbySource->getSimpleOptionsArray();
    }

    public function getOptionValue(): string
    {
        $dataFromForm = $this->dataPersistor->get(self::PERSISTOR_DATA_FORM_KEY);
        try {
            if (!empty($dataFromForm) && isset($dataFromForm['hobby']['value'])) {
                $value = $dataFromForm['hobby']['value'];
                $this->dataPersistor->clear(self::PERSISTOR_DATA_FORM_KEY);
            } else {
                $value = (string)$this->attributeProvider
                    ->getHobbyAttribute((int)$this->customerSession->getCustomerId())
                    ->getValue();
            }
        } catch (\Exception $e) {
            $value = '';
        }

        return $value;
    }

    public function getSaveHobbyUrl(): string
    {
        return $this->url->getUrl('tereshkov_demo/customer/hobby_save');
    }
}
