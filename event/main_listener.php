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
	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup'							=> 'load_language_on_setup',
			'core.text_formatter_s9e_configure_after'	=> 'configure_media_embed',
			'core.help_manager_add_block_before'		=> 'media_embed_help',
		];
	}

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language $language
	 * @param \phpbb\template\template $template
	 */
	public function __construct(\phpbb\language\language $language, \phpbb\template\template $template)
	{
		$this->language = $language;
		$this->template = $template;
	}

	/**
	 * Load common lang files during user setup
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'phpbb/mediaembed',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
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

		foreach ($configurator->MediaEmbed->defaultSites->getIds() as $siteId)
		{
			if (isset($configurator->BBCodes[$siteId]))
			{
				continue;
			}

			$configurator->MediaEmbed->add($siteId);
		}
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

			$this->template->assign_block_vars('faq_block.faq_row', [
				'FAQ_QUESTION'	=> $this->language->lang('HELP_EMBEDDING_MEDIA_QUESTION'),
				'FAQ_ANSWER'	=> $this->language->lang('HELP_EMBEDDING_MEDIA_ANSWER', $demo_text, $demo_display),
			]);
		}
	}
}
