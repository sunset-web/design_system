<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

return [
	'css' => 'dist/main.feedback.vue.bundle.css',
	'js' => 'dist/main.feedback.vue.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
	],
	'skip_core' => true,
];
