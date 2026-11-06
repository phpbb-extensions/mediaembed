<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\cache;

use phpbb\cache\driver\driver_interface as cache_driver;
use Symfony\Component\Finder\Finder;

/**
 * Media Embed cache handling class.
 */
class cache
{
	/** @var string Cache key used for decoded custom media widths */
	protected const MAX_WIDTH_CACHE_KEY = '_phpbb_mediaembed_max_width';

	/** @var cache_driver */
	protected $cache;

	/** @var string Cache key used for the parser */
	protected $cache_key_parser;

	/** @var string Cache key used for the renderer */
	protected $cache_key_renderer;

	/**
	 * Constructor
	 *
	 * @param cache_driver $cache              Cache driver object
	 * @param string       $cache_key_parser   Cache key used for the parser
	 * @param string       $cache_key_renderer Cache key used for the renderer
	 */
	public function __construct(cache_driver $cache, $cache_key_parser, $cache_key_renderer)
	{
		$this->cache = $cache;
		$this->cache_key_parser = $cache_key_parser;
		$this->cache_key_renderer = $cache_key_renderer;
	}

	/**
	 * Purge cached MediaEmbed files
	 *
	 * @return void
	 */
	public function purge_mediaembed_cache()
	{
		$finder = new Finder();
		$finder
			->name('http.*')
			->in($this->cache->cache_dir)
			->files();

		foreach ($finder as $file)
		{
			$this->cache->remove_file($file->getRealPath());
		}
	}

	/**
	 * Purge cached TextFormatter files
	 *
	 * @return void
	 */
	public function purge_textformatter_cache()
	{
		$this->cache->destroy($this->cache_key_parser);
		$this->cache->destroy($this->cache_key_renderer);
	}

	/**
	 * Get cached custom media widths.
	 *
	 * @return array|false Cached widths, or false on a cache miss
	 */
	public function get_media_embed_max_width()
	{
		return $this->cache->get(self::MAX_WIDTH_CACHE_KEY);
	}

	/**
	 * Cache custom media widths.
	 *
	 * @param array $max_widths Decoded custom media widths
	 * @return void
	 */
	public function set_media_embed_max_width(array $max_widths)
	{
		$this->cache->put(self::MAX_WIDTH_CACHE_KEY, $max_widths);
	}

	/**
	 * Purge cached custom media widths.
	 *
	 * @return void
	 */
	public function purge_media_embed_max_width()
	{
		$this->cache->destroy(self::MAX_WIDTH_CACHE_KEY);
	}
}
