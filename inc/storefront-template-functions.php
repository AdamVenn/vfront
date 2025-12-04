<?php
/**
 * Storefront template functions.
 *
 * @package storefront
 */

if ( ! function_exists( 'storefront_display_comments' ) ) {
	/**
	 * Storefront display comments
	 *
	 * @since  1.0.0
	 */
	function storefront_display_comments() {
		// If comment support is on and comments are open or we have at least one comment, load up the comment template.
		if ( post_type_supports( 'post', 'comments' ) && ( comments_open() || 0 !== intval( get_comments_number() ) ) ) :
			comments_template();
		endif;
	}
}

if ( ! function_exists( 'storefront_comment' ) ) {
	/**
	 * Storefront comment template
	 *
	 * @param array $comment the comment array.
	 * @param array $args the comment args.
	 * @param int   $depth the comment depth.
	 * @since 1.0.0
	 */
	function storefront_comment( $comment, $args, $depth ) {
		if ( 'div' === $args['style'] ) {
			$tag       = 'div';
			$add_below = 'comment';
		} else {
			$tag       = 'li';
			$add_below = 'div-comment';
		}
		?>
		<<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>">
		<div class="comment-body">
		<div class="comment-meta commentmetadata">
			<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 128 ); ?>
			<?php printf( wp_kses_post( '<cite class="fn">%s</cite>', 'storefront' ), get_comment_author_link() ); ?>
			</div>
			<?php if ( '0' === $comment->comment_approved ) : ?>
				<em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'storefront' ); ?></em>
				<br />
			<?php endif; ?>

			<a href="<?php echo esc_url( htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ); ?>" class="comment-date">
				<?php echo '<time datetime="' . esc_attr( get_comment_date( 'c' ) ) . '">' . esc_html( get_comment_date() ) . '</time>'; ?>
			</a>
		</div>
		<?php if ( 'div' !== $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-content">
		<?php endif; ?>
		<div class="comment-text">
		<?php comment_text(); ?>
		</div>
		<div class="reply">
		<?php
		comment_reply_link(
			array_merge(
				$args,
				array(
					'add_below' => $add_below,
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
				)
			)
		);
		?>
		<?php edit_comment_link( __( 'Edit', 'storefront' ), '  ', '' ); ?>
		</div>
		</div>
		<?php if ( 'div' !== $args['style'] ) : ?>
		</div>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'storefront_footer_widgets' ) ) {
	/**
	 * Display the footer widget regions.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_footer_widgets() {
		$rows    = intval( apply_filters( 'storefront_footer_widget_rows', 1 ) );
		$regions = intval( apply_filters( 'storefront_footer_widget_columns', 4 ) );

		for ( $row = 1; $row <= $rows; $row++ ) :

			// Defines the number of active columns in this footer row.
			for ( $region = $regions; 0 < $region; $region-- ) {
				if ( is_active_sidebar( 'footer-' . esc_attr( $region + $regions * ( $row - 1 ) ) ) ) {
					$columns = $region;
					break;
				}
			}

			if ( isset( $columns ) ) :
				?>
				<div class=<?php echo '"footer-widgets row-' . esc_attr( $row ) . ' col-' . esc_attr( $columns ) . ' fix"'; ?>>
				<?php
				for ( $column = 1; $column <= $columns; $column++ ) :
					$footer_n = $column + $regions * ( $row - 1 );

					if ( is_active_sidebar( 'footer-' . esc_attr( $footer_n ) ) ) :
						?>
					<div class="block footer-widget-<?php echo esc_attr( $column ); ?>">
						<?php dynamic_sidebar( 'footer-' . esc_attr( $footer_n ) ); ?>
					</div>
						<?php
					endif;
				endfor;
				?>
			</div><!-- .footer-widgets.row-<?php echo esc_attr( $row ); ?> -->
				<?php
				unset( $columns );
			endif;
		endfor;
	}
}

if ( ! function_exists( 'storefront_header_widget_region' ) ) {
	/**
	 * Display header widget region
	 *
	 * @since  1.0.0
	 */
	function storefront_header_widget_region() {
		if ( is_active_sidebar( 'header-1' ) ) {
			?>
		<div class="header-widget-region" role="complementary">
			<div class="col-full">
				<?php dynamic_sidebar( 'header-1' ); ?>
			</div>
		</div>
			<?php
		}
	}
}

