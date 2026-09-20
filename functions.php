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

	wp_enqueue_script(
		'calymyk-new-hero-cards',
		get_template_directory_uri() . '/assets/js/hero-cards.js',
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
 * Category presentation settings.
 *
 * Each standard post category can have its own accent color and icon.
 * The values are stored as term meta so the frontend can render the
 * category consistently in cards and other promotion surfaces.
 */
function calymyk_new_category_icon_options() {
	return array(
		'database'      => 'Baza danych',
		'document'      => 'Dokument',
		'chart'         => 'Wykres',
		'education'     => 'Edukacja',
		'shopping-cart' => 'Zakupy',
		'wallet'        => 'Portfel',
		'gift'          => 'Prezent',
		'trophy'        => 'Nagroda',
	);
}

function calymyk_new_category_icon_svg( $icon ) {

	$file_name = isset( $icon_files[ $icon ] ) ? $icon_files[ $icon ] : 'document.svg';
	$file_path = get_template_directory() . '/assets/icons/categories/' . $file_name;

	if ( ! file_exists( $file_path ) ) {
		return '';
	}

	$svg = file_get_contents( $file_path );

	return is_string( $svg ) ? $svg : '';
}

function calymyk_new_register_category_meta() {
	register_term_meta(
		'category',
		'_calymyk_category_color',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_hex_color',
			'auth_callback'     => function () {
				return current_user_can( 'manage_categories' );
			},
		)
	);

	register_term_meta(
		'category',
		'_calymyk_category_icon',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => function ( $value ) {
				$options = calymyk_new_category_icon_options();
				return isset( $options[ $value ] ) ? $value : 'document';
			},
			'auth_callback'     => function () {
				return current_user_can( 'manage_categories' );
			},
		)
	);
}
add_action( 'init', 'calymyk_new_register_category_meta', 20 );

