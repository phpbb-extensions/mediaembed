<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\tests\acp;

require_once __DIR__ . '/../../../../../includes/functions_acp.php';

use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\language\language;
use phpbb\log\log;
use phpbb\mediaembed\cache\cache as media_cache;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\textformatter\s9e\factory as textformatter;
use phpbb\user;
use Symfony\Component\DependencyInjection\ContainerInterface;

class acp_controller_test extends \phpbb_test_case
{
	/** @var ContainerInterface */
	protected $container;

	/** @var config $config */
	protected $config;

	/** @var db_text $config_text */
	protected $config_text;

	/** @var language $language */
	protected $language;

	/** @var log $log */
	protected $log;

	/** @var media_cache $media_cache */
	protected $media_cache;

	/** @var request $request */
	protected $request;

	/** @var template $template */
	protected $template;

	/** @var textformatter $textformatter */
	protected $textformatter;

	/** @var user $user */
	protected $user;

	protected function setUp(): void
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$this->language = new language($lang_loader);
		$this->log = $this->getMockBuilder('\phpbb\log\log')
			->disableOriginalConstructor()
			->getMock();
		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->disableOriginalConstructor()
			->getMock();
		$this->user = new user($this->language, '\phpbb\datetime');
		$this->user->data['user_id'] = 2;
		$this->user->ip = '0.0.0.0';
		$this->request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$this->media_cache = $this->getMockBuilder('\phpbb\mediaembed\cache\cache')
			->disableOriginalConstructor()
			->getMock();
		$this->config_text = $this->getMockBuilder('\phpbb\config\db_text')
			->disableOriginalConstructor()
			->getMock();
		$this->config = new config([
			'media_embed_bbcode' => '0',
			'media_embed_allow_sig' => '0',
			'media_embed_parse_urls' => '0',
			'media_embed_enable_cache' => '0',
			'media_embed_full_width' => '0',
		]);
		$this->textformatter = $this->getMockBuilder('\phpbb\textformatter\s9e\factory')
			->disableOriginalConstructor()
			->getMock();
		$this->u_action = $phpbb_root_path . 'adm/index.php?i=-phpbb-ads-acp-main_module&mode=manage';

