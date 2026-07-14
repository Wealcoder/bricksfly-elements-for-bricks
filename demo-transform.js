/**
 * Demo-build source transform (shared by the Gulp demo tasks).
 *
 * The production source stays byte-for-byte identical. The demo build swaps a
 * handful of license "source of truth" functions so every Pro feature unlocks
 * without a license, using marker comments embedded in the PHP source:
 *
 *   Paired swap — replace the production block with the demo body:
 *     /* DEMO:REPLACE-START * /   ...production code...   /* DEMO:REPLACE-END * /
 *     /* DEMO:WITH
 *        ...demo code (kept commented in production)...
 *     DEMO:END * /
 *
 *   Standalone insert — no REPLACE block, just inject the demo body:
 *     /* DEMO:WITH
 *        return; // e.g. suppress a notice
 *     DEMO:END * /
 */

'use strict';

const through = require('through2');

const PAIRED = /\/\*\s*DEMO:REPLACE-START\s*\*\/[\s\S]*?\/\*\s*DEMO:REPLACE-END\s*\*\/\s*\/\*\s*DEMO:WITH\b([\s\S]*?)DEMO:END\s*\*\//g;
const STANDALONE = /\/\*\s*DEMO:WITH\b([\s\S]*?)DEMO:END\s*\*\//g;

function applyDemoTransform(code) {
	code = code.replace(PAIRED, (_match, withBody) => withBody);
	code = code.replace(STANDALONE, (_match, withBody) => withBody);
	return code;
}

function demoTransformStream() {
	return through.obj(function (file, _enc, cb) {
		if (file.isBuffer() && /\.php$/i.test(file.path)) {
			const out = applyDemoTransform(file.contents.toString('utf8'));
			file.contents = Buffer.from(out, 'utf8');
		}
		cb(null, file);
	});
}

module.exports = { applyDemoTransform, demoTransformStream };
