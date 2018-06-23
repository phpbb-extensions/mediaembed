<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\config\db_text $config_text */
	protected $config_text;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var bool Disable the media embed plugin (plain url parsing) */
	protected $disable_plugin = false;

	/** @var bool Disable the media tag (bbcode parsing) */
	protected $disable_tag = false;

	public static function getSubscribedEvents()
	{
		return [
			'core.text_formatter_s9e_configure_after'	=> 'configure_media_embed',
			'core.display_custom_bbcodes'				=> 'setup_media_bbcode',
			'core.permissions'							=> 'set_permissions',
			'core.help_manager_add_block_before'		=> 'media_embed_help',
			'core.posting_modify_message_text'			=> 'check_forum_permission',
			'core.ucp_pm_compose_modify_parse_before'	=> 'check_pm_permission',
			'core.message_parser_check_message'			=> [['check_signature'], ['check_magic_urls'], ['check_bbcode_enabled']],
			'core.text_formatter_s9e_parser_setup'		=> 'disable_media_embed',
		];
	}

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth         $auth
	 * @param \phpbb\config\config     $config
	 * @param \phpbb\config\db_text    $config_text
	 * @param \phpbb\language\language $language
	 * @param \phpbb\template\template $template
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\language\language $language, \phpbb\template\template $template)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->language = $language;
		$this->template = $template;
		$this->config_text = $config_text;
	}

	/**
	 * Configure Media Embed PlugIn
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function configure_media_embed($event)
	{
		/** @var \s9e\TextFormatter\Configurator $configurator */
		$configurator = $event['configurator'];

		foreach ($this->get_siteIds() as $siteId)
		{
			if (isset($configurator->BBCodes[$siteId]))
			{
				continue;
			}

			$configurator->MediaEmbed->add($siteId);
		}

		// Disable plain url parsing
		if (!$this->config->offsetGet('media_embed_parse_urls'))
		{
			unset($configurator->MediaEmbed);
		}
	}

	/**
	 * Set template switch for displaying the [media] BBCode button
	 */
	public function setup_media_bbcode()
	{
		$this->language->add_lang('common', 'phpbb/mediaembed');
		$this->template->assign_var('S_BBCODE_MEDIA', $this->config->offsetGet('media_embed_bbcode'));
	}

	/**
	 * Set media embed forum and user PM permission
	 *
	 * @param	\phpbb\event\data	$event	The event object
	 * @return	void
	 */
	public function set_permissions($event)
	{
		$event->update_subarray('permissions', 'f_mediaembed', ['lang' => 'ACL_F_MEDIAEMBED', 'cat' => 'content']);
		$event->update_subarray('permissions', 'u_pm_mediaembed', ['lang' => 'ACL_U_PM_MEDIAEMBED', 'cat' => 'pm']);
	}

	/**
	 * Add Media Embed help to the BBCode Guide
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function media_embed_help($event)
	{
		if ($event['block_name'] === 'HELP_BBCODE_BLOCK_OTHERS')
		{
			$this->language->add_lang('help', 'phpbb/mediaembed');

			$this->template->assign_block_vars('faq_block', [
				'BLOCK_TITLE'	=> $this->language->lang('HELP_EMBEDDING_MEDIA'),
				'SWITCH_COLUMN'	=> false,
			]);

			$uid = $bitfield = $flags = '';
			$demo_text = $this->language->lang('HELP_EMBEDDING_MEDIA_DEMO');
			generate_text_for_storage($demo_text, $uid, $bitfield, $flags, true, true);
			$demo_display = generate_text_for_display($demo_text, $uid, $bitfield, $flags);
			$list_sites = implode(', ', $this->get_siteIds());

			$this->template->assign_block_vars('faq_block.faq_row', [
				'FAQ_QUESTION'	=> $this->language->lang('HELP_EMBEDDING_MEDIA_QUESTION'),
				'FAQ_ANSWER'	=> $this->language->lang('HELP_EMBEDDING_MEDIA_ANSWER', $demo_text, $demo_display, $list_sites),
			]);
		}
	}

	/**
	 * Disable Media Embed plugin and tag if necessary
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function disable_media_embed($event)
	{
		/** @var \phpbb\textformatter\s9e\parser $service  */
		$service = $event['parser'];
		$parser = $service->get_parser();

		if ($this->disable_plugin)
		{
			$parser->disablePlugin('MediaEmbed');
		}

		if ($this->disable_tag)
		{
			$parser->disableTag('MEDIA');
		}
	}

	/**
	 * Check if forum permission allows Media Embed
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function check_forum_permission($event)
	{
		if (!$this->auth->acl_get('f_mediaembed', $event['forum_id']) || !$this->auth->acl_get('f_bbcode', $event['forum_id']))
		{
			$this->disable_plugin = true;
			$this->disable_tag = true;
		}
	}

	/**
	 * Check if user permission allows Media Embed in private messages
	 */
	public function check_pm_permission()
	{
		if (!$this->auth->acl_get('u_pm_mediaembed'))
		{
			$this->disable_plugin = true;
			$this->disable_tag = true;
		}
	}

	/**
	 * Check if signature posting is allowed.
	 * Posting signatures is 'sig', reparsing signatures is 'user_signature'.
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function check_signature($event)
	{
		if (($event['mode'] === 'sig' || $event['mode'] === 'text_reparser.user_signature') && !$this->config->offsetGet('media_embed_allow_sig'))
		{
			$this->disable_plugin = true;
			$this->disable_tag = true;
		}
	}

	/**
	 * Check if magic urls is allowed.
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function check_magic_urls($event)
	{
		if (!$event['allow_magic_url'] || !$this->config->offsetGet('media_embed_parse_urls'))
		{
			$this->disable_plugin = true;
		}
	}

	/**
	 * Check if bbcodes are allowed.
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function check_bbcode_enabled($event)
	{
		if (!$event['allow_bbcode'])
		{
			// Want to leave plugin enabled but it seems plugin won't work
			// when tag is disabled, so we have to disable both it seems.
			$this->disable_plugin = true;
			$this->disable_tag = true;
		}
	}

	/**
	 * Get allowed sites for media embedding
	 *
	 * @return array An array of sites
	 */
	protected function get_siteIds()
	{
		$siteIds = $this->config_text->get('media_embed_sites');

		return $siteIds ? json_decode($siteIds, true) : [];
	}
}
