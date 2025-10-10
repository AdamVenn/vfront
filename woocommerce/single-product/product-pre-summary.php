<?php
/**
 * Single Product Pre-Summary
 *
 * A template part to be shown on a WooCommerce product after the title and before the product video.
 * Custom content can be added in the 'edit product' page.
 *
 * @package WooCommerce\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

do_action( 'vfront_product_pre_summary_start' );

$custom_content = get_post_meta( $product->get_id(), 'product_pre_summary_content', true );

if ( $custom_content ) {
	?>
	<div class="product-pre-summary">
	<?php
	echo wp_kses_post( $custom_content );
	?>
	</div>
	<?php
}

do_action( 'vfront_product_pre_summary_end' );
?>