if ( ! function_exists( 'storefront_site_branding' ) ) {
	/**
	 * Site branding wrapper and display
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_site_branding() {
		?>
		<div class="site-branding">
			<?php storefront_site_title_or_logo(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'storefront_site_title_or_logo' ) ) {
	/**
	 * Display the site title or logo
	 *
	 * @since 2.1.0
	 * @param bool $echo Echo the string or return it.
	 * @return string
	 */
	function storefront_site_title_or_logo( $echo = true ) {
		$html = '';
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$html .= '<div class="logo">' . get_custom_logo() . '</div>';
		}

		$html .= '<div class="site-title-and-description">';
		$html .= '<div class="beta site-title">';

		if ( is_front_page() || is_home() ) {
			// Don't add link to home page if already on home page.
			$html .= '<p>' . esc_html( get_bloginfo( 'name' ) ) . '</p>';
		} else {
			$html .= '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
		}

		$html .= '</div>'; // site-title.

		if ( '' !== get_bloginfo( 'description' ) ) {
			$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
		}
		$html .= '</div>'; // site-title-and-description.

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'storefront_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_primary_navigation() {
		?>
		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'storefront' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'container_class' => 'primary-navigation',
				)
			);
			?>
		</nav><!-- #site-navigation -->
		<?php
	}
}

if ( ! function_exists( 'storefront_secondary_navigation' ) ) {
	/**
	 * Display Secondary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_secondary_navigation() {
		if ( has_nav_menu( 'secondary' ) ) {
			?>
			<nav class="secondary-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Secondary Navigation', 'storefront' ); ?>">
				<?php
					wp_nav_menu(
						array(
							'theme_location' => 'secondary',
							'fallback_cb'    => '',
						)
					);
				?>
			</nav><!-- #site-navigation -->
			<?php
		}
	}
}

if ( ! function_exists( 'vfront_myaccount_to_login' ) ) {
	/**
	 * Replaces 'my account' with 'log in' if the user is not logged in.
	 *
	 * @see wp_nav_menu_objects filter
	 * @param array    $sorted_menu_items Array of WP_Post objects to be displayed in the header.
	 * @param stdClass $args An object containing wp_nav_menu() arguments.
	 */
	function vfront_myaccount_to_login( $sorted_menu_items, $args ) {
		if ( is_user_logged_in() ) {
			return $sorted_menu_items;
		}

		$my_account_string = _x( 'My account', 'used for comparison only', 'storefront' );
		$login_string      = __( 'Log In', 'storefront' );

		foreach ( $sorted_menu_items as $menu_item ) {
			if ( $menu_item->title && $menu_item->title === $my_account_string ) {
				$menu_item->title = $login_string;
			}
		}

		return $sorted_menu_items;
	}
}

if ( ! function_exists( 'storefront_skip_links' ) ) {
	/**
	 * Skip links
	 *
	 * @since  1.4.1
	 * @return void
	 */
	function storefront_skip_links() {
		?>
		<a class="skip-link screen-reader-text" href="#site-navigation"><?php esc_html_e( 'Skip to navigation', 'storefront' ); ?></a>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'storefront' ); ?></a>
		<?php
	}
}

