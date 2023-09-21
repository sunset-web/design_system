<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/hl-list.bundle.css',
	'js' => 'dist/hl-list.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
		'ui.vue3.pinia',
	],
	'skip_core' => true,
];