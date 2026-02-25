<?php
/**
 * Single Post Template
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
<body <?php body_class( 'is-preload is-article-visible dimension-standalone' ); ?>>
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
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="close" aria-label="<?php esc_attr_e( 'Back to home', 'nuvora-dimension' ); ?>">Close</a>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<h2 class="major"><?php the_title(); ?></h2>

				<?php if ( has_post_thumbnail() ) : ?>
					<span class="image main"><?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?></span>
				<?php endif; ?>

				<div class="nuvora-post-meta">
					<span class="nuvora-post-meta__date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
					<span class="nuvora-post-meta__sep">&mdash;</span>
					<span class="nuvora-post-meta__author"><?php the_author(); ?></span>
					<?php if ( get_the_category_list() ) : ?>
						<span class="nuvora-post-meta__sep">&mdash;</span>
						<span class="nuvora-post-meta__cats"><?php the_category( ', ' ); ?></span>
					<?php endif; ?>
					<?php the_tags( '<span class="nuvora-post-meta__sep">&mdash;</span><span class="nuvora-post-meta__tags">', ', ', '</span>' ); ?>
				</div>

				<div class="nuvora-post-content">
					<?php the_content(); ?>
				</div>

				<?php wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nuvora-dimension' ),
					'after'  => '</div>',
				) ); ?>

				<div class="nuvora-post-nav">
					<?php the_post_navigation( array(
						'prev_text' => ' %title',
						'next_text' => '%title ',
					) ); ?>
				</div>

				<?php if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif; ?>

				</div><!-- #post-<?php the_ID(); ?> -->

			<?php endwhile; endif; ?>

			<a href="<?php echo esc_url( home_url() ); ?>" class="button nuvora-back-btn"> <?php esc_html_e( 'Back Home', 'nuvora-dimension' ); ?></a>
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
