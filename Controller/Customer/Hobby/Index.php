<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Controller\Customer\Hobby;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Tereshkov\Demo\Controller\Customer\AbstractAction;

class Index extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        ForwardFactory $forwardFactory,
        CustomerSession $customerSession,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($forwardFactory, $customerSession);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        if ($this->isAuthenticate()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('My Hobby'));
        } else {
            $resultPage = $this->forwardToNoRoute();
        }

        return $resultPage;
    }
}
