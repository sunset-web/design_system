<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/change-personal.bundle.css',
	'js' => 'dist/change-personal.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
	],
	'skip_core' => true,
];