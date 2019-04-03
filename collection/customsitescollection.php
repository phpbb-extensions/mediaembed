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

	/** @var string $ext_root_path */
	protected $ext_root_path;

	/**
	 * Constructor
	 *
	 * @param \phpbb\extension\manager $extension_manager
	 * @param string                   $ext_root_path
	 */
	public function __construct($extension_manager, $ext_root_path)
	{
		$this->extension_manager = $extension_manager;
		$this->ext_root_path = $ext_root_path;
	}

	/**
	 * Get custom site definitions collection object
	 *
	 * @return array Collection of JSON site definition files
	 */
	public function get_custom_sites_collection()
	{
		$finder = $this->extension_manager->get_finder();

		return $finder
			->set_extensions(array('phpbb/mediaembed'))
			->suffix('.json')
			->extension_directory('collection/sites')
			->get_files();
	}
}
