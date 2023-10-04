var config = {
    config: {
        mixins: {
            'mage/gallery/gallery': {
                'js/gallery/gallery-mixin': true
            },
            'mage/collapsible': {
                'js/mage/collapsible-mixin': true
            },
            'mage/validation': {
                'js/mage/validation-mixin': true
            }
        }
    },
    map: {
        '*': {
            amPopup: 'js/am-popup',
            amCollapsible: 'js/am-collapsible',
            'fotorama/fotorama': 'js/gallery/fotorama-custom',
            swiper: 'Magento_Theme/js/swiper-bundle.min',
            themeCustomJs: 'Magento_Theme/gw-theme',
        }
    },
    paths: {
        slick: 'Amasty_Base/vendor/slick/slick.min'
    },
    shim: {
        slick: {
            deps: [ 'jquery' ]
        }
    }
};
