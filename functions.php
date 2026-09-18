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
 * Provide the standard promotion content structure for new posts.
 */
function calymyk_new_promotion_content_template() {
	return <<<'HTML'
<!-- wp:group {"className":"cm-promotion-section cm-promotion-intro","layout":{"type":"default"}} -->
<div class="wp-block-group cm-promotion-section cm-promotion-intro">
	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading">Jak skorzystać z promocji</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>Opisz tutaj najważniejsze informacje i zasady skorzystania z promocji.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"cm-promotion-section cm-promotion-steps","layout":{"type":"default"}} -->
<div class="wp-block-group cm-promotion-section cm-promotion-steps">
	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading">Nawigator kroków</h2>
	<!-- /wp:heading -->

	<!-- wp:group {"className":"cm-promotion-step","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-promotion-step">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading">Krok 1</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>Opisz pierwszy krok.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"cm-promotion-step","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-promotion-step">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading">Krok 2</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>Opisz drugi krok.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"cm-promotion-step","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-promotion-step">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading">Krok 3</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>Opisz trzeci krok.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"className":"cm-promotion-editor-note"} -->
	<p class="cm-promotion-editor-note">Potrzebujesz więcej kroków? Zduplikuj blok „Krok”.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"cm-promotion-section cm-promotion-prep","layout":{"type":"default"}} -->
<div class="wp-block-group cm-promotion-section cm-promotion-prep">
	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading">Przygotuj przed startem</h2>
	<!-- /wp:heading -->

	<!-- wp:list -->
	<ul class="wp-block-list">
		<li>Wpisz tutaj pierwszy element.</li>
		<li>Wpisz tutaj drugi element.</li>
		<li>Wpisz tutaj trzeci element.</li>
	</ul>
	<!-- /wp:list -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"cm-promotion-section cm-promotion-faq","layout":{"type":"default"}} -->
<div class="wp-block-group cm-promotion-section cm-promotion-faq">
	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading">FAQ</h2>
	<!-- /wp:heading -->

	<!-- wp:details -->
<details class="wp-block-details"><summary>Wpisz pytanie</summary><!-- wp:paragraph --><p>Wpisz odpowiedź.</p><!-- /wp:paragraph --></details>
	<!-- /wp:details -->

	<!-- wp:details -->
<details class="wp-block-details"><summary>Wpisz pytanie</summary><!-- wp:paragraph --><p>Wpisz odpowiedź.</p><!-- /wp:paragraph --></details>
	<!-- /wp:details -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"cm-promotion-section cm-promotion-terms","layout":{"type":"default"}} -->
<div class="wp-block-group cm-promotion-section cm-promotion-terms">
	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading">Regulamin promocji</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><a href="#">Dodaj link do regulaminu promocji →</a></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
}

/**
 * Pre-fill the standard promotion structure when creating a new post.
 */
function calymyk_new_default_promotion_content( $content, $post ) {
	if ( $post instanceof WP_Post && 'post' === $post->post_type && 'auto-draft' === $post->post_status && '' === trim( $content ) ) {
		return calymyk_new_promotion_content_template();
	}

	return $content;
}
add_filter( 'default_content', 'calymyk_new_default_promotion_content', 10, 2 );

/**
 * Register promotion metadata for the block editor.
 */
function calymyk_new_register_promotion_meta() {
	register_post_meta(
		'post',
		'_calymyk_post_tool',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_calymyk_promotion_start',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_calymyk_promotion_end',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'calymyk_new_register_promotion_meta', 20 );

/**
 * Load promotion controls directly in the block editor sidebar.
 */
function calymyk_new_enqueue_editor_assets() {
	$asset = get_template_directory() . '/assets/js/promotion-meta.js';

	wp_enqueue_script(
		'calymyk-new-promotion-meta',
		get_template_directory_uri() . '/assets/js/promotion-meta.js',
		array( 'wp-blocks', 'wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-plugins' ),
		file_exists( $asset ) ? filemtime( $asset ) : wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'calymyk_new_enqueue_editor_assets' );

/**
 * Save promotion metadata from classic editor/meta-box contexts as a fallback.
 */
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
 * Render three random posts below each promotion.
 */
function calymyk_new_related_posts_shortcode() {
	$current_id = get_the_ID();

	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => array( $current_id ),
			'orderby'        => 'rand',
			'no_found_rows'  => true,
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	$output = '<section class="cm-related-posts"><div class="cm-related-posts__header"><div><p class="cm-eyebrow">WIĘCEJ DO ODKRYCIA</p><h2 class="cm-related-posts__title">Zobacz również</h2></div></div><div class="cm-related-posts__grid">';

	while ( $query->have_posts() ) {
		$query->the_post();

		$output .= '<article class="cm-related-post-card">';

		if ( has_post_thumbnail() ) {
			$output .= '<a class="cm-related-post-card__image" href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'medium_large' ) . '</a>';
		}

		$categories = get_the_category();
		if ( $categories ) {
			$output .= '<p class="cm-related-post-card__category">' . esc_html( $categories[0]->name ) . '</p>';
		}

		$output .= '<h3 class="cm-related-post-card__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';

		$excerpt = get_the_excerpt();
		if ( $excerpt ) {
			$output .= '<p class="cm-related-post-card__excerpt">' . esc_html( wp_trim_words( $excerpt, 24 ) ) . '</p>';
		}

		$output .= '<div class="cm-related-post-card__footer"><a class="cm-related-post-card__link" href="' . esc_url( get_permalink() ) . '">Czytaj więcej →</a><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time></div>';
		$output .= '</article>';
	}

	wp_reset_postdata();

	return $output . '</div></section>';
}
add_shortcode( 'calymyk_related_posts', 'calymyk_new_related_posts_shortcode' );

/**
 * Render three random tools below each tool page.
 */
function calymyk_new_related_tools_shortcode() {
	$current_id = get_the_ID();

	$query = new WP_Query(
		array(
			'post_type'      => 'tool',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => array( $current_id ),
			'orderby'        => 'rand',
			'no_found_rows'  => true,
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	$output = '<section class="cm-related-posts cm-related-tools"><div class="cm-related-posts__header"><div><p class="cm-eyebrow">WIĘCEJ DO ODKRYCIA</p><h2 class="cm-related-posts__title">Zobacz również</h2></div></div><div class="cm-related-posts__grid">';

	while ( $query->have_posts() ) {
		$query->the_post();

		$output .= '<article class="cm-related-post-card cm-related-tool-card">';

		if ( has_post_thumbnail() ) {
			$output .= '<a class="cm-related-post-card__image" href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'medium_large' ) . '</a>';
		}

		$categories = get_the_terms( get_the_ID(), 'tool_category' );
		if ( $categories && ! is_wp_error( $categories ) ) {
			$output .= '<p class="cm-related-post-card__category">' . esc_html( $categories[0]->name ) . '</p>';
		}

		$output .= '<h3 class="cm-related-post-card__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';

		$excerpt = get_the_excerpt();
		if ( $excerpt ) {
			$output .= '<p class="cm-related-post-card__excerpt">' . esc_html( wp_trim_words( $excerpt, 24 ) ) . '</p>';
		}

		$output .= '<div class="cm-related-post-card__footer"><a class="cm-related-post-card__link" href="' . esc_url( get_permalink() ) . '">Zobacz narzędzie →</a></div>';
		$output .= '</article>';
	}

	wp_reset_postdata();

	return $output . '</div></section>';
}
add_shortcode( 'calymyk_related_tools', 'calymyk_new_related_tools_shortcode' );

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

