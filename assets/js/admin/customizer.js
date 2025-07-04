( function ( wp, $ ) {
	'use strict';

	if ( ! wp || ! wp.customize ) {
		return;
	}

	// Function to format value based on unit class
	function formatValue( value, $span ) {
		if ( $span.hasClass( 'v-customizer-percentage' ) ) {
			return value + '%';
		}
		if ( $span.hasClass( 'v-customizer-px' ) ) {
			return value + 'px';
		}
		return value;
	}

	// Find all sliders in the customizer and bind their values
	wp.customize.bind( 'ready', function () {
		$( 'input[type="range"]' ).each( function () {
			const $slider = $( this );
			const settingName = $slider.attr( 'data-customize-setting-link' );

			if ( settingName ) {
				wp.customize( settingName, function ( value ) {
					value.bind( function ( newVal ) {
						const $settingSlider = $(
							'[data-customize-setting-link="' +
								settingName +
								'"]'
						);
						const $rangeSpan =
							$settingSlider.next( '.range-value' );
						const formattedVal = formatValue( newVal, $rangeSpan );
						$rangeSpan.text( formattedVal );
						$settingSlider.val( newVal );
					} );
				} );
			}
		} );
	} );
} )( wp, jQuery );
