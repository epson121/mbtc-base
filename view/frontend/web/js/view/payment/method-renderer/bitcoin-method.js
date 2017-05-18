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
                //isPlaceOrderActionAllowed : ko.observable(false)
            },

            getInstructions: function () {
                var instructions = window.checkoutConfig.payment.bitcoin.instructions;
                return instructions ? instructions : "";
            }

            /**
             * Initialize view.
             *
             * @return {exports}
             */
            //initialize: function () {

            //}

            /** Returns send check to info */
            //getMailingAddress: function() {
            //    return window.checkoutConfig.payment.checkmo.mailingAddress;
            //},

            /** Returns payable to info */
            //getPayableTo: function() {
            //    return window.checkoutConfig.payment.checkmo.payableTo;
            //}
        });

    }
);
