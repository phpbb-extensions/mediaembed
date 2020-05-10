<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Translated By : Bassel Taha Alhitary <http://alhitary.net>
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
	'ACP_MEDIA_SETTINGS'				=> 'إعدادات إدراج مُحتوى الوسائط',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'من هنا تستطيع ضبط الإعدادات الخاصة بالإضافة “إدراج مُحتوى الوسائط”.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'ظهور زر BBCode للوسائط <samp>[MEDIA]</samp>',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'اختيارك “لا”, يعني منع ظهور زر BBCode الخاص بالوسائط <samp>[MEDIA]</samp> في محرر الكتابة, وبالرغم من ذلك يستطيع الأعضاء استخدام الوسم <samp>[media]</samp> في مُشاركاتهم.',
	'ACP_MEDIA_ALLOW_SIG'				=> 'التوقيعات',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'السماح للعضو بإستخدام وسم الوسائط في التوقيع.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases, a simple URL does not provide all the information needed to embed a resource and the external content has to be downloaded, inspected and the information extracted. This only happens once at parsing time, but if the same text is parsed multiple times (e.g. when editing a text) a local copy of the external content can be saved in the cache for performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'تحويل الروابط العادية',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'اختار “نعم” لتحويل الروابط العادية (التي لم تندرج بين الوسوم <samp>[media]</samp> أو <samp>[url]</samp>) إلى محتوى الوسائط. مع الملاحظة بأن تطبيق هذا الخيار سيكون على المشاركات الجديدة فقط, ولن تؤثر على المشاركات القديمة.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is being purged by cron once per day (if enabled above). The cache can be manially purged here.',
	'ACP_MEDIA_SITE_TITLE'				=> 'الموقع: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'هذا الموقع يتعارض مع BBCode آخر موجود: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'إدارة مواقع الوسائط',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'من هنا تستطيع إدارة المواقع التي تريد السماح بإدارج محتوى الوسائط منها.',
	'ACP_MEDIA_SITES_ERROR'				=> 'لا يوجد مواقع وسائط يُمكن عرضها.',
	'ACP_MEDIA_SITES_MISSING'			=> 'المواقع التالية لم تعد مدعومة أو تعمل. نرجوا إعادة إرسال هذه الصفحة لإزالتها.',
]);
