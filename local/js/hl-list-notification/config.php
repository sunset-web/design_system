<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/hl-list-notification.bundle.css',
	'js' => 'dist/hl-list-notification.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
		'local.Pagenavigation',
	],
	'skip_core' => true,
];