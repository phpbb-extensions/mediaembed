<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\collection;

class customsitescollection
{
	/** @var \phpbb\extension\manager */
	protected $extension_manager;

	/**
	 * Constructor
	 *
	 * @param \phpbb\extension\manager $extension_manager
	 */
	public function __construct(\phpbb\extension\manager $extension_manager)
	{
		$this->extension_manager = $extension_manager;
	}

	/**
	 * Get a collection of custom YAML site definition files
	 *
	 * @return array Collection of YAML site definition files
	 */
	public function get_custom_sites_collection()
	{
		$finder = $this->extension_manager->get_finder();

		return $finder
			->set_extensions(array('phpbb/mediaembed'))
			->suffix('.yml')
			->extension_directory('collection/sites')
			->get_files();
	}
}
