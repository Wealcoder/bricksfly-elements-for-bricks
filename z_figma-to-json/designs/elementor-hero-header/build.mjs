#!/usr/bin/env node
/**
 * Elementor → Bricks: hero-header.
 * Source: 01_elementor/input-templates/hero-header/elementor-7342-2026-09-27.json (+ .png)
 * Output: 01_elementor/output-templates/hero-header/hero-header-bricks.json
 *
 * Bricks native + Bricksfly FREE only. Every element name / setting key verified with tools/lookup.mjs.
 * Elementor breakpoints → guideline keys (same widths):
 *   laptop → laptop · tablet_extra → tablet_landscape · tablet → tablet_portrait
 *   mobile_extra → mobile_landscape · mobile → mobile_portrait
 * Elementor defaults reproduced explicitly: container gap 20px, mobile (≤767) child containers
 * width 100% + flex-wrap: wrap, containers position: relative.
 *
 * Usage: node designs/elementor-hero-header/build.mjs
 */

import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const outFile = resolve(here, '../../01_elementor/output-templates/hero-header/hero-header-bricks.json');

// --- Tokens (from the Elementor JSON; kit-inherited colors sampled from the PNG) ----------
const C = {
	purple: '#5C2EDE', // JSON value (screenshot renders #7500D7 — see notes)
	yellow: '#FFC300',
	dark: '#0F0629',
	black: '#000000', // heading + client text inherit the kit color (sampled)
	body: '#494552',
	orange: '#FFAD47',
	white: '#FFFFFF',
};
const FONT = 'Sora';
const color = (raw) => ({ raw });
const img = (url, external = true) => ({ url, external, filename: url.split('/').pop() });
const U = {
	bg: 'https://crowdytheme.com/assets/wp-content/uploads/2025/05/Group-427320115-1-2.webp',
	underline: 'https://crowdytheme.com/assets/wp-content/uploads/2025/05/Vector-31.webp',
	globe: 'https://templates.animation-addons.com/agivo-free/wp-content/uploads/sites/176/2025/05/ezgif.com-speed.gif',
	avatars: 'https://crowdytheme.com/assets/wp-content/uploads/2025/05/Group-427320116.webp',
	// The JSON's crowdytheme.com SVG URLs return 404; same files live on the template site.
	watch: 'https://templates.animation-addons.com/agivo-free/wp-content/uploads/sites/176/2025/05/watch-video-1.svg',
	arrow: 'https://templates.animation-addons.com/agivo-free/wp-content/uploads/sites/176/2025/05/Group-427320117.svg',
	sparkle: 'https://templates.animation-addons.com/agivo-free/wp-content/uploads/sites/176/2025/05/Group-41408.svg',
};

