<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\collection;

class upstreamsitescollection
{
	/**
	 * Get changed upstream definitions plus hand-maintained compatibility overrides.
	 *
	 * @return array
	 */
	public function get_collection()
	{
		$upstream = require __DIR__ . '/generated/upstream_sites.php';
		$compatibility = require __DIR__ . '/compatibility_sites.php';

		return array_replace($upstream['sites'], $compatibility);
	}

	/**
	 * Get site IDs removed by target TextFormatter release.
	 *
	 * @return array
	 */
	public function get_removed_sites()
	{
		$upstream = require __DIR__ . '/generated/upstream_sites.php';

		return $upstream['removed_sites'];
	}

	/**
	 * Get generated collection metadata.
	 *
	 * @return array
	 */
	public function get_metadata()
	{
		$upstream = require __DIR__ . '/generated/upstream_sites.php';

		return [
			'base_version' => $upstream['base_version'],
			'target_version' => $upstream['target_version'],
		];
	}
}
