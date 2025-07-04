<?php
/**
 * Storefront Customizer Class
 *
 * @package  storefront
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Storefront_Customizer' ) ) :

	/**
	 * The Storefront Customizer class
	 */
	class Storefront_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'customize_register' ), 10 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 130 );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_customizer_js' ) );
			add_action( 'customize_controls_print_styles', array( $this, 'customizer_custom_control_css' ) );
			add_action( 'customize_register', array( $this, 'edit_default_customizer_settings' ), 99 );
			add_action( 'init', array( $this, 'default_theme_mod_values' ), 10 );
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'vfront_add_slider_labels' ) );
		}

		/**
		 * Returns an array of the desired default Storefront Options
		 *
		 * @return array
		 */
		public function get_storefront_default_setting_values() {
			/**
			 * Filters for the default Storefront Options.
			 *
			 * @param array $args
			 * @package     storefront
			 * @since       2.0.0
			 */
			return apply_filters(
				'storefront_setting_default_values',
				$args = array(
					'v_text_color'              => '#6d6d6d',
					'v_heading_color'           => '#404040',
					'v_border_color'            => '#404040',
					'v_accent_color'            => '#7f54b3',
					'v_link_color'              => '#7f54b3',
					'v_container_color'         => '#f0f0f0',
					'v_box_color'               => '#9b9b9b',
					'v_header_background_color' => '#ffffff',
					'v_header_text_color'       => '#404040',
					'v_footer_background_color' => '#f0f0f0',
					'v_footer_text_color'       => '#6d6d6d',
					'v_button_background_color' => '#eeeeee',
					'v_button_text_color'       => '#333333',
					'v_background_color'        => '#ffffff',
					'v_gradient_factor'         => 0,
					'v_backdrop_blur'           => 0,
					'v_backdrop_brightness'     => 0,
					'v_links_nav_to_content'       => false,
				)
			);
		}

		/**
		 * Adds a value to each Storefront setting if one isn't already present.
		 *
		 * @uses get_storefront_default_setting_values()
		 */
		public function default_theme_mod_values() {
			foreach ( $this->get_storefront_default_setting_values() as $mod => $val ) {
				add_filter( 'theme_mod_' . $mod, array( $this, 'get_theme_mod_value' ), 10 );
			}
		}

		/**
		 * Get theme mod value.
		 *
		 * @param string $value Theme modification value.
		 * @return string
		 */
		public function get_theme_mod_value( $value ) {
			$key = substr( current_filter(), 10 );

			$set_theme_mods = get_theme_mods();

			if ( isset( $set_theme_mods[ $key ] ) ) {
				return $value;
			}

			$values = $this->get_storefront_default_setting_values();

			return isset( $values[ $key ] ) ? $values[ $key ] : $value;
		}

		/**
		 * Set Customizer setting defaults.
		 * These defaults need to be applied separately as child themes can filter storefront_setting_default_values
		 *
		 * @param  WP_Customize_Manager $wp_customize the Customizer object.
		 * @uses   get_storefront_default_setting_values()
		 */
		public function edit_default_customizer_settings( $wp_customize ) {
			foreach ( $this->get_storefront_default_setting_values() as $mod => $val ) {
				$wp_customize->get_setting( $mod )->default = $val;
			}
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer along with several other settings.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since  1.0.0
		 */
		public function customize_register( $wp_customize ) {

			// Selective refresh.
			if ( function_exists( 'add_partial' ) ) {
				$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
				$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

				$wp_customize->selective_refresh->add_partial(
					'custom_logo',
					array(
						'selector'        => '.site-branding',
						'render_callback' => array( $this, 'get_site_logo' ),
					)
				);

				$wp_customize->selective_refresh->add_partial(
					'blogname',
					array(
						'selector'        => '.site-title.beta a',
						'render_callback' => array( $this, 'get_site_name' ),
					)
				);

				$wp_customize->selective_refresh->add_partial(
					'blogdescription',
					array(
						'selector'        => '.site-description',
						'render_callback' => array( $this, 'get_site_description' ),
					)
				);
			}

			/**
			 * Custom controls
			 */
			require_once dirname( __FILE__ ) . '/class-storefront-customizer-control-radio-image.php';
			require_once dirname( __FILE__ ) . '/class-storefront-customizer-control-arbitrary.php';

			/**
			 * Settings section
			 */
			$wp_customize->add_section(
				'v_settings',
				array(
					'title'    => __( 'Theme settings', 'storefront' ),
					'priority' => 40,
				)
			);

			$wp_customize->add_setting(
				'v_links_nav_to_content',
				array(
					'default'           => apply_filters( 'v_links_nav_to_content', true ),
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'v_links_nav_to_content',
				array(
					'type'        => 'checkbox',
					'section'     => 'v_settings',
					'label'       => __( 'Navigate past header', 'storefront' ),
					'description' => __( 'When the user clicks on a link in the menu bar, the page loads scrolled to the content, saving them scrolling past the header', 'storefront' ),
					'priority'    => 10,
				)
			);

			/**
			 * Add the color scheme section
			 */
			$wp_customize->add_section(
				'v_color_scheme',
				array(
					'title'    => __( 'Color Scheme', 'storefront' ),
					'priority' => 45,
				)
			);

			/**
			 * Text Color
			 */
			$wp_customize->add_setting(
				'v_text_color',
				array(
					/**
					 * Filters for modifying the default text color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_text_color', '#6d6d6d' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_text_color',
					array(
						'label'    => __( 'Text color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_text_color',
						'priority' => 15,
					)
				)
			);

			/**
			 * Background Color
			 */
			$wp_customize->add_setting(
				'v_background_color',
				array(
					/**
					 * Filters for modifying the default background color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_background_color', '#ffffff' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_background_color',
					array(
						'label'    => __( 'Background color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_background_color',
						'priority' => 20,
					)
				)
			);

			/**
			 * Container Color
			 */
			$wp_customize->add_setting(
				'v_container_color',
				array(
					/**
					 * Filters for modifying the default container color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_container_color', '#f0f0f0' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_container_color',
					array(
						'label'    => __( 'Container color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_container_color',
						'priority' => 23,
					)
				)
			);

			/**
			 * Box Color
			 */
			$wp_customize->add_setting(
				'v_box_color',
				array(
					/**
					 * Filters for modifying the default box color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_box_color', '#9b9b9b' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_box_color',
					array(
						'label'    => __( 'Box color (forms fields, etc.)', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_box_color',
						'priority' => 24,
					)
				)
			);

			/**
			 * Accent Color
			 */
			$wp_customize->add_setting(
				'v_accent_color',
				array(
					/**
					 * Filters for modifying the default accent color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_accent_color', '#7f54b3' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_accent_color',
					array(
						'label'    => __( 'Accent color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_accent_color',
						'priority' => 31,
					)
				)
			);

			/**
			 * Link Color
			 */
			$wp_customize->add_setting(
				'v_link_color',
				array(
					/**
					 * Filters for modifying the default accent color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_link_color', '#7f54b3' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_link_color',
					array(
						'label'    => __( 'Link color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_link_color',
						'priority' => 31,
					)
				)
			);

			/**
			 * Header Image
			 */
			$wp_customize->add_control(
				new Arbitrary_Storefront_Control(
					$wp_customize,
					'storefront_header_image_heading',
					array(
						'section'  => 'header_image',
						'type'     => 'heading',
						'label'    => __( 'Header background image', 'storefront' ),
						'priority' => 6,
					)
				)
			);

			/**
			 * Border Color
			 */
			$wp_customize->add_setting(
				'v_border_color',
				array(
					/**
					 * Filters for modifying the default border color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_border_color', '#404040' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_border_color',
					array(
						'label'    => __( 'Border color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_border_color',
						'priority' => 24,
					)
				)
			);

			/**
			 * Header Color
			 */
			$wp_customize->add_setting(
				'v_heading_color',
				array(
					/**
					 * Filters for modifying the default heading color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_heading_color', '#404040' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_heading_color',
					array(
						'label'    => __( 'Heading color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_heading_color',
						'priority' => 18,
					)
				)
			);

			/**
			 * Header Background
			 */
			$wp_customize->add_setting(
				'v_header_background_color',
				array(
					/**
					 * Filters for modifying the default header background color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_header_background_color', '#ffffff' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_header_background_color',
					array(
						'label'    => __( 'Header background color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_header_background_color',
						'priority' => 51,
					)
				)
			);

			/**
			 * Header text color
			 */
			$wp_customize->add_setting(
				'v_header_text_color',
				array(
					/**
					 * Filters for modifying the default header text color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_header_text_color', '#404040' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_header_text_color',
					array(
						'label'    => __( 'Header text color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_header_text_color',
						'priority' => 52,
					)
				)
			);

			/**
			 * Footer section
			 */
			$wp_customize->add_section(
				'storefront_footer',
				array(
					'title'       => __( 'Footer', 'storefront' ),
					'priority'    => 28,
					'description' => __( 'Customize the look & feel of your website footer.', 'storefront' ),
				)
			);

			/**
			 * Footer Background
			 */
			$wp_customize->add_setting(
				'v_footer_background_color',
				array(
					/**
					 * Filters for modifying the default footer background color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_footer_background_color', '#f0f0f0' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_footer_background_color',
					array(
						'label'    => __( 'Footer background color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_footer_background_color',
						'priority' => 61,
					)
				)
			);

			/**
			 * Footer text color
			 */
			$wp_customize->add_setting(
				'v_footer_text_color',
				array(
					/**
					 * Filters for modifying the default footer text color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_footer_text_color', '#6d6d6d' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_footer_text_color',
					array(
						'label'    => __( 'Footer text color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_footer_text_color',
						'priority' => 63,
					)
				)
			);

			/**
			 * Buttons section
			 */
			$wp_customize->add_section(
				'storefront_buttons',
				array(
					'title'       => __( 'Buttons', 'storefront' ),
					'priority'    => 45,
					'description' => __( 'Customize the look & feel of your website buttons.', 'storefront' ),
				)
			);

			/**
			 * Button background color
			 */
			$wp_customize->add_setting(
				'v_button_background_color',
				array(
					/**
					 * Filters for modifying the default button background color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_button_background_color', '#eeeeee' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_button_background_color',
					array(
						'label'    => __( 'Button background color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_button_background_color',
						'priority' => 21,
					)
				)
			);

			/**
			 * Button text color
			 */
			$wp_customize->add_setting(
				'v_button_text_color',
				array(
					/**
					 * Filters for modifying the default button text color.
					 *
					 * @param string Hex color value.
					 * @package  storefront
					 * @since    2.0.0
					 */
					'default'           => apply_filters( 'storefront_default_button_text_color', '#333333' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'v_button_text_color',
					array(
						'label'    => __( 'Button text color', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_button_text_color',
						'priority' => 22,
					)
				)
			);

			/**
			 * Color effects
			 */
			$wp_customize->add_setting(
				'v_gradient_factor',
				array(
					/**
					 * Filters for modifying the amount of gradient in the color schemes.
					 *
					 * @param int -255 to 255 gradient factor value.
					 */
					'default'           => apply_filters( 'storefront_default_gradient_factor', 0 ),
					'sanitize_callback' => 'vfront_sanitize_gradient_factor',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'v_gradient_factor',
					array(
						'label'    => __( 'Gradient Factor', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_gradient_factor',
						'priority' => 2,
						'type'     => 'range',
						'input_attrs' => array(
							'min'   => -64,
							'max'   => 64,
							'step'  => 1,
							'value' => 0,
						),
						'custom_class' => 'customizer-slider',
					)
				)
			);

			$wp_customize->add_setting(
				'v_backdrop_blur',
				array(
					/**
					 * Filters for modifying the amount of blur in backdrop-filter effects.
					 *
					 * @param int 0 to 30 in pixels.
					 */
					'default'           => apply_filters( 'v_backdrop_blur', 5 ),
					'sanitize_callback' => 'vfront_sanitize_blur_radius',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'v_backdrop_blur',
					array(
						'label'    => __( 'Backdrop-filter blur', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_backdrop_blur',
						'priority' => 5,
						'type'     => 'range',
						'input_attrs' => array(
							'min'   => 0,
							'max'   => 30,
							'step'  => 1,
							'value' => 5,
						),
						'custom_class' => 'customizer-slider',
					)
				)
			);

			$wp_customize->add_setting(
				'v_backdrop_brightness',
				array(
					/**
					 * Filters for modifying the amount of brightness in backdrop-filter effects.
					 *
					 * @param int 0 to 300 in percent.
					 */
					'default'           => apply_filters( 'v_backdrop_brightness', 150 ),
					'sanitize_callback' => 'vfront_sanitize_brightness',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'v_backdrop_brightness',
					array(
						'label'    => __( 'Backdrop-filter brightness', 'storefront' ),
						'section'  => 'v_color_scheme',
						'settings' => 'v_backdrop_brightness',
						'priority' => 10,
						'type'     => 'range',
						'input_attrs' => array(
							'min'   => 0,
							'max'   => 300,
							'step'  => 1,
							'value' => 150,
						),
						'custom_class' => 'customizer-slider',
					)
				)
			);
		}

		/**
		 * Get all of the Storefront theme mods.
		 *
		 * @return array $storefront_theme_mods The Storefront Theme Mods.
		 */
		public function get_storefront_theme_mods() {
			$storefront_theme_mods = array(
				'background_color'            => get_theme_mod( 'v_background_color' ),
				'accent_color'                => get_theme_mod( 'v_accent_color' ),
				'link_color'                  => get_theme_mod( 'v_link_color' ),
				'header_background_color'     => get_theme_mod( 'v_header_background_color' ),
				'header_text_color'           => get_theme_mod( 'v_header_text_color' ),
				'footer_background_color'     => get_theme_mod( 'v_footer_background_color' ),
				'footer_text_color'           => get_theme_mod( 'v_footer_text_color' ),
				'text_color'                  => get_theme_mod( 'v_text_color' ),
				'heading_color'               => get_theme_mod( 'v_heading_color' ),
				'border_color'                => get_theme_mod( 'v_border_color' ),
				'container_color'             => get_theme_mod( 'v_container_color' ),
				'box_color'                   => get_theme_mod( 'v_box_color' ),
				'button_background_color'     => get_theme_mod( 'v_button_background_color' ),
				'button_text_color'           => get_theme_mod( 'v_button_text_color' ),
				'gradient_factor'             => get_theme_mod( 'v_gradient_factor' ),
				'backdrop_blur'               => get_theme_mod( 'v_backdrop_blur' ),
				'backdrop_brightness'         => get_theme_mod( 'v_backdrop_brightness' ),
				'links_nav_to_main'           => get_theme_mod( 'v_links_nav_to_content' ),
			);

			/**
			 * Filters for Storefront Theme Mods.
			 *
			 * @param array Associative array of theme mods for color options.
			 * @package  storefront
			 * @since    2.0.0
			 */
			return apply_filters( 'storefront_theme_mods', $storefront_theme_mods );
		}

		/**
		 * Get Customizer css.
		 *
		 * @see get_storefront_theme_mods()
		 * @return array $styles the css
		 */
		public function get_css() {
			$mods = $this->get_storefront_theme_mods();

			$gradient_factor = $mods['gradient_factor'];
			$backdrop_blur_radius = $mods['backdrop_blur'];
			$backdrop_brightness = $mods['backdrop_brightness'];

			$mods = array_filter(
				$mods,
				function( $value, $key ) {
					if ( ! is_string( $key ) ) {
						return false;
					}
					return str_contains( $key, 'color' );
				},
				ARRAY_FILTER_USE_BOTH
			);
			$css = ':root {
	/* Color scheme from customizer */
';
			foreach ( $mods as $key => $value ) {
				// Create a CSS variable for each entry.
				$css .= "    --{$key}: {$value};\n";
				$dark = storefront_adjust_color_brightness( $value, $gradient_factor );
				$css .= "    --{$key}_dark: {$dark};\n";
			}

			$css .= "--backdrop_blur: {$backdrop_blur_radius}px;\n";
			$css .= "--backdrop_brightness: {$backdrop_brightness}%;\n";

			$css .= "}\n";

			/**
			 * Filters for Storefront Customizer CSS.
			 *
			 * @param object Object of CSS rulesets.
			 * @package  storefront
			 * @since    2.0.0
			 */
			return apply_filters( 'storefront_customizer_css', $css );
		}

		/**
		 * Add CSS in <head> for styles handled by the theme customizer
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_customizer_css() {
			wp_add_inline_style( 'storefront-style', $this->get_css() );
		}

		/**
		 * Add JS used by the theme customizer
		 *
		 * @return void
		 */
		public function add_customizer_js() {
			global $storefront_version;

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'customizer-js', get_template_directory_uri() . '/assets/js/admin/customizer' . $suffix . '.js', array( 'jquery', 'customize-preview' ), $storefront_version, true );
		}

		/**
		 * Add JS to label the color effects sliders
		 *
		 * @return void
		 */
		public function vfront_add_slider_labels() {
			?>
			<script>
			wp.customize.control('v_gradient_factor', function(control) {
				control.container.append('<span class="range-value">' + control.setting() + '</span>');
			});
			wp.customize.control('v_backdrop_blur', function(control) {
				control.container.append('<span class="range-value v-customizer-px">' + control.setting() + 'px</span>');
			});
			wp.customize.control('v_backdrop_brightness', function(control) {
				control.container.append('<span class="range-value v-customizer-percentage">' + control.setting() + '%</span>');
			});
			</script>
			<?php
		}

		/**
		 * Add CSS for custom controls
		 *
		 * This function incorporates CSS from the Kirki Customizer Framework
		 *
		 * The Kirki Customizer Framework, Copyright Aristeides Stathopoulos (@aristath),
		 * is licensed under the terms of the GNU GPL, Version 2 (or later)
		 *
		 * @link https://github.com/reduxframework/kirki/
		 * @since  1.5.0
		 */
		public function customizer_custom_control_css() {
			?>
			<style>
			.customize-control-radio-image input[type=radio] {
				display: none;
			}

			.customize-control-radio-image label {
				display: block;
				width: 48%;
				float: left;
				margin-right: 4%;
			}

			.customize-control-radio-image label:nth-of-type(2n) {
				margin-right: 0;
			}

			.customize-control-radio-image img {
				opacity: .5;
			}

			.customize-control-radio-image input[type=radio]:checked + label img,
			.customize-control-radio-image img:hover {
				opacity: 1;
			}

			</style>
			<?php
		}

		/**
		 * Get site logo.
		 *
		 * @since 2.1.5
		 * @return string
		 */
		public function get_site_logo() {
			return storefront_site_title_or_logo( false );
		}

		/**
		 * Get site name.
		 *
		 * @since 2.1.5
		 * @return string
		 */
		public function get_site_name() {
			return get_bloginfo( 'name', 'display' );
		}

		/**
		 * Get site description.
		 *
		 * @since 2.1.5
		 * @return string
		 */
		public function get_site_description() {
			return get_bloginfo( 'description', 'display' );
		}

		/**
		 * Check if current page is using the Homepage template.
		 *
		 * @since 2.3.0
		 * @return bool
		 */
		public function is_homepage_template() {
			$template = get_post_meta( get_the_ID(), '_wp_page_template', true );

			if ( ! $template || 'template-homepage.php' !== $template || ! has_post_thumbnail( get_the_ID() ) ) {
				return false;
			}

			return true;
		}

	}

endif;

return new Storefront_Customizer();
