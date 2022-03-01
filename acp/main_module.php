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
	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\config\db_text $config_text */
	protected $config_text;

	/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \phpbb\mediaembed\cache\cache $media_cache */
	protected $media_cache;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var array $enabled_sites */
	protected $enabled_sites;

	/** @var string $page_title */
	public $page_title;

	/** @var string $tpl_name */
	public $tpl_name;

	/** @var string $u_action */
	public $u_action;

	/** @var array An array of errors */
	protected $errors = [];

	/**
	 * Constructor
	 *
	 * @throws \Exception
	 */
	public function __construct()
	{
		global $phpbb_container;

		$this->container   = $phpbb_container;
		$this->config      = $this->container->get('config');
		$this->config_text = $this->container->get('config_text');
		$this->language    = $this->container->get('language');
		$this->log         = $this->container->get('log');
		$this->media_cache = $this->container->get('phpbb.mediaembed.cache');
		$this->request     = $this->container->get('request');
		$this->template    = $this->container->get('template');
		$this->user        = $this->container->get('user');

		$this->language->add_lang('acp', 'phpbb/mediaembed');
	}

	/**
	 * Main ACP module
	 *
	 * @param int    $id   The module ID (not used)
	 * @param string $mode The module mode (manage|settings)
	 * @throws \Exception
	 */
	public function main($id, $mode)
	{
		$mode = strtolower($mode);

		$this->tpl_name   = 'acp_phpbb_mediaembed_' . $mode;
		$this->page_title = $this->language->lang('ACP_MEDIA_' . strtoupper($mode));

		$form_key = 'phpbb/mediaembed';
		add_form_key($form_key);

		if ($this->request->is_set_post('action_purge_cache'))
		{
			$this->purge_mediaembed_cache();
		}

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}

			$this->{'save_' . $mode}();
		}

		$this->{'display_' . $mode}();
	}

	/**
	 * Add settings template vars to the form
	 */
	protected function display_settings()
	{
		$this->template->assign_vars([
			'S_MEDIA_EMBED_BBCODE'		=> $this->config['media_embed_bbcode'],
			'S_MEDIA_EMBED_ALLOW_SIG'	=> $this->config['media_embed_allow_sig'],
			'S_MEDIA_EMBED_PARSE_URLS'	=> $this->config['media_embed_parse_urls'],
			'S_MEDIA_EMBED_ENABLE_CACHE'=> $this->config['media_embed_enable_cache'],
			'S_MEDIA_EMBED_FULL_WIDTH'	=> $this->config['media_embed_full_width'],
			'S_MEDIA_EMBED_MAX_WIDTHS'	=> $this->get_media_embed_max_width(),
			'U_ACTION'					=> $this->u_action,
		]);
	}

	/**
	 * Add manage sites template vars to the form
	 *
	 * @throws \Exception
	 */
	protected function display_manage()
	{
		$this->template->assign_vars([
			'MEDIA_SITES'	=> $this->get_sites(),
			'U_ACTION'		=> $this->u_action,
			'ERRORS'		=> $this->errors,
		]);
	}

	/**
	 * Get a list of available sites
	 *
	 * @return array An array of available sites
	 * @throws \Exception
	 */
	protected function get_sites()
	{
		$sites = [];

		$configurator = $this->container->get('text_formatter.s9e.factory')->get_configurator();
		foreach ($configurator->MediaEmbed->defaultSites as $siteId => $siteConfig)
		{
			$disabled = isset($configurator->BBCodes[$siteId]);
			$sites[$siteId] = [
				'id'		=> $siteId,
				'name'		=> $siteConfig['name'],
				'title'		=> $this->language->lang($disabled ? 'ACP_MEDIA_SITE_DISABLED' : 'ACP_MEDIA_SITE_TITLE', $siteId),
				'enabled'	=> in_array($siteId, $this->get_enabled_sites()),
				'disabled'	=> $disabled,
			];
		}

		ksort($sites);

		$this->errors = array_diff($this->get_enabled_sites(), array_keys($sites));

		return $sites;
	}

	/**
	 * Get enabled media sites stored in the database
	 *
	 * @return array An array of enabled sites
	 */
	protected function get_enabled_sites()
	{
		if ($this->enabled_sites === null)
		{
			$sites = json_decode($this->config_text->get('media_embed_sites'), true);
			$this->enabled_sites = is_array($sites) ? $sites : [];
		}

		return $this->enabled_sites;
	}

	/**
	 * Save site managed data to the database
	 */
	protected function save_manage()
	{
		$this->config_text->set('media_embed_sites', json_encode($this->request->variable('mark', [''])));

		$this->media_cache->purge_textformatter_cache();

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PHPBB_MEDIA_EMBED_MANAGE');

		trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
	}

	/**
	 * Save settings data to the database
	 */
	protected function save_settings()
	{
		$this->config->set('media_embed_bbcode', $this->request->variable('media_embed_bbcode', 0));
		$this->config->set('media_embed_allow_sig', $this->request->variable('media_embed_allow_sig', 0));
		$this->config->set('media_embed_parse_urls', $this->request->variable('media_embed_parse_urls', 0));
		$this->config->set('media_embed_enable_cache', $this->request->variable('media_embed_enable_cache', 0));
		$this->config->set('media_embed_full_width', $this->request->variable('media_embed_full_width', 0));

		$this->set_media_embed_max_width();

		$this->media_cache->purge_textformatter_cache();

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PHPBB_MEDIA_EMBED_SETTINGS');

		if (count($this->errors))
		{
			trigger_error($this->language->lang('ACP_MEDIA_ERROR_MSG', implode('<br>', $this->errors)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
	}

	/**
	 * Purge all MediaEmbed cache files
	 */
	protected function purge_mediaembed_cache()
	{
		$this->media_cache->purge_mediaembed_cache();

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED');

		trigger_error($this->language->lang('PURGE_CACHE_SUCCESS') . adm_back_link($this->u_action));
	}

	/**
	 * Store the media embed max width value to the config text as JSON,
	 * with some basic input validation and array formatting.
	 */
	protected function set_media_embed_max_width()
	{
		$input = $this->request->variable('media_embed_max_width', '');

		if ($input)
		{
			$lines = array_unique(explode("\n", $input));

			foreach ($lines as $key => $line)
			{
				$parts = explode(':', $line);
				if (count($parts) !== 2)
				{
					unset($lines[$key]);
					continue;
				}

				$lines[$key] = array_combine(['site', 'width'], array_map('trim', $parts));
			}

			$input = json_encode(array_filter($lines, [$this, 'validate']));
		}

		$this->config_text->set('media_embed_max_width', strtolower($input));
	}

	/**
	 * Get the stored media embed max width data from config text and convert
	 * from JSON to the formatting used in the ACP textarea field.
	 *
	 * @return string
	 */
	protected function get_media_embed_max_width()
	{
		$config = json_decode($this->config_text->get('media_embed_max_width'), true);

		if ($config)
		{
			foreach ($config as &$item)
			{
				$item = implode(':', $item);
			}

			unset($item);
		}

		return $config ? implode("\n", $config) : '';
	}

	/**
	 * Validate the input for media embed max widths
	 * 'site' key value should be a word
	 * 'width' key value should be a number appended with either px or %
	 *
	 * @param array $input The array to check
	 * @return bool True if array contains valid values, false if not
	 * @throws \Exception
	 */
	protected function validate($input)
	{
		// First, lets get all the available media embed site IDs
		static $default_sites;

		if (null === $default_sites)
		{
			$configurator = $this->container->get('text_formatter.s9e.factory')->get_configurator();
			$default_sites = array_keys(iterator_to_array($configurator->MediaEmbed->defaultSites));
		}

		// Next create an array to hold any errors
		$errors = [];

		// Check to see if the site id provided exists in Media Embed
		if (!in_array($input['site'], $default_sites))
		{
			$errors[] = $this->language->lang('ACP_MEDIA_INVALID_SITE', $input['site'], $input['width']);
		}

		// Check to see if the width provided is a valid number followed px or %
		if (!preg_match('/^\d+(?:%|px)$/', $input['width']))
		{
			$errors[] = $this->language->lang('ACP_MEDIA_INVALID_WIDTH', $input['site'], $input['width']);
		}

		// Update the errors object with any new errors
		$this->errors = array_merge($this->errors, $errors);

		return empty($errors);
	}
}
