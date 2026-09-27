#!/usr/bin/env node
/**
 * Elementor → Bricks: "03-steps" (Our Working Process: highlighted title, text, 3 step cards joined by arrows).
 * Source: 01_elementor/input-templates/03-steps/elementor-7349-2026-09-27.json (+ screenshot.png)
 * Output: 01_elementor/output-templates/03-steps/03-steps-bricks.json
 *
 * Bricks native + Bricksfly FREE only. Every element name / setting key verified with tools/lookup.mjs.
 * Elementor breakpoints → guideline keys (same widths):
 *   laptop → laptop · tablet_extra → tablet_landscape · tablet → tablet_portrait
 *   mobile_extra → mobile_landscape · mobile → mobile_portrait
 * Elementor defaults reproduced explicitly: container gap 20px, child containers width 100% + wrap on mobile,
 * containers position: relative, icon widget line box (7px) under SVG icons.
 *
 * Not reproduced (Elementor custom CSS, needs approval): column hover → card purple, texts white, outlined
 * "step" swap. The hidden (opacity 0) "#title" duplicates that only served this hover are left out.
 *
 * Usage: node designs/elementor-03-steps/build.mjs
 */

import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const outFile = resolve(here, '../../01_elementor/output-templates/03-steps/03-steps-bricks.json');

const C = {
	purple: '#5C2EDE',
	dark: '#0F0629',
	body: '#494552',
	name: '#2C2933',
	line: '#E6E9F0',
	white: '#FFFFFF',
	transparent: 'rgba(2, 1, 1, 0)', // Elementor #02010100
};
const FONT = 'Sora';
const color = (raw) => ({ raw });
const img = (url) => ({ url, external: true, filename: url.split('/').pop() });
const ASSETS = 'https://templates.animation-addons.com/agivo-free/wp-content/uploads/sites/176/2025/05';
// The JSON sets the highlight background position/size but carries no image; the screenshot shows the
// same yellow swoosh used in the other sections.
const UNDERLINE = 'https://crowdytheme.com/assets/wp-content/uploads/2025/05/Vector-31.webp';

