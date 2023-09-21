<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/droplist-header.bundle.css',
	'js' => 'dist/droplist-header.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
		'ui.vue3.pinia',
	],
	'skip_core' => true,
];