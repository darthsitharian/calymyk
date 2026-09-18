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

/**
 * Tool metadata: external URL and related promotion posts.
 */
function calymyk_new_add_tool_meta_boxes() {
	add_meta_box(
		'calymyk_tool_details',
		'Szczegóły narzędzia',
		'calymyk_new_render_tool_details_meta_box',
		'tool',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_tool', 'calymyk_new_add_tool_meta_boxes' );

function calymyk_new_render_tool_details_meta_box( $post ) {
	wp_nonce_field( 'calymyk_tool_details', 'calymyk_tool_details_nonce' );
	$url = get_post_meta( $post->ID, '_calymyk_tool_url', true );
	$related = get_post_meta( $post->ID, '_calymyk_tool_promotions', true );
	$related = is_array( $related ) ? array_map( 'absint', $related ) : array();

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>
	<p>
		<label for="calymyk_tool_url"><strong>URL narzędzia</strong></label><br>
		<input type="url" id="calymyk_tool_url" name="calymyk_tool_url" value="<?php echo esc_attr( $url ); ?>" class="widefat" placeholder="https://example.com/">
	</p>
	<p><strong>Aktualne promocje / powiązane wpisy</strong></p>
	<?php if ( $posts ) : ?>
		<div style="max-height:220px;overflow:auto;border:1px solid #dcdcde;padding:8px;">
			<?php foreach ( $posts as $related_post ) : ?>
				<label style="display:block;margin:0 0 7px;">
					<input type="checkbox" name="calymyk_tool_promotions[]" value="<?php echo esc_attr( $related_post->ID ); ?>" <?php checked( in_array( $related_post->ID, $related, true ) ); ?>>
					<?php echo esc_html( get_the_title( $related_post ) ); ?>
				</label>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p>Brak opublikowanych wpisów do powiązania.</p>
	<?php endif; ?>
	<?php
}

function calymyk_new_save_tool_details( $post_id ) {
	if ( ! isset( $_POST['calymyk_tool_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['calymyk_tool_details_nonce'] ) ), 'calymyk_tool_details' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'tool' !== get_post_type( $post_id ) ) {
		return;
	}

	$url = isset( $_POST['calymyk_tool_url'] ) ? esc_url_raw( wp_unslash( $_POST['calymyk_tool_url'] ) ) : '';
	if ( $url ) {
		update_post_meta( $post_id, '_calymyk_tool_url', $url );
	} else {
		delete_post_meta( $post_id, '_calymyk_tool_url' );
	}

	$promotions = isset( $_POST['calymyk_tool_promotions'] ) && is_array( $_POST['calymyk_tool_promotions'] )
		? array_values( array_filter( array_map( 'absint', wp_unslash( $_POST['calymyk_tool_promotions'] ) ) ) )
		: array();
	$promotions = array_values(
		array_filter(
			$promotions,
			function ( $id ) {
				return 'post' === get_post_type( $id ) && 'publish' === get_post_status( $id );
			}
		)
	);
	update_post_meta( $post_id, '_calymyk_tool_promotions', $promotions );
}
add_action( 'save_post_tool', 'calymyk_new_save_tool_details' );

/**
 * Render the external tool CTA.
 */
function calymyk_new_tool_url_shortcode() {
	$url = get_post_meta( get_the_ID(), '_calymyk_tool_url', true );
	if ( ! $url ) {
		return '';
	}
	return '<p class="cm-tool-single__cta"><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">Odwiedź narzędzie →</a></p>';
}
add_shortcode( 'calymyk_tool_url', 'calymyk_new_tool_url_shortcode' );

function calymyk_new_tool_archive_cta_shortcode() {
	$url = get_post_meta( get_the_ID(), '_calymyk_tool_url', true );
	if ( ! $url ) {
		return '';
	}
	return '<p class="cm-tool-archive-card__cta"><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">Odwiedź narzędzie →</a></p>';
}
add_shortcode( 'calymyk_tool_archive_cta', 'calymyk_new_tool_archive_cta_shortcode' );
add_shortcode( 'calymyk_tool_url', 'calymyk_new_tool_url_shortcode' );

/**
 * Render related promotion posts.
 */
function calymyk_new_tool_promotions_shortcode() {
	$ids = get_post_meta( get_the_ID(), '_calymyk_tool_promotions', true );
	$ids = is_array( $ids ) ? array_values( array_filter( array_map( 'absint', $ids ) ) ) : array();
	if ( ! $ids ) {
		return '';
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'posts_per_page' => 6,
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	$output = '<section class="cm-tool-promotions"><p class="cm-eyebrow">AKTUALNE PROMOCJE</p><div class="cm-tool-promotions__grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		$output .= '<article class="cm-tool-promotion">';
		if ( has_post_thumbnail() ) {
			$output .= '<a class="cm-tool-promotion__image" href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'medium_large' ) . '</a>';
		}
		$output .= '<h3 class="cm-tool-promotion__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
		$output .= '<p class="cm-tool-promotion__date">' . esc_html( get_the_date() ) . '</p>';
		$output .= '</article>';
	}
	wp_reset_postdata();

	return $output . '</div></section>';
}
add_shortcode( 'calymyk_tool_promotions', 'calymyk_new_tool_promotions_shortcode' );
