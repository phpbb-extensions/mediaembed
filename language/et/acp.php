<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * Estonian translation by phpBBeesti.net
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
	'ACP_MEDIA_SETTINGS'				=> 'Manustatud meedia seaded',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Sellel leheküljel on sul võimalik seadistada laienduse manustatud meedia seadeid.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Näita <samp>[MEDIA]</samp> BBkoodi positamise leheküljel',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Kui pole lubatud, siis BBkoodi nuppu ei näidata, kuid siiski on kasutajatel võimalik kasutada e <samp>[media]</samp> silti oma postitustes.',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Luba kasutaja signatuurides',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Kas soovid lubada manustatud meediat oma kasutaja signatuuridest või siiski mitte.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases you may notice slower than normal performance when loading media from other sites, especially while loading the same content multiple times (e.g. when editing a post). Enabling this will cache the information Media Embed gathers from sites locally and should improve performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convert plain URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Enable this to convert plain URLs (not wrapped in <samp>[media]</samp> or <samp>[url]</samp> tags) to embedded media content. Note that changing this setting will only affect new posts, as existing posts have already been parsed.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is automatically purged once per day, however this button can be used to manually purge its cache now.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Lehekülje ID: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'See lehekülg on konfliktis juba eksisteeriva BBkoodiga: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Halda Manustatud Meedia Lehekülgesi',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Sellel leheküljel on sul võimalik hallata veebilehti, kust sa soovid lubada manustada sisu.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Ei ole kuvada ühtegi meedia lehekülge.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);
