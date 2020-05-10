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

use \Symfony\Component\Finder\Finder;

/**
 * mediaembed cron task.
 */
class purge
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/**
	 * Constructor
	 *
	 * @param \phpbb\cache\driver\driver_interface  $cache      Cache jbject
	 * @access public
	 */
	public function __construct(\phpbb\cache\driver\driver_interface $cache)
	{
		$this->cache = $cache;
		$this->finder = new Finder();
	}

	/**
	 * Purge all MediaEmbed cache files
	 */
	public function purge()
	{
		$this->finder
			->name('http.*')
			->in($this->cache->cache_dir)
			->files();

		foreach ($this->finder as $file)
		{
			$this->cache->remove_file($file->getRealPath());
		}
	}
}
