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

//**
 * Limit the Zyskomat recommendation Query Loop to posts
 * assigned to the standard "Polecamy" category.
 *
 * Query Loop passes its queryId to the post-template context,
 * so we scope this filter to the dedicated Zyskomat loop.
 */
function calymyk_new_recommendations_query( $query, $block, $page ) {

	if (
		! isset( $block->context['queryId'] )
		|| 20 !== (int) $block->context['queryId']
	) {
		return $query;
	}

	$query['category_name'] = 'polecamy';

	return $query;
}

add_filter( 'query_loop_block_query_vars', 'calymyk_new_recommendations_query', 10, 3 );
