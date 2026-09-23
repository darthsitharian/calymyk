(function () {
	'use strict';

	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { ComboboxControl, TextControl, TextareaControl, Button, ToggleControl } = wp.components;
	const { createElement, useEffect, useState } = wp.element;
	const { useSelect } = wp.data;
	const { useEntityProp } = wp.coreData;
	const apiFetch = wp.apiFetch;
	const { addQueryArgs } = wp.url;

	function PromotionMetaPanel() {
		const [meta, setMeta] = useEntityProp('postType', 'post', 'meta');
		const [toolOptions, setToolOptions] = useState([]);
		const [isLoadingTools, setIsLoadingTools] = useState(false);
		const safeMeta = meta || {};

		const postType = useSelect(function (select) {
			return select('core/editor').getCurrentPostType();
		}, []);

		const selectedToolId = parseInt(safeMeta._calymyk_post_tool || 0, 10) || 0;
		const selectedTool = useSelect(function (select) {
			return selectedToolId
				? select('core').getEntityRecord('postType', 'tool', selectedToolId)
				: null;
		}, [selectedToolId]);

		function fetchToolOptions(search) {
			setIsLoadingTools(true);

			return apiFetch({
				path: addQueryArgs('/wp/v2/tool', {
					per_page: 20,
					orderby: 'title',
					order: 'asc',
					status: 'publish',
					search: search || undefined
				})
			}).then(function (results) {
				const options = results.map(function (tool) {
					return {
						label: tool.title && tool.title.rendered ? tool.title.rendered : '(bez nazwy)',
						value: String(tool.id)
					};
				});

				setToolOptions(options);
				return options;
			}).finally(function () {
				setIsLoadingTools(false);
			});
		}

		useEffect(function () {
			fetchToolOptions('');
		}, []);

		function updateMeta(key, value) {
			setMeta(Object.assign({}, safeMeta, { [key]: value }));
		}

		if (postType !== 'post') {
			return null;
		}

		const prepItems = Array.isArray(safeMeta._calymyk_promotion_prep) ? safeMeta._calymyk_promotion_prep : ['', ''];
		const faqItems = Array.isArray(safeMeta._calymyk_promotion_faq) ? safeMeta._calymyk_promotion_faq : [
			{ question: '', answer: '' },
			{ question: '', answer: '' }
		];

		const documentItems = Array.isArray(safeMeta._calymyk_promotion_documents)
			? safeMeta._calymyk_promotion_documents
			: (safeMeta._calymyk_promotion_terms_url ? [
				{ label: 'Regulamin promocji', url: safeMeta._calymyk_promotion_terms_url }
			] : [
				{ label: '', url: '' }
			]);

		function updateDocument(index, key, value) {
			const next = documentItems.slice();
			next[index] = Object.assign({}, next[index], { [key]: value });
			updateMeta('_calymyk_promotion_documents', next);
		}



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
			createElement('div', { style: { marginBottom: '16px' } },
				createElement('label', { style: { display: 'block', marginBottom: '8px' } }, 'Narzędzie'),
				createElement(ComboboxControl, {
					label: 'Narzędzie',
					placeholder: 'Wyszukaj narzędzie…',
					value: selectedToolId ? String(selectedToolId) : '',
					options: selectedTool
						? [{
							label: selectedTool.title && selectedTool.title.rendered ? selectedTool.title.rendered : '(bez nazwy)',
							value: String(selectedTool.id)
						}, ...toolOptions.filter(function (option) { return option.value !== String(selectedTool.id); })]
						: toolOptions,
					isLoading: isLoadingTools,
					expandOnFocus: true,
					onFilterValueChange: function (value) {
						fetchToolOptions(value || '');
					},
					onChange: function (value) {
						updateMeta('_calymyk_post_tool', value ? parseInt(value, 10) || 0 : 0);
					}
				})
			),
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
			createElement('hr', {}),
			createElement('h3', {}, 'Ważne dokumenty'),
			documentItems.map(function (item, index) {
				return createElement('div', { key: 'document-' + index, style: { marginBottom: '16px' } },
					createElement(TextControl, {
						label: 'Nazwa dokumentu ' + (index + 1),
						value: item.label || '',
						onChange: function (value) { updateDocument(index, 'label', value); }
					}),
					createElement(TextControl, {
						label: 'Link do dokumentu',
						type: 'url',
						value: item.url || '',
						onChange: function (value) { updateDocument(index, 'url', value); }
					}),
					createElement(Button, {
						isDestructive: true,
						isSmall: true,
						onClick: function () {
							updateMeta('_calymyk_promotion_documents', documentItems.filter(function (_, i) { return i !== index; }));
						}
					}, 'Usuń dokument')
				);
			}),
			createElement(Button, {
				variant: 'secondary',
				onClick: function () {
					updateMeta('_calymyk_promotion_documents', documentItems.concat([{ label: '', url: '' }]));
				}
			}, '+ Dodaj dokument'),
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
