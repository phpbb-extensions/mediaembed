<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Translated By : Bassel Taha Alhitary - www.alhitary.net
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'إعدادات إدراج مُحتوى الوسائط',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'من هنا تستطيع ضبط الإعدادات الخاصة بالإضافة : إدراج مُحتوى الوسائط.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'تفعيل ',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'اختيارك "لا" يعني منع ظهور زر الـ BBCode : <samp>[MEDIA]</samp> في مُحرر الكتابة ولكن يستطيع الأعضاء استخدام الوسم في مُشاركاتهم',
	'ACP_MEDIA_ALLOW_SIG'				=> 'التوقيعات ',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'السماح للعضو بإستخدام هذه الإضافة في التوقيع.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convert plain URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Enable this to convert plain URLs (not wrapped in <samp>[media]</samp> or <samp>[url]</samp> tags) to embedded media content. Note that changing this setting will only affect new posts, as existing posts have already been parsed.',
	'ACP_MEDIA_SITE_TITLE'				=> 'الموقع : %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'هذا الموقع يتعارض مع BBCode آخر موجود : [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'إدارة مواقع الوسائط',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'من هنا تستطيع إدارة المواقع التي تريد السماح بإدارج محتوى الوسائط منها.',
	'ACP_MEDIA_SITES_ERROR'				=> 'لا يوجد مواقع وسائط يُمكن عرضها.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
));