if ( ! function_exists( 'storefront_homepage_header' ) ) {
	/**
	 * Display the page header without the featured image
	 *
	 * @since 1.0.0
	 */
	function storefront_homepage_header() {
		edit_post_link( __( 'Edit this section', 'storefront' ), '', '', '', 'button storefront-hero__button-edit' );
		?>
		<header class="entry-header">
			<?php
			the_title( '<h1 class="entry-title">', '</h1>' );
			?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'storefront_page_header' ) ) {
	/**
	 * Display the page header
	 *
	 * @since 1.0.0
	 */
	function storefront_page_header() {
		if ( is_front_page() ) {
			return;
		}
		?>
		<header class="entry-header">
			<?php
			storefront_post_thumbnail( 'full' );
			the_title( '<h1 class="entry-title">', '</h1>' );
			?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'storefront_page_content' ) ) {
	/**
	 * Display the post content
	 *
	 * @since 1.0.0
	 */
	function storefront_page_content() {
		?>
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . __( 'Pages:', 'storefront' ),
						'after'  => '</div>',
					)
				);
			?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'storefront_post_header' ) ) {
	/**
	 * Display the post header with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function storefront_post_header() {
		?>
		<header class="entry-header">
		<?php

		/**
		 * Functions hooked in to storefront_post_header_before action.
		 *
		 * @hooked storefront_post_meta - 10
		 */
		do_action( 'storefront_post_header_before' );

		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		}

		do_action( 'storefront_post_header_after' );
		?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'storefront_post_content' ) ) {
	/**
	 * Display the post content with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function storefront_post_content() {
		?>
		<div class="entry-content">
		<?php

		/**
		 * Functions hooked in to storefront_post_content_before action.
		 *
		 * @hooked storefront_post_thumbnail - 10
		 */
		do_action( 'storefront_post_content_before' );

		the_content(
			sprintf(
				/* translators: %s: post title */
				__( 'Continue reading %s', 'storefront' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
			)
		);

		do_action( 'storefront_post_content_after' );

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'storefront' ),
				'after'  => '</div>',
			)
		);
		?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'storefront_post_meta' ) ) {
	/**
	 * Display the post meta
	 *
	 * @since 1.0.0
	 */
	function storefront_post_meta() {
		if ( 'post' !== get_post_type() ) {
			return;
		}

		// Posted on.
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$output_time_string = sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>', esc_url( get_permalink() ), $time_string );

		$posted_on = '
			<span class="posted-on">' .
			/* translators: %s: post date */
			sprintf( __( 'Posted on %s', 'storefront' ), $output_time_string ) .
			'</span>';

		// Author.
		if ( post_type_supports( 'post', 'author' ) ) {
			$author = sprintf(
				'<span class="post-author">%1$s <a href="%2$s" class="url fn" rel="author">%3$s</a></span>',
				__( 'by', 'storefront' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_html( get_the_author() )
			);
		} else {
			$author = '';
		}

		// Comments.
		$comments = '';
		if ( post_type_supports( 'post', 'comments' ) ) {
			if ( ! post_password_required() && ( comments_open() || 0 !== intval( get_comments_number() ) ) ) {
				$comments_number = get_comments_number_text( __( 'Leave a comment', 'storefront' ), __( '1 Comment', 'storefront' ), __( '% Comments', 'storefront' ) );

				$comments = sprintf(
					'<span class="post-comments">&mdash; <a href="%1$s">%2$s</a></span>',
					esc_url( get_comments_link() ),
					$comments_number
				);
			}
		}

		echo wp_kses(
			sprintf( '%1$s %2$s %3$s', $posted_on, $author, $comments ),
			array(
				'span' => array(
					'class' => array(),
				),
				'a'    => array(
					'href'  => array(),
					'title' => array(),
					'rel'   => array(),
				),
				'time' => array(
					'datetime' => array(),
					'class'    => array(),
				),
			)
		);
	}
}

if ( ! function_exists( 'storefront_edit_post_link' ) ) {
	/**
	 * Display the edit link
	 *
	 * @since 2.5.0
	 */
	function storefront_edit_post_link() {
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'storefront' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<div class="edit-link">',
			'</div>'
		);
	}
}

if ( ! function_exists( 'storefront_post_taxonomy' ) ) {
	/**
	 * Display the post taxonomies
	 *
	 * @since 2.4.0
	 */
	function storefront_post_taxonomy() {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'storefront' ) );

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'storefront' ) );
		?>

		<aside class="entry-taxonomy">
			<?php if ( $categories_list ) : ?>
			<div class="cat-links">
				<?php echo esc_html( _n( 'Category:', 'Categories:', count( get_the_category() ), 'storefront' ) ); ?> <?php echo wp_kses_post( $categories_list ); ?>
			</div>
			<?php endif; ?>

			<?php if ( $tags_list ) : ?>
			<div class="tags-links">
				<?php echo esc_html( _n( 'Tag:', 'Tags:', count( get_the_tags() ), 'storefront' ) ); ?> <?php echo wp_kses_post( $tags_list ); ?>
			</div>
			<?php endif; ?>
		</aside>

		<?php
	}
}

if ( ! function_exists( 'storefront_paging_nav' ) ) {
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 */
	function storefront_paging_nav() {
		global $wp_query;

		$args = array(
			'type'      => 'list',
			'next_text' => _x( 'Next', 'Next post', 'storefront' ),
			'prev_text' => _x( 'Previous', 'Previous post', 'storefront' ),
		);

		the_posts_pagination( $args );
	}
}

if ( ! function_exists( 'storefront_post_nav' ) ) {
	/**
	 * Display navigation to next/previous post when applicable.
	 */
	function storefront_post_nav() {
		$args = array(
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'storefront' ) . ' </span>%title',
			'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'storefront' ) . ' </span>%title',
		);
		the_post_navigation( $args );
	}
}

