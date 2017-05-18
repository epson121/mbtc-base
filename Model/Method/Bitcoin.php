<?php

namespace Mbtc\Base\Model\Method;

use Magento\Framework\DataObject;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\Context;
use Mbtc\Base\Block\Form;
use Mbtc\Base\Block\Info;
use Mbtc\Base\Model\BitcoinPaymentInterface;
use Mbtc\Base\Model\Method\BitcoinProvider;

class Bitcoin implements BitcoinPaymentInterface
{

    const PAYMENT_METHOD_BITCOIN= 'bitcoin';

    /**
     * @var string
     */
    private static $titleKey = 'title';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $formBlockType;

    /**
     * @var string
     */
    private $infoBlockType;

    /**
     * @var ValueHandlerPoolInterface
     */
    private $valueHandlerPool;

    /**
     * @var string
     */
    private $code;

    /**
     * @var BitcoinProvider
     */
    private $bitcoinProvider;

    /**
     * @var ManagerInterface
     */
    private $eventManager;


    private $context;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param Context $context
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param BitcoinProvider $bitcoinProvider
     * @param ManagerInterface $eventManager
     * @param $formBlockType
     * @param $infoBlockType
     * @param $code
     */
    public function __construct(
        ConfigInterface $config,
        Context $context,
        ValueHandlerPoolInterface $valueHandlerPool,
        BitcoinProvider $bitcoinProvider,
        ManagerInterface $eventManager,
        $formBlockType,
        $infoBlockType,
        $code
    ) {
        $this->config = $config;
        $this->valueHandlerPool = $valueHandlerPool;
        $this->bitcoinProvider = $bitcoinProvider;
        $this->formBlockType = $formBlockType;
        $this->eventManager = $eventManager;
        $this->infoBlockType = $infoBlockType;
        $this->code = $code;
        $this->context = $context;
    }

    /**
     * Unifies configured value handling logic
     *
     * @param string $field
     * @param null $storeId
     * @return mixed
     */
    private function getConfiguredValue($field, $storeId = null)
    {
        $handler = $this->valueHandlerPool->get($field);
        $subject = ['field' => $field];

        return $handler->handle($subject, $storeId ?: $this->getStore());
    }


    /**
     * @return BitcoinProvider
     */
    public function getBitcoinProvider()
    {
        return $this->bitcoinProvider;
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     *
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     *
     * @deprecated
     */
    public function getFormBlockType()
    {
        return Form::class;
    }

    /**
     * Retrieve payment method title
     *
     * @return string
     *
     */
    public function getTitle()
    {
        return $this->getConfiguredValue(self::$titleKey);
    }

    /**
     * Store id setter
     * @param int $storeId
     * @return void
     */
    public function setStore($storeId)
    {
        $this->storeId = (int)$storeId;
    }

    /**
     * Store id getter
     * @return int
     */
    public function getStore()
    {
        return $this->storeId;
    }

    /**
     * Check order availability
     *
     * @return bool
     *
     */
    public function canOrder()
    {
        return true;
    }

    /**
     * Check authorize availability
     *
     * @return bool
     *
     */
    public function canAuthorize()
    {
        return true;
    }

    /**
     * Check capture availability
     *
     * @return bool
     *
     */
    public function canCapture()
    {
        return true;
    }

    /**
     * Check partial capture availability
     *
     * @return bool
     *
     */
    public function canCapturePartial()
    {
        return false;
    }

    /**
     * Check whether capture can be performed once and no further capture possible
     *
     * @return bool
     *
     */
    public function canCaptureOnce()
    {
        return true;
    }

    /**
     * Check refund availability
     *
     * @return bool
     *
     */
    public function canRefund()
    {
        return false;
    }

    /**
     * Check partial refund availability for invoice
     *
     * @return bool
     *
     */
    public function canRefundPartialPerInvoice()
    {
        return false;
    }

    /**
     * Check void availability
     * @return bool
     *
     */
    public function canVoid()
    {
        return false;
    }

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return true;
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return true;
    }

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     *
     */
    public function canEdit()
    {
        return false;
    }

    /**
     * Check fetch transaction info availability
     *
     * @return bool
     *
     */
    public function canFetchTransactionInfo()
    {
        return true;
    }

