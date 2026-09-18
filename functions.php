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

/**
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

/**
 * Register the Calymyk Tool content type.
 */
function calymyk_new_register_tools() {

	register_post_type(
		'tool',
		array(
			'labels' => array(
				'name'          => 'Narzędzia',
				'singular_name' => 'Narzędzie',
				'add_new_item'  => 'Dodaj narzędzie',
				'edit_item'     => 'Edytuj narzędzie',
				'new_item'      => 'Nowe narzędzie',
				'view_item'     => 'Zobacz narzędzie',
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-admin-tools',
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'narzedzia' ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'taxonomies'   => array( 'tool_category' ),
			'show_in_nav_menus' => true,
		)
	);

	register_taxonomy(
		'tool_category',
		array( 'tool' ),
		array(
			'labels' => array(
				'name'          => 'Kategorie narzędzi',
				'singular_name' => 'Kategoria narzędzia',
			),
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => 'kategoria-narzedzia' ),
		)
	);
}

add_action( 'init', 'calymyk_new_register_tools' );
/**
 * Refresh rewrite rules when the theme is activated so custom post type URLs work.
 */
function calymyk_new_refresh_rewrite_rules() {
	calymyk_new_register_tools();
	flush_rewrite_rules();
}

add_action( 'after_switch_theme', 'calymyk_new_refresh_rewrite_rules' );
