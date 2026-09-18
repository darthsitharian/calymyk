(function () {
	'use strict';

	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { Button, TextControl, TextareaControl } = wp.components;
	const { createElement, Fragment } = wp.element;

	function StepsEdit({ attributes, setAttributes }) {
		const steps = attributes.steps || [];
		const update = (index, key, value) => {
			const next = steps.slice();
			next[index] = Object.assign({}, next[index], { [key]: value });
			setAttributes({ steps: next });
		};
		return createElement('div', useBlockProps({ className: 'cm-promotion-editor-block cm-promotion-steps-editor' }),
			createElement('p', { className: 'cm-eyebrow' }, 'NAWIGATOR KROKÓW'),
			steps.map((step, index) => createElement('div', { className: 'cm-promotion-step-editor', key: index },
				createElement('div', { className: 'cm-promotion-step-editor__number' }, String(index + 1).padStart(2, '0')),
				createElement('div', { className: 'cm-promotion-step-editor__fields' },
					createElement(TextControl, { label: 'Nazwa kroku', value: step.title || '', onChange: v => update(index, 'title', v) }),
					createElement(TextareaControl, { label: 'Opis', value: step.description || '', onChange: v => update(index, 'description', v), rows: 3 }),
					createElement(Button, { isDestructive: true, isSmall: true, onClick: () => setAttributes({ steps: steps.filter((_, i) => i !== index) }) }, 'Usuń krok')
				)
			)),
			createElement(Button, { variant: 'secondary', onClick: () => setAttributes({ steps: steps.concat([{ title: 'Krok ' + (steps.length + 1), description: '' }]) }) }, '+ Dodaj krok')
		);
	}

	function PrepEdit({ attributes, setAttributes }) {
		const items = attributes.items || [];
		return createElement('div', useBlockProps({ className: 'cm-promotion-editor-block cm-promotion-prep-editor' }),
			createElement('p', { className: 'cm-eyebrow' }, 'PRZYGOTUJ PRZED STARTEM'),
			items.map((item, index) => createElement(TextControl, { key: index, label: 'Element ' + (index + 1), value: item, onChange: v => { const next = items.slice(); next[index] = v; setAttributes({ items: next }); } })),
			createElement(Button, { variant: 'secondary', onClick: () => setAttributes({ items: items.concat(['']) }) }, '+ Dodaj element'),
			items.length > 0 && createElement(Button, { isDestructive: true, isSmall: true, onClick: () => setAttributes({ items: items.slice(0, -1) }) }, 'Usuń ostatni element')
		);
	}

	function FaqEdit({ attributes, setAttributes }) {
		const items = attributes.items || [];
		const update = (index, key, value) => { const next = items.slice(); next[index] = Object.assign({}, next[index], { [key]: value }); setAttributes({ items: next }); };
		return createElement('div', useBlockProps({ className: 'cm-promotion-editor-block cm-promotion-faq-editor' }),
			createElement('p', { className: 'cm-eyebrow' }, 'FAQ'),
			items.map((item, index) => createElement('div', { className: 'cm-promotion-faq-editor__item', key: index },
				createElement(TextControl, { label: 'Pytanie', value: item.question || '', onChange: v => update(index, 'question', v) }),
				createElement(TextareaControl, { label: 'Odpowiedź', value: item.answer || '', onChange: v => update(index, 'answer', v), rows: 3 }),
				createElement(Button, { isDestructive: true, isSmall: true, onClick: () => setAttributes({ items: items.filter((_, i) => i !== index) }) }, 'Usuń pytanie')
			)),
			createElement(Button, { variant: 'secondary', onClick: () => setAttributes({ items: items.concat([{ question: '', answer: '' }]) }) }, '+ Dodaj pytanie')
		);
	}

	function TermsEdit({ attributes, setAttributes }) {
		return createElement('div', useBlockProps({ className: 'cm-promotion-editor-block cm-promotion-terms-editor' }),
			createElement('p', { className: 'cm-eyebrow' }, 'REGULAMIN PROMOCJI'),
			createElement(TextControl, { label: 'Link do regulaminu', type: 'url', value: attributes.url || '', onChange: v => setAttributes({ url: v }), placeholder: 'https://...' }),
			createElement('p', { className: 'description' }, 'Link jest wymaganym elementem każdej promocji.')
		);
	}

	registerBlockType('calymyk/promotion-steps', { title: 'Nawigator kroków', icon: 'list-view', category: 'calymyk', edit: StepsEdit, save: () => null });
	registerBlockType('calymyk/promotion-prep', { title: 'Przygotuj przed startem', icon: 'yes-alt', category: 'calymyk', edit: PrepEdit, save: () => null });
	registerBlockType('calymyk/promotion-faq', { title: 'FAQ promocji', icon: 'editor-help', category: 'calymyk', edit: FaqEdit, save: () => null });
	registerBlockType('calymyk/promotion-terms', { title: 'Regulamin promocji', icon: 'admin-links', category: 'calymyk', edit: TermsEdit, save: () => null });
}());
