<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\migrations;

/**
 * Migration to install mediaembed cache and cron task data
 */
class m5_cache extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\phpbb\mediaembed\migrations\m4_permissions'];
	}

	public function effectively_installed()
	{
		return isset($this->config['media_embed_enable_cache'])
			&& isset($this->config['mediaembed_last_gc']);
	}

	public function update_data()
	{
		return [
			['config.add', ['media_embed_enable_cache', 0]],
			['config.add', ['mediaembed_last_gc', 0, true]],
		];
	}
}
