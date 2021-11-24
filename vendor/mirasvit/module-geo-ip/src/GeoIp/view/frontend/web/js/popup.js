define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, Component, modal, t) {
    'use strict';
    return Component.extend({
        defaults: {
            //template:  'Mirasvit_GeoIp/popup',
            //container: '.mst-geo-ip__popup',
            //
            popup: {
                type:      'notification',
                title:     '',
                text:      '',
                acceptUrl: '',
                rejectUrl: '',

                ajaxMode:              false,
                isShowRequest:         false,
                popupTypeNone:         null,
                popupTypeNotification: null,
                popupTypeConfirmation: null
            }
        },

        modal: {},

        initialize: function () {
            var self = this;

            this._super();

            _.bindAll(this, 'handleDocumentClick', 'handleAccept', 'handleReject');

            let $div = $('<div/>').html(this.popup.text);
            if (this.popup.ajaxMode) {
                $(window).on('mstGeoIpPopupOpen', function (e, popupType) {
                    if (popupType === 'confirmation') {
                        this.modal = modal(this.getButtonConfigConfirmationAjax(popupType), $div);
                        this.modal.openModal();
                    }
                    if (popupType === 'notification') {
                        this.modal = modal(this.getButtonConfigNotificationAjax(popupType), $div);
                        this.modal.openModal();
                    }
                }.bind(this));
            } else {
                this.modal = modal(this.getButtonConfig(), $div);
                if (this.popup.isShowRequest) {
                    this.modal.openModal();
                }
            }
        },

        getButtonConfig: function () {
            let config = {
                title:            this.popup.title,
                clickableOverlay: false
            };

            if (this.popup.type === this.popup.popupTypeConfirmation) {
                config.buttons = [
                    {
                        text:  t('Accept'),
                        click: function () {
                            window.location.href = this.popup.acceptUrl
                        }.bind(this)
                    },
                    {
                        text:  t('Decline'),
                        click: function () {
                            window.location.href = this.popup.rejectUrl
                        }.bind(this)
                    }
                ];
            } else if (this.popup.type === this.popup.popupTypeNotification) {
                config.buttons = [
                    {
                        text:  t('Ok'),
                        click: function () {
                            window.location.href = this.popup.acceptUrl
                        }.bind(this)
                    }
                ];
            }
            return config;
        },

        getButtonConfigConfirmationAjax: function () {
            let config = {
                title:            this.popup.title,
                clickableOverlay: false
            };

            config.buttons = [
                {
                    text:  t('Accept'),
                    click: function () {
                        $(window).trigger('mstGeoIpProcessRedirectAccept');
                        this.modal.closeModal();
                    }.bind(this)
                },
                {
                    text:  t('Decline'),
                    click: function () {
                        $(window).trigger('mstGeoIpProcessRedirectReject');
                        this.modal.closeModal();
                    }.bind(this)
                }
            ];

            return config;
        },

        getButtonConfigNotificationAjax: function () {
            let config = {
                title:            this.popup.title,
                clickableOverlay: false
            };

            config.buttons = [
                {
                    text:  t('Ok'),
                    click: function () {
                        $(window).trigger('mstGeoIpProcessRedirectDone');
                    }.bind(this)
                }
            ];

            return config;
        },

        initObservable: function () {
            this._super()
                .observe('isVisible', false);

            return this;
        },

        handleDocumentClick: function (e) {
            if (!$(e.target).closest(this.container).length) {
                if (this.isVisible()) {
                    this.isVisible(false);
                }
            }
        },


        handleAccept: function (_, e) {
            var self = this;

            e.preventDefault();

            self.isVisible(false);
            subscriber.subscribe();

            track.log("accept", "prompt", this.prompt.prompt_id, 1);

            this.localStorage.set('status', 'accept');
        },

        handleReject: function (_, e) {
            e.preventDefault();

            this.isVisible(false);

            track.log("reject", "prompt", this.prompt.prompt_id, 1);

            this.localStorage.set('status', 'reject');
        }

    });
});
