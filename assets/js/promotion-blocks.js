(function () {
	'use strict';

	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { Button, TextControl, TextareaControl } = wp.components;
	const { createElement } = wp.element;
	const { useSelect } = wp.data;
	const { dispatch } = wp.data;

	function usePromotionMeta(key, fallback) {
		const meta = useSelect(select => select('core/editor').getEditedPostAttribute('meta') || {}, []);
		const value = meta[key];
		const data = value === undefined ? fallback : value;
		const setValue = next => dispatch('core/editor').editPost({ meta: Object.assign({}, meta, { [key]: next }) });
		return [data, setValue];
	}

	function StepsEdit() {
		const [steps, setSteps] = usePromotionMeta('_calymyk_promotion_steps', []);
		const update = (index, key, value) => {
			const next = steps.slice(); next[index] = Object.assign({}, next[index], { [key]: value }); setSteps(next);
		};
		return createElement('div', useBlockProps({ className: 'cm-promotion-editor-block cm-promotion-steps-editor' }),
			createElement('p', { className: 'cm-eyebrow' }, 'NAWIGATOR KROKÓW'),
			steps.map((step, index) => {
				const fields = step.fields || [];
				const updateField = (fi, key, value) => { const ns=steps.slice(), nf=fields.slice(); nf[fi]=Object.assign({},nf[fi],{[key]:value}); ns[index]=Object.assign({},ns[index],{fields:nf}); setSteps(ns); };
				return createElement('div',{className:'cm-promotion-step-editor',key:index},
					createElement('div',{className:'cm-promotion-step-editor__number'},String(index+1).padStart(2,'0')),
					createElement('div',{className:'cm-promotion-step-editor__fields'},
						createElement(TextControl,{label:'Nazwa kroku',value:step.title||'',onChange:v=>update(index,'title',v)}),
						createElement(TextareaControl,{label:'Opis kroku',value:step.description||'',onChange:v=>update(index,'description',v),rows:3}),
						createElement('div',{className:'cm-promotion-step-editor__content-fields'},
							createElement('strong',null,'Pola z treścią'),
							fields.map((field,fi)=>createElement('div',{className:'cm-promotion-step-editor__content-field',key:fi},
								createElement(TextControl,{label:'Nagłówek pola',value:field.title||'',onChange:v=>updateField(fi,'title',v)}),
								createElement(TextareaControl,{label:'Treść pola',value:field.content||'',onChange:v=>updateField(fi,'content',v),rows:3}),
								createElement(Button,{isDestructive:true,isSmall:true,onClick:()=>{const ns=steps.slice();ns[index]=Object.assign({},ns[index],{fields:fields.filter((_,i)=>i!==fi)});setSteps(ns);}},'Usuń pole')
							)),
							createElement(Button,{variant:'secondary',isSmall:true,onClick:()=>{const ns=steps.slice();ns[index]=Object.assign({},ns[index],{fields:fields.concat([{title:'',content:''}])});setSteps(ns);}},'+ Dodaj pole treści')
						),
						createElement(Button,{isDestructive:true,isSmall:true,onClick:()=>setSteps(steps.filter((_,i)=>i!==index))},'Usuń krok')
					)
				);
			}),
			createElement(Button,{variant:'secondary',onClick:()=>setSteps(steps.concat([{title:'Krok '+(steps.length+1),description:'',fields:[]}]))},'+ Dodaj krok')
		);
	}

	function PrepEdit() {
		const [items,setItems]=usePromotionMeta('_calymyk_promotion_prep',[]);
		return createElement('div',useBlockProps({className:'cm-promotion-editor-block cm-promotion-prep-editor'}),
			createElement('p',{className:'cm-eyebrow'},'PRZYGOTUJ PRZED STARTEM'),
			items.map((item,index)=>createElement(TextControl,{key:index,label:'Element '+(index+1),value:item,onChange:v=>{const next=items.slice();next[index]=v;setItems(next);}})),
			createElement(Button,{variant:'secondary',onClick:()=>setItems(items.concat(['']))},'+ Dodaj element'),
			items.length>0&&createElement(Button,{isDestructive:true,isSmall:true,onClick:()=>setItems(items.slice(0,-1))},'Usuń ostatni element')
		);
	}

	function FaqEdit() {
		const [items,setItems]=usePromotionMeta('_calymyk_promotion_faq',[]);
		const update=(index,key,value)=>{const next=items.slice();next[index]=Object.assign({},next[index],{[key]:value});setItems(next);};
		return createElement('div',useBlockProps({className:'cm-promotion-editor-block cm-promotion-faq-editor'}),
			createElement('p',{className:'cm-eyebrow'},'FAQ'),
			items.map((item,index)=>createElement('div',{className:'cm-promotion-faq-editor__item',key:index},
				createElement(TextControl,{label:'Pytanie',value:item.question||'',onChange:v=>update(index,'question',v)}),
				createElement(TextareaControl,{label:'Odpowiedź',value:item.answer||'',onChange:v=>update(index,'answer',v),rows:3}),
				createElement(Button,{isDestructive:true,isSmall:true,onClick:()=>setItems(items.filter((_,i)=>i!==index))},'Usuń pytanie')
			)),
			createElement(Button,{variant:'secondary',onClick:()=>setItems(items.concat([{question:'',answer:''}]))},'+ Dodaj pytanie')
		);
	}

	function TermsEdit() {
		const [documents,setDocuments]=usePromotionMeta('_calymyk_promotion_documents',[]);
		const update=(index,key,value)=>{const next=documents.slice();next[index]=Object.assign({},next[index],{[key]:value});setDocuments(next);};
		return createElement('div',useBlockProps({className:'cm-promotion-editor-block cm-promotion-terms-editor'}),
			createElement('p',{className:'cm-eyebrow'},'DOKUMENTY PROMOCJI'),
			documents.map((doc,index)=>createElement('div',{className:'cm-promotion-faq-editor__item',key:index},
				createElement(TextControl,{label:'Nazwa dokumentu',value:doc.label||'',onChange:v=>update(index,'label',v)}),
				createElement(TextControl,{label:'Link do dokumentu',type:'url',value:doc.url||'',onChange:v=>update(index,'url',v)}),
				createElement(Button,{isDestructive:true,isSmall:true,onClick:()=>setDocuments(documents.filter((_,i)=>i!==index))},'Usuń dokument')
			)),
			createElement(Button,{variant:'secondary',onClick:()=>setDocuments(documents.concat([{label:'Regulamin promocji',url:''}]))},'+ Dodaj dokument')
		);
	}

	registerBlockType('calymyk/promotion-steps',{title:'Nawigator kroków',icon:'list-view',category:'calymyk',edit:StepsEdit,save:()=>null});
	registerBlockType('calymyk/promotion-prep',{title:'Przygotuj przed startem',icon:'yes-alt',category:'calymyk',edit:PrepEdit,save:()=>null});
	registerBlockType('calymyk/promotion-faq',{title:'FAQ promocji',icon:'editor-help',category:'calymyk',edit:FaqEdit,save:()=>null});
	registerBlockType('calymyk/promotion-terms',{title:'Dokumenty promocji',icon:'admin-links',category:'calymyk',edit:TermsEdit,save:()=>null});
}());
