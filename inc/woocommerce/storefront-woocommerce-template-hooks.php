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

// Add container in summary to narrow content.
add_action( 'woocommerce_single_product_summary', 'vfront_price_wc_wrapper_open', 8 );

add_action( 'woocommerce_single_product_summary', 'storefront_edit_post_link', 60 );

// Add container in summary to narrow content.
add_action( 'woocommerce_single_product_summary', 'vfront_price_wc_wrapper_close', 1000 );

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