		$this->container = $this->get_test_case_helpers()->set_s9e_services();
	}

	/**
	 * Returns new controller.
	 *
	 * @return	\phpbb\mediaembed\controller\acp_controller
	 */
	public function get_controller()
	{
		$controller = new \phpbb\mediaembed\controller\acp_controller(
			$this->config,
			$this->config_text,
			$this->language,
			$this->log,
			$this->media_cache,
			$this->request,
			$this->template,
			$this->textformatter,
			$this->user
		);
		$controller->set_page_url($this->u_action);

		return $controller;
	}

	public function get_page_title_data()
	{
		return [
			['settings', 'ACP_MEDIA_SETTINGS'],
			['manage', 'ACP_MEDIA_MANAGE'],
		];
	}

	/**
	 * @dataProvider get_page_title_data
	 */
	public function test_get_page_title($mode, $expected)
	{
		$controller = $this->get_controller();
		self::assertEquals($controller->get_page_title($mode), $this->language->lang($expected));
	}

	public function test_display_settings()
	{
		$this->config_text->expects(self::once())
			->method('get')
			->with('media_embed_max_width')
			->willReturn('[{"site":"youtube","width":"100%"}]');

		$this->template->expects(self::once())
			->method('assign_vars')
			->with([
				'S_MEDIA_EMBED_BBCODE' => $this->config['media_embed_bbcode'],
				'S_MEDIA_EMBED_ALLOW_SIG' => $this->config['media_embed_allow_sig'],
				'S_MEDIA_EMBED_PARSE_URLS' => $this->config['media_embed_parse_urls'],
				'S_MEDIA_EMBED_ENABLE_CACHE' => $this->config['media_embed_enable_cache'],
				'S_MEDIA_EMBED_FULL_WIDTH' => $this->config['media_embed_full_width'],
				'S_MEDIA_EMBED_MAX_WIDTHS' => 'youtube:100%',
				'U_ACTION' => $this->u_action,
			]);

		$controller = $this->get_controller();
		$controller->display_settings();
	}

	public function test_display_manage()
	{
		$this->template->expects(self::once())
			->method('assign_vars');
		$this->textformatter->expects(self::once())
			->method('get_configurator')
			->willReturn($this->container
				->get('text_formatter.s9e.factory')
				->get_configurator());

		$controller = $this->get_controller();
		$controller->display_manage();
	}

	public function save_setting_data()
	{
		return [
			[true, ['youtube:100%', '[{"site":"youtube","width":"100%"}]'], ['code' => E_USER_NOTICE, 'message' => 'CONFIG_UPDATED']],
			[true, ['youtube', '[]'], ['code' => E_USER_NOTICE, 'message' => 'CONFIG_UPDATED']],
			[true, ['', ''], ['code' => E_USER_NOTICE, 'message' => 'CONFIG_UPDATED']],
			[false, ['youtube:100', '[]'], ['code' => E_USER_WARNING, 'message' => 'ACP_MEDIA_ERROR_MSG']],
			[false, ['foobars:100%', '[]'], ['code' => E_USER_WARNING, 'message' => 'ACP_MEDIA_ERROR_MSG']],
		];
	}

	/**
	 * @dataProvider save_setting_data
	 */
	public function test_save_setting($success, $data, $expected)
	{
		$this->request
			->expects(self::exactly(6))
			->method('variable')
			->withConsecutive(
				['media_embed_bbcode', 0],
				['media_embed_allow_sig', 0],
				['media_embed_parse_urls', 0],
				['media_embed_enable_cache', 0],
				['media_embed_full_width', 0],
				['media_embed_max_width', '']
			)
			->willReturnOnConsecutiveCalls(
				1,
				1,
				1,
				1,
				1,
				$data[0]
			);

		$this->config_text->expects(self::once())
			->method('set')
			->with('media_embed_max_width', $data[1]);

		$this->textformatter->expects(self::atMost(1))
			->method('get_configurator')
			->willReturn($this->container
				->get('text_formatter.s9e.factory')
				->get_configurator());

		$controller = $this->get_controller();
		$result = $controller->save_settings();

		$this->assertEquals(1, $this->config['media_embed_bbcode']);
		$this->assertEquals(1, $this->config['media_embed_allow_sig']);
		$this->assertEquals(1, $this->config['media_embed_parse_urls']);
		$this->assertEquals(1, $this->config['media_embed_enable_cache']);
		$this->assertEquals(1, $this->config['media_embed_full_width']);

		$this->assertEquals($expected, $result);
	}

	public function test_save_manage()
	{
		$this->request
			->expects(self::once())
			->method('variable')
			->with('mark', [''])
			->willReturn(['foo', 'bar']);

		$this->config_text->expects(self::once())
			->method('set')
			->with('media_embed_sites', '["foo","bar"]');

		$this->log->expects(self::once())
			->method('add');

		$this->media_cache->expects(self::once())
			->method('purge_textformatter_cache');

		$controller = $this->get_controller();
		$result = $controller->save_manage();

		$this->assertEquals(['code' => E_USER_NOTICE, 'message' => 'CONFIG_UPDATED'], $result);
	}

	public function test_purge_mediaembed_cache()
	{
		$this->log->expects(self::once())
			->method('add');

		$this->media_cache->expects(self::once())
			->method('purge_mediaembed_cache');

		$controller = $this->get_controller();
		$result = $controller->purge_mediaembed_cache();

		$this->assertEquals(['code' => E_USER_NOTICE, 'message' => 'PURGE_CACHE_SUCCESS'], $result);
	}
}
