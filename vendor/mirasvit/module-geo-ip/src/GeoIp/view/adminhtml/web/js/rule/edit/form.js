define([
    'underscore',
    'Magento_Ui/js/form/form'
], function (_, Form) {
    'use strict';
    
    return Form.extend({
        defaults: {
            imports: {
                sourceType:       '${ $.provider }:data.source_type',
                isChangeStore:    '${ $.provider }:data.is_change_store',
                isChangeCurrency: '${ $.provider }:data.is_change_currency',
                isRedirect:       '${ $.provider }:data.is_redirect',
                isRestrict:       '${ $.provider }:data.is_restrict'
            },
            listens: {
                sourceType:       'handleSourceTypeUpdate',
                isChangeStore:    'handleIsChangeStoreUpdate',
                isChangeCurrency: 'handleIsChangeCurrencyUpdate',
                isRedirect:       'handleIsRedirectUpdate',
                isRestrict:       'handleIsRestrictUpdate'
            }
        },
        
        initialize: function () {
            this._super();
            
            setInterval(function () {
                this.handleSourceTypeUpdate();
                this.handleIsChangeStoreUpdate();
                this.handleIsChangeCurrencyUpdate();
                this.handleIsRedirectUpdate();
                this.handleIsRestrictUpdate();
            }.bind(this), 300);
        },
        
        handleSourceTypeUpdate: function () {
            var conditions = this.getChild("conditions");
            
            if (!conditions) {
                return;
            }
            
            var country = conditions.getChild('source_value_country');
            var locale = conditions.getChild('source_value_locale');
            var ip = conditions.getChild('source_value_ip');
            
            if (!country || !locale || !ip) {
                return;
            }
            
            if (this.sourceType === 'country') {
                country.show();
                locale.hide();
                ip.hide();
            } else if (this.sourceType === 'locale') {
                country.hide();
                locale.show();
                ip.hide();
            } else if (this.sourceType === 'ip') {
                country.hide();
                locale.hide();
                ip.show();
            } else {
                country.hide();
                locale.hide();
                ip.hide();
            }
        },
        
        handleIsChangeStoreUpdate: function () {
            this.toggleField('to_store', this.isChangeStore);
        },
        
        handleIsChangeCurrencyUpdate: function () {
            this.toggleField('to_currency', this.isChangeCurrency);
        },
        
        handleIsRedirectUpdate: function () {
            this.toggleField('to_redirect_url', this.isRedirect);
        },
        
        handleIsRestrictUpdate: function () {
            this.toggleField('to_restrict_url', this.isRestrict);
        },
        
        toggleField: function (actionField, visibility) {
            var actions = this.getChild('actions');
            
            if (!actions) {
                return;
            }
            
            var field = actions.getChild(actionField);
            
            if (!field) {
                return;
            }
            
            parseInt(visibility) ? field.show() : field.hide();
        }
    });
});