<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\tests;

class cron_test extends \phpbb_test_case
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\mediaembed\cron\purge_cache */
	protected $cron_task;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\mediaembed\cache\cache */
	protected $cache;

	protected function setUp(): void
	{
		parent::setUp();

		$this->config = new \phpbb\config\config([]);
		$this->cache = $this->getMockBuilder('\phpbb\mediaembed\cache\cache')
			->disableOriginalConstructor()
			->getMock();

		$this->cron_task = new \phpbb\mediaembed\cron\purge_cache($this->config, $this->cache);
	}

	/**
	 * Test the cron task runs correctly
	 */
	public function test_run()
	{
		// Get the last_run
		$last_run = $this->config['mediaembed_last_gc'];

		// Test purge_cache() is called only once
		$this->cache->expects(self::once())
			->method('purge_mediaembed_cache');

		// Run the cron task
		$this->cron_task->run();

		// Assert the autogroups_last_run value has been updated
		self::assertNotEquals($last_run, $this->config['mediaembed_last_gc']);
	}

	/**
	 * Data set for test_should_run
	 *
	 * @return array Array of test data
	 */
	public static function should_run_data()
	{
		return [
			[time(), false],
			[strtotime('23 hours ago'), false],
			[strtotime('25 hours ago'), true],
			['', true],
			[0, true],
			[null, true],
		];
	}

	/**
	 * Test cron task should run after 24 hours
	 *
	 * @dataProvider should_run_data
	 */
	public function test_should_run($time, $expected)
	{
		// Set the last cron run time
		$this->config['mediaembed_last_gc'] = $time;

		// Assert we get the expected result from should_run()
		self::assertSame($expected, $this->cron_task->should_run());
	}

	public static function is_runnable_data()
	{
		return [
			[true],
			[false],
		];
	}

	/**
	 * Test the cron task is runnable
	 *
	 * @dataProvider is_runnable_data
	 */
	public function test_is_runnable($expected)
	{
		$this->config['media_embed_enable_cache'] = $expected;

		self::assertSame($expected, $this->cron_task->is_runnable());
	}
}
