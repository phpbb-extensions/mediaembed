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

use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\mediaembed\collection\customsitescollection;
use phpbb\mediaembed\collection\upstreamsitescollection;
use phpbb\mediaembed\ext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Configure MediaEmbed formatter and runtime cache.
 */
class formatter_listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var db_text */
	protected $config_text;

	/** @var customsitescollection */
	protected $custom_sites;

	/** @var upstreamsitescollection */
	protected $upstream_sites;

	/** @var string */
	protected $cache_dir;

	/** @var bool|null Cached result of is_phpbb4() */
	protected $is_phpbb4;

	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.text_formatter_s9e_configure_after' => [['add_custom_sites', 3], ['enable_media_sites', 2], ['configure_url_parsing', 1], ['modify_tag_templates', 0]],
			'core.text_formatter_s9e_parser_setup' => 'setup_cache_dir',
		];
	}

	/**
	 * Constructor.
	 *
	 * @param config                  $config         Configuration service
	 * @param db_text                 $config_text    Text configuration service
	 * @param customsitescollection   $custom_sites   Custom site definitions
	 * @param upstreamsitescollection $upstream_sites Upstream site definitions
	 * @param string                  $cache_dir      Media scraping cache directory
	 */
	public function __construct(config $config, db_text $config_text, customsitescollection $custom_sites, upstreamsitescollection $upstream_sites, $cache_dir)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->custom_sites = $custom_sites;
		$this->upstream_sites = $upstream_sites;
		$this->cache_dir = $cache_dir;
	}

	/**
	 * Add upstream and custom site definitions to MediaEmbed.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function add_custom_sites($event)
	{
		if (!$this->is_phpbb4())
		{
			foreach ($this->upstream_sites->get_removed_sites() as $site_id)
			{
				unset($event['configurator']->MediaEmbed->defaultSites[$site_id]);
			}

			foreach ($this->upstream_sites->get_collection() as $site_id => $site_config)
			{
				$event['configurator']->MediaEmbed->defaultSites->add($site_id, $site_config);
			}
		}

		foreach ($this->custom_sites->get_collection() as $site)
		{
			$name = basename($site, ext::YML);
			$event['configurator']->MediaEmbed->defaultSites->add($name, Yaml::parseFile($site));
		}
	}

	/**
	 * Enable configured media sites.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function enable_media_sites($event)
	{
		foreach ($this->get_site_ids() as $site_id)
		{
			if (isset($event['configurator']->BBCodes[$site_id]))
			{
				continue;
			}

			try
			{
				$event['configurator']->MediaEmbed->add($site_id);
			}
			catch (\RuntimeException $e)
			{
				continue;
			}
		}
	}

	/**
	 * Configure plain URL parsing.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function configure_url_parsing($event)
	{
		if (!$this->config->offsetGet('media_embed_parse_urls'))
		{
			$event['configurator']->MediaEmbed->finalize();
			unset($event['configurator']->MediaEmbed);
		}
	}

	/**
	 * Apply compatibility and privacy changes to generated tag templates.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function modify_tag_templates($event)
	{
		try
		{
			$tag = $event['configurator']->tags['YOUTUBE'];
			$tag->template = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $tag->template);
			if (!$this->is_phpbb4())
			{
				$tag->template = str_replace(' allowfullscreen', ' referrerpolicy="origin" allowfullscreen', $tag->template);
			}
		}
		catch (\RuntimeException $e)
		{
			// YouTube is not enabled.
		}
	}

	/**
	 * Configure media scraping cache directory.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function setup_cache_dir($event)
	{
		if ($this->cache_dir && $this->config->offsetGet('media_embed_enable_cache'))
		{
			$event['parser']->get_parser()->registeredVars['cacheDir'] = $this->cache_dir;
		}
	}

	/**
	 * Get enabled media site identifiers.
	 *
	 * @return array
	 */
	protected function get_site_ids()
	{
		$site_ids = $this->config_text->get('media_embed_sites');

		return $site_ids ? json_decode($site_ids, true) : [];
	}

	/**
	 * Check whether current phpBB version is 4.0 or newer.
	 *
	 * @return bool
	 */
	private function is_phpbb4()
	{
		if ($this->is_phpbb4 === null)
		{
			$this->is_phpbb4 = phpbb_version_compare($this->config['version'], '4.0.0-a1', '>=');
		}

		return $this->is_phpbb4;
	}
}
