<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Generate MediaEmbed definition delta between two TextFormatter releases.
 *
 * Usage:
 * php scripts/update_mediaembed_sites.php --target=2.19.3
 * php scripts/update_mediaembed_sites.php --target=2.19.3 --check
 * php scripts/update_mediaembed_sites.php --target-file=/path/to/new.php --base-file=/path/to/old.php
 *
 */

if (PHP_SAPI !== 'cli')
{
	http_response_code(404);
	exit;
}

require __DIR__ . '/update_mediaembed_sites_lib.php';

exit(update_mediaembed_sites());
