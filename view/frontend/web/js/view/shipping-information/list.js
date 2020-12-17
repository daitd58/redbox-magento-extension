/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'mageUtils',
    'uiComponent',
    'uiLayout',
    'Magento_Checkout/js/model/quote'
], function ($, ko, utils, Component, layout, quote) {
    'use strict';

    var defaultRendererTemplate = {
        parent: '${ $.$data.parentName }',
        name: '${ $.$data.name }',
        component: 'Inpost_Lockers/js/view/shipping-information/address-renderer/inpost'
    };

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-information/list',
            rendererTemplates: {}
        },

        /** @inheritdoc */
        initialize: function () {
            var self = this;

            this._super()
                .initChildren();

            quote.shippingAddress.subscribe(function (address) {
                self.createRendererComponent(address);
            });

            return this;
        },

        /** @inheritdoc */
        initConfig: function () {
            this._super();
            // the list of child components that are responsible for address rendering
            this.rendererComponents = {};

            return this;
        },

        /** @inheritdoc */
        initChildren: function () {
            return this;
        },

        /**
         * Create new component that will render given address in the address list
         *
         * @param {Object} address
         */
        createRendererComponent: function (address) {
            var rendererTemplate, templateData, rendererComponent;
            $.each(this.rendererComponents, function (index, component) {
                component.visible(false);
            });

            if (this.rendererComponents[address.getType()]) {
                this.rendererComponents[address.getType()].address(address);
                this.rendererComponents[address.getType()].visible(true);
            } else {
                // rendererTemplates are provided via layout
                rendererTemplate = address.getType() != undefined && this.rendererTemplates[address.getType()] != undefined ? //eslint-disable-line
                    utils.extend({}, defaultRendererTemplate, this.rendererTemplates[address.getType()]) :
                    defaultRendererTemplate;
                templateData = {
                    parentName: this.name,
                    name: address.getType()
                };

                address = this.initLocker(address);

                rendererComponent = utils.template(rendererTemplate, templateData);
                utils.extend(
                    rendererComponent,
                    {
                        address: ko.observable(address),
                        visible: ko.observable(true)
                    }
                );
                layout([rendererComponent]);
                this.rendererComponents[address.getType()] = rendererComponent;
            }
        },

        initLocker: function (address) {
            var locker = window.localStorage.getItem('selected_locker');
            if (locker) {
                locker = JSON.parse(locker);
                if (locker.hasOwnProperty('id')) {
                    if (!address.hasOwnProperty('extensionAttributes')) {
                        Object.defineProperty(address, 'extensionAttributes', {
                            value: {},
                            writable: true,
                            enumerable: true,
                            configurable: true
                        });
                    }

                    if (!address.extensionAttributes.hasOwnProperty('lockerMachine')) {
                        Object.defineProperty(address.extensionAttributes, 'lockerMachine', {
                            writable: true,
                            enumerable: true,
                            configurable: true
                        });
                    }

                    address.extensionAttributes.lockerMachine = locker.id;
                }
            }
            return address;
        }
    });
});
