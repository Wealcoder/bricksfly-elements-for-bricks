#!/usr/bin/env node
/**
 * Generates output/faq-page.json (Bricks template export) from faq.png.
 * Every element name / setting key used here was verified with tools/lookup.mjs.
 *
 * Usage: node designs/faq/build.mjs
 */

import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const outFile = resolve(here, '../../output/faq-page.json');

// --- Design tokens (sampled from faq.png) ----------------------------------
const C = {
	page: '#f8f2e9',
	card: '#fffaf7',
	cardBorder: '#fef2eb',
	navPill: '#f5efe7',
	cta: '#ede2d6',
	dark: '#18181b',
	body: '#52525c',
	nav: '#323232',
	orange: '#f67d32',
	white: '#ffffff',
};
const SERIF = 'Source Serif 4';
const SANS = 'Figtree';
const color = (hex) => ({ raw: hex });

// --- Element helpers --------------------------------------------------------
let seq = 0;
const content = [];
function id() {
	// Deterministic 6-char lowercase alphanumeric IDs.
	seq += 1;
	let n = 1500000000 + seq * 7919;
	let s = '';
	while (s.length < 6) {
		s += 'abcdefghijklmnopqrstuvwxyz0123456789'[n % 36];
		n = Math.floor(n / 36);
	}
	return s;
}

/** el(name, settings, children?, label?) → returns id; children are ids created before. */
function el(name, settings = {}, children = [], label) {
	const eid = id();
	const node = { id: eid, name, parent: 0, children, settings };
	if (label) node.label = label;
	content.push(node);
	for (const cid of children) content.find((e) => e.id === cid).parent = eid;
	return eid;
}

const placeholder = (w, h, text) => ({
	url: `https://placehold.co/${w}x${h}/png?text=${encodeURIComponent(text)}`,
	external: true,
	filename: `${text.toLowerCase().replace(/\W+/g, '-')}.png`,
});

const container = (children, extra = {}) =>
	el('container', { _width: '100%', _widthMax: '1320px', ...extra }, children);

const section = (children, extra = {}, label) =>
	el('section', { _padding: { right: '24', left: '24' }, ...extra }, children, label);

const pillButton = (text, bg, fg, label) =>
	el(
		'button',
		{
			text,
			circle: true,
			size: 'lg',
			link: { type: 'external', url: '#' },
			_background: { color: color(bg) },
			_typography: { 'font-family': SANS, 'font-weight': '500', 'font-size': '18', color: color(fg) },
			_padding: { top: '12', right: '30', bottom: '12', left: '30' },
		},
		[],
		label,
	);

