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

use s9e\TextFormatter\Plugins\MediaEmbed\Configurator\Collections\XmlFileDefinitionCollection;

class customsitescollection
{
	/** @var string $root_path */
	protected $root_path;

	/**
	 * Constructor
	 *
	 * @param string $phpbb_root_path
	 */
	public function __construct($phpbb_root_path)
	{
		$this->root_path = $phpbb_root_path;
	}

	/**
	 * Get custom XML site definitions collection object
	 *
	 * @return object XmlFileDefinitionCollection
	 */
	public function get_custom_sites_collection()
	{
		return new XmlFileDefinitionCollection($this->root_path . 'ext/phpbb/mediaembed/collection/xml');
	}
}
