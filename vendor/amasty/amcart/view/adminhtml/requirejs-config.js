var config = {
    map: {
        '*': {
            amasty_acart_schedule: 'Amasty_Acart/js/schedule',
            amasty_acart_test: 'Amasty_Acart/js/test',
            amasty_acart_reports: 'Amasty_Acart/js/reports'
        }
    },

    shim: {
        'es6-collections': {
            deps: ['Amasty_Acart/vendor/amcharts/plugins/polyfill.min']
        },

        'Amasty_Acart/vendor/amcharts/core.min': {
            deps: ['es6-collections']
        },

        'Amasty_Acart/vendor/amcharts/charts.min': {
            deps: ['Amasty_Acart/vendor/amcharts/core.min']
        },

        'Amasty_Acart/vendor/amcharts/themes/animated.min': {
            deps: ['Amasty_Acart/vendor/amcharts/core.min']
        }
    }
};
