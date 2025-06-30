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
			add_action( 'customize_controls_print_styles', array( $this, 'customizer_custom_control_css' ) );
			add_action( 'customize_register', array( $this, 'edit_default_customizer_settings' ), 99 );
			add_action( 'init', array( $this, 'default_theme_mod_values' ), 10 );
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
					'background_color'          => 'ffffff',
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
		 * @param  array $wp_customize the Customizer object.
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

			// Move background color setting alongside background image.
			$wp_customize->get_control( 'background_color' )->section  = 'v_color_scheme';
			$wp_customize->get_control( 'background_color' )->priority = 5;

			// Change background image section title & priority.
			$wp_customize->get_section( 'background_image' )->title    = __( 'Background', 'storefront' );
			$wp_customize->get_section( 'background_image' )->priority = 30;

			// Change header image section title & priority.
			$wp_customize->get_section( 'header_image' )->title    = __( 'Header', 'storefront' );
			$wp_customize->get_section( 'header_image' )->priority = 25;

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

		}

		/**
		 * Get all of the Storefront theme mods.
		 *
		 * @return array $storefront_theme_mods The Storefront Theme Mods.
		 */
		public function get_storefront_theme_mods() {
			$storefront_theme_mods = array(
				'background_color'            => storefront_get_content_background_color(),
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
			}

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
