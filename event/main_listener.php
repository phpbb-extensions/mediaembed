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
	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\config\db_text $config_text */
	protected $config_text;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var bool $signature Posting mode is signature */
	protected $signature = false;

	public static function getSubscribedEvents()
	{
		return [
			'core.text_formatter_s9e_configure_after'	=> 'configure_media_embed',
			'core.display_custom_bbcodes'				=> 'setup_media_bbcode',
			'core.help_manager_add_block_before'		=> 'media_embed_help',
			'core.message_parser_check_message'			=> 'set_signature',
			'core.text_formatter_s9e_parser_setup'		=> 'disable_in_signature',
		];
	}

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config     $config
	 * @param \phpbb\config\db_text    $config_text
	 * @param \phpbb\language\language $language
	 * @param \phpbb\template\template $template
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\language\language $language, \phpbb\template\template $template)
	{
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
			generate_text_for_storage($demo_text, $uid, $bitfield, $flags, true);
			$demo_display = generate_text_for_display($demo_text, $uid, $bitfield, $flags);
			$list_sites = implode(', ', $this->get_siteIds());

			$this->template->assign_block_vars('faq_block.faq_row', [
				'FAQ_QUESTION'	=> $this->language->lang('HELP_EMBEDDING_MEDIA_QUESTION'),
				'FAQ_ANSWER'	=> $this->language->lang('HELP_EMBEDDING_MEDIA_ANSWER', $demo_text, $demo_display, $list_sites),
			]);
		}
	}

	/**
	 * Set the signature property.
	 * Posting signatures is 'sig', reparsing signatures is 'user_signature'.
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function set_signature($event)
	{
		$this->signature = $event['mode'] === 'sig' || $event['mode'] === 'text_reparser.user_signature';
	}

	/**
	 * Disable the Media Embed plugin when posting mode is a signature
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function disable_in_signature($event)
	{
		if (!$this->signature || $this->config->offsetGet('media_embed_allow_sig'))
		{
			return;
		}

		/** @var \phpbb\textformatter\s9e\parser $service  */
		$service = $event['parser'];
		$parser = $service->get_parser();
		$parser->disablePlugin('MediaEmbed');
		$parser->disableTag('MEDIA');
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
