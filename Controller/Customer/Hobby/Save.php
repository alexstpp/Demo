<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Controller\Customer\Hobby;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Tereshkov\Demo\Controller\Customer\AbstractAction;
use Tereshkov\Demo\Model\Customer\Hobby\Form\DataProcessor;
use Tereshkov\Demo\ViewModel\Customer\Hobby\Form as HobbyForm;

class Save extends AbstractAction implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ForwardFactory $forwardFactory,
        CustomerSession $customerSession,
        RedirectFactory $resultRedirectFactory,
        CustomerRepositoryInterface $customerRepository,
        ManagerInterface $messageManager,
        DataPersistorInterface $dataPersistor,
        DataProcessor $dataProcessor,
        RequestInterface $request
    ) {
        parent::__construct($forwardFactory, $customerSession);
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
        $this->dataPersistor = $dataPersistor;
        $this->dataProcessor = $dataProcessor;
        $this->request = $request;
    }

    public function execute()
    {
        if (!$this->isAuthenticate()) {
            return $this->forwardToNoRoute();
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
            $customer = $this->dataProcessor->process($this->request, $customer);

            $this->customerRepository->save($customer);
            $this->messageManager->addSuccessMessage(__('The hobby was successfully saved.'));
            $this->dataPersistor->clear(HobbyForm::PERSISTOR_DATA_FORM_KEY);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->addDataToPersistor();
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong when saving hobby.')
            );
            $this->addDataToPersistor();
        }

        return $resultRedirect->setPath('*/*/hobby_index');
    }

    private function addDataToPersistor(): void
    {
        $data = $this->request->getPostValue();
        $this->dataPersistor->set(HobbyForm::PERSISTOR_DATA_FORM_KEY, $data);
    }

    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
