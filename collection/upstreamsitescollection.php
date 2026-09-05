<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\collection;

class upstreamsitescollection
{
	/**
	 * Get changed upstream definitions with hand-maintained compatibility patches.
	 *
	 * @return array
	 */
	public function get_collection()
	{
		$upstream = require __DIR__ . '/generated/upstream_sites.php';
		$patches = require __DIR__ . '/compatibility_sites.php';
		$sites = $upstream['sites'];

		foreach ($patches as $site_id => $patch)
		{
			foreach (isset($patch['unset']) ? $patch['unset'] : [] as $path)
			{
				$this->unset_path($sites[$site_id], $path);
			}
			foreach (isset($patch['replace']) ? $patch['replace'] : [] as $path => $value)
			{
				$this->set_path($sites[$site_id], $path, $value);
			}
			foreach (isset($patch['append']) ? $patch['append'] : [] as $path => $values)
			{
				$current = $this->get_path($sites[$site_id], $path);
				$this->set_path($sites[$site_id], $path, array_merge((array) $current, $values));
			}
		}

		return $sites;
	}

	private function get_path(array $definition, $path)
	{
		foreach (explode('.', $path) as $key)
		{
			$definition = $definition[$key];
		}

		return $definition;
	}

	private function set_path(array &$definition, $path, $value)
	{
		$keys = explode('.', $path);
		$last_key = array_pop($keys);
		foreach ($keys as $key)
		{
			$definition =& $definition[$key];
		}
		$definition[$last_key] = $value;
	}

	private function unset_path(array &$definition, $path)
	{
		$keys = explode('.', $path);
		$last_key = array_pop($keys);
		foreach ($keys as $key)
		{
			$definition =& $definition[$key];
		}
		unset($definition[$last_key]);
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
