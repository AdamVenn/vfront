<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.7.0
 */

use Automattic\WooCommerce\Enums\ProductType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( get_theme_mod( 'vfront_show_meta', true ) ) { ?>
		<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( ProductType::VARIABLE ) ) ) : ?>
			<?php // phpcs:disable Squiz.PHP.DisallowMultipleAssignments ?>
			<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'storefront' ); ?> <span class="sku"><?php echo $sku = $product->get_sku() ? esc_html( $sku ) : esc_html__( 'N/A', 'storefront' ); ?></span></span>

		<?php endif; ?>

		<?php echo esc_html( wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'storefront' ) . ' ', '</span>' ) ); ?>

		<?php echo esc_html( wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'storefront' ) . ' ', '</span>' ) ); ?>

	<?php } ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
