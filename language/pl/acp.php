<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Polska wersja językowa phpBB Media Embed 1.1.1 - 25.02.2020, Mateusz Dutko (vader) www.rnavspotters.pl
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
	'ACP_MEDIA_SETTINGS'				=> 'Ustawienia osadzania multimediów',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Tutaj można dokonać konfiguracji rozszerzenia Media Embed.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Wyświetl znacznik BBcode <samp>[MEDIA]</samp> na forum',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Jeśli wybrano Nie, to znacznik <samp>[MEDIA]</samp> nie będzie wyświetlony, jednakże nadal można go używać na forum.',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Znacznik <samp>[MEDIA]</samp> w podpisach',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Zezwól na używanie multimediów w sygnaturze.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases you may notice slower than normal performance when loading media from other sites, especially while loading the same content multiple times (e.g. when editing a post). Enabling this will cache the information Media Embed gathers from sites locally and should improve performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Konwersja adresów URL',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Jeśli wybrano Tak, to adresy URL zostaną przekonwertowane bez użycia znacznika BBCode <samp>[media]</samp> lub <samp>[url]</samp>. Ta opcja wpłynie tylko na nowo osadzone multimedia. Dotychczasowe adresy URL nie zostaną przetworzone.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is automatically purged once per day, however this button can be used to manually purge its cache now.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID strony: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Ta strona ma konflikt z istniejącym znacznikiem BBCode: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Zarządzaj stronami osadzania multimediów',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Tutaj można dokonać konfiguracji wyświetlania elementów stron przez rozszerzenie Media Embed.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nie ma żadnych stron do wyświetlenia.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Poniższe strony nie są dłużej wspierane. Kliknij wyślij, aby zaktualizować listę.',
]);
