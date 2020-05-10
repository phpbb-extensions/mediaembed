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

use \phpbb\config\config;
use \phpbb\mediaembed\cache\purge;

/**
 * Mediaembed cron task.
 */
class purge_cache extends \phpbb\cron\task\base
{
	/** @var config $config */
	protected $config;

	/** @var purge $purge_cache */
	protected $purge_cache;

	/**
	 * Constructor
	 *
	 * @param config       $config      Config object
	 * @param purge        $purge_cache Purge cache object
	 * @access public
	 */
	public function __construct(config $config, purge $purge_cache)
	{
		$this->config = $config;
		$this->purge_cache = $purge_cache;
	}

	/**
	 * {@inheritDoc}
	 */
	public function run()
	{
		try
		{
			$this->purge_cache->mediaembed_purge_cache();
			$this->config->set('mediaembed_last_gc', time(), false);
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
}