// --- 1. Header ----------------------------------------------------------------
function header() {
	const logo = el('image', { image: placeholder(144, 40, 'Logo'), altText: 'Animation Addons', _width: '144px' }, [], 'Logo');

	const links = ['Solution', 'Feature', 'Preset Library', 'Pricing', 'FAQ'].map((text) =>
		el('text-link', { text, link: { type: 'external', url: '#' } }, [], 'Nav link'),
	);
	const closeToggle = el('toggle', { _hidden: { _cssClasses: 'brx-toggle-div' } }, [], 'Toggle (Close: Mobile)');
	const navItems = el(
		'block',
		{
			tag: 'ul',
			_hidden: { _cssClasses: 'brx-nav-nested-items' },
			_background: { color: color(C.navPill) },
			_border: {
				width: { top: '1', right: '1', bottom: '1', left: '1' },
				style: 'solid',
				color: color(C.white),
				radius: { top: '100', right: '100', bottom: '100', left: '100' },
			},
			_padding: { top: '8', right: '24', bottom: '8', left: '24' },
			'_background:tablet_portrait': { color: color(C.page) },
			'_border:tablet_portrait': { width: { top: '0', right: '0', bottom: '0', left: '0' }, radius: { top: '0', right: '0', bottom: '0', left: '0' } },
		},
		[...links, closeToggle],
		'Nav items',
	);
	const openToggle = el('toggle', {}, [], 'Toggle (Open: Mobile)');
	const nav = el(
		'nav-nested',
		{
			gap: '40',
			mobileMenu: 'tablet_portrait',
			mobileMenuBackgroundColor: color(C.page),
			itemTypography: { 'font-family': SANS, 'font-weight': '400', 'font-size': '18', color: color(C.nav) },
			'itemTypography:tablet_portrait': { 'font-size': '24' },
			'_order:tablet_portrait': '3',
		},
		[navItems, openToggle],
		'Nav',
	);

	const avatar = el(
		'image',
		{
			image: placeholder(40, 40, 'Avatar'),
			altText: 'Account',
			_width: '40px',
			_height: '40px',
			_objectFit: 'cover',
			_border: { radius: { top: '50%', right: '50%', bottom: '50%', left: '50%' } },
		},
		[],
		'Avatar',
	);
	const cta = el(
		'button',
		{
			text: 'Get 25% Off',
			circle: true,
			link: { type: 'external', url: '#' },
			_background: { color: color(C.orange) },
			_typography: { 'font-family': SANS, 'font-weight': '500', 'font-size': '17', 'line-height': '1.5', color: color(C.white) },
			_padding: { top: '7', right: '22', bottom: '7', left: '22' },
			'_display:mobile_portrait': 'none',
		},
		[],
		'Header CTA',
	);
	const right = el(
		'div',
		{ _display: 'flex', _direction: 'row', _alignItems: 'center', _columnGap: '10', '_order:tablet_portrait': '2' },
		[avatar, cta],
		'Header actions',
	);

	return section(
		[container([logo, nav, right], { _direction: 'row', _justifyContent: 'space-between', _alignItems: 'center', '_columnGap:tablet_portrait': '16' })],
		{ _padding: { top: '16', right: '24', bottom: '16', left: '24' }, _background: { color: color(C.page) } },
		'Header',
	);
}

// --- 2. FAQ -------------------------------------------------------------------
const FAQ = [
	['How many sections does BricksFly include?', 'Answer placeholder — replace with the real answer.'],
	[
		'How do I add a BricksFly section to my site?',
		'hree ways: click Live Copy and paste it straight onto your Bricks canvas, download it as JSON and import it, or import it directly. Every section works with all three methods.',
	],
	['Are the sections animation-ready?', 'Answer placeholder — replace with the real answer.'],
	['Do I need Bricks Builder to use the sections?', 'Answer placeholder — replace with the real answer.'],
	['Can I customize the sections?', 'Answer placeholder — replace with the real answer.'],
	["What's the difference between a section and a template?", 'Answer placeholder — replace with the real answer.'],
	['Will adding sections slow down my site?', 'Answer placeholder — replace with the real answer.'],
];

function accordion() {
	const stateIcon = (icon, state) =>
		el(
			'icon',
			{
				icon: { icon, library: 'ionicons' },
				iconSize: '24',
				iconColor: color(C.dark),
				_flexShrink: '0',
				isAccordionIcon: true,
				accordionTitleIconState: state,
			},
			[],
			state === 'collapsed' ? 'Icon (collapsed)' : 'Icon (expanded)',
		);

	const items = FAQ.map(([q, a]) => {
		const title = el(
			'block',
			{ _display: 'flex', _flexWrap: 'nowrap', _alignItems: 'center', _direction: 'row', _justifyContent: 'space-between', _columnGap: '16', _hidden: { _cssClasses: 'accordion-title-wrapper' } },
			[el('heading', { text: q, tag: 'h3' }), stateIcon('ion-md-add', 'collapsed'), stateIcon('ion-md-remove', 'expanded')],
			'Title',
		);
		const body = el('block', { _hidden: { _cssClasses: 'accordion-content-wrapper' } }, [el('text-basic', { text: a, tag: 'p' })], 'Content');
		return el(
			'block',
			{
				_background: { color: color(C.card) },
				_border: {
					width: { top: '1', right: '1', bottom: '1', left: '1' },
					style: 'solid',
					color: color(C.cardBorder),
					radius: { top: '10', right: '10', bottom: '10', left: '10' },
				},
				_margin: { bottom: '10' },
			},
			[title, body],
			'Item',
		);
	});

	return el(
		'accordion-nested',
		{
			expandItem: '1',
			faqSchema: true,
			titleHeight: '62px',
			titlePadding: { top: '14', right: '24', bottom: '14', left: '24' },
			titleTypography: { 'font-family': SERIF, 'font-weight': '400', 'font-size': '24', 'line-height': '1.3', color: color(C.dark) },
			'titleTypography:mobile_portrait': { 'font-size': '19' },
			contentPadding: { top: '0', right: '24', bottom: '20', left: '24' },
			contentTypography: { 'font-family': SANS, 'font-size': '17', 'line-height': '1.7', color: color(C.body) },
		},
		items,
	);
}

