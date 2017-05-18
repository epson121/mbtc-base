define([
    'uiComponent',
    'jquery',
    'qrcodejs'
], function (Component, $) {
    'use strict';

    return Component.extend({
        initialize: function (container, options) {
            this._super();
            var self = this;



            $(document).ready(function(){
                console.log(container);
                console.log(options);

                console.log(QRCode);

            });


        }
    });
});

