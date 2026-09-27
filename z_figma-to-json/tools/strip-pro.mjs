#!/usr/bin/env node
/**
 * Strip Bricksfly Pro (and dead/unknown) settings from a team template export so it uses
 * Bricks native + Bricksfly FREE only. Writes a cleaned copy; never touches the input.
 *
 * Usage:
 *   node tools/strip-pro.mjs <in.json> <out.json> [--drop=key1,key2]
 *
 * Removed automatically (all breakpoint/pseudo variants, e.g. "aab_delay:mobile"):
 *   - settings that are Bricksfly PRO for that element (in controls.json, not in controls-free.json)
 *   - dead pre-rename starter-animation keys ("_aab_*" → now "_bricksfly_*")
 *   - keys listed in --drop (e.g. leftovers from other plugins such as "blc_live_copy")
 * Anything else that is not a free control is only REPORTED — decide per template, then --drop it.
 * Pro *elements* are reported, never removed: they need a manual replacement.
 * Also sets type/templateType to "content" (rule 7).
 */

import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const args = process.argv.slice(2);
const [input, output] = args.filter((a) => !a.startsWith('--'));
const drop = new Set(((args.find((a) => a.startsWith('--drop=')) || '--drop=').split('=')[1] || '').split(',').filter(Boolean));

if (!input || !output) {
	console.error('Usage: node tools/strip-pro.mjs <in.json> <out.json> [--drop=key1,key2]');
	process.exit(2);
}

const free = JSON.parse(readFileSync(resolve(here, '../reference/controls-free.json'), 'utf8')).elements;
const full = JSON.parse(readFileSync(resolve(here, '../reference/controls.json'), 'utf8')).elements;
const NON_CONTROL_SETTINGS = new Set(['_cssGlobalClasses', '_interactions', '_conditions', '_hidden']);

const template = JSON.parse(readFileSync(input, 'utf8'));
const removed = { pro: {}, dead: {}, dropped: {} };
const unknown = [];
const proElements = [];
const renamed = [];
const count = (bucket, key) => (bucket[key] = (bucket[key] || 0) + 1);

for (const el of template.content) {
	const freeDef = free[el.name];
	if (!freeDef) {
		proElements.push(`#${el.id} ${el.name}${full[el.name] ? ' (Bricksfly PRO element)' : ' (unknown element)'}`);
		continue;
	}
	for (const key of Object.keys(el.settings || {})) {
		const base = key.split(':')[0];
		if (NON_CONTROL_SETTINGS.has(base) || freeDef.controls[base]) continue;
		if (drop.has(base)) { count(removed.dropped, base); delete el.settings[key]; continue; }
		if (full[el.name]?.controls[base]) { count(removed.pro, base); delete el.settings[key]; continue; }
		if (base.startsWith('_aab_')) { count(removed.dead, base); delete el.settings[key]; continue; }
		unknown.push(`#${el.id} ${el.name}: ${key}`);
	}
	// Button Pro (free widget) renamed its numeric styles "1".."8" → "pro-1".."pro-8"; same markup.
	if (el.name === 'aab-button-pro') {
		for (const key of Object.keys(el.settings)) {
			if (key.split(':')[0] === 'btnStyle' && /^[1-8]$/.test(String(el.settings[key]))) {
				renamed.push(`#${el.id} ${key}: "${el.settings[key]}" → "pro-${el.settings[key]}"`);
				el.settings[key] = `pro-${el.settings[key]}`;
			}
		}
	}
}

template.type = 'content';
template.templateType = 'content';
writeFileSync(output, JSON.stringify(template, null, '\t') + '\n');

const list = (bucket) => Object.entries(bucket).map(([k, n]) => `${k}×${n}`).join(', ') || '—';
console.log(`Wrote ${output}`);
console.log(`Removed PRO settings:  ${list(removed.pro)}`);
console.log(`Removed dead _aab_*:   ${list(removed.dead)}`);
console.log(`Removed via --drop:    ${list(removed.dropped)}`);
console.log(`Renamed legacy values:  ${renamed.join('; ') || '—'}`);
console.log(`PRO/unknown elements (replace manually): ${proElements.join('; ') || '—'}`);
console.log(`Unknown settings kept (review):          ${unknown.join('; ') || '—'}`);
