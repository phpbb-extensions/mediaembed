<?php

namespace phpbb\mediaembed\tests;

use phpbb\extension\manager;
use phpbb\filesystem\filesystem;
use phpbb\mediaembed\collection\customsitescollection;
use PHPUnit\Framework\MockObject\MockObject;

class custom_sites_collection_test extends \phpbb_test_case
{
	/** @var manager|MockObject */
	protected $ext_manager;
	protected $phpbb_root_path;
	protected $php_ext;

	protected function setUp(): void
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->ext_manager = $this->getMockBuilder('\phpbb\extension\manager')
			->disableOriginalConstructor()
			->getMock();

		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;
	}

	public function test_get_collection()
	{
		$finder = new \phpbb\finder(
			new filesystem(),
			$this->phpbb_root_path,
			$this->getMockBuilder('\phpbb\cache\service')->disableOriginalConstructor()->getMock(),
			$this->php_ext,
			'_ext_finder'
		);

		$this->ext_manager->expects(self::once())
			->method('get_finder')
			->willReturn($finder);

		$customsitescollection = new customsitescollection($this->ext_manager);

		$collection = $customsitescollection->get_collection();

		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/allocine.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/bilibili.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/dotsub.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/ebaumsworld.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/mastodon.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/moddb.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/ok.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/schooltube.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/snotr.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/tenor.yml', $collection);
		$this->assertContains('phpBB/ext/phpbb/mediaembed/collection/sites/videopress.yml', $collection);
		$this->assertNotContains('phpBB/ext/phpbb/mediaembed/collection/sites/youtube.yml', $collection);
	}
}
