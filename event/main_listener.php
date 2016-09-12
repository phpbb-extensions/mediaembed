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
	/**
	 * @inheritdoc
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup'							=> 'load_language_on_setup',
			'core.text_formatter_s9e_configure_after'	=> 'configure_media_embed',
		];
	}

	/**
	 * Load common lang files during user setup
	 *
	 * @access public
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
	 * @access public
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
}
