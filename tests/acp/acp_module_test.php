<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\acp;

require_once __DIR__ . '/../../../../../includes/functions_module.php';

class acp_module_test extends \phpbb_test_case
{
	/** @var bool A return value for check_form_key() */
	public static $valid_form = true;

	/** @var \phpbb_mock_extension_manager */
	protected $extension_manager;

	/** @var \phpbb\module\module_manager */
	protected $module_manager;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request|\PHPUnit\Framework\MockObject\MockObject  */
	protected $request;

	/** @var \phpbb\template\template|\PHPUnit\Framework\MockObject\MockObject  */
	protected $template;

	/** @var \Symfony\Component\DependencyInjection\ContainerInterface&\PHPUnit\Framework\MockObject\MockObject  */
	protected $phpbb_container;

	/** @var \phpbb\mediaembed\controller\acp_controller|\PHPUnit\Framework\MockObject\MockObject  */
	protected $acp_controller;

	protected function setUp(): void
	{
		global $phpbb_dispatcher, $phpbb_extension_manager, $phpbb_root_path, $phpEx;

		$this->extension_manager = new \phpbb_mock_extension_manager(
			$phpbb_root_path,
			[
				'phpbb/mediaembed' => [
					'ext_name' => 'phpbb/mediaembed',
					'ext_active' => '1',
					'ext_path' => 'ext/phpbb/mediaembed/',
				],
			]);
		$phpbb_extension_manager = $this->extension_manager;

		$this->language = new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx));
		$this->request = $this->createMock('\phpbb\request\request');
		$this->template = $this->createMock('\phpbb\template\template');
		$this->phpbb_container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$this->acp_controller = $this->createMock('\phpbb\mediaembed\controller\acp_controller');

		$this->module_manager = new \phpbb\module\module_manager(
			new \phpbb\cache\driver\dummy(),
			$this->createMock('\phpbb\db\driver\driver_interface'),
			$this->extension_manager,
			MODULES_TABLE,
			$phpbb_root_path,
			$phpEx
		);

		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();
	}

	public function test_module_info()
	{
		self::assertEquals([
			'\\phpbb\\mediaembed\\acp\\main_module' => [
				'filename'	=> '\\phpbb\\mediaembed\\acp\\main_module',
				'title'		=> 'ACP_PHPBB_MEDIA_EMBED',
				'modes'		=> [
					'settings'	=> [
						'title'	=> 'ACP_PHPBB_MEDIA_EMBED_SETTINGS',
						'auth'	=> 'ext_phpbb/mediaembed && acl_a_bbcode',
						'cat'	=> ['ACP_PHPBB_MEDIA_EMBED']
					],
					'manage'	=> [
						'title'	=> 'ACP_PHPBB_MEDIA_EMBED_MANAGE',
						'auth'	=> 'ext_phpbb/mediaembed && acl_a_bbcode',
						'cat'	=> ['ACP_PHPBB_MEDIA_EMBED']
					],
				],
			],
		], $this->module_manager->get_module_infos('acp', 'acp_main_module'));
	}

	public static function module_auth_test_data()
	{
		return [
			// module_auth, expected result
			['ext_foo/bar', false],
			['ext_phpbb/mediaembed', true],
		];
	}

	/**
	 * @dataProvider module_auth_test_data
	 */
	public function test_module_auth($module_auth, $expected)
	{
		self::assertEquals($expected, \p_master::module_auth($module_auth, 0));
	}

	public static function main_module_test_data()
	{
		return [
			['display', 'manage', null], // test the display of manage sites module
			['display', 'settings', null], // test the display of settings module
			['save', 'manage', E_USER_NOTICE], // test successful saving of manage sites
			['save', 'settings', E_USER_NOTICE], // test successful saving of settings
			['save', 'manage', E_USER_WARNING], // test failed saving of manage sites
			['save', 'settings', E_USER_WARNING], // test failed saving of settings
		];
	}

	/**
	 * @dataProvider main_module_test_data
	 */
	public function test_main_module($action, $mode, $expected)
	{
		global $phpbb_container, $language, $request, $template;

		$language = $this->language;
		$request = $this->request;
		$template = $this->template;
		$phpbb_container = $this->phpbb_container;

		$save = $action === 'save';
		self::$valid_form = $expected !== E_USER_WARNING;

		if (!defined('IN_ADMIN'))
		{
			define('IN_ADMIN', true);
		}

		$services = ['phpbb.mediaembed.acp_controller', 'request', 'language'];
		$returns = [$this->acp_controller, $request, $language];
		$callCount = 0;
		$phpbb_container
			->method('get')
			->willReturnCallback(function($service) use ($services, $returns, &$callCount) {
				$this->assertEquals($services[$callCount], $service);
				return $returns[$callCount++];
			});

		$this->acp_controller
			->expects(self::once())
			->method('set_page_url');

		$this->acp_controller
			->expects(self::$valid_form ? self::once() : self::never())
			->method("{$action}_$mode")
			->willReturn($save ? ['code' => $expected, 'message' => ''] : $expected);

		if ($save)
		{
			$expected_args = ['action_purge_cache', 'submit'];
			$expected_returns = [false, true];
			$invocation = 0;
			$request
				->method('is_set_post')
				->willReturnCallback(function($arg) use (&$invocation, $expected_args, $expected_returns) {
					self::assertEquals($expected_args[$invocation], $arg);
					return $expected_returns[$invocation++];
				});

			$this->setExpectedTriggerError($expected);
		}

		$p_master = new \p_master();
		$p_master->module_ary[0]['is_duplicate'] = 0;
		$p_master->module_ary[0]['url_extra'] = '';
		$p_master->load('acp', '\phpbb\mediaembed\acp\main_module', $mode);
	}

	public function test_main_module_cache()
	{
		global $phpbb_container, $request, $template;

		$request = $this->request;
		$template = $this->template;
		$phpbb_container = $this->phpbb_container;

		if (!defined('IN_ADMIN'))
		{
			define('IN_ADMIN', true);
		}

		$expected_args = ['phpbb.mediaembed.acp_controller', 'request'];
		$expected_returns = [$this->acp_controller, $request];
		$invocation = 0;
		$phpbb_container
			->expects(self::exactly(2))
			->method('get')
			->willReturnCallback(function($arg) use (&$invocation, $expected_args, $expected_returns) {
				self::assertEquals($expected_args[$invocation], $arg);
				return $expected_returns[$invocation++];
			});

		$request
			->expects(self::atLeastOnce())
			->method('is_set_post')
			->with('action_purge_cache')
			->willReturn(true);

		$this->acp_controller
			->expects(self::once())
			->method('purge_mediaembed_cache')
			->willReturn(['code' => E_USER_NOTICE, 'message' => '']);

		$this->setExpectedTriggerError(E_USER_NOTICE);

		$p_master = new \p_master();
		$p_master->module_ary[0]['is_duplicate'] = 0;
		$p_master->module_ary[0]['url_extra'] = '';
		$p_master->load('acp', '\phpbb\mediaembed\acp\main_module', null);
	}
}

/**
 * Mock check_form_key()
 * Note: use the same namespace as the acp_controller
 *
 * @return bool
 */
function check_form_key()
{
	return \phpbb\mediaembed\acp\acp_module_test::$valid_form;
}

/**
 * Mock add_form_key()
 * Note: use the same namespace as the acp_controller
 */
function add_form_key()
{
}

/**
 * Mock adm_back_link()
 * Note: use the same namespace as the acp_controller
 */
function adm_back_link()
{
}
