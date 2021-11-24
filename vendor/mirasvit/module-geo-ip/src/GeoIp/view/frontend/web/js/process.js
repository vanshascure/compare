define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mirasvit.geoIpProcess', {
        options: {
            ajaxMode:   false,
            urlProcess: null,
            urlResult:  null,
            redirectUrls: null,

            popupTypeNone:         null,
            popupTypeNotification: null,
            popupTypeConfirmation: null,

            ruleSelectorRedirect: null,
            ruleSelectorCurrency: null,
            ruleSelectorRestrict: null,
            ruleSelectorStore:    null
        },

        redirectUrl: null,

        storeId: null,

        _create: function () {
            this.events();
            this.send(this.options.urlProcess, {
                    relativePageUrl: window.location.pathname + window.location.search + window.location.hash
                },
                function (response) {
                    if (response.data === undefined || response.data.ruleSelector === undefined) {
                        return;
                    }

                    switch (response.data.ruleSelector) {
                        case this.options.ruleSelectorRedirect:
                            this.redirectHandler(response.data);
                            break;
                        case this.options.ruleSelectorCurrency:
                            this.currencyHandler(response.data);
                            break;
                        case this.options.ruleSelectorRestrict:
                            this.restrictHandler(response.data);
                            break;
                        case this.options.ruleSelectorStore:
                            this.storeHandler(response.data);
                            break;
                    }

                }.bind(this)
            );
        },

        redirectHandler: function (responseData) {
            if (responseData.redirectUrl === undefined) {
                return;
            }

            if (responseData.isProcessOnFirstVisit && responseData.isLocationChanged) {
                return;
            }

            this.redirectUrl = responseData.redirectUrl;

            switch (responseData.popupType) {
                case this.options.popupTypeNone:
                    $(window).trigger('mstGeoIpProcessRedirectDone');
                    break;
                case this.options.popupTypeNotification:
                    $(window).trigger('mstGeoIpPopupOpen', [this.options.popupTypeNotification]);
                    break;
                case this.options.popupTypeConfirmation:
                    if (responseData.isRequestApproved) {
                        $(window).trigger('mstGeoIpProcessRedirectDone');
                    } else if (!responseData.isRequestRejected) {
                        $(window).trigger('mstGeoIpPopupOpen', [this.options.popupTypeConfirmation]);
                    }
                    break;
            }
        },

        currencyHandler: function (responseData) {
            this.redirectUrl = responseData.redirectUrl;
            $(window).trigger('mstGeoIpProcessCurrencyDone');
        },

        restrictHandler: function (responseData) {
            this.redirectUrl = responseData.redirectUrl;
            window.location.href = this.redirectUrl;
        },

        storeHandler: function (responseData) {
            if (responseData.redirectUrl === undefined) {
                return;
            }

            if (responseData.storeId === undefined) {
                return;
            }

            if (responseData.isProcessOnFirstVisit && responseData.isLocationChanged) {
                return;
            }

            this.redirectUrl = responseData.redirectUrl;

            switch (responseData.popupType) {
                case this.options.popupTypeNone:
                    $(window).trigger('mstGeoIpProcessRedirectDone');
                    break;
                case this.options.popupTypeNotification:
                    $(window).trigger('mstGeoIpPopupOpen', [this.options.popupTypeNotification]);
                    break;
                case this.options.popupTypeConfirmation:
                    if (responseData.isRequestApproved) {
                        $(window).trigger('mstGeoIpProcessRedirectDone');
                    }
                    if (!responseData.isRequestApproved && !responseData.isRequestRejected) {
                        this.storeId = responseData.storeId;
                        $(window).trigger('mstGeoIpPopupOpen', [this.options.popupTypeConfirmation]);
                    }
                    break;
            }
        },

        events: function () {
            $(window).on('mstGeoIpProcessCurrencyDone', function () {
                if (this.redirectUrl === undefined) {
                    console.log('redirectUrl is empty');
                    return;
                }

                this.send(this.options.urlResult, {
                        ruleSelector: this.options.ruleSelectorCurrency,
                        result:       'done'
                    },
                    function () {
                        window.location.href = this.redirectUrl;
                    }.bind(this)
                );
            }.bind(this));

            $(window).on('mstGeoIpProcessRedirectDone', function () {
                if (this.redirectUrl === undefined) {
                    console.log('redirectUrl is empty');
                    return;
                }

                this.send(this.options.urlResult, {
                        ruleSelector: this.options.ruleSelectorRedirect,
                        result:       'done'
                    },
                    function () {
                        window.location.href = this.redirectUrl;
                    }.bind(this)
                );
            }.bind(this));

            $(window).on('mstGeoIpProcessRedirectAccept', function () {
                if (this.redirectUrl === undefined) {
                    console.log('redirectUrl is empty');
                    return;
                }

                let data = {
                    ruleSelector: this.options.ruleSelectorRedirect,
                    result:       'accept'
                };
                if (this.storeId) {
                    data.ruleSelector = this.options.ruleSelectorStore;
                    data.storeId = this.storeId;
                }

                this.send(this.options.urlResult, data, function (resp) {
                        if (resp['data']['status_after_save']['ruleSelector'] == this.options.ruleSelectorStore) {
                            this.redirectUrl = resp['data']['redirectUrl'];
                        }

                        try {
                            let oldUrl = new URL(window.location.href);
                            let newUrl = new URL(this.redirectUrl);
                            if (newUrl.hostname != oldUrl.hostname) {
                                let redirectUrl = new URL(this.options.redirectUrls[this.storeId]);
                                redirectUrl.searchParams.append('redirectUrl', this.redirectUrl);
                                redirectUrl.searchParams.append('storeId', this.storeId);
                                window.location.href = redirectUrl.href;
                            } else {
                                window.location.href = this.redirectUrl;
                            }
                        } catch (e) {
                            window.location.href = this.redirectUrl;
                        }
                    }.bind(this)
                );
            }.bind(this));

            $(window).on('mstGeoIpProcessRedirectReject', function () {
                this.send(this.options.urlResult, {
                        ruleSelector: this.options.ruleSelectorRedirect,
                        result:       'reject'
                    }
                );
            }.bind(this));
        },

        send: function (url, dataObj, successCallback) {
            $.ajax({
                type:    'POST',
                url:     url,
                data:    dataObj,
                success: function (response) {
                    if (successCallback) {
                        successCallback(response);
                    }
                }
            });
        }


    });

    return $.mirasvit.geoIpProcess;
});
