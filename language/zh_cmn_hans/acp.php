<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * @简体中文语言　David Yin <https://www.phpbbchinese.com/>
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
	'ACP_MEDIA_SETTINGS'				=> 'Media Embed Settings',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> '这里可设置 Media Embed PlugIn 的相关参数。',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> '在发帖页面显示 <samp>[MEDIA]</samp> BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> '若禁用， BBCode 按钮就不会显示，但用户还是可以在帖子中使用 <samp>[media]</samp> 标签。',
	'ACP_MEDIA_ALLOW_SIG'				=> '允许使用在用户签名',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> '允许在用户签名处显示嵌入媒体内容。',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases you may notice slower than normal performance when loading media from other sites, especially while loading the same content multiple times (e.g. when editing a post). Enabling this will cache the information Media Embed gathers from sites locally and should improve performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convert plain URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Enable this to convert plain URLs (not wrapped in <samp>[media]</samp> or <samp>[url]</samp> tags) to embedded media content. Note that changing this setting will only affect new posts, as existing posts have already been parsed.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is automatically purged once per day, however this button can be used to manually purge its cache now.',
	'ACP_MEDIA_SITE_TITLE'				=> '网站 id: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> '此网站与现有的 BBCode: [%s] 有冲突',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> '管理可嵌入媒体的网站',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> '你可以管理你所允许 Media Embed PlugIn 在帖子中显示内容的网站。',
	'ACP_MEDIA_SITES_ERROR'				=> '没有可用的媒体网站用于显示。.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);