function calymyk_new_category_icon_picker( $selected ) {
	$options = calymyk_new_category_icon_options();
	?>
	<div class="cm-category-icon-picker" role="radiogroup" aria-label="Wybór ikony kategorii">
		<input type="hidden" id="calymyk_category_icon" name="calymyk_category_icon" value="<?php echo esc_attr( $selected ); ?>">
		<?php foreach ( $options as $value => $label ) : ?>
			<button
				type="button"
				class="cm-category-icon-option<?php echo $value === $selected ? ' is-selected' : ''; ?>"
				data-icon="<?php echo esc_attr( $value ); ?>"
				aria-pressed="<?php echo $value === $selected ? 'true' : 'false'; ?>"
				title="<?php echo esc_attr( $label ); ?>"
			>
				<span class="cm-category-icon-option__preview"><?php echo calymyk_new_category_icon_svg( $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="cm-category-icon-option__label"><?php echo esc_html( $label ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>
	<?php
}

function calymyk_new_category_color_field( $color ) {
	?>
	<div class="cm-category-color-control">
		<input type="color" id="calymyk_category_color" name="calymyk_category_color" value="<?php echo esc_attr( $color ); ?>">
		<code class="cm-category-color-value"><?php echo esc_html( $color ); ?></code>
	</div>
	<p class="description">Kolor zakładki kategorii wyświetlanej przy wpisach.</p>
	<?php
}

function calymyk_new_category_add_fields() {
	?>
	<div class="form-field">
		<label for="calymyk_category_color">Kolor kategorii</label>
		<?php calymyk_new_category_color_field( '#00B85C' ); ?>
	</div>
	<div class="form-field">
		<label>Ikona kategorii</label>
		<?php calymyk_new_category_icon_picker( 'document' ); ?>
	</div>
	<?php
}
add_action( 'category_add_form_fields', 'calymyk_new_category_add_fields' );

function calymyk_new_category_edit_fields( $term ) {
	$color = get_term_meta( $term->term_id, '_calymyk_category_color', true ) ?: '#00B85C';
	$icon  = get_term_meta( $term->term_id, '_calymyk_category_icon', true ) ?: 'document';
	?>
	<tr class="form-field">
		<th scope="row"><label for="calymyk_category_color">Kolor kategorii</label></th>
		<td><?php calymyk_new_category_color_field( $color ); ?></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label>Ikona kategorii</label></th>
		<td><?php calymyk_new_category_icon_picker( $icon ); ?></td>
	</tr>
	<?php
}
add_action( 'category_edit_form_fields', 'calymyk_new_category_edit_fields' );

function calymyk_new_category_admin_assets() {
	$screen = get_current_screen();
	if ( ! $screen || 'edit-tags' !== $screen->base && 'term' !== $screen->base ) {
		return;
	}
	if ( 'category' !== $screen->taxonomy ) {
		return;
	}
	?>
	<style>
		.cm-category-color-control {
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.cm-category-color-control input[type="color"] {
			width: 52px;
			height: 36px;
			padding: 2px;
			border: 1px solid #c3c4c7;
			border-radius: 6px;
			background: #fff;
			cursor: pointer;
		}
		.cm-category-color-value {
			font-size: 12px;
			font-weight: 600;
		}
		.cm-category-icon-picker {
			display: grid;
			grid-template-columns: repeat(4, minmax(100px, 130px));
			gap: 10px;
			max-width: 570px;
		}
		.cm-category-icon-option {
			min-height: 94px;
			padding: 12px 8px;
			border: 1px solid #c3c4c7;
			border-radius: 8px;
			background: #fff;
			color: #1d2327;
			box-shadow: none;
			cursor: pointer;
			text-align: center;
		}
		.cm-category-icon-option:hover {
			border-color: #2271b1;
			color: #2271b1;
		}
		.cm-category-icon-option.is-selected {
			border-color: #2271b1;
			box-shadow: 0 0 0 1px #2271b1;
			color: #2271b1;
		}
		.cm-category-icon-option__preview {
			display: flex;
			align-items: center;
			justify-content: center;
			height: 42px;
		}
		.cm-category-icon-option__preview svg {
			width: 30px;
			height: 30px;
		}
		.cm-category-icon-option__label {
			display: block;
			margin-top: 6px;
			font-size: 12px;
			line-height: 1.2;
		}
		@media (max-width: 782px) {
			.cm-category-icon-picker {
				grid-template-columns: repeat(2, minmax(120px, 1fr));
				max-width: 400px;
			}
		}
	</style>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const iconInput = document.getElementById('calymyk_category_icon');
			const colorInput = document.getElementById('calymyk_category_color');
			const colorValue = document.querySelector('.cm-category-color-value');

			document.querySelectorAll('.cm-category-icon-option').forEach(function (button) {
			button.addEventListener('click', function () {
				if (!iconInput) return;
				iconInput.value = button.dataset.icon;
				document.querySelectorAll('.cm-category-icon-option').forEach(function (item) {
					item.classList.remove('is-selected');
					item.setAttribute('aria-pressed', 'false');
				});
				button.classList.add('is-selected');
				button.setAttribute('aria-pressed', 'true');
			});
			});

			if (colorInput && colorValue) {
				colorInput.addEventListener('input', function () {
					colorValue.textContent = colorInput.value.toUpperCase();
				});
			}
		});
	</script>
	<?php
}
add_action( 'admin_footer', 'calymyk_new_category_admin_assets' );

function calymyk_new_save_category_fields( $term_id ) {
	if ( isset( $_POST['calymyk_category_color'] ) ) {
		$color = sanitize_hex_color( wp_unslash( $_POST['calymyk_category_color'] ) );
		if ( $color ) {
			update_term_meta( $term_id, '_calymyk_category_color', $color );
		} else {
			delete_term_meta( $term_id, '_calymyk_category_color' );
		}
	}

	if ( isset( $_POST['calymyk_category_icon'] ) ) {
		$icon    = sanitize_key( wp_unslash( $_POST['calymyk_category_icon'] ) );
		$options = calymyk_new_category_icon_options();
		if ( isset( $options[ $icon ] ) ) {
			update_term_meta( $term_id, '_calymyk_category_icon', $icon );
		}
	}
}
add_action( 'created_category', 'calymyk_new_save_category_fields' );
add_action( 'edited_category', 'calymyk_new_save_category_fields' );

/**
 * Render the category icon for the current promotion.
 */
function calymyk_new_category_icon_shortcode() {
	$categories = get_the_category();
	if ( empty( $categories ) ) {
		return '';
	}

	$category = $categories[0];
	$color    = get_term_meta( $category->term_id, '_calymyk_category_color', true ) ?: '#00B85C';


	return '<span class="cm-category-icon" style="--cm-category-color:' . esc_attr( $color ) . '" aria-label="' . esc_attr( $category->name ) . '" title="' . esc_attr( $category->name ) . '"></span>';
}
add_shortcode( 'calymyk_category_icon', 'calymyk_new_category_icon_shortcode' );

/**
 * Dynamic category icon block for Query Loop cards.
 */
function calymyk_new_register_category_icon_block() {
	register_block_type(
		'calymyk/category-icon',
		array(
			'api_version'      => 3,
			'render_callback'  => 'calymyk_new_render_category_icon_block',
		)
	);
}
add_action( 'init', 'calymyk_new_register_category_icon_block', 31 );
function calymyk_new_render_category_navigation_block() {
	$excluded_ids = array( (int) get_option( 'default_category' ) );
	$polecamy      = get_category_by_slug( 'polecamy' );

	if ( $polecamy ) {
		$excluded_ids[] = (int) $polecamy->term_id;
	}

	$categories = get_categories(
		array(
			'hide_empty' => true,
			'exclude'    => array_values( array_unique( $excluded_ids ) ),
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( empty( $categories ) ) {
		return '';
	}

	$output = '<nav class="cm-category-nav" aria-label="Kategorie"><ul class="cm-category-nav__list">';

	foreach ( $categories as $category ) {
		$color = get_term_meta( $category->term_id, '_calymyk_category_color', true ) ?: '#00B85C';

		$output .= '<li class="cm-category-nav__item">';
		$output .= '<a class="cm-category-nav__link" href="' . esc_url( get_category_link( $category ) ) . '">';
		$output .= '<span class="cm-category-nav__icon" style="--cm-category-color:' . esc_attr( $color ) . '" aria-hidden="true"></span>';
		$output .= '<span class="cm-category-nav__label">' . esc_html( $category->name ) . '</span>';
		$output .= '</a>';
		$output .= '</li>';
	}

	$output .= '</ul></nav>';

	return $output;
}

function calymyk_new_register_category_navigation_block() {
	register_block_type(
		'calymyk/category-navigation',
		array(
			'api_version'     => 3,
			'render_callback' => 'calymyk_new_render_category_navigation_block',
		)
	);
}
add_action( 'init', 'calymyk_new_register_category_navigation_block', 32 );


function calymyk_new_render_category_icon_block() {
	return calymyk_new_category_icon_shortcode();
}

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
 * Register Calymyk promotion blocks.
 */
function calymyk_new_register_promotion_blocks() {
	wp_register_script(
		'calymyk-new-promotion-blocks',
		get_template_directory_uri() . '/assets/js/promotion-blocks.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		wp_get_theme()->get( 'Version' ),
		true
	);

	register_block_type(
		'calymyk/promotion-steps',
		array(
			'api_version'   => 3,
			'attributes'    => array(
				'steps' => array(
					'type'    => 'array',
					'default' => array(
						array( 'title' => 'Krok 1', 'description' => '', 'fields' => array() ),
						array( 'title' => 'Krok 2', 'description' => '', 'fields' => array() ),
					),
				),
			),
			'editor_script' => 'calymyk-new-promotion-blocks',
			'render_callback' => 'calymyk_new_render_promotion_steps',
		)
	);

	register_block_type(
		'calymyk/promotion-prep',
		array(
			'api_version'   => 3,
			'attributes'    => array(
				'items' => array(
					'type'    => 'array',
					'default' => array( '', '', '' ),
				),
			),
			'editor_script' => 'calymyk-new-promotion-blocks',
			'render_callback' => 'calymyk_new_render_promotion_prep',
		)
	);

	register_block_type(
		'calymyk/promotion-faq',
		array(
			'api_version'   => 3,
			'attributes'    => array(
				'items' => array(
					'type'    => 'array',
					'default' => array(
						array( 'question' => '', 'answer' => '' ),
						array( 'question' => '', 'answer' => '' ),
					),
				),
			),
			'editor_script' => 'calymyk-new-promotion-blocks',
			'render_callback' => 'calymyk_new_render_promotion_faq',
		)
	);

	register_block_type(
		'calymyk/promotion-terms',
		array(
			'api_version'   => 3,
			'attributes'    => array(
				'url' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'editor_script' => 'calymyk-new-promotion-blocks',
			'render_callback' => 'calymyk_new_render_promotion_terms',
		)
	);
}
add_action( 'init', 'calymyk_new_register_promotion_blocks', 30 );

/**
 * Add a dedicated inserter category for Calymyk blocks.
 */
function calymyk_new_block_category( $categories ) {
	$categories[] = array(
		'slug'  => 'calymyk',
		'title' => 'Calymyk',
	);

	return $categories;
}
add_filter( 'block_categories_all', 'calymyk_new_block_category' );



/**
 * Give every new standard post the same promotion block structure.
 */
function calymyk_new_post_template( $args, $post_type ) {
	if ( 'post' !== $post_type ) {
		return $args;
	}

	$args['template'] = array(
		array(
			'core/paragraph',
			array(
				'placeholder' => 'Napisz tutaj treść promocji…',
			),
		),
		array( 'calymyk/promotion-steps' ),
	);

	return $args;
}
add_filter( 'register_post_type_args', 'calymyk_new_post_template', 10, 2 );



function calymyk_new_render_promotion_steps( $attributes ) {
	$steps = isset( $attributes['steps'] ) && is_array( $attributes['steps'] ) ? $attributes['steps'] : array();
	$output = '<section class="cm-promotion-section cm-promotion-steps"><p class="cm-eyebrow">NAWIGATOR KROKÓW</p><div class="cm-promotion-steps__list">';

	foreach ( $steps as $index => $step ) {
		$title       = ! empty( $step['title'] ) ? $step['title'] : 'Krok ' . ( $index + 1 );
		$description = isset( $step['description'] ) ? $step['description'] : '';
		$fields      = isset( $step['fields'] ) && is_array( $step['fields'] ) ? $step['fields'] : array();

		$output .= '<details class="cm-promotion-step">';
		$output .= '<summary class="cm-promotion-step__toggle"><span class="cm-promotion-step__header"><span class="cm-promotion-step__number">' . esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</span><span class="cm-promotion-step__heading"><span class="cm-promotion-step__eyebrow">KROK ' . esc_html( $index + 1 ) . '</span><span class="cm-promotion-step__title">' . esc_html( $title ) . '</span></span></span><svg class="cm-promotion-step__toggle-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"></path></svg></summary>';
		$output .= '<div class="cm-promotion-step__body">';

		if ( $description ) {
			$output .= '<p class="cm-promotion-step__description">' . wp_kses_post( $description ) . '</p>';
		}

		if ( $fields ) {
			$valid_fields = array();

			foreach ( $fields as $field ) {
				$field_title   = isset( $field['title'] ) ? trim( $field['title'] ) : '';
				$field_content = isset( $field['content'] ) ? trim( $field['content'] ) : '';

				if ( ! $field_title && ! $field_content ) {
					continue;
				}

				$valid_fields[] = array(
					'title'   => $field_title,
					'content' => $field_content,
				);
			}

			if ( $valid_fields ) {
				$output .= '<div class="cm-promotion-step__fields">';

				foreach ( $valid_fields as $field ) {
					$output .= '<div class="cm-promotion-step__field">';

					if ( $field['title'] ) {
						$output .= '<p class="cm-promotion-step__field-title">' . esc_html( $field['title'] ) . '</p>';
					}

					if ( $field['content'] ) {
						$output .= '<div class="cm-promotion-step__field-content">' . wpautop( wp_kses_post( $field['content'] ) ) . '</div>';
					}

					$output .= '</div>';
				}

				$output .= '</div>';
			}
		}

		$output .= '</div></details>';
	}

	return $output . '</div></section>';
}

function calymyk_new_render_promotion_prep( $attributes ) {
	$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
	$output = '<section class="cm-promotion-section cm-promotion-prep"><p class="cm-eyebrow">PRZYGOTUJ PRZED STARTEM</p><ul>';
	foreach ( $items as $item ) {
		if ( '' !== trim( $item ) ) {
			$output .= '<li>' . esc_html( $item ) . '</li>';
		}
	}
	return $output . '</ul></section>';
}

function calymyk_new_render_promotion_faq( $attributes ) {
	$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
	$output = '<section class="cm-promotion-section cm-promotion-faq"><p class="cm-eyebrow">FAQ</p><div class="cm-promotion-faq__list">';
	foreach ( $items as $item ) {
		$question = isset( $item['question'] ) ? trim( $item['question'] ) : '';
		$answer = isset( $item['answer'] ) ? trim( $item['answer'] ) : '';
		if ( ! $question && ! $answer ) {
			continue;
		}
		$output .= '<details><summary>' . esc_html( $question ?: 'Pytanie' ) . '</summary><p>' . esc_html( $answer ) . '</p></details>';
	}
	return $output . '</div></section>';
}

function calymyk_new_render_promotion_terms( $attributes ) {
	$url = isset( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '';
	$output = '<section class="cm-promotion-section cm-promotion-terms"><p class="cm-eyebrow">REGULAMIN PROMOCJI</p>';
	if ( $url ) {
		$output .= '<p><a href="' . $url . '" target="_blank" rel="noopener noreferrer">Zobacz regulamin promocji →</a></p>';
	} else {
		$output .= '<p class="cm-promotion-editor-note">Dodaj link do regulaminu promocji.</p>';
	}
	return $output . '</section>';
}

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
		'_calymyk_promotion_prep',
		array(
			'type'              => 'array',
			'single'            => true,
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
			),
			'sanitize_callback' => function ( $value ) {
				return is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : array();
			},
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_calymyk_promotion_faq',
		array(
			'type'              => 'array',
			'single'            => true,
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'question' => array( 'type' => 'string' ),
							'answer'   => array( 'type' => 'string' ),
						),
					),
				),
			),
			'sanitize_callback' => function ( $value ) {
				if ( ! is_array( $value ) ) {
					return array();
				}
				return array_map(
					function ( $item ) {
					return array(
						'question' => isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '',
						'answer'   => isset( $item['answer'] ) ? sanitize_textarea_field( $item['answer'] ) : '',
					);
					},
					$value
				);
			},
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_calymyk_promotion_terms_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_calymyk_promotion_documents',
		array(
			'type'         => 'array',
			'single'       => true,
			'show_in_rest' => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'label' => array( 'type' => 'string' ),
							'url'   => array( 'type' => 'string' ),
						),
					),
				),
			),
			'sanitize_callback' => function ( $value ) {
				if ( ! is_array( $value ) ) {
					return array();
				}
				return array_values(
					array_filter(
						array_map(
							function ( $item ) {
								return array(
									'label' => isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '',
									'url'   => isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '',
								);
							},
							$value
						),
						function ( $item ) {
							return ! empty( $item['label'] ) || ! empty( $item['url'] );
						}
					)
				);
			},
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_calymyk_promotion_unlimited',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
		)
	);

	register_post_meta(
		'post',
		'_calymyk_promotion_referral_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
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
 * Ensure promotion metadata is available to the block editor for standard posts.
 */
function calymyk_new_enable_post_custom_fields() {
	add_post_type_support( 'post', 'custom-fields' );
}
add_action( 'init', 'calymyk_new_enable_post_custom_fields', 20 );

/**
 * Load promotion controls directly in the block editor sidebar.
 */
function calymyk_new_enqueue_editor_assets() {
	$asset = get_template_directory() . '/assets/js/promotion-meta.js';

	wp_enqueue_script(
		'calymyk-new-promotion-meta',
		get_template_directory_uri() . '/assets/js/promotion-meta.js',
		array( 'wp-blocks', 'wp-components', 'wp-core-data', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-plugins' ),
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
 * Render visible breadcrumbs for single promotion posts.
 *
 * Each promotion belongs to one category, so the breadcrumb path uses
 * that category (and its parents, when applicable) as the content hierarchy.
 */
function calymyk_new_breadcrumbs_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}

	$categories = get_the_category();
	$category   = ! empty( $categories ) ? $categories[0] : null;

	$items = array(
		array(
			'label' => 'Strona główna',
			'url'   => home_url( '/' ),
		),
	);

	if ( $category ) {
		$ancestors = get_ancestors( $category->term_id, 'category' );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_category( $ancestor_id );
			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$items[] = array(
					'label' => $ancestor->name,
					'url'   => get_category_link( $ancestor->term_id ),
				);
			}
		}

		$items[] = array(
			'label' => $category->name,
			'url'   => get_category_link( $category->term_id ),
		);
	}

	$items[] = array(
		'label' => get_the_title(),
		'url'   => '',
	);

	$output = '<nav class="cm-breadcrumbs" aria-label="Okruszki"><ol>';
	$last_index = count( $items ) - 1;

	foreach ( $items as $index => $item ) {
		$output .= '<li' . ( $index === $last_index ? ' class="cm-breadcrumbs__current"' : '' ) . '>';
		if ( $item['url'] && $index !== $last_index ) {
			$output .= '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		} else {
			$output .= '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
		}
		$output .= '</li>';
	}

	return $output . '</ol></nav>';
}
add_shortcode( 'calymyk_breadcrumbs', 'calymyk_new_breadcrumbs_shortcode' );

