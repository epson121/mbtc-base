/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        //var config = window.checkoutConfig.payment;
        //console.log(config);
        //console.log("TEST");
        rendererList.push(
            {
                type: 'bitcoin',
                component: 'Mbtc_Base/js/view/payment/method-renderer/bitcoin-method'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
