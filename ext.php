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
		return $this->s9e_textformatter_installed() && !$this->s9e_mediamebed_installed();
	}

	/**
	 * Check if s9e TextFormatter is installed (it must be
	 * to enable this extension).
	 *
	 * @return bool
	 */
	public function s9e_textformatter_installed()
	{
		return class_exists('\s9e\TextFormatter\Configurator');
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
