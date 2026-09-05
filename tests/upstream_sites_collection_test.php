<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\tests;

use phpbb\mediaembed\collection\upstreamsitescollection;
use s9e\TextFormatter\Configurator;

class upstream_sites_collection_test extends \phpbb_test_case
{
	public function test_metadata()
	{
		$collection = new upstreamsitescollection();
		$metadata = $collection->get_metadata();

		$this->assertSame('2.11.5', $metadata['base_version']);
		$this->assertRegExp('/^\d+\.\d+\.\d+$/D', $metadata['target_version']);
		$this->assertTrue(version_compare($metadata['target_version'], $metadata['base_version'], '>'));
		$this->assertNotEmpty($collection->get_removed_sites());
	}

	public function test_collection_is_compatible_with_bundled_textformatter()
	{
		$collection = new upstreamsitescollection();
		$sites = $collection->get_collection();
		$configurator = new Configurator();

		$this->assertNotEmpty($sites);
		foreach ($sites as $site_id => $site_config)
		{
			$this->assertArrayNotHasKey('helper', $site_config, "Unsupported helper remains on '$site_id'");
			$configurator->MediaEmbed->defaultSites->add($site_id, $site_config);
			$configurator->MediaEmbed->add($site_id);
		}

		$configurator->finalize();
	}

	public function test_compatibility_file_contains_only_minimal_patches()
	{
		$patches = require __DIR__ . '/../collection/compatibility_sites.php';

		$this->assertSame(['bluesky', 'mastodon', 'pastebin', 'peertube', 'vk', 'xenforo'], array_keys($patches));
		foreach ($patches as $site_id => $patch)
		{
			$this->assertNotEmpty($patch, "Empty compatibility patch for '$site_id'");
			$this->assertEmpty(array_diff(array_keys($patch), ['append', 'replace', 'unset']), "Full definition found in compatibility patch for '$site_id'");
		}
	}

	public function test_upstream_examples_match_their_definitions()
	{
		$sites = (new upstreamsitescollection())->get_collection();

		foreach ($sites as $site_id => $site_config)
		{
			foreach ((array) $site_config['example'] as $example)
			{
				$this->assertExampleHost($site_id, $site_config, $example);
				$this->assertExampleMatches($site_id, $site_config, $example);
			}
		}
	}

	public function test_non_scraping_examples_are_parsed()
	{
		$sites = (new upstreamsitescollection())->get_collection();
		$configurator = new Configurator();
		foreach ($sites as $site_id => $site_config)
		{
			$configurator->MediaEmbed->defaultSites->add($site_id, $site_config);
			$configurator->MediaEmbed->add($site_id);
		}
		extract($configurator->finalize(), EXTR_SKIP);

		foreach ($sites as $site_id => $site_config)
		{
			if (!empty($site_config['scrape']))
			{
				continue;
			}
			foreach ((array) $site_config['example'] as $example)
			{
				$this->assertStringContainsString('<' . strtoupper($site_id), $parser->parse($example), "Example was not parsed for '$site_id': $example");
			}
		}
	}

	protected function assertExampleHost($site_id, array $site_config, $example)
	{
		$example_host = strtolower(parse_url($example, PHP_URL_HOST));
		$host_matches = false;
		foreach ((array) $site_config['host'] as $host)
		{
			if ($example_host === $host || substr($example_host, -strlen('.' . $host)) === '.' . $host)
			{
				$host_matches = true;
				break;
			}
		}

		$this->assertTrue($host_matches, "Example host does not match '$site_id': $example");
	}

	protected function assertExampleMatches($site_id, array $site_config, $example)
	{
		$matched = $this->matchesAnyRegexp($example, isset($site_config['extract']) ? $site_config['extract'] : []);
		$scrape_configs = isset($site_config['scrape']) ? $site_config['scrape'] : [];
		if ($scrape_configs && !isset($scrape_configs[0]))
		{
			$scrape_configs = [$scrape_configs];
		}
		foreach ($scrape_configs as $scrape_config)
		{
			if (is_array($scrape_config) && isset($scrape_config['match']))
			{
				$matched = $this->matchesAnyRegexp($example, $scrape_config['match']) || $matched;
			}
		}

		$this->assertTrue($matched, "Example does not match '$site_id': $example");
	}

	protected function matchesAnyRegexp($value, $regexps)
	{
		foreach ((array) $regexps as $regexp)
		{
			if (preg_match($regexp, $value))
			{
				return true;
			}
		}

		return false;
	}
}
