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
	/** @var string $sites_dir */
	protected $sites_dir;

	/**
	 * Constructor
	 *
	 * @param string $sites_dir
	 */
	public function __construct($sites_dir)
	{
		$this->sites_dir = $sites_dir;
	}

	/**
	 * Get custom XML site definitions collection object
	 *
	 * @return object XmlFileDefinitionCollection
	 */
	public function get_custom_sites_collection()
	{
		return new XmlFileDefinitionCollection($this->sites_dir);
	}
}
