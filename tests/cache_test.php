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

class cache_test extends \phpbb_test_case
{
	/** @var \phpbb\cache\driver\driver_interface|\PHPUnit\Framework\MockObject\MockObject */
	protected $cache_driver;

	/** @var \phpbb\mediaembed\cache\cache */
	protected $membed_cache;

	/** @var string Cache key used for the parser */
	protected $cache_key_parser = 'text_formatter.cache.parser.key';

	/** @var string Cache key used for the renderer */
	protected $cache_key_renderer = 'text_formatter.cache.renderer.key';

	protected function setUp(): void
	{
		parent::setUp();

		$this->cache_driver = $this->getMockBuilder('phpbb\cache\driver\file')
			->disableOriginalConstructor()
			->onlyMethods(['destroy', 'get', 'put', 'remove_file'])
			->getMock();

		$this->membed_cache = new \phpbb\mediaembed\cache\cache(
			$this->cache_driver,
			$this->cache_key_parser,
			$this->cache_key_renderer
		);
	}

	public function test_purge_mediaembed_cache()
	{
		$this->cache_driver->cache_dir = __DIR__ . '/fixtures/cache';

		$this->cache_driver
			->expects($this->exactly(2))
			->method('remove_file');

		$this->membed_cache->purge_mediaembed_cache();
	}

	public function test_purge_textformatter_cache()
	{
		$expected_args = [$this->cache_key_parser, $this->cache_key_renderer];
		$invocation = 0;
		$this->cache_driver
			->expects($this->exactly(count($expected_args)))
			->method('destroy')
			->willReturnCallback(function($arg) use (&$invocation, $expected_args) {
				self::assertEquals($expected_args[$invocation++], $arg);
			});

		$this->membed_cache->purge_textformatter_cache();
	}

	public function test_media_embed_max_width_cache()
	{
		$max_widths = [['site' => 'youtube', 'width' => '480px']];
		$this->cache_driver->expects(self::once())
			->method('get')
			->with('_phpbb_mediaembed_max_width')
			->willReturn($max_widths);
		$this->cache_driver->expects(self::once())
			->method('put')
			->with('_phpbb_mediaembed_max_width', $max_widths);
		$this->cache_driver->expects(self::once())
			->method('destroy')
			->with('_phpbb_mediaembed_max_width');

		self::assertSame($max_widths, $this->membed_cache->get_media_embed_max_width());
		$this->membed_cache->set_media_embed_max_width($max_widths);
		$this->membed_cache->purge_media_embed_max_width();
	}
}
