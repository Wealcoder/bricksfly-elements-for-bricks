#!/usr/bin/env node
/**
 * Elementor → Bricks: "02 - section" (services: big "service" word, highlighted title, 4 service cards, CTA).
 * Source: 01_elementor/input-templates/02 - section/elementor-7345-2026-09-27.json (+ screenshot.png)
 * Output: 01_elementor/output-templates/02-section/02-section-bricks.json
 *
 * Bricks native + Bricksfly FREE only. Every element name / setting key verified with tools/lookup.mjs.
 * Elementor breakpoints → guideline keys (same widths):
 *   laptop → laptop · tablet_extra → tablet_landscape · tablet → tablet_portrait
 *   mobile_extra → mobile_landscape · mobile → mobile_portrait
 * Elementor defaults reproduced explicitly: container gap 20px, child containers width 100% on mobile,
 * containers position: relative, container background/border transition 0.3s.
 *
 * Usage: node designs/elementor-02-section/build.mjs
 */

import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const outFile = resolve(here, '../../01_elementor/output-templates/02-section/02-section-bricks.json');

// --- Tokens (Elementor JSON; kit-inherited colors sampled from the screenshot) -------------
const C = {
	purple: '#5C2EDE',
	yellow: '#FFC300',
	dark: '#0F0629',
	black: '#000000', // card titles inherit the kit heading color (sampled)
	body: '#494552',
	listText: '#555960', // icon-list text inherits the kit text color (sampled)
	stroke: '#CECDD1',
	white: '#FFFFFF',
	hoverBorder: '#F2F3F5',
	transparent: 'rgba(2, 1, 1, 0)', // Elementor #02010100
	purpleClear: 'rgba(92, 46, 222, 0)', // Elementor #5C2EDE00
};
const FONT = 'Sora';
const NUMBER_FONT = 'Douglas Wolves'; // custom font on the Elementor site — must be uploaded in Bricks
const color = (raw) => ({ raw });
const img = (url) => ({ url, external: true, filename: url.split('/').pop() });
const ASSETS = 'https://templates.animation-addons.com/agivo-free/wp-content/uploads/sites/176/2025/05';
// Highlight underline: the JSON sets the highlight background (position/size) but carries no image —
// the screenshot shows the same yellow swoosh as the hero section, so that file is reused.
const UNDERLINE = 'https://crowdytheme.com/assets/wp-content/uploads/2025/05/Vector-31.webp';

// --- Element helpers ---------------------------------------------------------------------
let seq = 0;
const content = [];
function id() {
	seq += 1;
	let n = 1800000000 + seq * 7919;
	let s = '';
	while (s.length < 6) {
		s += 'abcdefghijklmnopqrstuvwxyz0123456789'[n % 36];
		n = Math.floor(n / 36);
	}
	return s;
}
function el(name, settings = {}, children = [], label) {
	const eid = id();
	const node = { id: eid, name, parent: 0, children, settings };
	if (label) node.label = label;
	content.push(node);
	for (const cid of children) content.find((e) => e.id === cid).parent = eid;
	return eid;
}
const sides = (v) => ({ top: v, right: v, bottom: v, left: v });
const pad = (top, right, bottom, left) => ({ top, right, bottom, left });

// Native SVG element: `fill` recolors it like Elementor's icon color. Bricks' importer downloads `file`
// SVGs into the Media Library when "Import images" is ticked (needs an `id` + SVG upload permission).
const svg = (file, fileId, size, fill, label) =>
	// 7px bottom margin = the line box Elementor's inline-block icon wrapper adds under the icon (measured).
	el('svg', { file: { id: fileId, url: `${ASSETS}/${file}`, filename: file }, width: String(size), height: String(size), fill: color(fill), _alignSelf: 'flex-start', _margin: { bottom: '7' } }, [], label);

// Elementor wcf--button (rollover-left) → Bricksfly Button Pro, Default style + Rollover Left hover
const button = (text, url, style) =>
	el('aab-button-pro', {
		btnStyle: 'base-default',
		btnHoverVariant: 'rollover-left',
		btnText: text,
		btnLink: { type: 'external', url },
		btnBorder: { width: sides('1'), style: 'solid', color: color(C.purple), radius: sides('33') },
		...style,
	});

