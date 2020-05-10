<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Slovak translation by Senky (https://github.com/senky)
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
	'ACP_MEDIA_SETTINGS'				=> 'Nastavenia vkladania médií',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Tu môžete konfigurovať nastavenia pre plugin vkladania médií.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Zobraziť <samp>[MEDIA]</samp> BB kód pri prispievaní',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Ak je toto nezaškrtnuté, BB kód nebude zobrazený, no používatelia budú môcť používať <samp>[media]</samp> kód v ich príspevkoch',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Povoliť v podpisoch',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Povolí v podpisoch používateľov vložiť obsah médií.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases, a simple URL does not provide all the information needed to embed a resource and the external content has to be downloaded, inspected and the information extracted. This only happens once at parsing time, but if the same text is parsed multiple times (e.g. when editing a text) a local copy of the external content can be saved in the cache for performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convert plain URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Enable this to convert plain URLs (not wrapped in <samp>[media]</samp> or <samp>[url]</samp> tags) to embedded media content. Note that changing this setting will only affect new posts, as existing posts have already been parsed.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is being purged by cron once per day (if enabled above). The cache can be manially purged here.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID stránky: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Táto stránka má konflikt s existujúcim BB kódom: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Spravovať stránky vkladania médií',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Tu môžete spravovať stránky, pre ktoré chcete povoliť získavanie obsahu pluginu vkladania médií vkladanie médií.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nie sú žiadne stránky médií na zobrazenie.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);