/**
 * Add BreadcrumbList structured data for single promotion posts.
 * The visible breadcrumb trail remains the primary user-facing navigation.
 */
function calymyk_new_breadcrumbs_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$categories = get_the_category();
	$category   = ! empty( $categories ) ? $categories[0] : null;
	$items      = array(
		array(
			'name' => 'Strona główna',
			'item' => home_url( '/' ),
		),
	);

	if ( $category ) {
		$ancestors = get_ancestors( $category->term_id, 'category' );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_category( $ancestor_id );
			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$items[] = array(
					'name' => $ancestor->name,
					'item' => get_category_link( $ancestor->term_id ),
				);
			}
		}

		$items[] = array(
			'name' => $category->name,
			'item' => get_category_link( $category->term_id ),
		);
	}

	$items[] = array(
		'name' => get_the_title(),
	);

	$list_items = array();
	foreach ( $items as $index => $item ) {
		$list_item = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => $item['name'],
		);
		if ( ! empty( $item['item'] ) ) {
			$list_item['item'] = $item['item'];
		}
		$list_items[] = $list_item;
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list_items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'calymyk_new_breadcrumbs_schema' );

/**
 * Render compact promotion meta boxes below the featured image.
 */
function calymyk_new_promotion_meta_row_shortcode() {
	$start = get_post_meta( get_the_ID(), '_calymyk_promotion_start', true );
	$end = get_post_meta( get_the_ID(), '_calymyk_promotion_end', true );
	$unlimited = (bool) get_post_meta( get_the_ID(), '_calymyk_promotion_unlimited', true );
	$referral = get_post_meta( get_the_ID(), '_calymyk_promotion_referral_url', true );

	if ( ! $start && ! $end && ! $unlimited && ! $referral ) return '';

	$format_date = static function ( $date ) {
		$timestamp = strtotime( $date );
		return $timestamp ? wp_date( 'j.m.Y', $timestamp ) : '';
	};
	$start_label = $format_date( $start );
	$end_label = $format_date( $end );
	if ( $unlimited && $start_label ) {
		$duration = 'od ' . $start_label . ' · do odwołania';
	} elseif ( $unlimited ) {
		$duration = 'do odwołania';
	} elseif ( $start_label && $end_label ) {
		$duration = $start_label . ' – ' . $end_label;
	} elseif ( $start_label ) {
		$duration = 'od ' . $start_label;
	} else {
		$duration = 'do ' . $end_label;
	}

	$output = '<div class="cm-promotion-meta-row">';
	$output .= '<section class="cm-promotion-meta-box"><p class="cm-eyebrow">CZAS TRWANIA PROMOCJI</p><p class="cm-promotion-meta-box__value">' . esc_html( $duration ) . '</p></section>';
	if ( $referral ) {
		$output .= '<a class="cm-promotion-meta-box cm-promotion-referral-box" href="' . esc_url( $referral ) . '" target="_blank" rel="nofollow sponsored noopener noreferrer"><span>Przejdź do promocji</span></a>';
	}
	return $output . '</div>';
}
add_shortcode( 'calymyk_promotion_meta_row', 'calymyk_new_promotion_meta_row_shortcode' );

