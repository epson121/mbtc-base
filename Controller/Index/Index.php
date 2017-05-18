<?php

namespace Mbtc\Base\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use BitWasp\BitcoinLib\BIP32;
use Mbtc\Base\Cron\CheckConfirmations;
use Mbtc\Base\Cron\FetchLatestBlock;
use Mbtc\Base\Cron\UpdateExchangeRate;
use Mbtc\Base\Helper\Data;

class Index extends Action
{

    private $helper;

    public function __construct(
        Context $context,
        Data $helper,
        UpdateExchangeRate $exchangeRate,
        CheckConfirmations $checkConfirmations,
        FetchLatestBlock $fetchLatestBlock
    ) {
        $this->helper = $helper;
        $this->exchangeRateCron = $exchangeRate;
        $this->checkConfirmations = $checkConfirmations;
        $this->fetchLatestBlock = $fetchLatestBlock;
        parent::__construct($context);
    }

    public function execute()
    {

//        $this->exchangeRateCron->execute();
//        $this->fetchLatestBlock->execute();
            $this->checkConfirmations->execute();
//        $extended_key = BIP32::build_key('xpub661MyMwAqRbcGRBxcbMJ9o31UNqSAsjZf6q7qcJRCwmxisaBfraKEmQGYjdQd2SCp1WM1w8QKWdWh4j52dDQDBThmzABfB7xYWyxVuQBAnF', 'm/0/0');
//        $extended_key2 = BIP32::build_key('tpubD6NzVbkrYhZ4Y7zourYo7r4218a5eMs6ZLjkf8ycx44rvmxGMNcLAjh1YjZzToJdPPMbhiN8mQdWQThvecSUScnLkErYBnCxtmLfNqnx9cG', 'm/0/0');
//        $a = 1;
//        $master = BIP32::master_key(bin2hex(mcrypt_create_iv(64, \MCRYPT_DEV_URANDOM)));
//        $master = BIP32::master_key('069d52dc8b61c64116f89f00c6cd58be4772a8ef0a73908f136d8aa811798f18c6bb077ba939af29fcaf74dc0dcd1fd48c96c16d4d3fd801d341f14f4de2b3c1');
//
//        $def = "0'/0";
//        $key = BIP32::build_key($master, $def);
//
//        echo "Generated key: note that all depth=1 keys are hardened. \n {$key[1]}        : {$key[0]}\n";
//        echo "             : ".BIP32::key_to_address($key[0])."\n";
//
//        $pub = BIP32::extended_private_to_public($key);
//        echo "Public key\n {$pub[1]}        : {$pub[0]}\n";
//        echo "             : ".BIP32::key_to_address($pub[0])."\n";
    }
}
