<?php
/**
 * Nuvora Theme Functions
 * @package Nuvora
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NUVORA_VERSION', '2.0.0' );
define( 'NUVORA_DIR', get_template_directory() );
define( 'NUVORA_URI', get_template_directory_uri() );

// ── Theme Setup ────────────────────────────────────────────────────────────────
function nuvora_setup() {
	load_theme_textdomain( 'nuvora-dimension', NUVORA_DIR . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-background', array(
		'default-color' => '1b1f22',
	) );
	add_theme_support( 'custom-header', array(
		'default-text-color' => 'ffffff',
		'width'              => 1000,
		'height'             => 250,
		'flex-width'         => true,
		'flex-height'        => true,
	) );
	add_editor_style( 'assets/css/editor-style.css' );
	register_nav_menus( array(
		'nuvora-primary' => __( 'Nuvora Dimension Header Nav', 'nuvora-dimension' ),
	) );

	// Block styles and patterns
	register_block_style( 'core/paragraph', array(
		'name'  => 'nuvora-dimension-dimmed',
		'label' => __( 'Dimmed', 'nuvora-dimension' ),
	) );
	register_block_pattern( 'nuvora-dimension/intro-panel', array(
		'title'       => __( 'Nuvora Intro Panel', 'nuvora-dimension' ),
		'description' => __( 'A simple intro panel layout.', 'nuvora-dimension' ),
		'content'     => '<!-- wp:paragraph --><p>' . esc_html__( 'Welcome to Nuvora. Edit this panel to introduce yourself.', 'nuvora-dimension' ) . '</p><!-- /wp:paragraph -->',
	) );
}
add_action( 'after_setup_theme', 'nuvora_setup' );

// ── Performance: Disable WordPress emoji (saves ~50KB JS+CSS+DNS on every page) ─
remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script'    );
remove_action( 'wp_print_styles',     'print_emoji_styles'               );
remove_action( 'admin_print_styles',  'print_emoji_styles'               );
remove_filter( 'the_content_feed',    'wp_staticize_emoji'               );
remove_filter( 'comment_text_rss',    'wp_staticize_emoji'               );
remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email'     );
add_filter( 'tiny_mce_plugins', function( $plugins ) {
	return array_diff( $plugins, array( 'wpemoji' ) );
} );
add_filter( 'wp_resource_hints', function( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$urls = array_filter( $urls, function( $url ) {
			return ( false === strpos( $url, 'https://s.w.org' ) );
		} );
	}
	return $urls;
}, 10, 2 );

// ── Performance: Remove unused WP head bloat ────────────────────────────────────
remove_action( 'wp_head', 'wp_generator' ); // WordPress version (security)

// ── Scripts & Styles ───────────────────────────────────────────────────────────
function nuvora_scripts() {

	// ── Preload critical woff2 fonts so browser fetches them ASAP ──
	add_action( 'wp_head', function() {
		echo '<link rel="preload" href="' . esc_url( NUVORA_URI . '/assets/webfonts/fa-solid-900.woff2' ) . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
		echo '<link rel="preload" href="' . esc_url( NUVORA_URI . '/assets/webfonts/fa-brands-400.woff2' ) . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
	}, 1 );

	// FontAwesome — local, woff2 only (legacy formats removed)
	wp_enqueue_style( 'fontawesome', NUVORA_URI . '/assets/css/fontawesome-all.min.css', array(), '5.15.4' );
	wp_enqueue_style( 'nuvora-main', NUVORA_URI . '/assets/css/main.css', array( 'fontawesome' ), NUVORA_VERSION );
	wp_enqueue_style( 'nuvora-animations', NUVORA_URI . '/assets/css/nuvora-animations.css', array( 'nuvora-main' ), NUVORA_VERSION );
	wp_enqueue_style( 'nuvora-dimension', NUVORA_URI . '/assets/css/nuvora-dimension.css', array( 'nuvora-main', 'nuvora-animations' ), NUVORA_VERSION );

	// Scripts — all in footer (true), defer added via filter below
	wp_enqueue_script( 'nuvora-browser',     NUVORA_URI . '/assets/js/browser.min.js',     array(),           NUVORA_VERSION, true );
	wp_enqueue_script( 'nuvora-breakpoints', NUVORA_URI . '/assets/js/breakpoints.min.js', array(),           NUVORA_VERSION, true );
	wp_enqueue_script( 'nuvora-util',        NUVORA_URI . '/assets/js/util.js',            array( 'jquery' ), NUVORA_VERSION, true );
	wp_enqueue_script( 'nuvora-main',        NUVORA_URI . '/assets/js/main.js',            array( 'jquery', 'nuvora-util', 'nuvora-browser', 'nuvora-breakpoints' ), NUVORA_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'nuvora_scripts' );

// ── Add defer to non-critical theme scripts ────────────────────────────────────
// dimension-fix.js is NOT deferred — it must run immediately to remove is-preload
add_filter( 'script_loader_tag', function( $tag, $handle ) {
	static $defer = array( 'nuvora-browser', 'nuvora-breakpoints', 'nuvora-util', 'nuvora-main', 'nuvora-blog' );
	if ( in_array( $handle, $defer, true ) && strpos( $tag, ' defer' ) === false ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}, 10, 2 );

// ── Admin Scripts ──────────────────────────────────────────────────────────────
function nuvora_admin_scripts( $hook ) {
	if ( 'appearance_page_nuvora-options' !== $hook ) return;
	wp_enqueue_style( 'fontawesome', NUVORA_URI . '/assets/css/fontawesome-all.min.css', array(), '5.15.4' );
	wp_enqueue_style( 'nuvora-admin', NUVORA_URI . '/assets/css/nuvora-admin-options.css', array(), NUVORA_VERSION );
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'nuvora_admin_scripts' );

// ── Options helpers ────────────────────────────────────────────────────────────
function nuvora_get_options() {
	$defaults = array(
		'site_title'        => get_bloginfo( 'name' ),
		'site_subtitle'     => get_bloginfo( 'description' ),
		'logo_icon'         => 'fa-gem',
		'bg_color'          => '#1b1f22',
		'bg_image'          => '',
		'accent_color'      => '#ffffff',
		'footer_text'       => '&copy; ' . get_bloginfo( 'name' ) . '. Design: <a href="https://html5up.net" target="_blank">HTML5 UP</a>.',
		'twitter_url'       => '',
		'facebook_url'      => '',
		'instagram_url'     => '',
		'github_url'        => '',
		'linkedin_url'      => '',
	);
	return wp_parse_args( get_option( 'nuvora_options', array() ), $defaults );
}

function nuvora_kses_links() {
	return array(
		'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array(), 'title' => array() ),
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
	);
}

// ── Admin Menu ─────────────────────────────────────────────────────────────────
function nuvora_admin_menu() {
	add_theme_page( 'Nuvora Dimension Options', 'Dimension Options', 'manage_options', 'nuvora-options', 'nuvora_options_page' );
}
add_action( 'admin_menu', 'nuvora_admin_menu' );

// ── Options Page ───────────────────────────────────────────────────────────────
function nuvora_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$saved = false;
	if ( isset( $_POST['nuvora_save'] ) && check_admin_referer( 'nuvora_options_save' ) ) {
		// Load existing saved values first — only overwrite fields present in this tab's form
		$existing = nuvora_get_options();

		// Map of all possible fields with their sanitizers
		$all_fields = array(
			'site_title'        => fn($v) => sanitize_text_field( $v ),
			'site_subtitle'     => fn($v) => wp_kses( wp_unslash( $v ), nuvora_kses_links() ),
			'logo_icon'         => fn($v) => sanitize_text_field( $v ),
			'bg_color'          => fn($v) => sanitize_hex_color( $v ) ?: '#1b1f22',
			'bg_image'          => fn($v) => esc_url_raw( $v ),
			'accent_color'      => fn($v) => sanitize_hex_color( $v ) ?: '#ffffff',
			'footer_text'       => fn($v) => wp_kses( wp_unslash( $v ), nuvora_kses_links() ),
			'twitter_url'       => fn($v) => esc_url_raw( $v ),
			'facebook_url'      => fn($v) => esc_url_raw( $v ),
			'instagram_url'     => fn($v) => esc_url_raw( $v ),
			'github_url'        => fn($v) => esc_url_raw( $v ),
			'linkedin_url'      => fn($v) => esc_url_raw( $v ),
		);

		// Build merged options: keep existing value for any field NOT in this POST
		$new_options = $existing;
		foreach ( $all_fields as $key => $sanitize ) {
			if ( isset( $_POST[ $key ] ) ) {
				$new_options[ $key ] = $sanitize( $_POST[ $key ] );
			}
		}

		update_option( 'nuvora_options', $new_options );
		$saved = true;
	}

	$o = nuvora_get_options();
	$tab = sanitize_key( $_GET['tab'] ?? 'header' );
	$tabs = array(
		'header'  => array( 'icon' => 'fas fa-heading',    'label' => 'Header & Identity' ),
		'design'  => array( 'icon' => 'fas fa-palette',    'label' => 'Background & Colors' ),
		'social'  => array( 'icon' => 'fas fa-share-alt',  'label' => 'Social Links' ),
		'nav'     => array( 'icon' => 'fas fa-bars',       'label' => 'Navigation' ),
		'footer'  => array( 'icon' => 'fas fa-shoe-prints','label' => 'Footer' ),
	);
	?>
	<div id="dim-wrap">

		<!-- SIDEBAR -->
		<div id="dim-sidebar">
			<div class="dim-brand">
				◈ Nuvora Dimension
				<span>Theme Options</span>
			</div>
			<nav>
				<ul>
					<?php foreach ( $tabs as $key => $t ) : ?>
					<li class="<?php echo $tab === $key ? 'active' : ''; ?>">
						<a href="<?php echo esc_url( admin_url( 'themes.php?page=nuvora-options&tab=' . $key ) ); ?>">
							<span class="dim-nav-icon"><i class="<?php echo esc_attr( $t['icon'] ); ?>"></i></span>
							<?php echo esc_html( $t['label'] ); ?>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>

		<!-- CONTENT -->
		<div id="dim-content">
			<form method="post" action="<?php echo esc_url( admin_url( 'themes.php?page=nuvora-options&tab=' . $tab ) ); ?>">
				<?php wp_nonce_field( 'nuvora_options_save' ); ?>

				<!-- SAVE BAR -->
				<div id="dim-save-bar">
					<span class="dim-save-title"><?php echo esc_html( $tabs[ $tab ]['label'] ); ?></span>
					<input type="submit" name="nuvora_save" class="button button-primary" value="💾  Save Changes" />
				</div>

				<div id="dim-panels">

					<?php if ( $saved ) : ?>
						<div class="dim-notice">✅ Settings saved successfully.</div>
					<?php endif; ?>

					<!-- ── HEADER & IDENTITY ── -->
					<?php if ( $tab === 'header' ) : ?>
					<h2 class="dim-panel-title">Header &amp; Identity</h2>

					<div class="dim-card">
						<div class="dim-card-head"><i class="fas fa-heading"></i> Site Header</div>
						<div class="dim-card-body">

							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong>Site Title</strong>
									<small>Displayed as the main heading</small>
								</div>
								<div class="dim-setting-control">
									<input type="text" name="site_title" value="<?php echo esc_attr( $o['site_title'] ); ?>" />
								</div>
							</div>

							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong>Subtitle / Tagline</strong>
									<small>Supports links, &lt;em&gt;, &lt;strong&gt;</small>
								</div>
								<div class="dim-setting-control">
									<textarea name="site_subtitle" id="dim_subtitle" rows="3"><?php echo esc_html( $o['site_subtitle'] ); ?></textarea>
									<div class="dim-link-helper">
										<label><i class="fas fa-link"></i> Insert link:</label>
										<input type="text" class="dim-link-text" placeholder="Link text" />
										<input type="url"  class="dim-link-url"  placeholder="https://..." />
										<select class="dim-link-target">
											<option value="_blank">New tab</option>
											<option value="_self">Same tab</option>
										</select>
										<button type="button" class="button dim-insert-link" data-target="dim_subtitle">+ Insert</button>
									</div>
								</div>
							</div>

							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong>Logo Icon</strong>
									<small>FontAwesome 5 class name<br><a href="https://fontawesome.com/icons?d=gallery&s=solid&m=free" target="_blank">Browse free icons ↗</a></small>
								</div>
								<div class="dim-setting-control">
									<div class="dim-icon-row">
										<div class="dim-icon-preview-box">
											<i id="dim_icon_prev" class="fas <?php echo esc_attr( $o['logo_icon'] ); ?>"></i>
										</div>
										<div style="flex:1">
											<input type="text" name="logo_icon" id="dim_logo_icon" value="<?php echo esc_attr( $o['logo_icon'] ); ?>" placeholder="fa-gem" oninput="document.getElementById('dim_icon_prev').className='fas '+this.value" />
											<small style="color:#787c82;font-size:11px;display:block;margin-top:4px;">e.g. fa-gem, fa-rocket, fa-star, fa-diamond</small>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>

					<!-- ── DESIGN / BG ── -->
					<?php elseif ( $tab === 'design' ) : ?>
					<h2 class="dim-panel-title">Background &amp; Colors</h2>

					<div class="dim-card">
						<div class="dim-card-head"><i class="fas fa-fill-drip"></i> Background</div>
						<div class="dim-card-body">

							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong>Background Color</strong>
									<small>Base page color</small>
								</div>
								<div class="dim-setting-control">
									<input type="text" name="bg_color" value="<?php echo esc_attr( $o['bg_color'] ); ?>" class="dim-color-picker" />
								</div>
							</div>

							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong>Background Image</strong>
									<small>Replaces default photo</small>
								</div>
								<div class="dim-setting-control">
									<div style="display:flex;gap:8px;align-items:center">
										<input type="text" name="bg_image" id="bg_image" value="<?php echo esc_attr( $o['bg_image'] ); ?>" placeholder="https://..." style="flex:1" />
										<button type="button" class="button" id="bg_image_btn" style="white-space:nowrap">📁 Choose</button>
										<?php if ( $o['bg_image'] ) : ?>
											<button type="button" class="button" id="bg_image_clear">✕</button>
										<?php endif; ?>
									</div>
									<?php if ( $o['bg_image'] ) : ?>
										<div class="dim-bg-preview">
											<img src="<?php echo esc_url( $o['bg_image'] ); ?>" alt="Background preview" />
										</div>
									<?php endif; ?>
								</div>
							</div>

							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong>Accent Color</strong>
									<small>Button and link highlight color</small>
								</div>
								<div class="dim-setting-control">
									<input type="text" name="accent_color" value="<?php echo esc_attr( $o['accent_color'] ); ?>" class="dim-color-picker" />
								</div>
							</div>

						</div>
					</div>

					<!-- ── SOCIAL ── -->
					<?php elseif ( $tab === 'social' ) : ?>
					<h2 class="dim-panel-title">Social Links</h2>
					<p style="color:#555;margin-bottom:20px;font-size:13px">Add your profile URLs. Leave blank to hide that icon.</p>

					<div class="dim-card">
						<div class="dim-card-head"><i class="fas fa-share-alt"></i> Social Profiles</div>
						<div class="dim-card-body">
							<?php
							$socials = array(
								'twitter_url'   => array( 'Twitter / X',  'fab fa-twitter',     'https://twitter.com/yourhandle' ),
								'facebook_url'  => array( 'Facebook',     'fab fa-facebook-f',  'https://facebook.com/yourpage' ),
								'instagram_url' => array( 'Instagram',    'fab fa-instagram',   'https://instagram.com/yourhandle' ),
								'github_url'    => array( 'GitHub',       'fab fa-github',      'https://github.com/yourusername' ),
								'linkedin_url'  => array( 'LinkedIn',     'fab fa-linkedin-in', 'https://linkedin.com/in/yourname' ),
							);
							foreach ( $socials as $key => list( $label, $icon, $ph ) ) : ?>
							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong><i class="<?php echo esc_attr( $icon ); ?>" style="width:16px;color:#2271b1"></i> <?php echo esc_html( $label ); ?></strong>
								</div>
								<div class="dim-setting-control">
									<input type="url" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $o[ $key ] ); ?>" placeholder="<?php echo esc_attr( $ph ); ?>" />
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- ── NAV ── -->
					<?php elseif ( $tab === 'nav' ) : ?>
					<h2 class="dim-panel-title">Navigation</h2>

					<div class="dim-info-box">
						<strong>How it works:</strong> The Dimension nav links to panel anchors using page slugs. Each page slug must match the Panel ID set in the Gutenberg block.
						<ol>
							<li>Go to <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>"><strong>Pages → Add New</strong></a> and create pages like <em>Intro</em>, <em>Work</em>, <em>About</em>, <em>Contact</em></li>
							<li>Edit each page → add a <strong>Nuvora Panel</strong> block → set Panel ID to match the slug (e.g. <code>intro</code>)</li>
							<li>Go to <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><strong>Appearance → Menus</strong></a> → create menu → add those pages → set location to <strong>"Dimension Header Nav"</strong></li>
						</ol>
					</div>

					<div class="dim-card">
						<div class="dim-card-head"><i class="fas fa-bars"></i> Quick Links</div>
						<div class="dim-card-body" style="padding:16px 20px">
							<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>" class="button button-primary" style="margin-right:8px">+ New Page</a>
							<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" class="button">Manage Menus</a>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" class="button" style="margin-left:8px">All Pages</a>
						</div>
					</div>

					<!-- ── FOOTER ── -->
					<?php elseif ( $tab === 'footer' ) : ?>
					<h2 class="dim-panel-title">Footer</h2>

					<div class="dim-card">
						<div class="dim-card-head"><i class="fas fa-shoe-prints"></i> Footer Text</div>
						<div class="dim-card-body">
							<div class="dim-setting">
								<div class="dim-setting-label">
									<strong>Copyright Text</strong>
									<small>Shown below social icons.<br>Supports links. Use <code>&amp;copy;</code> for ©</small>
								</div>
								<div class="dim-setting-control">
									<textarea name="footer_text" id="dim_footer_text" rows="2"><?php echo esc_html( $o['footer_text'] ); ?></textarea>
									<div class="dim-link-helper">
										<label><i class="fas fa-link"></i> Insert link:</label>
										<input type="text" class="dim-link-text" placeholder="Link text" />
										<input type="url"  class="dim-link-url"  placeholder="https://..." />
										<select class="dim-link-target">
											<option value="_blank">New tab</option>
											<option value="_self">Same tab</option>
										</select>
										<button type="button" class="button dim-insert-link" data-target="dim_footer_text">+ Insert</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<?php endif; ?>

				</div><!-- /dim-panels -->
			</form>
		</div><!-- /dim-content -->
	</div><!-- /dim-wrap -->

	<script>
	jQuery(document).ready(function($) {

		// Color pickers
		$('.dim-color-picker').wpColorPicker();

		// Media uploader
		$('#bg_image_btn').on('click', function(e) {
			e.preventDefault();
			var frame = wp.media({ title: 'Select Background Image', multiple: false });
			frame.on('select', function() {
				var att = frame.state().get('selection').first().toJSON();
				$('#bg_image').val(att.url);
			});
			frame.open();
		});

		$('#bg_image_clear').on('click', function() {
			$('#bg_image').val('');
		});

		// Link helpers
		$(document).on('click', '.dim-insert-link', function() {
			var $wrap  = $(this).closest('.dim-link-helper');
			var text   = $wrap.find('.dim-link-text').val().trim();
			var url    = $wrap.find('.dim-link-url').val().trim();
			var target = $wrap.find('.dim-link-target').val();
			var $field = $('#' + $(this).data('target'));
			if ( ! text || ! url ) { alert('Please enter both link text and a URL.'); return; }
			$field.val( $field.val() + '<a href="' + url + '" target="' + target + '">' + text + '</a>' );
			$wrap.find('.dim-link-text, .dim-link-url').val('');
		});

	});
	</script>
	<?php
}

// ── Dynamic CSS ────────────────────────────────────────────────────────────────
function nuvora_dynamic_css() {
	$o   = nuvora_get_options();
	$css = '#bg { background-color: ' . esc_attr( $o['bg_color'] ) . '; }';
	if ( $o['bg_image'] ) {
		$url  = esc_url( $o['bg_image'] );
		$css .= "#bg:after { background-image: url('{$url}') !important; background-size: cover !important; background-position: center !important; }";
	}
	echo '<style id="nuvora-dynamic-css">' . $css . '</style>' . "\n"; // phpcs:ignore
}
add_action( 'wp_head', 'nuvora_dynamic_css' );

// Note: Contact form functionality has been removed as it is plugin territory.
// Please use a dedicated contact form plugin such as Contact Form 7.

// ── Nav rendering ──────────────────────────────────────────────────────────────
function nuvora_render_nav() {
	if ( has_nav_menu( 'nuvora-primary' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'nuvora-primary',
			'container'      => false,
			'items_wrap'     => '%3$s',
			'walker'         => new Nuvora_Nav_Walker(),
			'fallback_cb'    => 'nuvora_nav_fallback',
		) );
	} else {
		nuvora_nav_fallback();
	}
}

function nuvora_nav_fallback() {
	$pages = get_pages( array( 'sort_column' => 'menu_order', 'number' => 8 ) );
	foreach ( $pages as $page ) {
		echo '<li><a href="#' . esc_attr( $page->post_name ) . '">' . esc_html( $page->post_title ) . '</a></li>';
	}
}

class Nuvora_Nav_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( $item->object === 'page' ) {
			// Pages: always use #slug so main.js can find the <article id="slug">
			$page   = get_post( $item->object_id );
			$anchor = $page ? '#' . $page->post_name : '#';
		} elseif ( strpos( $item->url, '#' ) !== false ) {
			// Custom link already has an anchor (e.g. /#intro or #intro)
			$anchor = '#' . ltrim( substr( $item->url, strpos( $item->url, '#' ) + 1 ), '#' );
		} else {
			// External or home link — link normally (opens in same page)
			$output .= '<li><a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a></li>';
			return;
		}
		$output .= '<li><a href="' . esc_attr( $anchor ) . '">' . esc_html( $item->title ) . '</a></li>';
	}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}

// ── Social icons ───────────────────────────────────────────────────────────────
function nuvora_social_icons() {
	$o     = nuvora_get_options();
	// Use the original theme's class pattern: "icon brands fa-twitter"
	// main.css handles rendering via .icon.brands:before { font-family: 'Font Awesome 5 Brands' }
	$links = array(
		array( 'url' => $o['twitter_url'],   'icon' => 'fa-twitter',     'label' => 'Twitter' ),
		array( 'url' => $o['facebook_url'],  'icon' => 'fa-facebook-f',  'label' => 'Facebook' ),
		array( 'url' => $o['instagram_url'], 'icon' => 'fa-instagram',   'label' => 'Instagram' ),
		array( 'url' => $o['github_url'],    'icon' => 'fa-github',      'label' => 'GitHub' ),
		array( 'url' => $o['linkedin_url'],  'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn' ),
	);
	$has = array_filter( array_column( $links, 'url' ) );
	if ( ! $has ) return '';
	$html = '<ul class="icons">';
	foreach ( $links as $link ) {
		if ( empty( $link['url'] ) ) continue;
		$html .= sprintf(
			'<li><a href="%s" class="icon brands %s" target="_blank" rel="noopener noreferrer"><span class="label">%s</span></a></li>',
			esc_url( $link['url'] ),
			esc_attr( $link['icon'] ),
			esc_html( $link['label'] )
		);
	}
	return $html . '</ul>';
}

// ── Blocks ─────────────────────────────────────────────────────────────────────
require_once NUVORA_DIR . '/blocks/nuvora-panel.php';
require_once NUVORA_DIR . '/blocks/nuvora-hero.php';

// ── Sidebar widget area ────────────────────────────────────────────────────────
function nuvora_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Nuvora Dimension Panel Sidebar', 'nuvora-dimension' ),
		'id'            => 'nuvora-panel-sidebar',
		'description'   => __( 'Widgets displayed in the panel sidebar area.', 'nuvora-dimension' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'nuvora_widgets_init' );

/**
 * Render the panel sidebar if active.
 */
