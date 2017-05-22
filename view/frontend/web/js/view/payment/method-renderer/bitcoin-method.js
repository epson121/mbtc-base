/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default'
    ],
    function (ko, Component) {
        'use strict';

        return Component.extend({


            defaults: {
                template: 'Mbtc_Base/payment/bitcoin'
            },

            getInstructions: function () {
                var instructions = window.checkoutConfig.payment.bitcoin.instructions;
                return instructions ? instructions : "";
            }

        });

    }
);
