define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery'
], function (ko, Component, CustomerData, $) {
    return Component.extend({
        default: {
            temperature: ko.observable()
        },

        initialize: function () {
            this._super();
            let temperature = CustomerData.get('weather');

            if (temperature() && temperature()['temperature']) {
                this._setTemperature(temperature()['temperature'])
            } else {
                temperature.subscribe(value => {
                    this._setTemperature(value['temperature']);
                }, this)
            }
        },

        /**
         *
         * @param temperature
         * @private
         */
        _setTemperature: function (temperature) {
            $('#weather-value').html(temperature);
        }
    });
});
