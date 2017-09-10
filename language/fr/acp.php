<?php
/**
 *
 * phpBB Media Embed PlugIn. An extension for the phpBB Forum Software package.
 * French translation by Galixte (http://www.galixte.com)
 *
 * @copyright (c) 2017 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'Paramètres des médias intégrés',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Depuis cette page il est possible de paramétrer les options de l’extension « phpBB Media Embed PlugIn ».',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Afficher le BBCode <samp>[MEDIA]</samp> sur la page de rédaction des messages',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Permet d’activer l’affichage du BBCode <samp>[MEDIA]</samp> sur la page de l’éditeur complet lors de la rédaction d’une réponse ou d’un nouveau sujet. Si l’affichage est désactivé, le bouton du BBCode ne sera pas affiché, mais les utilisateurs pourront saisir manuellement la balise <samp>[media]</samp> dans leurs messages sur les pages accessibles au moyen des boutons « Répondre », « Nouveau sujet » ou dans la réponse rapide.',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Autoriser dans la signature des membres',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Permet d’utiliser le BBCode <samp>[MEDIA]</samp> dans le contenu de la signature des membres.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID du service : %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Ce service entre en conflit avec un BBCode déjà installé sur le forum : [%s]',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Gestion des services pour les médias intégrés aux messages',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Depuis cette page il est possible d’autoriser les sites Web des services qui seront pris en charge par l’extension « phpBB Media Embed PlugIn » pour afficher leur contenu dans les messages.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Il n’y aucun site de médias à afficher.',
));
