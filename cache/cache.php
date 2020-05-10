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

use Symfony\Component\Finder\Finder;

/**
 * Media Embed cache handling class.
 */
class cache
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/**
	 * Constructor
	 *
	 * @param \phpbb\cache\driver\driver_interface  $cache   Cache driver object
	 * @access public
	 */
	public function __construct(\phpbb\cache\driver\driver_interface $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * Purge cached MediaEmbed files
	 */
	public function purge()
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
}
