<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\acp;

/**
 * phpBB Media Embed Plugin ACP module.
 */
class main_module
{
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		/** @var \phpbb\cache\driver\driver_interface $cache */
		$cache = $phpbb_container->get('cache');

		/** @var \phpbb\config\config $config */
		$config = $phpbb_container->get('config');

		/** @var \phpbb\request\request $request */
		$request = $phpbb_container->get('request');

		/** @var \phpbb\template\template $template */
		$template = $phpbb_container->get('template');

		/** @var \phpbb\language\language $language */
		$language = $phpbb_container->get('language');
		$language->add_lang('acp', 'phpbb/mediaembed');

		$form_key = 'phpbb/mediaembed';
		add_form_key($form_key);

		switch ($mode)
		{
			case 'manage':

				$this->tpl_name = 'acp_phpbb_mediaembed_manage';
				$this->page_title = $language->lang('ACP_MEDIA_MANAGE');

				/** @var \phpbb\config\db_text $config_text */
				$config_text = $phpbb_container->get('config_text');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID');
					}

					$config_text->set('media_embed_sites', json_encode($request->variable('mark', [''])));

					$cache->destroy($phpbb_container->getParameter('text_formatter.cache.parser.key'));
					$cache->destroy($phpbb_container->getParameter('text_formatter.cache.renderer.key'));

					trigger_error($language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
				}

				$sites = [];

				$allowed_sites = $config_text->get('media_embed_sites');
				$allowed_sites = $allowed_sites ? json_decode($allowed_sites, true) : [];

				/** @var \s9e\TextFormatter\Configurator $configurator */
				$configurator = $phpbb_container->get('text_formatter.s9e.factory')->get_configurator();
				foreach ($configurator->MediaEmbed->defaultSites->getIds() as $siteId)
				{
					if (isset($configurator->BBCodes[$siteId]))
					{
						continue;
					}

					$sites[] = [
						'name'		=> $siteId,
						'checked'	=> in_array($siteId, $allowed_sites),
					];
				}

				$template->assign_vars([
					'U_ACTION'		=> $this->u_action,
					'MEDIA_SITES'	=> $sites,
				]);

			break;

			case 'settings':
			default:

				$this->tpl_name = 'acp_phpbb_mediaembed_settings';
				$this->page_title = $language->lang('ACP_MEDIA_SETTINGS');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID');
					}

					$config->set('media_embed_bbcode', $request->variable('media_embed_bbcode', 0));

					trigger_error($language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'U_ACTION'				=> $this->u_action,
					'S_MEDIA_EMBED_BBCODE'	=> $config['media_embed_bbcode'],
				]);

			break;
		}
	}
}
