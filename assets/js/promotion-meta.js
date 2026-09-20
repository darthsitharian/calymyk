(function () {
	'use strict';

	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { SelectControl, TextControl, TextareaControl, Button, ToggleControl } = wp.components;
	const { createElement } = wp.element;
	const { useSelect } = wp.data;
	const { useEntityProp } = wp.coreData;

	function PromotionMetaPanel() {
		const [meta, setMeta] = useEntityProp('postType', 'post', 'meta');
		const safeMeta = meta || {};

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
			setMeta(Object.assign({}, safeMeta, { [key]: value }));
		}

		if (postType !== 'post') {
			return null;
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

		const prepItems = Array.isArray(safeMeta._calymyk_promotion_prep) ? safeMeta._calymyk_promotion_prep : ['', ''];
		const faqItems = Array.isArray(safeMeta._calymyk_promotion_faq) ? safeMeta._calymyk_promotion_faq : [
			{ question: '', answer: '' },
			{ question: '', answer: '' }
		];

		function updatePrep(index, value) {
			const next = prepItems.slice();
			next[index] = value;
			updateMeta('_calymyk_promotion_prep', next);
		}

		function updateFaq(index, key, value) {
			const next = faqItems.slice();
			next[index] = Object.assign({}, next[index], { [key]: value });
			updateMeta('_calymyk_promotion_faq', next);
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
				value: String(safeMeta._calymyk_post_tool || 0),
				options: toolOptions,
				onChange: function (value) {
					updateMeta('_calymyk_post_tool', parseInt(value, 10) || 0);
				}
			}),
			createElement(TextControl, {
				label: 'Promocja od',
				type: 'date',
				value: safeMeta._calymyk_promotion_start || '',
				onChange: function (value) {
					updateMeta('_calymyk_promotion_start', value);
				}
			}),
			createElement(ToggleControl, {
				label: 'Promocja do odwołania',
				checked: !!safeMeta._calymyk_promotion_unlimited,
				onChange: function (value) {
					updateMeta('_calymyk_promotion_unlimited', value);
				}
			}),
			createElement(TextControl, {
				label: 'Promocja do',
				type: 'date',
				value: safeMeta._calymyk_promotion_end || '',
				onChange: function (value) {
					updateMeta('_calymyk_promotion_end', value);
				}
			}),
			createElement(TextControl, {
				label: 'Reflink',
				type: 'url',
				value: safeMeta._calymyk_promotion_referral_url || '',
				onChange: function (value) { updateMeta('_calymyk_promotion_referral_url', value); }
			}),
			createElement(TextControl, {
				label: 'Link do regulaminu promocji',
				type: 'url',
				value: safeMeta._calymyk_promotion_terms_url || '',
				onChange: function (value) {
					updateMeta('_calymyk_promotion_terms_url', value);
				}
			}),
			createElement('hr', {}),
			createElement('h3', {}, 'Przygotuj przed startem'),
			prepItems.map(function (item, index) {
				return createElement(TextControl, {
					key: 'prep-' + index,
					label: 'Element ' + (index + 1),
					value: item,
					onChange: function (value) { updatePrep(index, value); }
				});
			}),
			createElement(Button, {
				variant: 'secondary',
				onClick: function () { updateMeta('_calymyk_promotion_prep', prepItems.concat([''])); }
			}, '+ Dodaj element'),
			createElement('hr', {}),
			createElement('h3', {}, 'FAQ'),
			faqItems.map(function (item, index) {
				return createElement('div', { key: 'faq-' + index, style: { marginBottom: '16px' } },
					createElement(TextControl, {
						label: 'Pytanie ' + (index + 1),
						value: item.question || '',
						onChange: function (value) { updateFaq(index, 'question', value); }
					}),
					createElement(TextareaControl, {
						label: 'Odpowiedź',
						value: item.answer || '',
						onChange: function (value) { updateFaq(index, 'answer', value); },
						rows: 3
					}),
					createElement(Button, {
						isDestructive: true,
						isSmall: true,
						onClick: function () {
							updateMeta('_calymyk_promotion_faq', faqItems.filter(function (_, i) { return i !== index; }));
						}
					}, 'Usuń pytanie')
				);
			}),
			createElement(Button, {
				variant: 'secondary',
				onClick: function () {
					updateMeta('_calymyk_promotion_faq', faqItems.concat([{ question: '', answer: '' }]));
				}
			}, '+ Dodaj pytanie')
		);
	}

	registerPlugin('calymyk-promotion-meta', {
		render: PromotionMetaPanel
	});
}());
