<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed;

class ext extends \phpbb\extension\base
{
	/**
	 * Extension can be enabled if the s9e TextFormatter is available
	 *
	 * {@inheritDoc}
	 */
	public function is_enableable()
	{
		return class_exists('\s9e\TextFormatter\Configurator');
	}
}
