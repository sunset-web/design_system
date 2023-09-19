<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

return [
	'css' => 'dist/ListDefault.bundle.css',
	'js' => 'dist/ListDefault.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
		'main.core.events',
	],
	'skip_core' => true,
];
