<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/droplist-header_notifications.bundle.css',
	'js' => 'dist/droplist-header_notifications.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue3',
		'ui.vue3.pinia',
		'local.droplist-header',
	],
	'skip_core' => true,
];