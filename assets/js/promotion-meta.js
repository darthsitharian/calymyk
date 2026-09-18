(function () {
	'use strict';

	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { SelectControl, TextControl } = wp.components;
	const { createElement } = wp.element;
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
