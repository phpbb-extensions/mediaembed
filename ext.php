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
	 * {@inheritDoc}
	 */
	public function is_enableable()
	{
		return $this->phpbb_version_is_valid() && !$this->s9e_mediamebed_installed();
	}

	/**
	 * Check the installed phpBB version meets this
	 * extension's requirements.
	 *
	 * Requires phpBB 3.2.0 and TextFormatter 0.8.1
	 *
	 * @return bool
	 */
	public function phpbb_version_is_valid()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=');
	}

	/**
	 * Check if s9e MediaEmbed extension for phpBB is installed
	 * (it must NOT be to enable this extension).
	 *
	 * @return bool
	 */
	public function s9e_mediamebed_installed()
	{
		return class_exists('\s9e\mediaembed\ext');
	}
}
