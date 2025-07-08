<?php
/**
 * Storefront WooCommerce hooks
 *
 * @package storefront
 */

/**
 * Homepage
 *
 * @see  storefront_product_categories()
 * @see  storefront_recent_products()
 * @see  storefront_featured_products()
 * @see  storefront_popular_products()
 * @see  storefront_on_sale_products()
 * @see  storefront_best_selling_products()
 */
add_action( 'homepage', 'storefront_product_categories', 20 );
add_action( 'homepage', 'storefront_recent_products', 30 );
add_action( 'homepage', 'storefront_featured_products', 40 );
add_action( 'homepage', 'storefront_popular_products', 50 );
add_action( 'homepage', 'storefront_on_sale_products', 60 );
add_action( 'homepage', 'storefront_best_selling_products', 70 );

/**
 * Layout
 *
 * @see  storefront_before_content()
 * @see  storefront_after_content()
 * @see  woocommerce_breadcrumb()
 * @see  storefront_shop_messages()
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_main_content', 'storefront_before_content', 10 );
add_action( 'woocommerce_after_main_content', 'storefront_after_content', 10 );
add_action( 'storefront_content_top', 'storefront_shop_messages', 15 );

if ( get_theme_mod( 'vfront_show_breadcrumbs', true ) ) {
	add_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
}

add_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper', 9 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 30 );
add_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper_close', 31 );

add_action( 'woocommerce_before_shop_loop', 'storefront_sorting_wrapper', 9 );
add_action( 'woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30 );
add_action( 'woocommerce_before_shop_loop', 'storefront_sorting_wrapper_close', 31 );

/**
 * Products
 *
 * @see storefront_edit_post_link()
 * @see storefront_upsell_display()
 * @see storefront_single_product_pagination()
 * @see storefront_sticky_single_add_to_cart()
 */

if ( ! get_theme_mod( 'vfront_use_original_gallery', false ) ) {
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
}

// Move title.
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );

add_action( 'woocommerce_before_single_product_summary', 'vfront_show_product_video', 20 );

// Move short description.
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 7 );

/**
 * WooCommerce payment methods buttons.
 */

// Stripe gateway uses:
// add_action( 'woocommerce_after_add_to_cart_form', [ $this, 'display_express_checkout_button_html' ], 1 );.

// Add container for cart form.
add_action( 'woocommerce_before_add_to_cart_form', 'vfront_cart_form_wrapper_open', 100 );

// Add container for WC Payments injected content.
add_action( 'woocommerce_after_add_to_cart_form', 'vfront_payment_gateways_wrapper_open', 8 );

