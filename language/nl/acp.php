<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang,[
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'Media Embed Instellingen',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Hier kunt u instellingen voor de Media Embed PlugIn configureren.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Toon de <samp>[MEDIA]</samp> BBCode op de reactie pagina',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Als dit is niet toegestaan zal de BBCode knop niet worden getoond maar gebruikers kunnen nog steeds de <samp>[media]</samp> tag in berichten gebruiken',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Toestaan in onderschriften',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Toestaan dat onderschriften ook ingevoegde media inhoud (Embed Media Content) bevatten.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases you may notice slower than normal performance when loading media from other sites, especially while loading the same content multiple times (e.g. when editing a post). Enabling this will cache the information Media Embed gathers from sites locally and should improve performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convert plain URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Enable this to convert plain URLs (not wrapped in <samp>[media]</samp> or <samp>[url]</samp> tags) to embedded media content. Note that changing this setting will only affect new posts, as existing posts have already been parsed.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is automatically purged once per day, however this button can be used to manually purge its cache now.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Site id: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Deze website heeft een conflict met een bestaande BBCode: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Beheer Media Embed Websites',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Hier kunt u de websites beheren van welke u de de Media Embed PlugIn wilt toestaan inhoud te tonen.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Er zijn geen media websites om te tonen.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);