if ( ! function_exists( 'storefront_homepage_content' ) ) {
	/**
	 * Display homepage content
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @return  void
	 */
	function storefront_homepage_content() {
		while ( have_posts() ) {
			the_post();

			get_template_part( 'content', 'homepage' );

		} // end of the loop.
	}
}

if ( ! function_exists( 'storefront_get_sidebar' ) ) {
	/**
	 * Display storefront sidebar
	 *
	 * @uses get_sidebar()
	 * @since 1.0.0
	 */
	function storefront_get_sidebar() {
		get_sidebar();
	}
}

if ( ! function_exists( 'storefront_post_thumbnail' ) ) {
	/**
	 * Display post thumbnail
	 *
	 * @var $size thumbnail size. thumbnail|medium|large|full|$custom
	 * @uses has_post_thumbnail()
	 * @uses the_post_thumbnail
	 * @param string $size the post thumbnail size.
	 * @since 1.5.0
	 */
	function storefront_post_thumbnail( $size = 'full' ) {
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( $size );
		}
	}
}

if ( ! function_exists( 'storefront_primary_navigation_wrapper' ) ) {
	/**
	 * The primary navigation wrapper
	 */
	function storefront_primary_navigation_wrapper() {
		echo '<div class="storefront-primary-navigation"><div class="col-full">';
	}
}

if ( ! function_exists( 'storefront_primary_navigation_wrapper_close' ) ) {
	/**
	 * The primary navigation wrapper close
	 */
	function storefront_primary_navigation_wrapper_close() {
		echo '</div><!-- col-full --></div><!-- storefront-primary-navigation -->';
	}
}

if ( ! function_exists( 'storefront_primary_navigation_wc_wrapper' ) ) {
	/**
	 * The primary navigation wrapper for WooCommerce widgets
	 */
	function storefront_primary_navigation_wc_wrapper() {
		echo '<div class="storefront-wc-header-widgets">';
	}
}

if ( ! function_exists( 'storefront_primary_navigation_wc_wrapper_close' ) ) {
	/**
	 * The primary navigation wrapper for WooCommerce widgets close
	 */
	function storefront_primary_navigation_wc_wrapper_close() {
		echo '</div><!-- storefront-wc-header-widgets -->';
	}
}

if ( ! function_exists( 'storefront_header_container' ) ) {
	/**
	 * The header container
	 */
	function storefront_header_container() {
		echo '<div class="col-full">';
	}
}

if ( ! function_exists( 'storefront_header_container_close' ) ) {
	/**
	 * The header container close
	 */
	function storefront_header_container_close() {
		echo '</div>';
	}
}

if ( get_theme_mod( 'v_links_nav_to_content', false ) ) {
	// Jump straight to main to save the user scrolling past the header.
	add_filter(
		'wp_nav_menu_objects',
		function ( $items ) {
			foreach ( $items as &$item ) {
				if ( ! strstr( $item->url, '#' ) && ! empty( $item->url ) ) {
					$item->url = $item->url . '#content';
				}
			}
			return $items;
		}
	);
}

add_filter(
	'pre_set_site_transient_update_themes',
	function ( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$theme      = wp_get_theme();
		$theme_slug = $theme->get_stylesheet();

		$url_zip  = 'https://github.com/AdamVenn/vfront/releases/download/latest/vfront.zip';
		$url_info = 'https://github.com/AdamVenn/vfront/tree/vfront-release';

		$transient->response[ $theme_slug ] = array(
			'theme'       => $theme_slug,
			'new_version' => '999.0.0', // Always higher than current version.
			'url'         => $url_info, // Optional theme details URL.
			'package'     => $url_zip,
		);

		return $transient;
	}
);

if ( ! function_exists( 'vfront_restrict_admin_login_endpoint' ) ) {
	/**
	 * Only allow admin login from main WordPress login page,
	 * i.e. not from WooCommerce my-account.
	 *
	 * @see authenticate filter
	 *
	 * @param WP_User $user The user object for the user logging in.
	 * @param string  $username User name.
	 * @param string  $password Password.
	 */
	function vfront_restrict_admin_login_endpoint( $user, $username, $password ) {
		if ( ! $user instanceof WP_User ) {
			return $user;
		}

		if ( ! array_intersect( (array) $user->roles, array( 'administrator' ) ) ) {
			return $user;
		}

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return $user;
		}

		$endpoint_in_use  = basename( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		$allowed_endpoint = basename( wp_login_url() );

		if ( $endpoint_in_use === $allowed_endpoint ) {
			return $user;
		}

		return new WP_Error( 'admin-error', 'Admins cannot login from here.' );

	}
}