// --- Element helpers ---------------------------------------------------------------------
let seq = 0;
const content = [];
function id() {
	seq += 1;
	let n = 1900000000 + seq * 7919;
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

// Native SVG element; Bricks' importer downloads `file` SVGs when "Import images" is ticked.
const svg = (file, fileId, size, extra = {}, label) =>
	el('svg', { file: { id: fileId, url: `${ASSETS}/${file}`, filename: file }, width: String(size), height: String(size), ...extra }, [], label);

// --- Build ------------------------------------------------------------------------------
function header() {
	const title = el(
		'block',
		{
			tag: 'custom',
			customTag: 'h2',
			_display: 'block',
			_width: '100%',
			_typography: { 'font-family': FONT, 'font-size': '56', 'font-weight': '500', 'text-transform': 'capitalize', 'line-height': '1.2', 'letter-spacing': '-0.25px', color: color(C.dark), 'text-align': 'center' },
			'_typography:mobile_landscape': { 'font-size': '36' },
			'_typography:mobile_portrait': { 'font-size': '30' },
		},
		[
			el('text-basic', { tag: 'span', text: 'Our ' }, [], 'Title'),
			el(
				'text-basic',
				{
					tag: 'span',
					text: 'Working',
					_typography: { color: color(C.purple), 'font-weight': '700' },
					_background: { image: img(UNDERLINE), position: 'custom', positionX: '29%', positionY: '94%', repeat: 'no-repeat', size: 'auto' },
					'_background:tablet_portrait': { position: 'custom', positionX: '0%', positionY: '98%' },
					'_background:mobile_portrait': { position: 'custom', positionX: '0%', positionY: '98%', size: 'contain' },
				},
				[],
				'Highlight',
			),
			el('text-basic', { tag: 'span', text: ' Process' }, [], 'Title end'),
		],
		'Title (h2)',
	);

	const intro = el('text', {
		text: '<p>where vision meets identity, empowering brands for bold tomorrow. Et auctor ac sed tincidunt fames.</p>',
		_typography: { 'font-family': FONT, 'font-size': '16', 'font-weight': '400', 'text-transform': 'capitalize', 'line-height': '1.6', color: color(C.body), 'text-align': 'center' },
		_width: '50%',
		'_width:mobile_landscape': '62%',
		'_width:mobile_portrait': '99%',
	});
	return [title, intro];
}

const STEP_TYPO = { 'font-family': FONT, 'font-size': '64', 'font-weight': '700', 'text-transform': 'capitalize', 'line-height': '1.3' };

function stepOutline(text, extra = {}) {
	// Outlined "step 0X" (transparent fill + 1px stroke) → Bricksfly Animated Heading, animation off
	return el('aab-animated-heading', {
		heading_text: text,
		heading_tag: 'h2',
		animation_type: 'none', // widget default is "reveal"
		text_align: 'center',
		heading_typo: STEP_TYPO,
		'heading_typo:laptop': { 'font-size': '58' },
		'heading_typo:tablet_landscape': { 'font-size': '50' },
		'heading_typo:tablet_portrait': { 'font-size': '46' },
		heading_color: color(C.transparent),
		stroke_color: color(C.line),
		stroke_width: '1px',
		...extra,
	}, [], `Step outline (${text})`);
}

const STEPS = [
	{ n: '01', name: 'get an order', icon: ['12.svg', 804], laptopName: true },
	{ n: '02', name: 'Design &amp; Development', icon: ['12-1.svg', 843], laptopName: true, cardPadTop: '91', absoluteStep: true },
	{ n: '03', name: 'deliver', icon: ['12-2.svg', 851], laptopName: false },
];

function step(s) {
	// Badge: circle + glyph share one SVG with empty fills. Elementor painted all purple and then the glyph
	// white with custom CSS; the native fill can't tell them apart → purple fill + white stroke outlines
	// the glyph (exact look needs the approved one-line CSS, see notes).
	const badge = svg(s.icon[0], s.icon[1], 80, {
		fill: color(C.purple),
		stroke: color(C.white),
		strokeWidth: '1.5',
		_alignSelf: 'center',
		_zIndex: s.n === '01' ? '5' : '2',
		_margin: { bottom: '7' },
	}, 'Badge (svg)');

	const outline = s.absoluteStep
		? stepOutline(`step ${s.n}`, { _position: 'absolute', _left: '17%', _top: '16%', _zIndex: '0' })
		: stepOutline(`step ${s.n}`, { _width: '100%', _margin: { bottom: '-42' } });

	const name = el('text', {
		text: `<p>${s.name}</p>`,
		_typography: { 'font-family': FONT, 'font-size': '24', 'font-weight': '400', 'text-transform': 'capitalize', 'font-style': 'normal', 'line-height': '1.5', color: color(C.name), 'text-align': 'center' },
		...(s.laptopName ? { '_typography:laptop': { 'font-size': '20' } } : {}),
		'_typography:tablet_landscape': { 'font-size': '18' },
		_margin: { top: '-34' },
		_position: 'relative',
	}, [], 'Step name');

	const desc = el('text', {
		text: '<p>Defining the strategies you will use Creating a distinct identity for your those goals or tasks.</p>',
		_typography: { 'font-family': FONT, 'font-size': '16', 'font-weight': '400', 'text-transform': 'capitalize', 'font-style': 'normal', 'line-height': '1.6', color: color(C.body), 'text-align': 'center' },
	}, [], 'Step text');

	const card = el('block', {
		_background: { color: color(C.white) },
		_border: { width: sides('1'), style: 'solid', color: color(C.line), radius: sides('18') },
		_boxShadow: { values: { offsetX: '0', offsetY: '16', blur: '32', spread: '0' }, color: color('rgba(9, 41, 30, 0.1)') },
		_padding: s.cardPadTop ? pad(s.cardPadTop, '32', '32', '32') : sides('32'),
		_overflow: 'hidden',
		_rowGap: '20',
		_alignItems: 'stretch', // Elementor column containers stretch their widgets (Bricks blocks don't by default)
		_position: 'relative',
	}, [outline, name, desc], `Card ${s.n}`);

	return el('block', {
		_width: '33.33%',
		'_width:mobile_portrait': '100%',
		_rowGap: '50',
		'_rowGap:mobile_portrait': '30',
		...(s.n === '01' ? { '_justifyContent:tablet_landscape': 'space-evenly' } : {}),
		_position: 'relative',
	}, [badge, card], `Step ${s.n}`);
}

// Arrow between steps (Elementor: 60px icon, padding-bottom 64, hidden on mobile)
const arrow = () =>
	svg('Group-427320030.svg', 836, 60, { fill: color(C.purple), _margin: { bottom: '64' }, _flexShrink: '0', '_display:mobile_portrait': 'none' }, 'Arrow (svg)');

function build() {
	const [title, intro] = header();
	const row = el(
		'block',
		{
			_width: '100%',
			_direction: 'row',
			_alignItems: 'flex-end',
			_flexWrap: 'nowrap',
			'_flexWrap:mobile_portrait': 'wrap',
			_columnGap: '20',
			_rowGap: '20',
			'_justifyContent:tablet_landscape': 'flex-end',
			'_alignItems:tablet_landscape': 'flex-end',
			'_columnGap:tablet_landscape': '10',
			'_rowGap:tablet_landscape': '10',
			'_columnGap:mobile_portrait': '20',
			'_rowGap:mobile_portrait': '20',
			_position: 'relative',
		},
		[step(STEPS[0]), arrow(), step(STEPS[1]), arrow(), step(STEPS[2])],
		'Steps',
	);

	const container = el(
		'container',
		{ _width: '100%', _widthMax: '1296px', _alignItems: 'center', _rowGap: '24', _position: 'relative' },
		[title, intro, row],
	);

	el(
		'section',
		{
			// Elementor custom CSS "background: linear-gradient(90deg, #F5F8FF 0%, #FFFCED 100%)" → native gradient
			_gradient: { applyTo: 'background', gradientType: 'linear', angle: '90', colors: [{ color: color('#F5F8FF'), stop: '0' }, { color: color('#FFFCED'), stop: '100' }] },
			_padding: pad('100', '20', '122', '20'),
			'_padding:tablet_landscape': pad('109', '20', '120', '20'),
			'_padding:tablet_portrait': pad('90', '20', '100', '20'),
			'_padding:mobile_landscape': pad('80', '20', '89', '20'),
			'_padding:mobile_portrait': pad('55', '15', '69', '15'),
		},
		[container],
		'Steps Section',
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

const template = { title: 'Working Process (from Elementor)', type: 'content', templateType: 'content', tags: [], bundles: [], content: ordered };
mkdirSync(dirname(outFile), { recursive: true });
writeFileSync(outFile, JSON.stringify(template, null, 2));
console.log(`Wrote ${outFile} (${ordered.length} elements)`);
