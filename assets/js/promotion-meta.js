(function () {
	'use strict';

	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { SelectControl, TextControl } = wp.components;
	const { createElement, useEffect } = wp.element;
	const { useSelect } = wp.data;
	const { parse } = wp.blocks;

	const PROMOTION_TEMPLATE = "<!-- wp:group {\"className\":\"cm-promotion-section cm-promotion-intro\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-section cm-promotion-intro\">\n<!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">Jak skorzystać z promocji</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p>Opisz tutaj najważniejsze informacje i zasady skorzystania z promocji.</p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"className\":\"cm-promotion-section cm-promotion-steps\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-section cm-promotion-steps\">\n<!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">Nawigator kroków</h2>\n<!-- /wp:heading -->\n<!-- wp:group {\"className\":\"cm-promotion-step\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-step\">\n<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Krok 1</h3>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p>Opisz pierwszy krok.</p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:group -->\n<!-- wp:group {\"className\":\"cm-promotion-step\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-step\">\n<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Krok 2</h3>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p>Opisz drugi krok.</p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:group -->\n<!-- wp:group {\"className\":\"cm-promotion-step\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-step\">\n<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Krok 3</h3>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p>Opisz trzeci krok.</p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:group -->\n<!-- wp:paragraph {\"className\":\"cm-promotion-editor-note\"} -->\n<p class=\"cm-promotion-editor-note\">Potrzebujesz więcej kroków? Zduplikuj blok „Krok”.</p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"className\":\"cm-promotion-section cm-promotion-prep\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-section cm-promotion-prep\">\n<!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">Przygotuj przed startem</h2>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Wpisz tutaj pierwszy element.</li><li>Wpisz tutaj drugi element.</li><li>Wpisz tutaj trzeci element.</li></ul>\n<!-- /wp:list -->\n</div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"className\":\"cm-promotion-section cm-promotion-faq\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-section cm-promotion-faq\">\n<!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">FAQ</h2>\n<!-- /wp:heading -->\n<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>Wpisz pytanie</summary><!-- wp:paragraph --><p>Wpisz odpowiedź.</p><!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>Wpisz pytanie</summary><!-- wp:paragraph --><p>Wpisz odpowiedź.</p><!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n</div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"className\":\"cm-promotion-section cm-promotion-terms\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group cm-promotion-section cm-promotion-terms\">\n<!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">Regulamin promocji</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><a href=\"#\">Dodaj link do regulaminu promocji →</a></p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:group -->";

	function PromotionMetaPanel() {
		const meta = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('meta') || {};
		}, []);

		const postType = useSelect(function (select) {
			return select('core/editor').getCurrentPostType();
		}, []);

		const tools = useSelect(function (select) {
			return select('core').getEntityRecords('postType', 'tool', {
			per_page: 100,
			orderby: 'title',
			order: 'asc',
			status: 'publish'
		});
		}, []);

		useEffect(function () {
			if (postType !== 'post') {
				return;
			}

			const editor = wp.data.select('core/editor');
			const blockEditor = wp.data.dispatch('core/block-editor');

			if (!editor || !blockEditor || editor.getEditedPostContent().trim() !== '') {
				return;
			}

			blockEditor.insertBlocks(parse(PROMOTION_TEMPLATE));
		}, [postType]);

		function updateMeta(key, value) {
			wp.data.dispatch('core/editor').editPost({
				meta: Object.assign({}, meta, { [key]: value })
			});
		}

		const toolOptions = [{ label: '— bez przypisania —', value: '0' }];
		if (tools) {
			tools.forEach(function (tool) {
				toolOptions.push({
					label: tool.title && tool.title.rendered ? tool.title.rendered : '(bez nazwy)',
					value: String(tool.id)
				});
			});
		}

		return createElement(
			PluginDocumentSettingPanel,
			{
				name: 'calymyk-promotion-details',
				title: 'Szczegóły promocji',
				icon: 'tag',
				initialOpen: true
			},
			createElement(SelectControl, {
				label: 'Narzędzie',
				value: String(meta._calymyk_post_tool || 0),
				options: toolOptions,
				onChange: function (value) {
					updateMeta('_calymyk_post_tool', parseInt(value, 10) || 0);
				}
			}),
			createElement(TextControl, {
				label: 'Promocja od',
				type: 'date',
				value: meta._calymyk_promotion_start || '',
				onChange: function (value) {
					updateMeta('_calymyk_promotion_start', value);
				}
			}),
			createElement(TextControl, {
				label: 'Promocja do',
				type: 'date',
				value: meta._calymyk_promotion_end || '',
				onChange: function (value) {
					updateMeta('_calymyk_promotion_end', value);
				}
			}),
			createElement('p', { className: 'components-base-control__help' }, 'Daty są opcjonalne.')
		);
	}

	registerPlugin('calymyk-promotion-meta', {
		render: PromotionMetaPanel
	});
}());
