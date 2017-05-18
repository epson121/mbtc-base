<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mbtc\Base\Gateway\Config;

/**
 * Class Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE = 'active';

    const KEY_INSTRUCTIONS = 'instructions';

    const KEY_TITLE = 'title';

    /**
     * Get Payment configuration status
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }

    public function getInstructions()
    {
        return $this->getValue(self::KEY_INSTRUCTIONS);
    }

    public function getTitle()
    {
        return $this->getValue(self::KEY_TITLE);
    }
}