// --- Sections ------------------------------------------------------------------------------
function heading() {
	// Background word "service": gradient-clipped text (Elementor custom CSS) → native Bricks text gradient
	const word = el('heading', {
		tag: 'h2',
		text: 'service',
		_typography: { 'font-family': FONT, 'font-size': '330', 'font-weight': '800', 'text-transform': 'capitalize', 'font-style': 'normal', 'line-height': '0.9' },
		'_typography:laptop': { 'font-size': '300' },
		'_typography:tablet_landscape': { 'font-size': '260' },
		'_typography:tablet_portrait': { 'font-size': '200' },
		'_typography:mobile_landscape': { 'font-size': '170' },
		'_typography:mobile_portrait': { 'font-size': '90', 'line-height': '1.1' },
		_gradient: {
			applyTo: 'text',
			gradientType: 'linear',
			angle: '90',
			colors: [
				{ color: color('rgba(240, 243, 250, 0.02)'), stop: '0' },
				{ color: color('#F0F3FA'), stop: '56' },
				{ color: color('rgba(240, 243, 250, 0.02)'), stop: '100' },
			],
		},
		'_margin:mobile_landscape': { top: '2' },
	}, [], 'Background word');

	// "We offer a wide range of digital [services]" → h2 with spans (highlight styled natively)
	const title = el(
		'block',
		{
			tag: 'custom',
			customTag: 'h2',
			_display: 'block',
			_width: '100%',
			_typography: { 'font-family': FONT, 'font-size': '56', 'font-weight': '500', 'text-transform': 'capitalize', 'font-style': 'normal', 'line-height': '1.2', 'letter-spacing': '-0.2px', color: color(C.dark), 'text-align': 'center' },
			'_typography:mobile_landscape': { 'font-size': '36' },
			'_typography:mobile_portrait': { 'font-size': '30' },
		},
		[
			el('text-basic', { tag: 'span', text: 'We offer a wide range of digital ' }, [], 'Title'),
			el(
				'text-basic',
				{
					tag: 'span',
					text: 'services',
					_typography: { color: color(C.purple), 'font-weight': '700', 'line-height': '1.1' },
					_background: { image: img(UNDERLINE), position: 'custom', positionX: '1px', positionY: '86%', repeat: 'no-repeat', size: 'auto' },
					'_background:tablet_portrait': { position: 'custom', positionX: '1px', positionY: '97%' },
					'_background:mobile_portrait': { position: 'custom', positionX: '1px', positionY: '98%' },
				},
				[],
				'Highlight',
			),
		],
		'Title (h2)',
	);

	const intro = el('text', {
		text: '<p>where vision meets identity, empowering brands for bold tomorrow. Et auctor ac sed tincidunt fames.</p>',
		_typography: { 'font-family': FONT, 'font-size': '16', 'font-weight': '400', 'text-transform': 'capitalize', 'font-style': 'normal', 'line-height': '1.6', color: color(C.body), 'text-align': 'center' },
		_width: '55%',
		'_width:laptop': '65%',
		'_width:tablet_landscape': '75%',
		'_width:tablet_portrait': '87%',
		'_width:mobile_landscape': '98%',
		'_width:mobile_portrait': '100%',
	});

	const titleWrap = el(
		'block',
		{
			_width: '67%',
			'_width:tablet_landscape': '80%',
			'_width:mobile_landscape': '79%',
			'_width:mobile_portrait': '100%',
			_justifyContent: 'center',
			_alignItems: 'center',
			_alignSelf: 'center',
			_rowGap: '20',
			_margin: { top: '-263' },
			'_margin:mobile_landscape': { top: '-212' },
			'_margin:mobile_portrait': { top: '-136' },
			_position: 'relative',
		},
		[title, intro],
		'Title container',
	);
	return [word, titleWrap];
}

