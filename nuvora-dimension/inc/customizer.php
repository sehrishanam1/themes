<?php
/**
 * Nuvora Theme Customizer
 * Quick live-preview settings via the WordPress Customizer.
 *
 * @package Nuvora
 */

function nuvora_customizer( $wp_customize ) {

	// ── Section: Nuvora Theme ─────────────────────────────────────────
	$wp_customize->add_section( 'nuvora_theme', array(
		'title'       => __( 'Nuvora Theme', 'nuvora-dimension' ),
		'description' => __( 'Quick settings for the Dimension theme. Full options available under Appearance → Theme Options.', 'nuvora-dimension' ),
		'priority'    => 30,
	) );

	// Site Title
	$wp_customize->add_setting( 'nuvora_site_title', array(
		'default'           => get_bloginfo( 'name' ),
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'nuvora_site_title', array(
		'label'   => __( 'Hero Title', 'nuvora-dimension' ),
		'section' => 'nuvora_theme',
		'type'    => 'text',
	) );

	// Background Color
	$wp_customize->add_setting( 'nuvora_bg_color', array(
		'default'           => '#1b1f22',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nuvora_bg_color', array(
		'label'   => __( 'Background Color', 'nuvora-dimension' ),
		'section' => 'nuvora_theme',
	) ) );

	// Background Image
	$wp_customize->add_setting( 'nuvora_bg_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'nuvora_bg_image', array(
		'label'   => __( 'Background Image', 'nuvora-dimension' ),
		'section' => 'nuvora_theme',
	) ) );

	// Live preview JS
	$wp_customize->selective_refresh->add_partial( 'nuvora_site_title', array(
		'selector' => '#header .inner h1',
	) );
}
add_action( 'customize_register', 'nuvora_customizer' );

// Live preview JS
function nuvora_customizer_preview_js() {
	wp_enqueue_script(
		'nuvora-customizer-preview',
		NUVORA_URI . '/inc/customizer-preview.js',
		array( 'customize-preview', 'jquery' ),
		NUVORA_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'nuvora_customizer_preview_js' );
