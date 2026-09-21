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
use phpbb\mediaembed\collection\sitescollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Configure MediaEmbed formatter and runtime cache.
 */
class formatter_listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var db_text */
	protected $config_text;

	/** @var sitescollection */
	protected $sites;

	/** @var string */
	protected $cache_dir;

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
	 * @param config          $config      Configuration service
	 * @param db_text         $config_text Text configuration service
	 * @param sitescollection $sites       Media site definitions
	 * @param string          $cache_dir   Media scraping cache directory
	 */
	public function __construct(config $config, db_text $config_text, sitescollection $sites, $cache_dir)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->sites = $sites;
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
		$this->sites->configure($event['configurator']);
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
			if (!$this->sites->is_phpbb4())
			{
				$tag->template = str_replace(' allowfullscreen', ' referrerpolicy="origin" allowfullscreen', $tag->template);
			}

			$tag->template = $this->configure_youtube_shorts($tag);
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
	 * Add tag filter to mark YouTube Shorts URLs.
	 *
	 * @param s9e\TextFormatter\Configurator\Items\Tag $tag Tag to add the filter to
	 * @param string $text Media URL src content
	 *
	 * @return void
	 */
	public static function filter_youtube_shorts($tag, $text)
	{
		if (strpos($text, '/shorts/') !== false)
		{
			$tag->setAttribute('is_shorts', '1');
		}
	}

	/**
	 * Modify YouTube tag template to vertically render YouTube Shorts
	 *
	 * @param s9e\TextFormatter\Configurator\Items\Tag $tag Tag to configure
	 *
	 * @return string
	 */
	public function configure_youtube_shorts($tag)
	{
		// Add 'is_shorts' tag attribute if does not exist
		if (!isset($tag->attributes['is_shorts']))
		{
			$tag->attributes->add('is_shorts')->required = false;
		}

		// Add filterchain to filter URLs using 'is_shorts' attribute
		$tag->filterChain
			->prepend([__CLASS__, 'filter_youtube_shorts'])
			->addParameterByName('text');

		// Modify template string to inject YouTube Shorts rendering attributes
		$modified_template = str_replace(
			['max-width:640px', 'padding-bottom:56.25%'],
			['max-width:360px', 'padding-bottom:177.77%'],
			(string) $tag->template
		);

		return
			'<xsl:choose>' .
				'<xsl:when test="@is_shorts">' . $modified_template . '</xsl:when>' .
				'<xsl:otherwise>' . (string) $tag->template . '</xsl:otherwise>' .
			'</xsl:choose>';
	}
}
