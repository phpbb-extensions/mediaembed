<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Turkish translation by ESQARE (https://www.phpbbturkey.com)
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
	'ACP_MEDIA_SETTINGS'				=> 'Medya (Ortam) Yerleştirme Ayarları',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Buradan Medya (Ortam) Yerleştirme Eklentisi için ayarları yapabilirsiniz.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> '<samp>[MEDIA]</samp> BBCode butonunu mesaj gönderme sayfasında göster',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'İzin verilmediği takdirde, BBCode butonu gösterilmeyecektir, ancak kullanıcılar hala <samp>[media]</samp> etiketini kendi mesajlarında kullanabilir',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Kullanıcı imzalarında izin ver',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Kullanıcı imzalarında yerleşik medya (ortam) içeriğine izin ver.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases, a simple URL does not provide all the information needed to embed a resource and the external content has to be downloaded, inspected and the information extracted. This only happens once at parsing time, but if the same text is parsed multiple times (e.g. when editing a text) a local copy of the external content can be saved in the cache for performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convert plain URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Enable this to convert plain URLs (not wrapped in <samp>[media]</samp> or <samp>[url]</samp> tags) to embedded media content. Note that changing this setting will only affect new posts, as existing posts have already been parsed.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is being purged by cron once per day (if enabled above). The cache can be manially purged here.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Site id numarası: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Bu site mevcut olan bir BBCode ile çakışıyor: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Medya (Ortam) Yerleştirme Sitelerini Yönet',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Buradan, içeriğini göstermek için Medya (Ortam) Yerleştirme Eklentisine izin vermek istediğiniz siteleri yönetebilirsiniz.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Gösterilecek hiç bir medya (ortam) sitesi yok.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);