    /**
     * Fetch transaction info
     *
     * @param InfoInterface $payment
     * @param string $transactionId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function fetchTransactionInfo(InfoInterface $payment, $transactionId)
    {
        throw new \DomainException("Not implemented");
    }

    /**
     * Retrieve payment system relation flag
     *
     * @return bool
     *
     */
    public function isGateway()
    {
        return false;
    }

    /**
     * Retrieve payment method online/offline flag
     *
     * @return bool
     *
     */
    public function isOffline()
    {
        return true;
    }

    /**
     * Flag if we need to run payment initialize while order place
     *
     * @return bool
     *
     */
    public function isInitializeNeeded()
    {
        return false;
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @param string $country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return true;
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canUseForCurrency($currencyCode)
    {
        return true;
    }

    /**
     * Retrieve block type for display method information
     *
     * @return string
     *
     * @deprecated
     */
    public function getInfoBlockType()
    {
        return $this->infoBlockType;
    }

    /**
     * Retrieve payment information model object
     *
     * @return InfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @deprecated
     */
    public function getInfoInstance()
    {
        return null;
    }

    /**
     * Retrieve payment information model object
     *
     * @param InfoInterface $info
     * @return void
     * @api
     */
    public function setInfoInstance(InfoInterface $info)
    {
        $this->getBitcoinProvider()->setInfoInstance($info);
    }

    /**
     * Validate payment method information object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function validate()
    {
        // todo
        // \Magento\Backend\Model\Session\Quote
        // $this->getSession()->getOrder()->getId()
        return $this;
    }

    /**
     * Order payment method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // todo
        return $this;
    }

    /**
     * Authorize payment method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // todo
        return $this;
    }

    /**
     * Capture payment method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // todo
        return $this;
    }

    /**
     * Refund specified amount for payment
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // todo
        return $this;
    }

    /**
     * Cancel payment method
     *
     * @param InfoInterface $payment
     * @return $this
     *
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        // todo
        return $this;
    }

    /**
     * Void payment method
     *
     * @param InfoInterface $payment
     * @return $this
     *
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        // todo
        return $this;
    }

    /**
     * Whether this method can accept or deny payment
     * @return bool
     *
     */
    public function canReviewPayment()
    {
        // todo
        return true;
    }

    /**
     * Attempt to accept a payment that us under review
     *
     * @param InfoInterface $payment
     * @return false
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function acceptPayment(InfoInterface $payment)
    {
        return false;
    }

    /**
     * Attempt to deny a payment that us under review
     *
     * @param InfoInterface $payment
     * @return false
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function denyPayment(InfoInterface $payment)
    {
        return false;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|\Magento\Store\Model\Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->getConfiguredValue($field, $storeId);
    }

    /**
     * Assign data to info model instance
     *
     * @param DataObject $data
     * @return $this
     *
     */
    public function assignData(DataObject $data)
    {

        $this->eventManager->dispatch(
            'payment_method_assign_data_' . $this->getCode(),
            [
                AbstractDataAssignObserver::METHOD_CODE => $this,
                AbstractDataAssignObserver::MODEL_CODE => $this->getInfoInstance(),
                AbstractDataAssignObserver::DATA_CODE => $data
            ]
        );

        $this->eventManager->dispatch(
            'payment_method_assign_data',
            [
                AbstractDataAssignObserver::METHOD_CODE => $this,
                AbstractDataAssignObserver::MODEL_CODE => $this->getInfoInstance(),
                AbstractDataAssignObserver::DATA_CODE => $data
            ]
        );

        return $this;
    }

    /**
     * Check whether payment method can be used
     *
     * @param CartInterface|null $quote
     * @return bool
     *
     */
    public function isAvailable(CartInterface $quote = null)
    {
        return true;
    }

    /**
     * Is active
     *
     * @param int|null $storeId
     * @return bool
     *
     */
    public function isActive($storeId = null)
    {
        return true;
    }

    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function initialize($paymentAction, $stateObject)
    {
        return $this;
    }

    /**
     * Get config payment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     *
     */
    public function getConfigPaymentAction()
    {
        return null;
    }
}