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
	$lang = array();
}

$lang = array_merge($lang, array(
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'Nastavenia vkladania médií',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Tu môžete konfigurovať nastavenia pre plugin vkladania médií.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Zobraziť <samp>[MEDIA]</samp> BB kód pri prispievaní',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Ak je toto nezaškrtnuté, BB kód nebude zobrazený, no používatelia budú môcť používať <samp>[media]</samp> kód v ich príspevkoch',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Povoliť v podpisoch',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Povolí v podpisoch používateľov vložiť obsah médií.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID stránky: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Táto stránka má konflikt s existujúcim BB kódom: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Spravovať stránky vkladania médií',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Tu môžete spravovať stránky, pre ktoré chcete povoliť získavanie obsahu pluginu vkladania médií vkladanie médií.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nie sú žiadne stránky médií na zobrazenie.',
));
