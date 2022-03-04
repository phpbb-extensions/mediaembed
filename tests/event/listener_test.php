<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\tests\event;

use Symfony\Component\DependencyInjection\ContainerInterface;

class listener_test extends \phpbb_database_test_case
{
	protected static function setup_extensions()
	{
		return ['phpbb/mediaembed'];
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/../../../../../../tests/text_formatter/s9e/fixtures/factory.xml');
	}

	/** @var string */
	protected $cache_dir;

	/** @var ContainerInterface */
	protected $container;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\template\template */
	protected $template;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\mediaembed\collection\customsitescollection */
	protected $custom_sites;

	/**
	 * Setup test environment
	 */
	protected function setUp(): void
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->cache_dir = $phpbb_root_path . 'cache/';

		$this->db = $this->new_dbal();

		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->getMock();

		$this->config = new \phpbb\config\config([
			'media_embed_bbcode' => 1,
			'media_embed_allow_sig' => 0,
			'media_embed_parse_urls' => 1,
			'media_embed_full_width' => 1,
		]);

		$this->config_text = $this->getMockBuilder('\phpbb\config\db_text')
			->disableOriginalConstructor()
			->getMock();

		$this->language = new \phpbb\language\language(
			new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)
		);

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();

		$this->custom_sites = $this->getMockBuilder('\phpbb\mediaembed\collection\customsitescollection')
			->disableOriginalConstructor()
			->getMock();

		$this->container = $this->get_test_case_helpers()->set_s9e_services();
	}

	/**
	 * Get the event listener
	 *
	 * @return \phpbb\mediaembed\event\main_listener
	 */
	protected function get_listener()
	{
		return new \phpbb\mediaembed\event\main_listener(
			$this->auth,
			$this->config,
			$this->config_text,
			$this->language,
			$this->template,
			$this->custom_sites,
			$this->cache_dir
		);
	}

	/**
	 * Test the event listener is constructed correctly
	 */
	public function test_construct()
	{
		static::assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->get_listener());
	}

	/**
	 * Test the event listener is subscribing events
	 */
	public function test_getSubscribedEvents()
	{
		static::assertEquals([
			'core.text_formatter_s9e_configure_after',
			'core.display_custom_bbcodes',
			'core.permissions',
			'core.help_manager_add_block_before',
			'core.posting_modify_message_text',
			'core.ucp_pm_compose_modify_parse_before',
			'core.message_parser_check_message',
			'core.text_formatter_s9e_parser_setup',
			'core.page_header',
		], array_keys(\phpbb\mediaembed\event\main_listener::getSubscribedEvents()));
	}

	/**
	 * Test the set_permissions_test event
	 */
	public function test_set_permissions()
	{
		// Assign $event data
		$event = new \phpbb\event\data([
			'permissions' => ['u_foo' => ['lang' => 'ACL_U_FOO', 'cat' => 'misc']],
		]);

		// Get the listener and call the set permissions methods
		$listener = $this->get_listener();
		$listener->set_permissions($event);

		// Assert permission keys are added
		self::assertArrayHasKey('f_mediaembed', $event['permissions']);
		self::assertArrayHasKey('u_pm_mediaembed', $event['permissions']);
	}

	/**
	 * Data for test_configure_media_embed
	 *
	 * @return array
	 */
	public function configure_media_embed_data()
	{
		return [
			['dailymotion', '[media]http://www.dailymotion.com/video/x222z1[/media]', 'DAILYMOTION id="x222z1"', false, true, true], // site using the MEDIA BBCode
			['dailymotion', '[media]http://www.dailymotion.com/video/x222z1[/media]', 'DAILYMOTION id="x222z1"', true, true, false], // ignored site using the MEDIA BBCode
			['facebook', 'https://www.facebook.com/video/video.php?v=10100658170103643', 'FACEBOOK id="10100658170103643"', false, true, true], // site using plain url
			['facebook', 'https://www.facebook.com/video/video.php?v=10100658170103643', 'FACEBOOK id="10100658170103643"', false, false, false], // disallow site using plain url
			['youtube', 'https://youtu.be/-cEzsCAzTak', 'YOUTUBE id="-cEzsCAzTak"', true, true, false], // ignored site using plain url
			['youtube', 'https://youtu.be/-cEzsCAzTak', 'YOUTUBE id="-cEzsCAzTak"', true, false, false], // ignored site and disallowed plain url
			['ok', '[media]https://ok.ru/video/549000643961[/media]', 'OK id="549000643961"', false, true, true], // custom site using the MEDIA BBCode
		];
	}

	/**
	 * Test the media embed configuration
	 *
	 * @dataProvider configure_media_embed_data
	 * @param string $tag        The media tag name
	 * @param string $code       The media code to parse
	 * @param string $id         The media identifier
	 * @param bool   $exists     Does the tag exist?
	 * @param bool   $parse_urls Can URLs be parsed?
	 * @param bool   $expected   Expected to be parsed
	 */
	public function test_configure_media_embed($tag, $code, $id, $exists, $parse_urls, $expected)
	{
		$this->custom_sites->expects(self::once())
			->method('get_collection')
			->willReturn([__DIR__ . '/../fixtures/sites/ok.yml']);

		// Update configs with test values
		$this->config['media_embed_parse_urls'] = $parse_urls;

		// Get the s9e configurator
		$configurator = $this->container
			->get('text_formatter.s9e.factory')
			->get_configurator();

		if ($exists)
		{
			// Add a BBCode. This will simulate an existing bbcode,
			// which should therefore be ignored by the media embed plugin.
			$configurator->BBCodes->add($tag);
		}

		// Force config_text to return all default and custom MediaEmbed sites
		$default_sites = array_keys(iterator_to_array($configurator->MediaEmbed->defaultSites));
		$custom_sites = ['ok'];
		$this->config_text->expects(self::once())
			->method('get')
			->with('media_embed_sites')
			->willReturn(json_encode(array_merge($default_sites, $custom_sites)));

		// Assign $event['configurator']
		$event = new \phpbb\event\data([
			'configurator'	=> $configurator,
		]);

		// Setup the listener and call the media embed configuration methods
		$listener = $this->get_listener();
		$listener->add_custom_sites($event);
		$listener->enable_media_sites($event);
		$listener->configure_url_parsing($event);

		// Get an instance of the parser
		$parser = null;
		extract($configurator->finalize(), EXTR_OVERWRITE);

		$assertion = $expected ? 'assertStringContainsString' : 'assertStringNotContainsString';

		$this->{$assertion}($id, $parser->parse($code));
	}

	/**
	 * Data for test_exception_errors
	 *
	 * @return array
	 */
	public function exception_errors_data()
	{
		return array(
			array('notok', '\Symfony\Component\Yaml\Exception\ParseException'), // Exception when custom site YAML is invalid
			array('invalid', '\InvalidArgumentException'), // Exception when MediaEmbed can't process the site definition
		);
	}

	/**
	 * Test expected exceptions are being thrown when errors are
	 * encountered with custom site definitions.
	 *
	 * @dataProvider exception_errors_data
	 * @param string $site
	 * @param string $exception
	 */
	public function test_exception_errors($site, $exception)
	{
		$this->expectException($exception);

		$this->custom_sites->expects(self::once())
			->method('get_collection')
			->willReturn([__DIR__ . "/../fixtures/sites/$site.yml"]);

		$this->config_text
			->method('get')
			->with('media_embed_sites')
			->willReturn(json_encode([$site]));

		// Get the s9e configurator
		$configurator = $this->container
			->get('text_formatter.s9e.factory')
			->get_configurator();

		// Assign $event['configurator']
		$event = new \phpbb\event\data([
			'configurator'	=> $configurator,
		]);

		// Setup the listener and call the media embed configuration methods
		$listener = $this->get_listener();
		$listener->add_custom_sites($event);
		$listener->enable_media_sites($event);
	}

	public function check_methods_data()
	{
		return [
			['check_signature', ['mode' => 'sig'], ['media_embed_allow_sig' => false], 1, 1],
			['check_signature', ['mode' => 'text_reparser.user_signature'], ['media_embed_allow_sig' => false], 1, 1],
			['check_signature', ['mode' => 'sig'], ['media_embed_allow_sig' => true], 0, 0],
			['check_signature', ['mode' => 'text_reparser.user_signature'], ['media_embed_allow_sig' => true], 0, 0],
			['check_signature', ['mode' => 'post'], ['media_embed_allow_sig' => false], 0, 0],
			['check_signature', ['mode' => 'post'], ['media_embed_allow_sig' => true], 0, 0],
			['check_magic_urls', ['allow_magic_url' => false], ['media_embed_parse_urls' => false], 1, 0],
			['check_magic_urls', ['allow_magic_url' => false], ['media_embed_parse_urls' => true], 1, 0],
			['check_magic_urls', ['allow_magic_url' => true], ['media_embed_parse_urls' => false], 1, 0],
			['check_magic_urls', ['allow_magic_url' => true], ['media_embed_parse_urls' => true], 0, 0],
			['check_bbcode_enabled', ['allow_bbcode' => true], [], 0, 0],
			['check_bbcode_enabled', ['allow_bbcode' => false], [], 1, 1],
		];
	}

	/**
	 * Test the check_signature method
	 *
	 * @dataProvider check_methods_data
	 * @param string $method         Name of event method being tested
	 * @param array  $data           Array of event data for testing
	 * @param array  $configs        Array of config data for testing
	 * @param int    $disable_plugin Expected times disable_plugin is called
	 * @param int    $disable_tag    Expected times disable_tag is called
	 */
	public function test_check_methods($method, $data, $configs, $disable_plugin, $disable_tag)
	{
		// Set config values with test data
		foreach ($configs as $key => $config)
		{
			$this->config[$key] = $config;
		}

		// Must use a mock of the s9e parser
		$mock = $this->mock_s9e_parser();

		// Test disablePlugin is called if expected
		$mock->expects(self::exactly($disable_plugin))
			->method('disablePlugin')
			->with('MediaEmbed');

		// Test disableTag is called if expected
		$mock->expects(self::exactly($disable_tag))
			->method('disableTag')
			->with('MEDIA');

		// Must use a mock of the phpbb parser to pass to the event
		$parser = $this->mock_phpbb_parser();

		// The phpbb parser must get the mocked s9e parser
		$parser->expects(self::once())
			->method('get_parser')
			->willReturn($mock);

		// Assign $event data
		$event = new \phpbb\event\data(array_merge($data, ['parser' => $parser]));

		// Get the listener and call the signature methods
		$listener = $this->get_listener();
		$listener->$method($event);
		$listener->disable_media_embed($event);
	}

	/**
	 * Data for test_check_permissions
	 *
	 * @return array
	 */
	public function check_permissions_data()
	{
		return [
			[2, 'f_mediaembed', false],
			[3, 'f_mediaembed', true],
			[2, 'f_bbcode', false],
			[3, 'f_bbcode', true],
			[0, 'u_pm_mediaembed', false],
			[0, 'u_pm_mediaembed', true],
		];
	}

	/**
	 * Test the check permissions methods
	 *
	 * @param bool   $forum_id   Forum id?
	 * @param string $permission The permission name
	 * @param bool   $allowed    Allowed?
	 *
	 * @dataProvider check_permissions_data
	 */
	public function test_check_permissions($forum_id, $permission, $allowed)
	{
		// Set default permissions map
		$acl_map = [
			['f_mediaembed', $forum_id, true],
			['f_bbcode', $forum_id, true],
			['u_pm_mediaembed', $forum_id, true],
		];

		// update permissions map with test values
		$acl_map = array_map(function ($arr) use ($permission, $forum_id, $allowed) {
			if ($arr[0] === $permission)
			{
				$arr = [$permission, $forum_id, $allowed];
			}
			return $arr;
		}, $acl_map);

		$this->auth->expects(self::atMost(3))
			->method('acl_get')
			->with(self::stringContains('_'), self::anything())
			->willReturnMap($acl_map);

		// Get the listener and call the methods
		$listener = $this->get_listener();
		switch ($permission)
		{
			case 'f_mediaembed':
			case 'f_bbcode':
				$listener->check_forum_permission(new \phpbb\event\data(['forum_id' => $forum_id]));
			break;

			case 'u_pm_mediaembed':
				$listener->check_pm_permission();
			break;
		}
		$listener->disable_media_embed(new \phpbb\event\data(['parser' => $this->container->get('text_formatter.parser')]));
	}

	/**
	 * Test the setup_media_bbcode method
	 */
	public function test_setup_media_bbcode()
	{
		$listener = $this->get_listener();

		$this->template->expects(self::once())
			->method('assign_var')
			->with('S_BBCODE_MEDIA', $this->config['media_embed_bbcode']);

		$listener->setup_media_bbcode();
	}

	public function test_setup_media_configs()
	{
		$listener = $this->get_listener();

		$this->template->expects(self::once())
			->method('assign_vars')
			->with([
				'S_MEDIA_EMBED_FULL_WIDTH'	=> $this->config['media_embed_full_width'],
				'S_MEDIA_EMBED_MAX_WIDTHS'	=> '',
			]);

		$listener->setup_media_configs();
	}

	public function test_media_embed_help()
	{
		// Test template methods and lang vars are called as expected
		$this->template->expects(self::exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				['faq_block', [
					'BLOCK_TITLE'	=> 'HELP_EMBEDDING_MEDIA',
					'SWITCH_COLUMN'	=> false
				]],
				['faq_block.faq_row', [
					'FAQ_QUESTION'	=> 'HELP_EMBEDDING_MEDIA_QUESTION',
					'FAQ_ANSWER'	=> 'HELP_EMBEDDING_MEDIA_ANSWER',
				]]
			);

		// Assign $event data
		$event = new \phpbb\event\data([
			'block_name' => 'HELP_BBCODE_BLOCK_OTHERS',
		]);

		// Get the listener and call the media_embed_help method
		$listener = $this->get_listener();
		$listener->media_embed_help($event);
	}

	/**
	 * Data for test_setup_cache_dir
	 *
	 * @return array
	 */
	public function setup_cache_dir_data()
	{
		return [
			[true],
			[false],
		];
	}

	/**
	 * Test the setup_cache_dir method
	 *
	 * @dataProvider setup_cache_dir_data
	 * @param boolean $cache
	 */
	public function test_setup_cache_dir($cache)
	{
		$this->config['media_embed_enable_cache'] = $cache;

		$parser = $this->container->get('text_formatter.parser');

		$event = new \phpbb\event\data([
			'parser' => $parser]
		);

		$listener = $this->get_listener();
		$listener->setup_cache_dir($event);

		if ($cache)
		{
			self::assertArrayHasKey('cacheDir', $parser->get_parser()->registeredVars);
			self::assertSame($this->cache_dir, $parser->get_parser()->registeredVars['cacheDir']);
		}
		else
		{
			self::assertArrayNotHasKey('cacheDir', $parser->get_parser()->registeredVars);
		}
	}

	protected function mock_s9e_parser()
	{
		return $this->getMockBuilder('s9e\\TextFormatter\\Parser')
			->disableOriginalConstructor()
			->getMock();
	}

	protected function mock_phpbb_parser()
	{
		return $this->getMockBuilder('phpbb\\textformatter\\s9e\\parser')
			->disableOriginalConstructor()
			->getMock();
	}
}