function faqSection() {
	const label = el(
		'div',
		{ _display: 'flex', _direction: 'row', _alignItems: 'center', _columnGap: '8' },
		[
			el('icon', { icon: { icon: 'ion-ios-help-circle', library: 'ionicons' }, iconSize: '20', iconColor: color(C.orange) }),
			el('text-basic', { text: 'FAQ', _typography: { 'font-family': SANS, 'font-weight': '500', 'font-size': '16', color: color(C.orange) } }),
		],
		'Eyebrow',
	);
	const heading = el('heading', {
		text: 'Frequently Asked Question',
		tag: 'h2',
		_widthMax: '660px',
		_typography: { 'font-family': SERIF, 'font-weight': '400', 'font-size': '76', 'line-height': '1', 'letter-spacing': '-1px', color: color(C.dark) },
		'_typography:tablet_portrait': { 'font-size': '60' },
		'_typography:mobile_portrait': { 'font-size': '44' },
	});
	const left = el('div', { _display: 'flex', _direction: 'column', _rowGap: '16' }, [label, heading], 'Intro left');
	const desc = el('text-basic', {
		text: 'Find answers to questions about features, pricing, and how to get most out of the platform so you can move forward with confidence and clarity.',
		tag: 'p',
		_widthMax: '480px',
		_typography: { 'font-family': SANS, 'font-size': '18', 'line-height': '1.6', color: color(C.body) },
	});
	const intro = el(
		'block',
		{
			_direction: 'row',
			_justifyContent: 'space-between',
			_alignItems: 'flex-end',
			_columnGap: '40',
			'_direction:tablet_portrait': 'column',
			'_alignItems:tablet_portrait': 'flex-start',
			'_rowGap:tablet_portrait': '24',
		},
		[left, desc],
		'Intro',
	);

	// Tabs (Nestable): categories on the left, one accordion per category on the right.
	const categories = ['General', 'General', 'General', 'General'];
	const chevron = () => el('icon', { icon: { icon: 'ion-ios-arrow-forward', library: 'ionicons' }, iconSize: '22', iconColor: color(C.dark) });
	const titles = categories.map((cat) =>
		el(
			'div',
			{ _display: 'flex', _direction: 'row', _justifyContent: 'space-between', _alignItems: 'center', _hidden: { _cssClasses: 'tab-title' } },
			[el('text-basic', { text: cat }), chevron()],
			'Title',
		),
	);
	const tabMenu = el(
		'block',
		{
			_direction: 'column',
			_display: 'flex',
			_width: '410px',
			_flexShrink: '0',
			_margin: { right: '88' },
			'_width:tablet_portrait': '100%',
			'_margin:tablet_portrait': { right: '0', bottom: '32' },
			_hidden: { _cssClasses: 'tab-menu' },
		},
		titles,
		'Tab menu',
	);
	const panes = categories.map(() => el('block', { _hidden: { _cssClasses: 'tab-pane' } }, [accordion()], 'Pane'));
	const tabContent = el('block', { _display: 'flex', _flexGrow: '1', _hidden: { _cssClasses: 'tab-content' } }, panes, 'Tab content');

	const tabs = el(
		'tabs-nested',
		{
			direction: 'row',
			'direction:tablet_portrait': 'column',
			titleWidth: '100%',
			titlePadding: { top: '16', right: '20', bottom: '16', left: '24' },
			titleTypography: { 'font-family': SERIF, 'font-weight': '400', 'font-size': '24', color: color(C.dark) },
			titleActiveBackgroundColor: color(C.card),
			titleActiveBorder: { radius: { top: '8', right: '8', bottom: '8', left: '8' } },
			contentPadding: { top: '0', right: '0', bottom: '0', left: '0' },
			contentBorder: { width: { top: '0', right: '0', bottom: '0', left: '0' } },
		},
		[tabMenu, tabContent],
		'FAQ tabs',
	);

	return section(
		[container([intro, tabs], { _direction: 'column', _rowGap: '88', '_rowGap:mobile_portrait': '48' })],
		{
			_padding: { top: '110', right: '24', bottom: '145', left: '24' },
			'_padding:tablet_portrait': { top: '90', bottom: '100' },
			'_padding:mobile_portrait': { top: '60', bottom: '70' },
			_background: { color: color(C.page) },
		},
		'FAQ',
	);
}

