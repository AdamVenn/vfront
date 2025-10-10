<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://woocommerce.com/document/template-structure/
 * @package    WooCommerce\Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$show_title = true;
global $post;
if ( $post ) {
	$hide_product_title = get_post_meta( $post->ID, 'vfront_hide_product_title_option', true );
	if ( isset( $hide_product_title ) && 'yes' === $hide_product_title ) {
		$show_title = false;
	}
}
if ( $show_title ) {
	the_title( '<h1 class="product_title entry-title">', '</h1>' );
}

