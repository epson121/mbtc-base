<?php

namespace Mbtc\Base\Model\Config\Source;


class BlockchainExplorers {


    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'blockr', 'label' => 'Blockr'],
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            0 => 'blockr',
        ];
    }


}