<?php
declare(strict_types=1);

namespace Tereshkov\Demo\Controller\Customer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\App\PageCache\NotCacheableInterface;

class AbstractAction implements NotCacheableInterface
{
    /**
     * @var ForwardFactory
     */
    private $forwardFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        ForwardFactory $forwardFactory,
        CustomerSession $customerSession
    ) {
        $this->forwardFactory = $forwardFactory;
        $this->customerSession = $customerSession;
    }

    protected function isAuthenticate(): bool
    {
        return $this->customerSession->authenticate();
    }

    protected function forwardToNoRoute(): Forward
    {
        /** @var Forward $forward */
        $forward = $this->forwardFactory->create();
        return $forward->forward('defaultNoRoute');
    }
}