const CARDS = [
	{ n: '01', title: 'Researching Products', icon: ['icon-01.svg', 676], bg: '#E6F8FE', width: { '': '47%', laptop: '48%', tablet_landscape: '48%', mobile_landscape: '47%' }, titleHover: true, items: ['ideation & evaluation', 'UX Review', 'research & development', 'Scoping Sessions'], listGapMobile: false },
	{ n: '02', title: 'Designing Products', icon: ['design.svg', 717], bg: '#FAF7E6', width: { '': '48%', laptop: '48%', mobile_landscape: '47%' }, titleHover: true, items: ['UX Design', 'UI Design', 'UX writing', 'mobile app design', 'Branding'], listGapMobile: true },
	{ n: '03', title: 'Developing Products', icon: ['icon-02.svg', 722], bg: '#FFEDEA', width: { '': '47%', laptop: '48%', mobile_landscape: '47%' }, titleHover: false, items: ['web development', 'software development', 'CMS development', 'Mobile app Development', 'Non-code development'], listGapMobile: true, margin: { '': '-42', mobile_landscape: '-14', mobile_portrait: '0' } },
	{ n: '04', title: 'Products Growth & care', icon: ['Growth.svg', 735], bg: '#ECF5E8', width: { '': '47%', laptop: '48%', mobile_landscape: '47%' }, titleHover: true, items: ['Search Engine Optimization (SEO)', 'Content Creation', 'Content Marketing', 'Social media marketing'], listGapMobile: true },
];

function card(c) {
	const number = el('aab-animated-heading', {
		heading_text: c.n,
		heading_tag: 'h2',
		animation_type: 'none', // widget default is "reveal"
		heading_typo: { 'font-family': NUMBER_FONT, 'font-size': '59', 'font-weight': '400', 'text-transform': 'uppercase', 'line-height': '1.1' },
		heading_color: color(C.transparent),
		stroke_color: color(C.stroke),
		stroke_width: '1px',
		_position: 'absolute',
		_top: '51px',
		_right: '46px',
	}, [], `Number ${c.n}`);

	const title = el('heading', {
		tag: 'h2',
		text: c.title,
		_typography: { 'font-family': FONT, 'font-size': '32', 'font-weight': '500', 'text-transform': 'capitalize', 'font-style': 'normal', 'line-height': '1.3', color: color(C.black) },
		'_typography:mobile_portrait': { 'font-size': '24', 'line-height': '1.2' },
		...(c.titleHover ? { '_typography:hover': { color: color(C.purple) } } : {}),
	});

	const text = el('text', {
		text: '<p>We always extend our helping hands to allow our clients to attain the desired results.</p>',
		_typography: { 'font-family': FONT, 'font-size': '16', 'font-weight': '400', 'text-transform': 'capitalize', 'line-height': '1.6', color: color(C.body) },
	});

	// Elementor Icon List (default fa-check icon, 14px) → Bricks List
	const list = el('list', {
		items: c.items.map((title, i) => ({ id: `${c.n}l${i}`.padEnd(6, 'x'), title })),
		icon: { library: 'fontawesomeSolid', icon: 'fas fa-check' },
		iconSize: '14px',
		iconColor: color(C.purple),
		separatorDisable: true,
		itemJustifyContent: 'flex-start',
		itemMargin: { bottom: '16' },
		...(c.listGapMobile ? { 'itemMargin:mobile_landscape': { bottom: '12' } } : {}),
		// Bricks' "#id li" margin beats its own "li:last-child { margin-bottom: 0 }" → cancel the last gap
		_margin: { bottom: '-16' },
		...(c.listGapMobile ? { '_margin:mobile_landscape': { bottom: '-12' } } : {}),
		titleMargin: pad('0', '0', '0', '8'),
		titleTypography: { 'font-family': FONT, 'font-size': '16', 'font-weight': '400', 'text-transform': 'capitalize', 'font-style': 'normal', 'line-height': '1.5', color: color(C.listText) },
		'titleTypography:hover': { color: color(C.purple) },
	});

	const btn = button('view details', 'https://templates.animation-addons.com/agivo-free/single-service/', {
		btnTypo: { 'font-family': FONT, 'font-size': '14', 'font-weight': '600', 'text-transform': 'capitalize', 'line-height': '1.2', 'letter-spacing': '0.5px' },
		btnColor: color(C.purple),
		btnBg: { color: color(C.purpleClear) },
		btnPadding: pad('14', '28', '14', '28'),
		'btnPadding:mobile_portrait': pad('12', '24', '12', '24'),
		btnHColor: color(C.white),
		btnHBorder: color(C.purple),
		btnRevealColor: color(C.purple),
	});

	const settings = {
		_rowGap: '24',
		_background: { color: color(c.bg) },
		'_background:hover': { color: color(C.white) },
		_border: { width: sides('1'), style: 'solid', color: color(C.transparent), radius: sides('16') },
		'_border:hover': { width: sides('1'), style: 'solid', color: color(C.hoverBorder), radius: sides('16') },
		_padding: sides('64'),
		'_padding:tablet_landscape': sides('50'),
		'_padding:tablet_portrait': sides('45'),
		'_padding:mobile_landscape': sides('40'),
		'_padding:mobile_portrait': sides('30'),
		_position: 'relative',
		_cssTransition: 'background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s',
		'_width:mobile_portrait': '100%',
	};
	for (const [bp, w] of Object.entries(c.width)) settings[bp ? `_width:${bp}` : '_width'] = w;
	for (const [bp, m] of Object.entries(c.margin || {})) settings[bp ? `_margin:${bp}` : '_margin'] = { top: m };

	const icon = svg(c.icon[0], c.icon[1], 70, C.dark, 'Icon (svg)');
	// Same child order as Elementor (card 1 has the number right after the icon, the others last)
	const kids = c.n === '01' ? [icon, number, title, text, list, btn] : [icon, title, text, list, btn, number];
	return el('block', settings, kids, `Service ${c.n}`);
}