/**
 * Render promotion terms in the post sidebar.
 */
function calymyk_new_promotion_referral_shortcode() {
	$url = get_post_meta( get_the_ID(), '_calymyk_promotion_referral_url', true );
	if ( ! $url ) return '';
	return '<section class="cm-promotion-sidebar-section"><p class="cm-eyebrow">REFLINK</p><p><a href="' . esc_url( $url ) . '" target="_blank" rel="nofollow sponsored noopener noreferrer">Przejdź przez reflink →</a></p></section>';
}
add_shortcode( 'calymyk_promotion_referral', 'calymyk_new_promotion_referral_shortcode' );

function calymyk_new_promotion_terms_shortcode() {
	$documents = get_post_meta( get_the_ID(), '_calymyk_promotion_documents', true );

	if ( ! is_array( $documents ) || empty( $documents ) ) {
		$legacy_url = get_post_meta( get_the_ID(), '_calymyk_promotion_terms_url', true );
		$documents  = $legacy_url
			? array( array( 'label' => 'Regulamin promocji', 'url' => $legacy_url ) )
			: array();
	}

	$valid_documents = array();
	foreach ( $documents as $document ) {
		$label = isset( $document['label'] ) ? trim( $document['label'] ) : '';
		$url   = isset( $document['url'] ) ? trim( $document['url'] ) : '';

		if ( ! $label || ! $url ) {
			continue;
		}

		$valid_documents[] = array(
			'label' => $label,
			'url'   => $url,
		);
	}

	if ( empty( $valid_documents ) ) {
		return '';
	}

	$output = '<section class="cm-promotion-sidebar-section cm-promotion-documents">';
	$output .= '<p class="cm-eyebrow">WAŻNE DOKUMENTY</p>';
	$output .= '<div class="cm-promotion-documents__list">';

	foreach ( $valid_documents as $document ) {
		$output .= '<a class="cm-promotion-document" href="' . esc_url( $document['url'] ) . '" target="_blank" rel="noopener noreferrer">';
		$output .= '<span>' . esc_html( $document['label'] ) . '</span>';
		$output .= '</a>';
	}

	$output .= '</div></section>';

	return $output;
}

