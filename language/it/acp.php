<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Italian language By alex75 https://www.phpbb-store.it
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
	'ACP_MEDIA_SETTINGS'				=> 'Impostazioni PlugIn Media Embed',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Qui puoi configurare le impostazioni per il PlugIn Media Embed.',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Visualizza BBCode <samp>[MEDIA]</samp> nella pagina di scrittura',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Se non consentito, il pulsante BBCode non verrà visualizzato, tuttavia gli utenti possono ancora utilizzare il tag <samp> [media] </samp> nei loro messaggi',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Consenti nelle firme utente',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Consenti nelle firme utente di visualizzare il contenuto multimediale incorporato.',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Enable Media Embed cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In some cases you may notice slower than normal performance when loading media from other sites, especially while loading the same content multiple times (e.g. when editing a post). Enabling this will cache the information Media Embed gathers from sites locally and should improve performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Converti URL semplici',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Abilitare per convertire URL semplici (non racchiuso tra i tag <samp>[media]</samp> o <samp>[url]</samp> tags) ed incorporare i media. Tieni presente che la modifica di questa impostazione avrà effetto solo sui nuovi post, in quanto i post esistenti sono già stati analizzati.',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purge Media Embed cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed cache is automatically purged once per day, however this button can be used to manually purge its cache now.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID sito: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Questo sito è in conflitto con un BBCode esistente: [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Gestisci siti Per il PlugIn Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Qui puoi gestire i siti che vuoi consentire al Plugin Media Embed di visualizzare il contenuto.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Non ci sono siti con media da visualizzare.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);
