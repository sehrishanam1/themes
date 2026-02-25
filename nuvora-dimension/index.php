<?php
/**
 * Index / Blog Archive Template
 * @package NuvoraDimension
 */

$options = nuvora_get_options();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<?php wp_head(); ?>
	<noscript><link rel="stylesheet" href="<?php echo esc_url( NUVORA_URI . '/assets/css/noscript.css' ); ?>" /></noscript>
</head>
<body <?php body_class( 'is-preload is-article-visible nuvora-standalone' ); ?>>
<?php wp_body_open(); ?>

<div id="wrapper">

	<header id="header" style="display:none;">
		<div class="logo">
			<?php if ( has_custom_logo() ) :
				the_custom_logo();
			else : ?>
				<span class="fas <?php echo esc_attr( $options['logo_icon'] ); ?> icon" style="font-size:2rem;line-height:5.5rem;"></span>
			<?php endif; ?>
		</div>
		<div class="content"><div class="inner">
			<h1><a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( $options['site_title'] ); ?></a></h1>
		</div></div>
	</header>

	<div id="main">
		<article>
			<h2 class="major"><?php the_archive_title(); ?></h2>

			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class( is_sticky() ? 'nuvora-post-card nuvora-post-sticky' : 'nuvora-post-card' ); ?>>
						<?php if ( is_sticky() ) : ?>
							<div class="nuvora-sticky-badge">
								<span class="fas fa-thumbtack"></span> <?php esc_html_e( 'Pinned', 'nuvora-dimension' ); ?>
							</div>
						<?php endif; ?>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="nuvora-post-card__image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="nuvora-post-card__body">
							<div class="nuvora-post-card__meta">
								<span class="nuvora-post-card__date"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></span>
								<?php if ( get_the_category_list() ) : ?>
									<span class="nuvora-post-card__sep">&bull;</span>
									<span class="nuvora-post-card__cats"><?php the_category( ', ' ); ?></span>
								<?php endif; ?>
								<span class="nuvora-post-card__sep">&bull;</span>
								<span class="nuvora-post-card__author"><?php the_author(); ?></span>
							</div>
							<?php the_tags( '<div class="nuvora-post-card__tags">', ', ', '</div>' ); ?>
							<h3 class="nuvora-post-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<p class="nuvora-post-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
							<a href="<?php the_permalink(); ?>" class="button small nuvora-post-card__btn">Read More</a>
						</div>
					</div>
				<?php endwhile; ?>
				<div class="nuvora-pagination"><?php the_posts_pagination(); ?></div>
			<?php else : ?>
				<p><?php esc_html_e( 'No posts found.', 'nuvora-dimension' ); ?></p>
			<?php endif; ?>

			<a href="<?php echo esc_url( home_url() ); ?>" class="button nuvora-back-btn">&#8592; <?php esc_html_e( 'Back Home', 'nuvora-dimension' ); ?></a>
		</article>
	</div>

	<footer id="footer" style="display:none;">
		<p class="copyright"><?php echo wp_kses_post( $options['footer_text'] ); ?></p>
	</footer>

</div>
<div id="bg"></div>

<?php wp_footer(); ?>
</body>
</html>