add_shortcode( 'calymyk_promotion_terms', 'calymyk_new_promotion_terms_shortcode' );

/**
 * Render promotion preparation checklist in the post sidebar.
 */
function calymyk_new_promotion_prep_shortcode() {
	$items = get_post_meta( get_the_ID(), '_calymyk_promotion_prep', true );
	if ( ! is_array( $items ) ) {
		return '';
	}
	$items = array_filter( array_map( 'trim', $items ) );
	if ( ! $items ) {
		return '';
	}

	$output = '<section class="cm-promotion-sidebar-section cm-promotion-prep-card">';
	$output .= '<div class="cm-promotion-sidebar-heading">';
	$output .= '<span class="cm-promotion-sidebar-heading__icon" aria-hidden="true">';
	$output .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m14.7 6.3 3-3a4 4 0 0 1-5.2 5.9L7 14.7a4 4 0 0 1-5.9-5.2l3-3 3.1 3.1 3.1-3.1-3.1-3.1 3-3a4 4 0 0 1 5.2 5.2l-3 3 3.1 3.1 3.1-3.1-3.1-3.1Z"/><path d="m14 14 6.5 6.5"/><path d="m18.5 17.5 2-2"/><path d="m16.5 20.5 2-2"/></svg>';
	$output .= '</span><h2>Przygotuj przed startem:</h2></div>';
	$output .= '<ul class="cm-promotion-sidebar-list">';
	foreach ( $items as $item ) {
		$output .= '<li><span class="cm-promotion-sidebar-list__icon cm-icon cm-icon--check" aria-hidden="true"></span><span>' . esc_html( $item ) . '</span></li>';
	}
	$output .= '</ul></section>';
	return $output;
}
add_shortcode( 'calymyk_promotion_prep', 'calymyk_new_promotion_prep_shortcode' );

