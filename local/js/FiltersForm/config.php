<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/FiltersForm.bundle.css',
	'js' => 'dist/FiltersForm.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
		'main.core.events',
	],
	'skip_core' => true,
];