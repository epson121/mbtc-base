<?php

namespace Mbtc\Base\Block;

use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Mbtc\Base\Helper\Data;

/**
 * Class Info
 */
class Info extends \Magento\Payment\Block\Info
{

    /**
     * @var string
     */
    protected $_template = 'Mbtc_Base::info/bitcoin.phtml';


    private $helper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    )
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrder()
    {
        return $this->getInfo()->getOrder();
    }

    public function getAddress() {
        $paymentAdditionalInfo = $this->getPaymentAdditionalInfo();
        if (isset($paymentAdditionalInfo['bitcoin_address'])) {
            return $paymentAdditionalInfo['bitcoin_address'];
        }

        return null;
    }


    public function getTransactionQrCode()
    {
        $paymentAdditionalInfo = $this->getPaymentAdditionalInfo();
        if (isset($paymentAdditionalInfo['bitcoin_qr_path'])) {
            $path = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $paymentAdditionalInfo['bitcoin_qr_path'];
            return $path;
        }
        return null;
    }

    public function getPaymentAdditionalInfo()
    {
        return $this->getOrder()->getPayment()->getAdditionalInformation();
    }

    public function getConfirmations()
    {
        $paymentAdditionalInfo = $this->getPaymentAdditionalInfo();
        $latestBlockHeight = (int)$this->helper->getLatestBlockHeight();

        if (isset($paymentAdditionalInfo['bitcoin_block_confirmed'])) {
            return $latestBlockHeight - $paymentAdditionalInfo['bitcoin_block_confirmed'] + 1;
        }

        return 0;
    }

}