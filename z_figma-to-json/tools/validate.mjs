#!/usr/bin/env node
/**
 * Validate a Bricks template export JSON against the controls Bricks + Bricksfly
 * actually register at runtime (reference/controls.json, from dump-controls.php).
 *
 * Usage:
 *   node tools/validate.mjs <file.json> [--breakpoints=guideline|standard|site] [--tier=free|pro] [--quiet-warnings]
 *
 * --tier=free (default): only Bricks native + Bricksfly FREE elements/settings
 *   (reference/controls-free.json). Pro elements and Pro extension settings are errors.
 * --tier=pro: also allow Bricksfly Pro (reference/controls.json).
 *
 * --breakpoints=guideline (default): the team breakpoints from
 *   guidelines/responsive-guidelines.md (desktop base + large_laptop, laptop, tablet_landscape,
 *   tablet_portrait, mobile_landscape, mobile_portrait, mobile).
 * --breakpoints=standard: Bricks defaults only (tablet_portrait, mobile_landscape, mobile_portrait).
 * --breakpoints=site: accept every breakpoint registered on the dumped site.
 *
 * Exit code 0 = no errors (warnings allowed), 1 = errors found.
 */

import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const args = process.argv.slice(2);
const file = args.find((a) => !a.startsWith('--'));
const bpMode = (args.find((a) => a.startsWith('--breakpoints=')) || '--breakpoints=guideline').split('=')[1];
const quietWarnings = args.includes('--quiet-warnings');
const tier = (args.find((a) => a.startsWith('--tier=')) || '--tier=free').split('=')[1];

if (!file) {
	console.error('Usage: node tools/validate.mjs <file.json> [--breakpoints=guideline|standard|site]');
	process.exit(2);
}

const fullReference = JSON.parse(readFileSync(resolve(here, '../reference/controls.json'), 'utf8'));
const reference = tier === 'pro' ? fullReference : JSON.parse(readFileSync(resolve(here, '../reference/controls-free.json'), 'utf8'));
const registry = reference.elements;
// Used only to explain errors: things that exist, but only with Bricksfly Pro.
const proRegistry = fullReference.elements;

const STANDARD_BREAKPOINTS = ['tablet_portrait', 'mobile_landscape', 'mobile_portrait'];
// Keep in sync with guidelines/responsive-guidelines.md (largest → smallest).
const GUIDELINE_BREAKPOINTS = ['large_laptop', 'laptop', 'tablet_landscape', 'tablet_portrait', 'mobile_landscape', 'mobile_portrait', 'mobile'];
const siteBreakpoints = reference.breakpoints.filter((b) => !b.base).map((b) => b.key);
const breakpoints = { site: siteBreakpoints, standard: STANDARD_BREAKPOINTS, guideline: GUIDELINE_BREAKPOINTS }[bpMode];
if (!breakpoints) {
	console.error(`Unknown breakpoint mode "${bpMode}"`);
	process.exit(2);
}
// Any key that could be a breakpoint suffix, so a wrong one is reported as "not allowed" rather than "unknown".
const allSiteBreakpoints = [...new Set(['desktop', ...siteBreakpoints, ...STANDARD_BREAKPOINTS, ...GUIDELINE_BREAKPOINTS])];

// Pseudo-class / pseudo-element suffixes Bricks accepts on style settings.
const PSEUDO = /^(hover|active|focus|focus-within|focus-visible|visited|before|after|first-child|last-child|nth-child\(.+\)|placeholder|checked|disabled|empty|not\(.+\)|marker|selection)$/;

// Settings Bricks stores on elements that are not registered as controls.
const NON_CONTROL_SETTINGS = new Set(['_cssGlobalClasses', '_interactions', '_conditions', '_hidden']);

const TYPOGRAPHY_KEYS = new Set([
	'font-family', 'font-weight', 'font-size', 'line-height', 'letter-spacing', 'color', 'text-align',
	'text-transform', 'font-style', 'text-decoration', 'white-space', 'text-wrap', 'font-variation-settings',
	'text-shadow', 'fallback', 'word-spacing',
]);
const SIDES = new Set(['top', 'right', 'bottom', 'left']);
const COLOR_KEYS = new Set(['raw', 'hex', 'rgb', 'hsl', 'id', 'name', 'php']);

const errors = [];
const warnings = [];
const err = (where, msg) => errors.push(`${where}: ${msg}`);
const warn = (where, msg) => warnings.push(`${where}: ${msg}`);

const isObj = (v) => v !== null && typeof v === 'object' && !Array.isArray(v);

function checkColor(where, v) {
	if (!isObj(v)) return err(where, `color must be an object like {"raw":"#fff"}, got ${JSON.stringify(v)}`);
	const bad = Object.keys(v).filter((k) => !COLOR_KEYS.has(k));
	if (bad.length) err(where, `unknown color keys ${bad.join(', ')}`);
}

