<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

			<div class="error-404 not-found">

				<div class="page-content">

					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'storefront' ); ?></h1>
					</header><!-- .page-header -->

					<p><?php esc_html_e( 'Nothing was found at this location. Try searching, or check out the links below.', 'storefront' ); ?></p>

					<section aria-label="<?php echo esc_html__( 'Search', 'storefront' ); ?>">
						<?php get_search_form(); ?>
					</section>

					<?php if ( storefront_is_woocommerce_activated() ) { ?>
						<div class="storefront-fourohfour">
							
							<section class="col-1" aria-label="<?php echo esc_html__( 'Promoted Products', 'storefront' ); ?>">
								<?php storefront_promoted_products(); ?>
							</section>
							<?php add_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 ); ?>
							<?php if ( get_theme_mod( 'vfront_show_categories', true ) ) { ?>
							<nav class="col-2" aria-label="<?php echo esc_html__( 'Product Categories', 'storefront' ); ?>">
								<?php
								the_widget(
									'WC_Widget_Product_Categories',
									array(
										'count' => 1,
									)
								);
								?>
							</nav>
							<?php } ?>

						</div><!-- storefront-fourohfour -->

						<section aria-label="<?php echo esc_html__( 'Popular Products', 'storefront' ); ?>">

							<h2><?php echo esc_html__( 'Popular Products', 'storefront' ); ?></h2>
							<?php
							$shortcode_content = storefront_do_shortcode(
								'best_selling_products',
								array(
									'per_page' => 4,
									'columns'  => 4,
								)
							);

							echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>

						</section><!-- Popular Products -->
					<?php } //storefront_is_woocommerce_activated ?>

				</div><!-- .page-content -->
			</div><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
