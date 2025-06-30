( function ( wp, $ ) {
    'use strict';

    if ( ! wp || ! wp.customize ) {
        return;
    }

    wp.customize('v_gradient_factor', function(value) {
        value.bind(function(newVal) {
            $('.range-value').text(newVal);
        });
    });

} )( wp, jQuery );
