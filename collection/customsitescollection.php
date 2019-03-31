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

/**
 * Collection of custom site definitions
 *
 * See https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Add_custom/
 */
class CustomSitesCollection
{
	/** @var array $custom_sites_collection Custom site definitions */
	protected $custom_sites_collection = [
		'ok' =>
		[
			'host'    => 'ok.ru',
			'name'    => 'Odnoklassniki',
			'extract' => [
				'!ok.ru/video/(?<id>\\d+)!',
				'!ok.ru/live/(?<id>\\d+)!',
			],
			'iframe'  => ['src' => 'https://ok.ru/videoembed/{@id}']
		],
	];

	/**
	 * Get custom site definitions array for media embedding
	 *
	 * @return array An array of custom site definitions
	 */
	public function get_custom_sites_collection()
	{
		return $this->custom_sites_collection;
	}
}