function checkSides(where, v) {
	if (!isObj(v)) return err(where, `expected {top,right,bottom,left} object, got ${JSON.stringify(v)}`);
	const bad = Object.keys(v).filter((k) => !SIDES.has(k));
	if (bad.length) err(where, `unknown side keys ${bad.join(', ')}`);
}

function checkValue(where, control, value) {
	switch (control.type) {
		case 'select': {
			let opts = control.options;
			if (!isObj(opts) || control.searchable) return;
			if (siteBreakpoints.every((k) => k in opts)) {
				// Options generated from the dumped site's breakpoints: swap in the active breakpoint set.
				opts = Object.fromEntries(Object.entries(opts).filter(([k]) => !siteBreakpoints.includes(k)));
				for (const k of breakpoints) opts[k] = k;
			}
			const values = Array.isArray(value) ? value : [value];
			for (const v of values) {
				if (!(String(v) in opts)) {
					err(where, `value ${JSON.stringify(v)} not in options [${Object.keys(opts).join(', ')}]`);
				}
			}
			return;
		}
		case 'checkbox':
			if (typeof value !== 'boolean') err(where, `checkbox must be boolean, got ${JSON.stringify(value)}`);
			return;
		case 'number':
		case 'slider':
			if (typeof value !== 'number' && typeof value !== 'string') err(where, `number must be number|string`);
			if (typeof value === 'number' && control.min !== undefined && value < control.min) warn(where, `below min ${control.min}`);
			if (typeof value === 'number' && control.max !== undefined && value > control.max) warn(where, `above max ${control.max}`);
			return;
		case 'text':
		case 'textarea':
		case 'editor':
		case 'code':
			if (typeof value !== 'string' && typeof value !== 'number') err(where, `${control.type} must be a string`);
			return;
		case 'color':
			return checkColor(where, value);
		case 'spacing':
		case 'dimensions':
			return checkSides(where, value);
		case 'typography': {
			if (!isObj(value)) return err(where, 'typography must be an object');
			for (const [k, v] of Object.entries(value)) {
				if (!TYPOGRAPHY_KEYS.has(k)) err(where, `unknown typography key "${k}"`);
				if (k === 'color') checkColor(`${where}.color`, v);
			}
			return;
		}
		case 'border': {
			if (!isObj(value)) return err(where, 'border must be an object');
			for (const [k, v] of Object.entries(value)) {
				if (k === 'width' || k === 'radius') checkSides(`${where}.${k}`, v);
				else if (k === 'color') checkColor(`${where}.color`, v);
				else if (k !== 'style') err(where, `unknown border key "${k}"`);
			}
			return;
		}
		case 'background':
			if (!isObj(value)) return err(where, 'background must be an object');
			if (value.color) checkColor(`${where}.color`, value.color);
			return;
		case 'image':
			if (!isObj(value) || (!value.url && !value.id && !value.useDynamicData)) err(where, 'image needs {url|id}');
			return;
		case 'icon':
			if (!isObj(value) || !value.library) err(where, 'icon needs {library, icon|svg}');
			return;
		case 'link':
			if (!isObj(value) || !value.type) err(where, 'link needs {type: internal|external|meta|...}');
			return;
		case 'repeater': {
			if (!Array.isArray(value)) return err(where, 'repeater must be an array');
			const fields = control.fields || {};
			value.forEach((item, i) => {
				if (!isObj(item)) return err(`${where}[${i}]`, 'repeater item must be an object');
				for (const [k, v] of Object.entries(item)) {
					if (k === 'id') continue;
					// Responsive repeater fields are stored as "field:breakpoint" (read by e.g. Bricksfly ResponsiveHelper::normalize).
					const [field, bp, ...extra] = k.split(':');
					if (!fields[field] || extra.length) err(`${where}[${i}]`, `unknown repeater field "${k}"`);
					else if (bp !== undefined && !breakpoints.includes(bp)) err(`${where}[${i}]`, `repeater field "${k}": breakpoint "${bp}" not allowed (${bpMode})`);
					else checkValue(`${where}[${i}].${k}`, fields[field], v);
				}
			});
			return;
		}
		case 'separator':
		case 'info':
			err(where, `"${control.type}" is a UI-only control and must not hold a value`);
			return;
		default:
			return;
	}
}

function parseSettingKey(key) {
	const [base, ...rest] = key.split(':');
	let breakpoint = null;
	let pseudo = null;
	for (const part of rest) {
		if (allSiteBreakpoints.includes(part) && !breakpoint && !pseudo) breakpoint = part;
		else if (PSEUDO.test(part) && !pseudo) pseudo = part;
		else return { base, invalid: part };
	}
	return { base, breakpoint, pseudo };
}

// ---------------------------------------------------------------------------

let data;
try {
	data = JSON.parse(readFileSync(resolve(file), 'utf8'));
} catch (e) {
	console.error(`INVALID JSON: ${e.message}`);
	process.exit(1);
}

