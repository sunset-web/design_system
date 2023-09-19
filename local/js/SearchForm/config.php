<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/SearchForm.bundle.css',
	'js' => 'dist/SearchForm.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
	],
	'skip_core' => true,
];