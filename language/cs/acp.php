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
	'ACP_MEDIA_SETTINGS'				=> 'Media Embed nastavení',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Zde můžete konfigurovat nastaven í pro plugin Media Embed.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Zobrazit <samp>[MEDIA]</samp> BBCode na stránce s příspěvkem.',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Pokud je zakázáno, BBCode tlačítko nebude zobrazeno, přesto mohou uživatelé ve svých příspěvcích využít <samp>[media]</samp>.',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Povolí v podpisech uživatelů',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Povolí zobrazovat v podpisech uživatelů vložená média.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases you may notice slower than normal performance when loading media from other sites, especially while loading the same content multiple times (e.g. when editing a post). Enabling this will cache the information Media Embed gathers from sites locally and should improve performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convert plain URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Enable this to convert plain URLs (not wrapped in <samp>[media]</samp> or <samp>[url]</samp> tags) to embedded media content. Note that changing this setting will only affect new posts, as existing posts have already been parsed.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is automatically purged once per day, however this button can be used to manually purge its cache now.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID stránky: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Došlo ke konfliktu této stránky s extistujícím BBCode: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Spravovat Media Embed stránky',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Zde můžete spravovat, jaké stránky mohou zobrazit obsaz z Media Embed pluginu.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nejsou zde žádné stránky s médii k zobrazení.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);
