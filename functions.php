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
 * Tool metadata: external URL.
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
	?>
	<p>
		<label for="calymyk_tool_url"><strong>URL narzędzia</strong></label><br>
		<input type="url" id="calymyk_tool_url" name="calymyk_tool_url" value="<?php echo esc_attr( $url ); ?>" class="widefat" placeholder="https://example.com/">
	</p>
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
}
add_action( 'save_post_tool', 'calymyk_new_save_tool_details' );

/**
 * Promotion metadata: assigned tool and promotion duration.
 */
function calymyk_new_add_post_promotion_meta_box() {
	add_meta_box(
		'calymyk_post_promotion',
		'Szczegóły promocji',
		'calymyk_new_render_post_promotion_meta_box',
		'post',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes_post', 'calymyk_new_add_post_promotion_meta_box' );

function calymyk_new_render_post_promotion_meta_box( $post ) {
	wp_nonce_field( 'calymyk_post_promotion', 'calymyk_post_promotion_nonce' );

	$tool_id  = absint( get_post_meta( $post->ID, '_calymyk_post_tool', true ) );
	$start    = get_post_meta( $post->ID, '_calymyk_promotion_start', true );
	$end      = get_post_meta( $post->ID, '_calymyk_promotion_end', true );
	$tools    = get_posts(
		array(
			'post_type'      => 'tool',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	?>
	<p>
		<label for="calymyk_post_tool"><strong>Narzędzie</strong></label><br>
		<select id="calymyk_post_tool" name="calymyk_post_tool" class="widefat">
			<option value="0">— bez przypisania —</option>
			<?php foreach ( $tools as $tool ) : ?>
				<option value="<?php echo esc_attr( $tool->ID ); ?>" <?php selected( $tool_id, $tool->ID ); ?>>
					<?php echo esc_html( get_the_title( $tool ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="calymyk_promotion_start"><strong>Promocja od</strong></label><br>
		<input type="date" id="calymyk_promotion_start" name="calymyk_promotion_start" value="<?php echo esc_attr( $start ); ?>" class="widefat">
	</p>
	<p>
		<label for="calymyk_promotion_end"><strong>Promocja do</strong></label><br>
		<input type="date" id="calymyk_promotion_end" name="calymyk_promotion_end" value="<?php echo esc_attr( $end ); ?>" class="widefat">
	</p>
	<p class="description">Daty są opcjonalne. Uzupełnij je, jeśli promocja ma określony czas trwania.</p>
	<?php
}

function calymyk_new_save_post_promotion( $post_id ) {
	if ( ! isset( $_POST['calymyk_post_promotion_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['calymyk_post_promotion_nonce'] ) ), 'calymyk_post_promotion' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) || 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	$tool_id = isset( $_POST['calymyk_post_tool'] ) ? absint( $_POST['calymyk_post_tool'] ) : 0;
	if ( $tool_id && 'tool' === get_post_type( $tool_id ) ) {
		update_post_meta( $post_id, '_calymyk_post_tool', $tool_id );
	} else {
		delete_post_meta( $post_id, '_calymyk_post_tool' );
	}

	$start = isset( $_POST['calymyk_promotion_start'] ) ? sanitize_text_field( wp_unslash( $_POST['calymyk_promotion_start'] ) ) : '';
	$end   = isset( $_POST['calymyk_promotion_end'] ) ? sanitize_text_field( wp_unslash( $_POST['calymyk_promotion_end'] ) ) : '';

	if ( preg_match( '/^\\d{4}-\\d{2}-\\d{2}$/', $start ) ) {
		update_post_meta( $post_id, '_calymyk_promotion_start', $start );
	} else {
		delete_post_meta( $post_id, '_calymyk_promotion_start' );
	}

	if ( preg_match( '/^\\d{4}-\\d{2}-\\d{2}$/', $end ) ) {
		update_post_meta( $post_id, '_calymyk_promotion_end', $end );
	} else {
		delete_post_meta( $post_id, '_calymyk_promotion_end' );
	}
}
add_action( 'save_post_post', 'calymyk_new_save_post_promotion' );

/**
 * Render the tool assigned to the current promotion.
 */
function calymyk_new_post_tool_shortcode() {
	$tool_id = absint( get_post_meta( get_the_ID(), '_calymyk_post_tool', true ) );
	if ( ! $tool_id || 'tool' !== get_post_type( $tool_id ) ) {
		return '';
	}

	$url = get_post_meta( $tool_id, '_calymyk_tool_url', true );
	$output = '<section class="cm-post-tool"><p class="cm-eyebrow">NARZĘDZIE</p>';
	$output .= '<h2 class="cm-post-tool__title"><a href="' . esc_url( get_permalink( $tool_id ) ) . '">' . esc_html( get_the_title( $tool_id ) ) . '</a></h2>';

	if ( $url ) {
		$output .= '<p class="cm-post-tool__cta"><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">Odwiedź narzędzie →</a></p>';
	}

	$output .= '</section>';

	return $output;
}
add_shortcode( 'calymyk_post_tool', 'calymyk_new_post_tool_shortcode' );

/**
 * Render the promotion duration.
 */
function calymyk_new_promotion_duration_shortcode() {
	$start = get_post_meta( get_the_ID(), '_calymyk_promotion_start', true );
	$end   = get_post_meta( get_the_ID(), '_calymyk_promotion_end', true );

	if ( ! $start && ! $end ) {
		return '';
	}

	$format_date = static function ( $date ) {
		$timestamp = strtotime( $date );
		return $timestamp ? wp_date( 'j.m.Y', $timestamp ) : '';
	};

	$start_label = $format_date( $start );
	$end_label   = $format_date( $end );

	if ( $start_label && $end_label ) {
		$label = $start_label . ' – ' . $end_label;
	} elseif ( $start_label ) {
		$label = 'od ' . $start_label;
	} else {
		$label = 'do ' . $end_label;
	}

	return '<section class="cm-promotion-duration"><p class="cm-eyebrow">CZAS TRWANIA PROMOCJI</p><p class="cm-promotion-duration__value">' . esc_html( $label ) . '</p></section>';
}
add_shortcode( 'calymyk_promotion_duration', 'calymyk_new_promotion_duration_shortcode' );

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