// --- 3. CTA -------------------------------------------------------------------
function ctaSection() {
	const heading = el('heading', {
		text: 'Build your next Elementor 4 website with the toolkit made for it.',
		tag: 'h2',
		_widthMax: '1100px',
		_typography: { 'font-family': SERIF, 'font-weight': '400', 'font-size': '58', 'line-height': '1.05', color: color(C.dark), 'text-align': 'center' },
		'_typography:tablet_portrait': { 'font-size': '46' },
		'_typography:mobile_portrait': { 'font-size': '34' },
	});
	const para = el('text-basic', {
		text: 'Get 40+ Atomic Widgets, 20+ Extensions, 30+ Website Templates, 500+ Section Templates, and advanced GSAP animation in one complete toolkit.',
		tag: 'p',
		_widthMax: '780px',
		_margin: { top: '20' },
		_typography: { 'font-family': SANS, 'font-size': '17', 'line-height': '1.7', color: color(C.body), 'text-align': 'center' },
	});
	const buttons = el(
		'div',
		{ _display: 'flex', _direction: 'row', _columnGap: '16', _rowGap: '12', _flexWrap: 'wrap', _justifyContent: 'center', _margin: { top: '36' } },
		[pillButton('Get 25% Early Bird Off', C.orange, C.white, 'Primary CTA'), pillButton('Review The Toolkit', C.white, C.dark, 'Secondary CTA')],
		'Buttons',
	);
	const badges = [
		['Limited to the first 200 Early Bird purchase', 'Limited'],
		['One Time payment', 'Payment'],
		['Lifetime Updates & Support', 'Support'],
		['15-Days Money Back Guarantee', 'Guarantee'],
	].map(([text, icon]) =>
		el(
			'div',
			{ _display: 'flex', _direction: 'row', _alignItems: 'center', _columnGap: '8' },
			[
				el('image', { image: placeholder(48, 48, icon), altText: icon, _width: '24px', _height: '24px' }, [], `${icon} icon`),
				el('text-basic', { text, _typography: { 'font-family': SANS, 'font-size': '15', color: color(C.body) } }),
			],
			'Badge',
		),
	);
	const badgeRow = el(
		'div',
		{ _display: 'flex', _direction: 'row', _flexWrap: 'wrap', _justifyContent: 'center', _columnGap: '16', _rowGap: '12', _margin: { top: '40' } },
		badges,
		'Badges',
	);

	return section(
		[container([heading, para, buttons, badgeRow], { _direction: 'column', _alignItems: 'center' })],
		{
			_padding: { top: '100', right: '24', bottom: '100', left: '24' },
			'_padding:mobile_portrait': { top: '64', bottom: '64' },
			_background: { color: color(C.cta) },
		},
		'CTA',
	);
}

// --- Build --------------------------------------------------------------------
header();
faqSection();
ctaSection();

// Bricks stores elements parent-first; order the flat list by a depth-first walk.
const byId = new Map(content.map((e) => [e.id, e]));
const ordered = [];
const walk = (eid) => {
	const e = byId.get(eid);
	ordered.push(e);
	e.children.forEach(walk);
};
content.filter((e) => e.parent === 0).forEach((e) => walk(e.id));

const template = {
	title: 'FAQ page (Figma POC)',
	type: 'content',
	templateType: 'content',
	tags: [],
	bundles: [],
	content: ordered,
};

mkdirSync(dirname(outFile), { recursive: true });
writeFileSync(outFile, JSON.stringify(template, null, 2));
console.log(`Wrote ${outFile} (${ordered.length} elements)`);
