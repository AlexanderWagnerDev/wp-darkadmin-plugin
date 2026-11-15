/**
 * DarkAdmin: dark TinyMCE content area.
 *
 * The TinyMCE editing area is an iframe, so admin stylesheets cannot reach it.
 * This script adds a small stylesheet to every TinyMCE document on the page,
 * using the DarkAdmin palette of the parent document. Loaded only on screens
 * where a plugin stylesheet asks for it (Fluent Forms).
 */
(function () {
	'use strict';

	var STYLE_ID = 'darkadmin-tinymce-dark';

	function token(name, fallback) {
		var value = window.getComputedStyle(document.documentElement).getPropertyValue(name).trim();
		return value || fallback;
	}

	function css() {
		var surface = token('--adm-surface-2', '#333333');
		var text = token('--adm-text', '#f0f0f0');
		var link = token('--adm-link', '#7b96f5');
		var border = token('--adm-border', '#3a3a3a');

		return (
			'html,body.mce-content-body{background:' + surface + ' !important;color:' + text + ' !important;}' +
			'body.mce-content-body a{color:' + link + ' !important;}' +
			'body.mce-content-body :is(p,li,td,th,h1,h2,h3,h4,h5,h6,span,div,blockquote,pre,code){color:inherit;}' +
			'body.mce-content-body :is(td,th,hr,blockquote){border-color:' + border + ' !important;}'
		);
	}

	function style(editor) {
		var doc = editor && editor.getDoc ? editor.getDoc() : null;
		if (!doc || !doc.head || doc.getElementById(STYLE_ID)) {
			return;
		}
		var el = doc.createElement('style');
		el.id = STYLE_ID;
		el.textContent = css();
		doc.head.appendChild(el);
	}

	function watch(editor) {
		if (editor.initialized) {
			style(editor);
		}
		editor.on('init', function () {
			style(editor);
		});
	}

	function hook() {
		if (!window.tinymce || typeof window.tinymce.on !== 'function') {
			return false;
		}
		window.tinymce.on('AddEditor', function (event) {
			watch(event.editor);
		});
		(window.tinymce.editors || []).forEach(watch);
		return true;
	}

	if (!hook()) {
		document.addEventListener('DOMContentLoaded', hook);
	}
})();
