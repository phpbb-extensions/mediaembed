<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\cron;

/**
 * mediaembed cron task.
 */
class mediaembed extends \phpbb\cron\task\base
{
	/** @var \phpbb\config\config $config Config object */
	protected $config;

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config                  $config     Config object
	 * @param \phpbb\cache\driver\driver_interface  $cache      Cache jbject
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\cache\driver\driver_interface $cache)
	{
		$this->config = $config;
		$this->cache = $cache;
	}

	/**
	 * {@inheritDoc}
	 */
	public function run()
	{
		try
		{
			$this->mediaembed_purge_cache();
		}
		catch (\RuntimeException $e)
		{
			return;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_runnable()
	{
		return (bool) $this->config['media_embed_enable_cache'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_run()
	{
		return $this->config['mediaembed_last_gc'] < strtotime('24 hours ago');
	}

	/**
	 * Purge all MediaEmbed cache files
	 */
	protected function mediaembed_purge_cache()
	{
		try
		{
			$iterator = new \DirectoryIterator($this->cache->cache_dir);
		}
		catch (\Exception $e)
		{
			return;
		}

		foreach ($iterator as $fileInfo)
		{
			if ($fileInfo->isDot() || $fileInfo->isDir())
			{
				continue;
			}

			$filename = $fileInfo->getFilename();
			if (strpos($filename, 'http.') === 0)
			{
				$this->cache->remove_file($fileInfo->getPathname());
			}
		}

		$this->config->set('mediaembed_last_gc', time(), false);
	}
}