/**
 * Render promotion FAQ in the post sidebar.
 */
function calymyk_new_promotion_faq_shortcode() {
	$items = get_post_meta( get_the_ID(), '_calymyk_promotion_faq', true );
	if ( ! is_array( $items ) ) {
		return '';
	}

	$has_items = false;
	$output = '<section class="cm-promotion-sidebar-section cm-promotion-faq-card">';
	$output .= '<div class="cm-promotion-sidebar-heading">';
	$output .= '<span class="cm-promotion-sidebar-heading__icon" aria-hidden="true">';
	$output .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11.5a8.5 8.5 0 1 1-4.1-7.3L20 4v7.5Z"/><path d="M9.7 9a2.6 2.6 0 1 1 4.6 1.7c-.9 1-1.9 1.2-1.9 2.8"/><path d="M12 17.2h.01"/></svg>';
	$output .= '</span><h2>Najczęściej zadawane pytania (FAQ)</h2></div>';
	$output .= '<div class="cm-promotion-sidebar-faq">';
	$number = 0;

	foreach ( $items as $item ) {
		$question = isset( $item['question'] ) ? trim( $item['question'] ) : '';
		$answer   = isset( $item['answer'] ) ? trim( $item['answer'] ) : '';
		if ( ! $question && ! $answer ) {
			continue;
		}

		$has_items = true;
		$number++;
		$output .= '<details><summary><span class="cm-promotion-sidebar-faq__number">' . esc_html( $number ) . '.</span><span class="cm-promotion-sidebar-faq__question">' . esc_html( $question ?: 'Pytanie' ) . '</span><span class="cm-promotion-sidebar-faq__chevron cm-icon cm-icon--chevron" aria-hidden="true"></span></summary>';

		if ( $answer ) {
			$output .= '<p>' . esc_html( $answer ) . '</p>';
		}

		$output .= '</details>';
	}

	$output .= '</div></section>';
	return $has_items ? $output : '';
}
add_shortcode( 'calymyk_promotion_faq', 'calymyk_new_promotion_faq_shortcode' );

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
	$unlimited = (bool) get_post_meta( get_the_ID(), '_calymyk_promotion_unlimited', true );

	if ( ! $start && ! $end && ! $unlimited ) {
		return '';
	}

	$format_date = static function ( $date ) {
		$timestamp = strtotime( $date );
		return $timestamp ? wp_date( 'j.m.Y', $timestamp ) : '';
	};

	$start_label = $format_date( $start );
	$end_label   = $format_date( $end );

	if ( $unlimited && $start_label ) {
		$label = 'od ' . $start_label . ' · do odwołania';
	} elseif ( $unlimited ) {
		$label = 'do odwołania';
	} elseif ( $start_label && $end_label ) {
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
/**
 * Hide the legacy "W SKRÓCIE" sidebar box on all single posts.
 * This also protects against a previously saved Site Editor template overriding the theme file.
 */
function calymyk_new_hide_legacy_summary_box( $block_content, $block ) {
	if ( is_admin() || ! is_singular( 'post' ) ) {
		return $block_content;
	}

	$class_name = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';

	if ( false !== strpos( $class_name, 'cm-single__sidebar-card' ) ) {
		return '';
	}

	return $block_content;
}
add_filter( 'render_block', 'calymyk_new_hide_legacy_summary_box', 10, 2 );
