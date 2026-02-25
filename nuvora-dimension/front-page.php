<?php
/**
 * Front Page Template — Nuvora Dimension
 * @package NuvoraDimension
 */

$options   = nuvora_get_options();
$logo_icon = esc_attr( $options['logo_icon'] );

// Gather all pages linked in the Dimension nav menu
function nuvora_get_nav_pages() {
	$pages = array();

	$blog_page_id   = (int) get_option( 'page_for_posts' );
	$home_url       = trailingslashit( home_url() );

	if ( has_nav_menu( 'nuvora-primary' ) ) {
		$locations = get_nav_menu_locations();
		$menu_id   = $locations['nuvora-primary'] ?? 0;
		if ( $menu_id ) {
			$items = wp_get_nav_menu_items( $menu_id );
			if ( $items ) {
				foreach ( $items as $item ) {

					if ( $item->object === 'page' ) {
						$page = get_post( $item->object_id );
						if ( ! $page || $page->post_status !== 'publish' ) continue;

						$is_blog = ( $blog_page_id && (int) $page->ID === $blog_page_id );

						if ( ! $is_blog ) {
							$slug  = strtolower( $page->post_name );
							$title = strtolower( $item->title ?: $page->post_title );
							if ( in_array( $slug,  array( 'blog', 'news', 'articles', 'posts' ), true ) ||
							     in_array( $title, array( 'blog', 'news', 'articles', 'posts' ), true ) ) {
								$is_blog = true;
							}
						}

						$pages[] = array(
							'slug'    => $page->post_name,
							'title'   => $item->title ?: $page->post_title,
							'content' => $is_blog ? '' : apply_filters( 'the_content', $page->post_content ),
							'thumb'   => get_the_post_thumbnail_url( $page->ID, 'large' ),
							'is_blog' => $is_blog,
							'page_id' => $page->ID,
						);

					} elseif ( $item->object === 'custom' ) {
						$url   = trailingslashit( $item->url );
						$title = strtolower( $item->title );
						$slug  = sanitize_title( $item->title );

						if ( strpos( $item->url, '#' ) !== false ) continue;

						$is_blog = false;
						if ( in_array( $title, array( 'blog', 'news', 'articles', 'posts' ), true ) ) {
							$is_blog = true;
						}
						$blog_slugs = array( 'blog', 'news', 'articles', 'posts' );
						foreach ( $blog_slugs as $bs ) {
							if ( $url === $home_url . $bs . '/' || $url === $home_url . $bs ) {
								$is_blog = true;
								break;
							}
						}

						$pages[] = array(
							'slug'    => $slug,
							'title'   => $item->title,
							'content' => '',
							'thumb'   => '',
							'is_blog' => $is_blog,
							'page_id' => 0,
						);

					} elseif ( in_array( $item->object, array( 'category', 'post_type_archive' ), true ) ) {
						continue;
					}
				}
			}
		}
	}

	return $pages;
}