// --- Element helpers ---------------------------------------------------------------------
let seq = 0;
const content = [];
function id() {
	seq += 1;
	let n = 1700000000 + seq * 7919;
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

/** Elementor "hidden-<device>" → Bricks _display per breakpoint (emits only where visibility changes). */
const BPS = ['', 'laptop', 'tablet_landscape', 'tablet_portrait', 'mobile_landscape', 'mobile_portrait'];
function visibility(hiddenOn, shown = 'block') {
	const out = {};
	let prev = true;
	for (const bp of BPS) {
		const visible = !hiddenOn.includes(bp || 'desktop');
		if (visible !== prev || (bp === '' && !visible)) out[bp ? `_display:${bp}` : '_display'] = visible ? shown : 'none';
		prev = visible;
	}
	return out;
}
const HIDE_MOBILES = visibility(['mobile_landscape', 'mobile_portrait']);
const pad = (top, right, bottom, left) => ({ top, right, bottom, left });

// Native SVG element (inline SVG, so `fill` recolors it like Elementor's icon color). Bricks' importer
// downloads `file` SVGs into the Media Library when "Import images" is ticked (needs an `id` + SVG upload
// permission); a static URL in the "Dynamic data" source is NOT rendered (treated as a tag).
const svg = (url, fileId, size, fill, extra = {}, label) =>
	el('svg', { file: { id: fileId, url, filename: url.split('/').pop() }, width: String(size), height: String(size), fill: color(fill), _alignSelf: 'flex-start', ...HIDE_MOBILES, ...extra }, [], label);

// --- Build ------------------------------------------------------------------------------
function hero() {
	// Title "Creative [ Solutions ]\nfor Every Vision" → h1 with 3 spans (highlight span styled natively)
	const title = el(
		'block',
		{
			tag: 'custom',
			customTag: 'h1',
			_display: 'block',
			_typography: { 'font-family': FONT, 'font-size': '72', 'font-weight': '700', 'line-height': '1', color: color(C.black) },
			'_typography:tablet_portrait': { 'font-size': '54' },
			'_typography:mobile_landscape': { 'font-size': '40' },
			'_typography:mobile_portrait': { 'font-size': '30' },
		},
		[
			el('text-basic', { tag: 'span', text: 'Creative ' }, [], 'Title'),
			el(
				'text-basic',
				{
					tag: 'span',
					text: 'Solutions',
					_typography: { color: color(C.purple), 'font-weight': '800', 'line-height': '1.4' },
					_background: { image: img(U.underline), position: 'custom', positionX: '0%', positionY: '92%', repeat: 'no-repeat', size: 'contain' },
					'_background:tablet_portrait': { position: 'custom', positionX: '0%', positionY: '88%' },
					'_background:mobile_portrait': { position: 'custom', positionX: '0%', positionY: '98%' },
				},
				[],
				'Highlight',
			),
			el('text-basic', { tag: 'span', text: 'for Every Vision', _display: 'block' }, [], 'Title line 2'),
		],
		'Heading (h1)',
	);

	const intro = el('text', {
		text: '<p>Introducing digital Agency, the unsung hero of stream communication in the world of Software as a Service.</p>',
		_typography: { 'font-family': FONT, 'font-size': '18', 'font-weight': '400', 'line-height': '1.8', color: color(C.body) },
		'_typography:mobile_portrait': { 'line-height': '1.4' },
		_width: '87%',
		'_width:mobile_portrait': '100%',
	});

	const button = el('aab-button-pro', {
		btnStyle: 'base-default',
		btnHoverVariant: 'rollover-left',
		btnText: 'contact us',
		btnLink: { type: 'external', url: 'https://templates.animation-addons.com/agivo-free/contact-us/' },
		btnTypo: { 'font-family': FONT, 'font-size': '16', 'font-weight': '700', 'text-transform': 'capitalize', 'line-height': '1.2', 'letter-spacing': '0.5px' },
		btnColor: color(C.white),
		btnBg: { color: color(C.purple) },
		btnBorder: { width: pad('1', '1', '1', '1'), style: 'solid', color: color(C.purple), radius: pad('33', '33', '33', '33') },
		btnPadding: pad('18', '36', '18', '36'),
		'btnPadding:mobile_portrait': pad('12', '24', '12', '24'),
		btnHColor: color(C.dark),
		btnHBorder: color(C.yellow),
		btnRevealColor: color(C.yellow),
	});

	const ctaRow = el(
		'block',
		{
			_direction: 'row',
			_justifyContent: 'flex-start',
			_alignItems: 'flex-start',
			_flexWrap: 'nowrap',
			'_flexWrap:mobile_portrait': 'wrap',
			_columnGap: '20',
			_rowGap: '20',
			_position: 'relative',
		},
		[
			button,
			svg(U.watch, 249, 170, C.dark, { _margin: { top: '-62' } }, 'Watch video (svg)'),
			// Elementor used padding-top 27 on the icon wrapper; margin keeps the SVG box at full size.
			svg(U.arrow, 250, 120, C.orange, { _margin: { top: '27' } }, 'Arrow (svg)'),
			svg(
				U.sparkle,
				251,
				46,
				C.dark,
				{
					_position: 'absolute',
					_left: '55%',
					'_left:laptop': '53%',
					'_left:tablet_landscape': '61%',
					'_left:tablet_portrait': '68%',
					_bottom: '-17%',
				},
				'Sparkle (svg)',
			),
		],
		'CTA row',
	);

	const left = el(
		'block',
		{
			_width: '61%',
			'_width:tablet_portrait': '63%',
			'_width:mobile_landscape': '51%',
			'_width:mobile_portrait': '100%',
			_rowGap: '30',
			_flexWrap: 'wrap',
			'_justifyContent:mobile_landscape': 'center',
			'_alignItems:mobile_landscape': 'flex-start',
			'_heightMin:tablet_landscape': '68vh',
			'_heightMin:mobile_portrait': '0vh',
			_position: 'relative',
		},
		[title, intro, ctaRow],
		'Content',
	);

	// Globe: Floating Elements inside a zero-height relative wrapper (= Elementor widget box)
	const floating = el(
		'block',
		{ _position: 'relative', ...visibility(['mobile_landscape', 'mobile_portrait'], 'flex') },
		[
			el('aab-floating-elements', {
				floating_items: [
					{
						id: 'e4a264',
						image: img(U.globe),
						size: 400,
						'size:tablet_portrait': 295,
						horizontalOrientation: 'left',
						offsetX: '21%',
						'offsetX:laptop': '10%',
						'offsetX:tablet_landscape': '-1%',
						'offsetX:mobile_landscape': '3%',
						'offsetX:mobile_portrait': '12%',
						verticalOrientation: 'top',
						offsetY: -435,
						'offsetY:tablet_portrait': -290,
						'offsetY:mobile_portrait': -305,
						liveAnimation: 'spin',
						zIndex: 1,
					},
				],
			}),
		],
		'Floating globe',
	);

	// Same GIF as a plain image, only on ≤880 (Elementor: hidden desktop → tablet)
	const globeMobile = el('image', { image: img(U.globe), ...visibility(['desktop', 'laptop', 'tablet_landscape', 'tablet_portrait']) }, [], 'Globe (mobile)');

	const clients = el(
		'block',
		{
			_width: '74%',
			'_width:laptop': '83%',
			'_width:tablet_landscape': '97%',
			'_width:tablet_portrait': '100%',
			_direction: 'row',
			_justifyContent: 'center',
			_alignItems: 'center',
			_flexWrap: 'nowrap',
			'_flexWrap:mobile_portrait': 'wrap',
			_columnGap: '20',
			_rowGap: '20',
			'_columnGap:tablet_portrait': '0',
			'_rowGap:tablet_portrait': '0',
			'_margin:tablet_portrait': { top: '25' },
			_position: 'relative',
		},
		[
			el('image', { image: img(U.avatars), '_margin:tablet_portrait': { right: '-11', left: '-13' } }, [], 'Avatars'),
			el('text', {
				text: '<p>Satisfied clients<br />15k+ in globe</p>',
				_typography: { 'font-family': FONT, 'font-size': '16', 'font-weight': '400', 'line-height': '1.6', color: color(C.black) },
			}),
		],
		'Clients',
	);

	const right = el(
		'block',
		{
			_width: '39%',
			'_width:tablet_portrait': '34%',
			'_width:mobile_landscape': '45%',
			'_width:mobile_portrait': '100%',
			_justifyContent: 'flex-end',
			'_justifyContent:tablet_portrait': 'center',
			'_justifyContent:mobile_portrait': 'flex-end',
			_alignItems: 'flex-end',
			'_alignItems:tablet_portrait': 'center',
			'_alignItems:mobile_portrait': 'center',
			_rowGap: '30',
			_heightMin: '551px',
			'_heightMin:laptop': '68vh',
			'_heightMin:tablet_portrait': '80vh',
			'_heightMin:mobile_portrait': '55vh',
			'_padding:tablet_portrait': { top: '75' },
			'_padding:mobile_portrait': { top: '0' },
			_position: 'relative',
		},
		[floating, globeMobile, clients],
		'Visual',
	);

	const row = el(
		'container',
		{
			_width: '100%',
			_widthMax: '1296px',
			_direction: 'row',
			_justifyContent: 'space-between',
			_alignItems: 'flex-start',
			_flexWrap: 'nowrap',
			'_flexWrap:tablet_portrait': 'wrap',
			_columnGap: '20',
			_rowGap: '20',
			'_columnGap:mobile_landscape': '0',
			'_rowGap:mobile_landscape': '0',
			'_columnGap:mobile_portrait': '20',
			'_rowGap:mobile_portrait': '20',
			_position: 'relative',
		},
		[left, right],
	);

	const marquee = el(
		'block',
		{
			_padding: pad('120', '0', '70', '0'),
			'_padding:laptop': pad('220', '0', '170', '0'),
			'_padding:mobile_portrait': pad('0', '0', '0', '0'),
			_position: 'relative',
		},
		[
			el('aab-brand-slider', {
				slideContent: 'text',
				// Elementor had 1 item and its loop filled the row; repeated so the Bricksfly loop has enough slides.
				textSlides: ['7c6201', '7c6202', '7c6203', '7c6204'].map((id) => ({ id, text: 'creative agency' })),
				speed: 9000,
				autoplay: 'on',
				autoplayDelay: 1,
				loop: 'on',
				reverseDirection: true, // Elementor direction: rtl
				textTypography: { 'font-family': FONT, 'font-size': '160', 'font-weight': '700', 'text-transform': 'capitalize', 'line-height': '1.3', 'letter-spacing': '0.5px' },
				// Elementor: transparent fill + 1px #0F0629 text stroke. No free/native stroke control → faint fill (see notes).
				textColor: color('rgba(15, 6, 41, 0.12)'),
				_zIndex: '0',
				...visibility(['laptop', 'tablet_landscape', 'tablet_portrait', 'mobile_landscape', 'mobile_portrait']),
			}),
		],
		'Marquee',
	);

	el(
		'section',
		{
			_heightMin: '100vh',
			_rowGap: '30',
			'_rowGap:mobile_portrait': '50',
			_background: { image: img(U.bg), position: 'center center', repeat: 'no-repeat', size: 'cover' },
			_margin: pad('-133', '0', '0', '0'),
			_padding: pad('180', '0', '0', '0'),
			'_padding:laptop': pad('203', '20', '0', '20'),
			'_padding:tablet_portrait': pad('183', '20', '0', '20'),
			'_padding:mobile_landscape': pad('170', '20', '0', '20'),
			'_padding:mobile_portrait': pad('135', '15', '0', '15'),
		},
		[row, marquee],
		'Hero Section',
	);
}

hero();

const byId = new Map(content.map((e) => [e.id, e]));
const ordered = [];
const walk = (eid) => {
	const e = byId.get(eid);
	ordered.push(e);
	e.children.forEach(walk);
};
content.filter((e) => e.parent === 0).forEach((e) => walk(e.id));

const template = { title: 'Hero Header (from Elementor)', type: 'content', templateType: 'content', tags: [], bundles: [], content: ordered };
mkdirSync(dirname(outFile), { recursive: true });
writeFileSync(outFile, JSON.stringify(template, null, 2));
console.log(`Wrote ${outFile} (${ordered.length} elements)`);
