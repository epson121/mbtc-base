<?php

namespace Mbtc\Base\Block\Checkout\Onepage\Success;


use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Info extends Template
{
    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var \Mbtc\Base\Helper\Data
     */
    public $helper;

    public $order;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        \Mbtc\Base\Helper\Data $helper,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getAddress() {
        $paymentAdditionalInfo = $this->getPaymentAdditionalInfo();
        $address = null;

        if (isset($paymentAdditionalInfo['bitcoin_address'])) {
            $address = $paymentAdditionalInfo['bitcoin_address'];
        }

        return $address;
    }

    public function getConfirmations() {
        return $this->helper->getNumConfirmations(ScopeInterface::SCOPE_WEBSITE, $this->_storeManager->getStore()->getWebsiteId());
    }

    public function getAmount()
    {
        $paymentAdditionalInfo = $this->getPaymentAdditionalInfo();
        $result = null;

        if (isset($paymentAdditionalInfo['bitcoin_amount'])) {
            $result = $paymentAdditionalInfo['bitcoin_amount'];
        }

        return $result;
    }

    public function getTransactionQrCode()
    {
        $paymentAdditionalInfo = $this->getPaymentAdditionalInfo();
        $path = null;

        if (isset($paymentAdditionalInfo['bitcoin_qr_path'])) {
            $path = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $paymentAdditionalInfo['bitcoin_qr_path'];
        }

        return $path;
    }

    public function getPaymentAdditionalInfo()
    {
        return $this->getOrder()->getPayment()->getAdditionalInformation();
    }

    public function getOrder()
    {
        if (!$this->order) {
            $this->order = $this->_checkoutSession->getLastRealOrder();
        }

        return $this->order;
    }

}
