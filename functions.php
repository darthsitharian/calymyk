<?php
/**
 * Calymyk New theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load theme assets.
 */
function calymyk_new_enqueue_assets() {

	wp_enqueue_style(
		'calymyk-new-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'calymyk-new-theme-toggle',
		get_template_directory_uri() . '/assets/js/theme-toggle.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}

add_action( 'wp_enqueue_scripts', 'calymyk_new_enqueue_assets' );
