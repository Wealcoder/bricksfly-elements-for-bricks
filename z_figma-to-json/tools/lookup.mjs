#!/usr/bin/env node
/**
 * Look up registered elements/controls in reference/controls.json.
 *
 *   node tools/lookup.mjs                      list all elements (name, label, category, #controls)
 *   node tools/lookup.mjs heading              list controls of "heading"
 *   node tools/lookup.mjs heading aab_text     only controls whose key contains "aab_text"
 *   node tools/lookup.mjs heading tag --full   full definition of matching controls
 *   add --pro to include Bricksfly Pro elements/settings (default: free tier only)
 */

import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
// Free tier by default (the POC may only use Bricks native + Bricksfly Free); --pro shows everything.
const catalog = process.argv.includes('--pro') ? 'controls.json' : 'controls-free.json';
const { elements } = JSON.parse(readFileSync(resolve(here, '../reference/' + catalog), 'utf8'));
const [name, filter] = process.argv.slice(2).filter((a) => !a.startsWith('--'));
const full = process.argv.includes('--full');

if (!name) {
	for (const [n, e] of Object.entries(elements)) {
		console.log(`${n.padEnd(28)} ${String(e.category).padEnd(14)} ${String(e.label).padEnd(28)} ${Object.keys(e.controls).length}${e.nestable ? ' nestable' : ''}`);
	}
	process.exit(0);
}

const el = elements[name];
if (!el) {
	console.error(`Unknown element "${name}"`);
	process.exit(1);
}

console.log(`${name} — ${el.label} (${el.class}, category: ${el.category})${el.nestable ? ' nestable' : ''}`);
for (const [key, c] of Object.entries(el.controls)) {
	if (filter && !key.includes(filter)) continue;
	if (c.type === 'separator' || c.type === 'info') continue;
	if (full) {
		console.log(key, JSON.stringify(c, null, 1));
		continue;
	}
	const opts = c.options && typeof c.options === 'object' ? ` [${Object.keys(c.options).slice(0, 12).join('|')}${Object.keys(c.options).length > 12 ? '|…' : ''}]` : '';
	const def = c.default !== undefined ? ` default=${JSON.stringify(c.default)}` : '';
	const req = c.required ? ` requires=${JSON.stringify(c.required)}` : '';
	console.log(`  ${key.padEnd(40)} ${String(c.type).padEnd(12)} ${c.label || ''}${opts}${def}${req}`);
}
