<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/Pagenavigation.bundle.css',
	'js' => 'dist/Pagenavigation.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
	],
	'skip_core' => true,
];