// Render blog post cards for the blog panel (front page)
function nuvora_render_blog_cards() {
	$per_page = 5;
	$paged    = max( 1, (int) ( $_GET['blog_paged'] ?? 1 ) );

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => $per_page,
		'post_status'         => 'publish',
		'paged'               => $paged,
		'ignore_sticky_posts' => true,
	);

	// On page 1, manually fetch sticky posts and prepend them
	$sticky_ids = get_option( 'sticky_posts', array() );
	if ( $paged === 1 && ! empty( $sticky_ids ) ) {
		$args['post__not_in'] = $sticky_ids; // exclude from main query to avoid dupes
	}

	$query = new WP_Query( $args );

	// Prepend sticky posts at top of page 1
	$sticky_query = null;
	if ( $paged === 1 && ! empty( $sticky_ids ) ) {
		$sticky_query = new WP_Query( array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'post__in'            => $sticky_ids,
			'posts_per_page'      => count( $sticky_ids ),
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		) );
	}
	$total      = (int) $query->found_posts;
	$max_pages  = (int) $query->max_num_pages;

	if ( ! $query->have_posts() ) {
		echo '<p>' . esc_html__( 'No posts found.', 'nuvora-dimension' ) . '</p>';
		return;
	}

	// Progress indicator
	$shown_to  = min( $paged * $per_page, $total );
	$shown_from = ( ( $paged - 1 ) * $per_page ) + 1;
	$pct        = $total > 0 ? round( ( $shown_to / $total ) * 100 ) : 100;
	?>
	<div class="nd-progress-bar" aria-hidden="true">
		<div class="nd-progress-bar__fill" id="nd-progress-fill" style="width: <?php echo esc_attr( $pct ); ?>%"></div>
	</div>

	<div class="nuvora-blog-list" id="nuvora-blog-list">
	<?php
	// Render sticky posts first (page 1 only)
	if ( $sticky_query && $sticky_query->have_posts() ) {
		while ( $sticky_query->have_posts() ) {
			$sticky_query->the_post();
			$thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
			$no_img_class = $thumb ? '' : ' nuvora-blog-item--no-image';
			?>
			<div class="nuvora-blog-item nuvora-blog-item--sticky<?php echo esc_attr( $no_img_class ); ?>">
				<div class="nuvora-sticky-badge">
					<span class="fas fa-thumbtack"></span> <?php esc_html_e( 'Pinned', 'nuvora-dimension' ); ?>
				</div>
				<?php if ( $thumb ) : ?>
					<a href="<?php the_permalink(); ?>" class="nuvora-blog-item__thumb" aria-hidden="true" tabindex="-1">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
					</a>
				<?php endif; ?>
				<div class="nuvora-blog-item__body">
					<div class="nuvora-blog-item__meta">
						<time class="nuvora-blog-item__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></time>
						<?php if ( get_the_category_list() ) : ?>
							<span class="nuvora-blog-item__sep">/</span>
							<?php foreach ( get_the_category() as $cat ) : ?>
								<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="nuvora-blog-item__cat-badge"><?php echo esc_html( $cat->name ); ?></a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<h3 class="nuvora-blog-item__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<p class="nuvora-blog-item__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 22 ); ?></p>
					<a href="<?php the_permalink(); ?>" class="button small nuvora-blog-item__btn">Read Article &rarr;</a>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
	}

	// Render regular posts
	while ( $query->have_posts() ) {
		$query->the_post();
		$thumb        = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
		$no_img_class = $thumb ? '' : ' nuvora-blog-item--no-image';
		?>
		<div class="nuvora-blog-item<?php echo esc_attr( $no_img_class ); ?>">
			<?php if ( $thumb ) : ?>
				<a href="<?php the_permalink(); ?>" class="nuvora-blog-item__thumb" aria-hidden="true" tabindex="-1">
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
				</a>
			<?php endif; ?>
			<div class="nuvora-blog-item__body">
				<div class="nuvora-blog-item__meta">
					<time class="nuvora-blog-item__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></time>
					<?php if ( get_the_category_list() ) : ?>
						<span class="nuvora-blog-item__sep">/</span>
						<?php foreach ( get_the_category() as $cat ) : ?>
							<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="nuvora-blog-item__cat-badge"><?php echo esc_html( $cat->name ); ?></a>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<h3 class="nuvora-blog-item__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h3>
				<p class="nuvora-blog-item__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 22 ); ?></p>
				<a href="<?php the_permalink(); ?>" class="button small nuvora-blog-item__btn">Read Article &rarr;</a>
			</div>
		</div>
		<?php
	}
	wp_reset_postdata();
	?>
	</div>

	<?php if ( $max_pages > 1 ) : ?>
	<div class="nd-pagination-wrap"
		data-total="<?php echo esc_attr( $total ); ?>"
		data-per-page="<?php echo esc_attr( $per_page ); ?>"
		data-current="<?php echo esc_attr( $paged ); ?>"
		data-max="<?php echo esc_attr( $max_pages ); ?>">
		<div class="nd-pagination-controls">
			<?php if ( $paged > 1 ) : ?>
				<button class="button nd-page-btn" data-page="<?php echo esc_attr( $paged - 1 ); ?>">
					&larr; <?php esc_html_e( 'Newer', 'nuvora-dimension' ); ?>
				</button>
			<?php else : ?>
				<span class="nd-page-btn nd-page-btn--disabled">&larr; <?php esc_html_e( 'Newer', 'nuvora-dimension' ); ?></span>
			<?php endif; ?>

			<span class="nd-pagination-info">
				<?php printf(
					esc_html__( '%1$d–%2$d of %3$d', 'nuvora-dimension' ),
					$shown_from,
					$shown_to,
					$total
				); ?>
			</span>

			<?php if ( $paged < $max_pages ) : ?>
				<button class="button nd-page-btn" data-page="<?php echo esc_attr( $paged + 1 ); ?>">
					<?php esc_html_e( 'Older', 'nuvora-dimension' ); ?> &rarr;
				</button>
			<?php else : ?>
				<span class="nd-page-btn nd-page-btn--disabled"><?php esc_html_e( 'Older', 'nuvora-dimension' ); ?> &rarr;</span>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php
}

