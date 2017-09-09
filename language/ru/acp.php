<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Russian translation by HD321kbps
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
	$lang = array();
}

$lang = array_merge($lang, array(
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'Настройки Media Embed',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Здесь вы можете настроить параметры для Media Embed.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Показывать <samp>[MEDIA]</samp> бб-код на странице размещения сообщений',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Если это запрещено, кнопка бб-кода не отображается, но пользователи могут использовать <samp>[media]</samp> бб-код в своих сообщениях.',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Разрешить в подписях пользователей',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Разрешить в подписях пользователей показывать Media Embed.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Id сайта: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Этот сайт не подключен у существующему бб-коду: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Управление сайтами для Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Здесь, вы можете управлять сайтами, которые вы хотите подключать к Media Embed.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Нет сайтов для отображения.',
));