// Mock elements to help with styling. Enable when testing.
add_action(
	'woocommerce_after_add_to_cart_form',
	function() {
		return;
		?>
		<div class="ppc-button-wrapper">
			<!-- Example PayPal Logo -->
			<svg version="1.1" viewBox="0 0 150 38.14" xmlns="http://www.w3.org/2000/svg"><rect x=".002242" y=".002242" width="150" height="38.14" style="fill:#ffc439;stroke-linecap:round;stroke-linejoin:round;stroke-width:.004483;stroke:#000000"/><g transform="matrix(.793 0 0 -.793 13.4 39.33)" clip-path="url(#b)" style="stroke-width:1.261"><path d="m32.42 40.98c-1.674 1.908-4.7 2.726-8.571 2.726h-11.24a1.609 1.609 0 01-1.59-1.357l-4.676-29.67a.964.964 0 01.953-1.114h6.936l1.742 11.05-.054-.346a1.604 1.604 0 001.583 1.357h3.296c6.475 0 11.54 2.63 13.03 10.24.044.225.082.444.115.658.44 2.812-.003 4.726-1.524 6.459" fill="#003087" style="stroke-width:1.261"/><path d="m117.3 26.86c-.424-2.784-2.55-2.784-4.606-2.784h-1.17l.821 5.198c.05.314.32.545.638.545h.537c1.4 0 2.722 0 3.404-.797.407-.477.53-1.185.376-2.162m-.895 7.264h-7.756a1.08 1.08 0 01-1.066-.91l-3.134-19.89a.647.647 0 01.638-.747h3.98c.371 0 .687.27.745.636l.89 5.64c.082.523.534.91 1.064.91h2.454c5.11 0 8.058 2.471 8.828 7.372.347 2.142.014 3.826-.989 5.005-1.103 1.296-3.058 1.982-5.653 1.982" fill="#009cde" style="stroke-width:1.261"/><path d="m62.01 26.86c-.424-2.784-2.55-2.784-4.607-2.784h-1.17l.821 5.198c.05.314.32.545.638.545h.537c1.4 0 2.722 0 3.404-.797.408-.477.531-1.185.377-2.162m-.895 7.264h-7.756c-.53 0-.982-.386-1.065-.91l-3.135-19.89a.646.646 0 01.638-.747h3.704c.53 0 .981.386 1.064.91l.847 5.365c.082.524.534.91 1.064.91h2.454c5.11 0 8.058 2.472 8.828 7.373.347 2.142.014 3.826-.989 5.005-1.103 1.296-3.058 1.982-5.653 1.982m18.01-14.4c-.36-2.122-2.043-3.547-4.192-3.547-1.077 0-1.94.347-2.494 1.003-.55.65-.756 1.577-.582 2.608.334 2.104 2.046 3.574 4.162 3.574 1.055 0 1.91-.35 2.476-1.012.569-.667.793-1.599.63-2.626m5.176 7.23h-3.714a.647.647 0 01-.64-.547l-.162-1.038-.26.376c-.804 1.167-2.597 1.558-4.387 1.558-4.103 0-7.608-3.11-8.29-7.47-.355-2.177.149-4.256 1.383-5.707 1.133-1.333 2.75-1.888 4.677-1.888 3.308 0 5.142 2.124 5.142 2.124l-.166-1.032a.646.646 0 01.639-.747h3.344c.53 0 .982.385 1.065.91l2.008 12.71a.647.647 0 01-.64.747" fill="#003087" style="stroke-width:1.261"/><path d="m134.4 19.72c-.36-2.122-2.043-3.547-4.192-3.547-1.077 0-1.94.347-2.494 1.003-.55.65-.756 1.577-.582 2.608.334 2.104 2.045 3.574 4.162 3.574 1.055 0 1.91-.35 2.476-1.012.569-.667.793-1.599.63-2.626m5.176 7.23h-3.714a.647.647 0 01-.64-.547l-.162-1.038-.26.376c-.804 1.167-2.597 1.558-4.387 1.558-4.102 0-7.607-3.11-8.29-7.47-.355-2.177.15-4.256 1.384-5.707 1.133-1.333 2.75-1.888 4.677-1.888 3.309 0 5.143 2.124 5.143 2.124l-.166-1.032a.644.644 0 01.637-.747h3.343c.53 0 .982.385 1.066.91l2.008 12.71a.647.647 0 01-.64.747" fill="#009cde" style="stroke-width:1.261"/><path d="m104.1 26.95h-3.734c-.357 0-.69-.177-.89-.473l-5.15-7.584-2.183 7.288a1.08 1.08 0 01-1.033.77h-3.669a.647.647 0 01-.612-.856l4.11-12.07-3.866-5.455a.647.647 0 01.528-1.02h3.73c.352 0 .683.173.885.463l12.41 17.92a.646.646 0 01-.53 1.015" fill="#003087" style="stroke-width:1.261"/><path d="m144 33.58-3.184-20.25a.647.647 0 01.639-.747h3.201c.53 0 .982.386 1.065.91l3.139 19.89a.646.646 0 01-.639.747h-3.582a.645.645 0 01-.639-.546" fill="#009cde" style="stroke-width:1.261"/><path d="m32.42 40.98c-1.674 1.908-4.7 2.726-8.571 2.726h-11.24a1.609 1.609 0 01-1.59-1.357l-4.676-29.67a.964.964 0 01.953-1.114h6.936l1.742 11.05-.054-.346a1.604 1.604 0 001.583 1.357h3.296c6.475 0 11.54 2.63 13.03 10.24.044.225.082.444.115.658.44 2.812-.003 4.726-1.524 6.459" fill="#003087" style="stroke-width:1.261"/><path d="m17.85 34.48a1.408 1.408 0 001.389 1.187h8.808c1.043 0 2.016-.068 2.905-.21a12.21 12.21 0 001.44-.322 7.957 7.957 0 001.551-.618c.442 2.813-.002 4.726-1.523 6.46-1.675 1.907-4.7 2.725-8.571 2.725h-11.24a1.609 1.609 0 01-1.588-1.357l-4.678-29.67a.964.964 0 01.952-1.115h6.937l1.742 11.05z" fill="#003087" style="stroke-width:1.261"/><path d="m33.94 34.52a18.29 18.29 0 00-.115-.658c-1.481-7.607-6.551-10.24-13.03-10.24h-3.297a1.602 1.602 0 01-1.582-1.357l-1.688-10.7-.48-3.036a.844.844 0 01.834-.976h5.847c.692 0 1.28.504 1.389 1.187l.057.298 1.102 6.984.07.386a1.407 1.407 0 001.39 1.187h.875c5.664 0 10.1 2.3 11.4 8.956.54 2.78.26 5.103-1.17 6.734a5.584 5.584 0 01-1.601 1.235" fill="#009cde" style="stroke-width:1.261"/><path d="m32.39 35.14c-.226.067-.459.127-.699.18s-.488.1-.742.14c-.89.145-1.862.213-2.906.213h-8.807a1.404 1.404 0 01-1.389-1.188l-1.872-11.87-.054-.345a1.602 1.602 0 001.582 1.357h3.297c6.475 0 11.54 2.63 13.03 10.24.044.225.081.443.115.658a7.998 7.998 0 01-1.218.514c-.109.036-.22.07-.333.104" fill="#012169" style="stroke-width:1.261"/></g></svg>
			<!-- Example PayPal Logo -->
		</div>

		<div id="wc-stripe-express-checkout-element" style="height: 48px;">
		<!-- Example Google Pay Logo -->	
			<svg version="1.1" viewBox="0 0 250 48" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><style type="text/css">
				.st0{fill:#FFFFFF;}
				.st1{fill:#4285F4;}
				.st2{fill:#34A853;}
				.st3{fill:#FBBC04;}
				.st4{fill:#EA4335;}
			</style><rect x=".0055" y=".0055" width="250" height="47.99" style="fill:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:.011;stroke:#000000"/><path class="st0" d="m122.3 23.52v11.34h-3.658v-28.05h9.511c2.317 0 4.512.8536 6.219 2.439 1.707 1.463 2.561 3.658 2.561 5.975 0 2.317-.8536 4.39-2.561 5.975-1.707 1.585-3.78 2.439-6.219 2.439zm0-13.29v9.755h6.097c1.341 0 2.683-.4878 3.536-1.463 1.951-1.829 1.951-4.878.1219-6.707l-.1219-.1219c-.9755-.9755-2.195-1.585-3.536-1.463z"/><path class="st0" d="m145.4 15.1c2.683 0 4.756.7316 6.341 2.195 1.585 1.463 2.317 3.414 2.317 5.853v11.71h-3.414v-2.683h-.1219c-1.463 2.195-3.536 3.292-5.975 3.292-2.073 0-3.902-.6097-5.365-1.829-1.341-1.219-2.195-2.927-2.195-4.756 0-1.951.7316-3.536 2.195-4.756 1.463-1.219 3.536-1.707 5.975-1.707 2.195 0 3.902.3658 5.243 1.219v-.8536c0-1.219-.4878-2.439-1.463-3.17-.9755-.8536-2.195-1.341-3.536-1.341-2.073 0-3.658.8536-4.756 2.561l-3.17-1.951c1.951-2.561 4.512-3.78 7.926-3.78zm-4.634 13.9c0 .9755.4878 1.829 1.219 2.317.8536.6097 1.829.9755 2.805.9755 1.463 0 2.926-.6097 4.024-1.707 1.219-1.097 1.829-2.439 1.829-3.902-1.097-.8536-2.683-1.341-4.756-1.341-1.463 0-2.683.3658-3.658 1.097-.9755.6097-1.463 1.463-1.463 2.561z"/><path class="st0" d="m173.8 15.71-12.07 27.68h-3.658l4.512-9.633-7.926-17.92h3.902l5.731 13.78h.1219l5.609-13.78h3.78z"/><path class="st1" d="m107.8 21.08c0-1.097-.1219-2.195-.2439-3.292h-15.24v6.219h8.658c-.3658 1.951-1.463 3.78-3.17 4.878v4.024h5.243c3.048-2.805 4.756-6.95 4.756-11.83z"/><path class="st2" d="m92.34 36.81c4.39 0 8.048-1.463 10.73-3.902l-5.243-4.024c-1.463.9755-3.292 1.585-5.487 1.585-4.146 0-7.804-2.805-9.023-6.707h-5.365v4.146c2.805 5.487 8.292 8.901 14.39 8.901z"/><path class="st3" d="m83.31 23.76c-.7316-1.951-.7316-4.146 0-6.219v-4.146h-5.365c-2.317 4.512-2.317 9.877 0 14.51z"/><path class="st4" d="m92.34 10.96c2.317 0 4.512.8536 6.219 2.439l4.634-4.634c-2.926-2.683-6.829-4.268-10.73-4.146-6.097 0-11.71 3.414-14.39 8.901l5.365 4.146c1.097-3.902 4.756-6.707 8.901-6.707z"/></svg>
			<!-- Example Google Pay Logo -->	
		</div>
		<?php
	},
	30
);

// Move all Paypal payments hooks into the container.
add_filter(
	'woocommerce_paypal_payments_single_product_renderer_hook',
	function() {
		return 'woocommerce_after_add_to_cart_form';
	}
);

add_filter(
	'woocommerce_paypal_payments_googlepay_single_product_button_render_hook',
	function() {
		return 'woocommerce_after_add_to_cart_form';
	}
);

add_filter(
	'woocommerce_paypal_payments_applepay_single_product_button_render_hook',
	function() {
		return 'woocommerce_after_add_to_cart_form';
	}
);

add_action( 'woocommerce_after_add_to_cart_form', 'vfront_payment_gateways_wrapper_close', 100 );
add_action( 'woocommerce_after_add_to_cart_form', 'vfront_cart_form_wrapper_close', 110 );

add_action( 'woocommerce_single_product_summary', 'storefront_edit_post_link', 60 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'storefront_upsell_display', 15 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );

add_action( 'woocommerce_after_single_product_summary', 'storefront_single_product_pagination', 30 );

add_action( 'storefront_after_footer', 'storefront_sticky_single_add_to_cart', 999 );

add_filter(
	'woocommerce_product_tabs',
	function ( $tabs ) {
		if ( ! get_theme_mod( 'vfront_use_original_gallery', false ) ) {
			$tabs['gallery'] = array(
				'title'    => __( 'Gallery', 'storefront' ),
				'callback' => 'vfront_woocommerce_gallery_tab_content',
				'priority' => 8,
			);
		}
		return $tabs;
	}
);

/**
 * Header
 *
 * @see storefront_primary_navigation_wc_wrapper()
 * @see storefront_product_search()
 * @see storefront_header_cart()
 * @see storefront_primary_navigation_wc_wrapper_close()
 */
add_action( 'storefront_header', 'storefront_primary_navigation_wc_wrapper', 64 );
add_action( 'storefront_header', 'storefront_header_cart', 66 );
add_action( 'storefront_header', 'storefront_product_search', 68 );
add_action( 'storefront_header', 'storefront_primary_navigation_wc_wrapper_close', 70 );

/**
 * Cart fragment
 *
 * @see storefront_cart_link_fragment()
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'storefront_cart_link_fragment' );

/**
 * Integrations
 *
 * @see storefront_woocommerce_brands_archive()
 * @see storefront_woocommerce_brands_single()
 * @see storefront_woocommerce_brands_homepage_section()
 */
if ( class_exists( 'WC_Brands' ) ) {
	add_action( 'woocommerce_archive_description', 'storefront_woocommerce_brands_archive', 5 );
	add_action( 'woocommerce_single_product_summary', 'storefront_woocommerce_brands_single', 4 );
	add_action( 'homepage', 'storefront_woocommerce_brands_homepage_section', 80 );
}

/**
 * Admin
 *
 * @see vfront_save_video_url_field()
 */

// Allow adding a product video.
add_filter(
	'woocommerce_product_data_tabs',
	function( $tabs ) {
		$tabs['Video'] = array(
			'label'     => __( 'Video', 'storefront' ),
			'target'    => 'video_data_tab_options',
			'class'     => array( 'show_if_simple', 'show_if_variable' ),
			'priority'  => 15,
		);
		return $tabs;
	}
);

// Save the chosen video URL to the database.
add_action( 'woocommerce_process_product_meta', 'vfront_save_video_url_field' );

// Content for the video tab in product admin page.
add_filter(
	'woocommerce_product_data_panels',
	function() {

		global $post;

		// The 'id' attribute needs to match the 'target' parameter set above.
		?>
		<div id='video_data_tab_options' class='panel woocommerce_options_panel'>
			<div class='options_group'>
			<?php
			woocommerce_wp_text_input(
				array(
					'label' => __( 'Video URL', 'storefront' ), // Text in the label in the editor.
					'style' => 'width: 100%;',
					'value' => get_post_meta( $post->ID, 'vid_url', true ),
					'id' => 'vid_url', // required, will be used as meta_key.
					'desc_tip' => 'false',
				)
			);
			wp_nonce_field( 'update-vid-url-' . get_the_ID(), 'update-vid-url-' . get_the_ID() );
			?>
			</div>
		</div>
		<?php
	}
);

// Remove unwanted tabs.
add_filter(
	'woocommerce_product_data_tabs',
	function( $tabs ) {
		unset( $tabs['marketplace-suggestions'] );

		global $post;
		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			return;
		}
		if ( $product->is_virtual() ) {
			unset( $tabs['inventory'] );
		}
		return $tabs;
	},
	98
);

/**
 * Navigation
 *
 * @see woocommerce_get_endpoint_url
 */

if ( get_theme_mod( 'v_links_nav_to_content', false ) ) {
	// Jump straight to main to save the user scrolling past the header.
	add_filter(
		'woocommerce_get_endpoint_url',
		function( $url ) {
			if ( ! strstr( $url, '#' ) && ! empty( $url ) ) {
				$url = $url . '#content';
			}
			return $url;
		}
	);
}