$nav_pages = nuvora_get_nav_pages();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<?php wp_head(); ?>
	<noscript><link rel="stylesheet" href="<?php echo esc_url( NUVORA_URI . '/assets/css/noscript.css' ); ?>" /></noscript>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="wrapper">

	<header id="header">
		<div class="logo">
			<?php if ( has_custom_logo() ) :
				the_custom_logo();
			else : ?>
				<span class="fas <?php echo $logo_icon; ?> icon" style="font-size:2rem;line-height:5.5rem;" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<div class="content">
			<div class="inner">
				<h1><?php echo esc_html( $options['site_title'] ); ?></h1>
				<p><?php echo wp_kses_post( $options['site_subtitle'] ); ?></p>
			</div>
		</div>
		<nav>
			<ul>
				<?php nuvora_render_nav(); ?>
			</ul>
		</nav>
	</header>

	<div id="main">

		<?php
		// ── Render Gutenberg block panels from the front page post ──
		$front_page_id = (int) get_option( 'page_on_front' );
		if ( $front_page_id ) {
			$front_post = get_post( $front_page_id );
			if ( $front_post && $front_post->post_status === 'publish' ) {
				global $post;
				$post = $front_post;
				setup_postdata( $post );
				echo apply_filters( 'the_content', $front_post->post_content ); // phpcs:ignore
				wp_reset_postdata();
			}
		}

		// ── Render each nav menu Page as an <article> panel ──
		foreach ( $nav_pages as $page ) :
			$slug    = esc_attr( $page['slug'] );
			$title   = esc_html( $page['title'] );
			$content = $page['content'];
			$thumb   = esc_url( $page['thumb'] );
			$is_blog = ! empty( $page['is_blog'] );
		?>
		<article id="<?php echo $slug; ?>"<?php if ( $is_blog ) echo ' class="nuvora-blog-panel"'; ?>>

			<?php if ( $is_blog ) : ?>

				<!-- ─ Blog Panel Header ─ -->
				<div class="nd-panel-header">
					<div>
						<div class="nd-panel-header__eyebrow"><?php esc_html_e( 'Latest Writing', 'nuvora-dimension' ); ?></div>
						<div class="nd-panel-header__title"><?php echo $title; ?></div>
					</div>
					<div class="nd-panel-header__badge" aria-hidden="true">
						<i class="fas fa-pen-nib"></i>
					</div>
				</div>

				<?php nuvora_render_blog_cards(); ?>

			<?php else : ?>

				<!-- ─ Standard Panel Header ─ -->
				<div class="nd-panel-header">
					<div class="nd-panel-header__title"><?php echo $title; ?></div>
				</div>

				<?php if ( $thumb ) : ?>
					<span class="image main">
						<img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" loading="lazy" />
					</span>
					<p class="nd-image-caption"><?php echo $title; ?></p>
				<?php endif; ?>

				<div class="page-content">
					<?php echo $content; // phpcs:ignore ?>
				</div>

			<?php endif; ?>

		</article>
		<?php endforeach; ?>

		<?php if ( empty( $nav_pages ) && ! $front_page_id ) :
			get_template_part( 'template-parts/default-panels' );
		endif; ?>

	</div>

	<footer id="footer">
		<?php echo nuvora_social_icons(); // phpcs:ignore ?>
		<p class="copyright"><?php echo wp_kses_post( $options['footer_text'] ); ?></p>
	</footer>

</div>

<div id="bg"></div>

<?php wp_footer(); ?>
</body>
</html>
