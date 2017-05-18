<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mbtc\Base\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Config;
use Magento\Vault\Model\Ui\Adminhtml\TokensConfigProvider;
use Magento\Payment\Model\CcConfigProvider;

/**
 * Class Form
 */
class Form extends \Magento\Payment\Block\Form
{

    /**
     * Bitcoin template
     *
     * @var string
     */
    protected $_template = 'Mbtc_Base::form/bitcoin.phtml';

}
