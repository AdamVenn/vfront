<?php
/**
 * Storefront WooCommerce Customizer Class
 *
 * @package  storefront
 * @since    2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Storefront_WooCommerce_Customizer' ) ) :

	/**
	 * The Storefront Customizer class
	 */
	class Storefront_WooCommerce_Customizer extends Storefront_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 2.4.0
		 * @return void
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'customize_register' ), 10 );
			add_filter( 'storefront_setting_default_values', array( $this, 'setting_default_values' ) );
		}

		/**
		 * Returns an array of the desired default Storefront Options
		 *
		 * @param array $defaults array of default options.
		 * @since 2.4.0
		 * @return array
		 */
		public function setting_default_values( $defaults = array() ) {
			$defaults['vfront_show_breadcrumbs']       = true;
			$defaults['storefront_sticky_add_to_cart'] = true;
			$defaults['storefront_product_pagination'] = true;

			return $defaults;
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer along with several other settings.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 2.4.0
		 */
		public function customize_register( $wp_customize ) {

			/**
			 * WooCommerce General
			 */
			$wp_customize->add_section(
				'storefront_wc_general',
				array(
					'title'    => __( 'General', 'storefront' ),
					'priority' => 5,
					'panel'    => 'woocommerce',
				)
			);

			$wp_customize->add_setting(
				'vfront_show_breadcrumbs',
				array(
					'default'           => apply_filters( 'vfront_show_breadcrumbs', true ),
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'vfront_show_breadcrumbs',
				array(
					'type'        => 'checkbox',
					'section'     => 'storefront_wc_general',
					'label'       => __( 'Show breadcrumbs', 'storefront' ),
					'description' => __( 'Show breadcrumbs', 'storefront' ),
					'priority'    => 40,
				)
			);

			/**
			 * Product Page
			 */
			$wp_customize->add_section(
				'storefront_single_product_page',
				array(
					'title'    => __( 'Product Page', 'storefront' ),
					'priority' => 10,
					'panel'    => 'woocommerce',
				)
			);

			$wp_customize->add_setting(
				'storefront_product_pagination',
				array(
					'default'           => apply_filters( 'storefront_default_product_pagination', true ),
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_setting(
				'storefront_sticky_add_to_cart',
				array(
					'default'           => apply_filters( 'storefront_default_sticky_add_to_cart', true ),
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_setting(
				'vfront_use_original_gallery',
				array(
					'default'           => apply_filters( 'vfront_use_original_gallery', false ),
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_setting(
				'vfront_show_meta',
				array(
					'default'           => apply_filters( 'vfront_show_meta', true ),
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'storefront_sticky_add_to_cart',
				array(
					'type'        => 'checkbox',
					'section'     => 'storefront_single_product_page',
					'label'       => __( 'Sticky Add-To-Cart', 'storefront' ),
					'description' => __( 'A small content bar at the top of the browser window which includes relevant product information and an add-to-cart button. It slides into view once the standard add-to-cart button has scrolled out of view.', 'storefront' ),
					'priority'    => 10,
				)
			);

			$wp_customize->add_control(
				'storefront_product_pagination',
				array(
					'type'        => 'checkbox',
					'section'     => 'storefront_single_product_page',
					'label'       => __( 'Product Pagination', 'storefront' ),
					'description' => __( 'Displays next and previous links on product pages. A product thumbnail is displayed with the title revealed on hover.', 'storefront' ),
					'priority'    => 20,
				)
			);

			$wp_customize->add_control(
				'vfront_use_original_gallery',
				array(
					'type'        => 'checkbox',
					'section'     => 'storefront_single_product_page',
					'label'       => __( 'Use original Storefront gallery', 'storefront' ),
					'description' => __( 'Use the product image gallery from the original Storefront theme', 'storefront' ),
					'priority'    => 30,
				)
			);

			$wp_customize->add_control(
				'vfront_show_meta',
				array(
					'type'        => 'checkbox',
					'section'     => 'storefront_single_product_page',
					'label'       => __( 'Show meta info', 'storefront' ),
					'description' => __( 'Show categories, tags, etc. on product page', 'storefront' ),
					'priority'    => 35,
				)
			);
		}

	}

endif;

return new Storefront_WooCommerce_Customizer();
