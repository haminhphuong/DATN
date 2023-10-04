var config = {
    map: {
        '*': {
            select2: 'Ecommage_CustomStoreLocation/js/select2.min',
        }
    },
    paths: {
        'swiper': 'Ecommage_CustomStoreLocation/js/swiper.min',
    },
    shim: {
        'swiper': {
            deps: ['jquery']
        },
        'select2': {
            deps: ['jquery', 'mage/translate']
        }
    }
};