// Wrapper
if (!isObj(data)) err('root', 'must be an object (Bricks template export)');
if (!Array.isArray(data.content)) err('root', '"content" array missing');
if (!data.title) warn('root', '"title" missing');
// Bricks' importer (Templates::import_template) reads these with is_array() — missing keys emit PHP warnings.
for (const k of ['tags', 'bundles']) if (!Array.isArray(data[k])) err('root', `"${k}" must be an array (use [])`);
if (!['content', 'header', 'footer', 'section', 'popup'].includes(data.templateType)) {
	warn('root', `templateType ${JSON.stringify(data.templateType)} unusual (expected "content")`);
}

const content = Array.isArray(data.content) ? data.content : [];
const byId = new Map();

for (const [i, el] of content.entries()) {
	const where = `content[${i}]`;
	if (!isObj(el)) { err(where, 'element must be an object'); continue; }
	if (typeof el.id !== 'string' || !/^[a-z0-9]{6}$/.test(el.id)) err(where, `id ${JSON.stringify(el.id)} must be 6 lowercase alphanumerics`);
	if (byId.has(el.id)) err(where, `duplicate id ${el.id}`);
	byId.set(el.id, el);
	const extra = Object.keys(el).filter((k) => !['id', 'name', 'parent', 'children', 'settings', 'label', 'themeStyles', 'cid'].includes(k));
	if (extra.length) err(where, `unknown element keys ${extra.join(', ')}`);
}

for (const el of content) {
	if (!isObj(el)) continue;
	const where = `#${el.id} (${el.name})`;
	const def = registry[el.name];

	// Structure
	if (!def && proRegistry[el.name]) err(where, `element "${el.name}" is Bricksfly PRO — not allowed (free only)`);
	else if (!def) err(where, `element name "${el.name}" is not registered in Bricks/Bricksfly`);
	if (el.parent !== 0 && !byId.has(el.parent)) err(where, `parent ${JSON.stringify(el.parent)} does not exist`);
	if (el.parent !== 0 && byId.has(el.parent) && !(byId.get(el.parent).children || []).includes(el.id)) {
		err(where, `parent ${el.parent} does not list this element in its children`);
	}
	if (!Array.isArray(el.children)) err(where, 'children must be an array');
	for (const cid of el.children || []) {
		const child = byId.get(cid);
		if (!child) err(where, `child ${cid} does not exist`);
		else if (child.parent !== el.id) err(where, `child ${cid} has parent ${child.parent}`);
	}
	if ((el.children || []).length && def && !def.nestable) err(where, `"${el.name}" is not nestable but has children`);
	if (el.parent === 0 && el.name !== 'section') warn(where, 'root-level element is not a section');

	// Settings
	if (el.settings !== undefined && !isObj(el.settings) && !(Array.isArray(el.settings) && el.settings.length === 0)) {
		err(where, 'settings must be an object');
		continue;
	}
	if (!def) continue;

	for (const [key, value] of Object.entries(el.settings || {})) {
		const sw = `${where} settings["${key}"]`;
		const parsed = parseSettingKey(key);
		if (parsed.invalid) { err(sw, `unknown key suffix ":${parsed.invalid}"`); continue; }
		if (parsed.breakpoint && !breakpoints.includes(parsed.breakpoint)) {
			err(sw, `breakpoint "${parsed.breakpoint}" not allowed (allowed: ${breakpoints.join(', ')})`);
		}
		if (NON_CONTROL_SETTINGS.has(parsed.base)) continue;
		const control = def.controls[parsed.base];
		if (!control && proRegistry[el.name]?.controls[parsed.base]) { err(sw, `"${parsed.base}" is a Bricksfly PRO setting — not allowed (free only)`); continue; }
		if (!control) { err(sw, `"${parsed.base}" is not a control of "${el.name}"`); continue; }
		checkValue(sw, control, value);
	}
}

// Cycles / reachability
const visited = new Set();
function walk(id, stack) {
	if (stack.has(id)) { err(`#${id}`, 'cycle in hierarchy'); return; }
	if (visited.has(id)) return;
	visited.add(id);
	stack.add(id);
	for (const c of byId.get(id)?.children || []) walk(c, stack);
	stack.delete(id);
}
content.filter((e) => isObj(e) && e.parent === 0).forEach((e) => walk(e.id, new Set()));
for (const id of byId.keys()) if (!visited.has(id)) err(`#${id}`, 'not reachable from a root element');

// Report
const names = {};
content.forEach((e) => { if (isObj(e)) names[e.name] = (names[e.name] || 0) + 1; });
console.log(`File: ${file}`);
console.log(`Elements: ${content.length}  ${Object.entries(names).map(([n, c]) => `${n}×${c}`).join(' ')}`);
console.log(`Breakpoint mode: ${bpMode} (${breakpoints.join(', ')})`);
console.log(`Tier: ${tier === 'pro' ? 'Bricks + Bricksfly Free + Pro' : 'Bricks + Bricksfly Free only'}`);
if (!quietWarnings) warnings.forEach((w) => console.log(`WARN  ${w}`));
errors.forEach((e) => console.log(`ERROR ${e}`));
console.log(`\n${errors.length} error(s), ${warnings.length} warning(s)`);
process.exit(errors.length ? 1 : 0);
