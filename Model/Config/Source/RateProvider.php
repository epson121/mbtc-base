<?php

namespace Mbtc\Base\Model\Config\Source;


class RateProvider
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'bitpay', 'label' => 'Bitpay'],
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            0 => 'Bitpay',
        ];
    }
}