function nuvora_panel_sidebar() {
	if ( is_active_sidebar( 'nuvora-panel-sidebar' ) ) {
		dynamic_sidebar( 'nuvora-panel-sidebar' );
	}
}

require_once NUVORA_DIR . '/inc/customizer.php';

// ── Blog Panel AJAX Pagination ───────────────────────────────────────────────
function nuvora_ajax_load_page() {
	$page     = max( 1, (int) ( $_POST['page'] ?? 1 ) );
	$per_page = 5;

	$sticky_ids = get_option( 'sticky_posts', array() );

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => $per_page,
		'post_status'         => 'publish',
		'paged'               => $page,
		'ignore_sticky_posts' => true,
	);

	// On page 1 exclude stickies from main query (they'll be prepended separately)
	if ( $page === 1 && ! empty( $sticky_ids ) ) {
		$args['post__not_in'] = $sticky_ids;
	}

	$query = new WP_Query( $args );

	// Fetch stickies separately for page 1
	$sticky_query = null;
	if ( $page === 1 && ! empty( $sticky_ids ) ) {
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

	if ( ! $query->have_posts() ) {
		wp_send_json_success( array( 'html' => '', 'progress' => 100 ) );
		return;
	}

	$total     = (int) $query->found_posts;
	$max_pages = (int) $query->max_num_pages;
	$shown_to  = min( $page * $per_page, $total );
	$shown_from = ( ( $page - 1 ) * $per_page ) + 1;
	$pct        = $total > 0 ? round( ( $shown_to / $total ) * 100 ) : 100;

	ob_start();
	?>
	<div class="nuvora-blog-list" id="nuvora-blog-list">
	<?php
	// Render sticky posts first (page 1 only)
	if ( $sticky_query && $sticky_query->have_posts() ) {
		while ( $sticky_query->have_posts() ) {
			$sticky_query->the_post();
			$thumb        = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
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
		data-current="<?php echo esc_attr( $page ); ?>"
		data-max="<?php echo esc_attr( $max_pages ); ?>">
		<div class="nd-pagination-controls">
			<?php if ( $page > 1 ) : ?>
				<button class="button nd-page-btn" data-page="<?php echo esc_attr( $page - 1 ); ?>">
					&larr; <?php esc_html_e( 'Newer', 'nuvora-dimension' ); ?>
				</button>
			<?php else : ?>
				<span class="nd-page-btn nd-page-btn--disabled">&larr; <?php esc_html_e( 'Newer', 'nuvora-dimension' ); ?></span>
			<?php endif; ?>
			<span class="nd-pagination-info">
				<?php //printf( esc_html__( '%1$dâ%2$d of %3$d', 'nuvora-dimension' ), $shown_from, $shown_to, $total ); ?>
				<?php printf( esc_html__( '%1$d-%2$d of %3$d', 'nuvora-dimension' ), $shown_from, $shown_to, $total ); ?>
			</span>
			<?php if ( $page < $max_pages ) : ?>
				<button class="button nd-page-btn" data-page="<?php echo esc_attr( $page + 1 ); ?>">
					<?php esc_html_e( 'Older', 'nuvora-dimension' ); ?> &rarr;
				</button>
			<?php else : ?>
				<span class="nd-page-btn nd-page-btn--disabled"><?php esc_html_e( 'Older', 'nuvora-dimension' ); ?> &rarr;</span>
			<?php endif; ?>
		</div>
	</div>
	<?php endif;
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'     => $html,
		'progress' => $pct,
	) );
}
add_action( 'wp_ajax_nuvora_load_page',        'nuvora_ajax_load_page' );
add_action( 'wp_ajax_nopriv_nuvora_load_page', 'nuvora_ajax_load_page' );

// Enqueue blog panel JS + pass ajaxurl
function nuvora_blog_scripts() {
	wp_enqueue_script(
		'nuvora-blog',
		NUVORA_URI . '/assets/js/blog.js',
		array( 'jquery' ),
		NUVORA_VERSION,
		true
	);
	wp_localize_script( 'nuvora-blog', 'nuvoraBlog', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'nuvora_blog_scripts' );