function build() {
	const [word, titleWrap] = heading();

	const cards = el(
		'block',
		{
			_direction: 'row',
			_flexWrap: 'wrap',
			_justifyContent: 'space-between',
			_alignItems: 'flex-start',
			_columnGap: '64',
			_rowGap: '64',
			'_columnGap:laptop': '45',
			'_rowGap:laptop': '45',
			'_columnGap:tablet_landscape': '39',
			'_rowGap:tablet_landscape': '39',
			'_columnGap:tablet_portrait': '32',
			'_rowGap:tablet_portrait': '32',
			'_columnGap:mobile_landscape': '39',
			'_rowGap:mobile_landscape': '39',
			_position: 'relative',
		},
		CARDS.map(card),
		'Services',
	);

	const cta = button('Browse all services', 'https://templates.animation-addons.com/agivo-free/service/', {
		_alignSelf: 'center', // Elementor btn_align center (btnAlign only justifies inside the shrink-wrapped root)
		btnTypo: { 'font-family': FONT, 'font-size': '16', 'font-weight': '700', 'text-transform': 'capitalize', 'line-height': '1.2', 'letter-spacing': '0.5px' },
		btnColor: color(C.white),
		btnBg: { color: color(C.purple) },
		btnHColor: color(C.dark),
		btnHBorder: color(C.yellow),
		btnRevealColor: color(C.yellow),
	});

	const container = el(
		'container',
		{ _width: '100%', _widthMax: '1296px', _rowGap: '64', '_rowGap:mobile_portrait': '35', _position: 'relative' },
		[word, titleWrap, cards, cta],
	);

	el(
		'section',
		{
			_padding: pad('103', '20', '120', '20'),
			'_padding:laptop': pad('55', '20', '120', '20'),
			'_padding:tablet_portrait': pad('107', '20', '100', '20'),
			'_padding:mobile_landscape': pad('75', '20', '90', '20'),
			'_padding:mobile_portrait': pad('59', '15', '60', '15'),
		},
		[container],
		'Services Section',
	);
}

build();

const byId = new Map(content.map((e) => [e.id, e]));
const ordered = [];
const walk = (eid) => {
	const e = byId.get(eid);
	ordered.push(e);
	e.children.forEach(walk);
};
content.filter((e) => e.parent === 0).forEach((e) => walk(e.id));

const template = { title: 'Services Section (from Elementor)', type: 'content', templateType: 'content', tags: [], bundles: [], content: ordered };
mkdirSync(dirname(outFile), { recursive: true });
writeFileSync(outFile, JSON.stringify(template, null, 2));
console.log(`Wrote ${outFile} (${ordered.length} elements)`);
