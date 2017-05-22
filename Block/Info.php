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

    public $paymentAdditionalInfo;

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


    public function getTransactionQrCode($qrPath)
    {
        if ($qrPath) {
            $path = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $qrPath;
            return $path;
        }
        return null;
    }

    public function getPaymentAdditionalInfo()
    {

        $info = $this->getOrder()->getPayment()->getAdditionalInformation();

        return [
            'confirmations'             => $this->getConfirmations(isset($info['bitcoin_block_confirmed']) ? $info['bitcoin_block_confirmed'] : null),
            'bitcoin_address'           => isset($info['bitcoin_address']) ? $info['bitcoin_address'] : null,
            'transaction_qr_code'       => $this->getTransactionQrCode(isset($info['bitcoin_qr_path']) ? $info['bitcoin_qr_path'] : null),
            'bitcoin_amount'            => isset($info['bitcoin_amount']) ? $info['bitcoin_amount'] : null
        ];

    }


    public function getConfirmations($blockConfirmed)
    {
        $latestBlockHeight = (int)$this->helper->getLatestBlockHeight();

        if ($blockConfirmed) {
            return $latestBlockHeight - $blockConfirmed + 1;
        }

        return 0;
    }

}