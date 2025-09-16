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

use phpbb\language\language;
use phpbb\language\language_file_loader;

class ext_test extends \phpbb_test_case
{
	/** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\DependencyInjection\ContainerInterface $container */
	protected $container;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\extension\manager $extension_manager */
	protected $extension_manager;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\finder\finder $extension_finder */
	protected $extension_finder;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\db\migrator $migrator */
	protected $migrator;

	protected function setUp(): void
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
			->disableOriginalConstructor()
			->getMock();

		$this->extension_manager = $this->getMockBuilder('\phpbb\extension\manager')
			->disableOriginalConstructor()
			->getMock();

		$this->extension_finder = $this->getMockBuilder('\phpbb\finder\finder')
			->disableOriginalConstructor()
			->getMock();

		$this->migrator = $this->getMockBuilder('\phpbb\db\migrator')
			->disableOriginalConstructor()
			->getMock();

		$services = ['ext.manager', 'language'];
		$returns = [$this->extension_manager, new language(new language_file_loader($phpbb_root_path, $phpEx))];
		$callCount = 0;
		$this->container->method('get')
			->willReturnCallback(function($service) use ($services, $returns, &$callCount) {
				$this->assertEquals($services[$callCount], $service);
				return $returns[$callCount++];
			});
	}

	protected function get_ext()
	{
		return new \phpbb\mediaembed\ext(
			$this->container,
			$this->extension_finder,
			$this->migrator,
			'phpbb/mediaembed',
			''
		);
	}

	public static function ext_test_data()
	{
		return [
			[false, true],
			[true, ['S9E_MEDIAEMBED_ERROR']],
		];
	}

	/**
	 * @dataProvider ext_test_data
	 * @param $s9e_present
	 * @param $expected
	 * @return void
	 */
	public function test_ext($s9e_present, $expected)
	{
		$this->extension_manager->expects(self::once())
			->method('is_enabled')
			->with('s9e/mediaembed')
			->willReturn($s9e_present);

		self::assertSame($expected, $this->get_ext()->is_enableable());
	}
}
