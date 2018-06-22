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
	static protected function setup_extensions()
	{
		return ['phpbb/mediaembed'];
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/../../../../../../tests/text_formatter/s9e/fixtures/factory.xml');
	}

	/** @var ContainerInterface */
	protected $container;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\config\db_text */
	protected $config_text;

	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\template\template */
	protected $template;

	/**
	 * Setup test environment
	 */
	public function setUp()
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->db = $this->new_dbal();

		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->getMock();

		$this->config = new \phpbb\config\config([
			'media_embed_bbcode' => 1,
			'media_embed_allow_sig' => 0,
			'media_embed_parse_urls' => 1,
		]);

		$this->config_text = $this->getMockBuilder('\phpbb\config\db_text')
			->disableOriginalConstructor()
			->getMock();

		$this->language = new \phpbb\language\language(
			new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)
		);

		$this->template = $this->getMockBuilder('\phpbb\template\template')
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
			$this->template
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
		$this->assertArrayHasKey('f_mediaembed', $event['permissions']);
		$this->assertArrayHasKey('u_pm_mediaembed', $event['permissions']);
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
		];
	}

	/**
	 * Test the configure_media_embed method
	 *
	 * @dataProvider configure_media_embed_data
	 */
	public function test_configure_media_embed($tag, $code, $id, $exists, $parse_urls, $expected)
	{
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

		// Mock config_text should return all MediaEmbed sites
		$this->config_text->expects($this->any())
			->method('get')
			->with('media_embed_sites')
			->will($this->returnValue(json_encode(array_keys(iterator_to_array($configurator->MediaEmbed->defaultSites)))));

		// Assign $event['configurator']
		$event = new \phpbb\event\data([
			'configurator'	=> $configurator,
		]);

		// Setup the listener and call the configure_media_embed method
		$listener = $this->get_listener();
		$listener->configure_media_embed($event);

		// Get an instance of the parser
		$parser = null;
		extract($configurator->finalize(), EXTR_OVERWRITE);

		$assertion = $expected ? 'assertContains' : 'assertNotContains';

		$this->{$assertion}($id, $parser->parse($code));
	}

	/**
	 * Data for test_check_signature
	 *
	 * @return array
	 */
	public function check_signature_data()
	{
		return [
			['sig', false, 1],
			['text_reparser.user_signature', false, 1],
			['sig', true, 0],
			['text_reparser.user_signature', true, 0],
			['post', false, 0],
			['post', true, 0],
		];
	}

	/**
	 * Test the check_signature method
	 *
	 * @param string $mode     The post mode
	 * @param bool   $allowed  Config value for media_embed_allow_sig
	 * @param int    $expected The expected times parser plugin methods are called
	 *
	 * @dataProvider check_signature_data
	 */
	public function test_check_signature($mode, $allowed, $expected)
	{
		// Set the allow sigs config value with test data
		$this->config['media_embed_allow_sig'] = $allowed;

		// Must use a mock of the s9e parser
		$mock = $this->mock_s9e_parser();

		// Test the expected parser method is called
		$mock->expects($this->exactly($expected))
			->method('disablePlugin')
			->with('MediaEmbed');

		// Test the expected parser method is called
		$mock->expects($this->exactly($expected))
			->method('disableTag')
			->with('MEDIA');

		// Must use a mock of the phpbb parser to pass to the event
		$parser = $this->mock_phpbb_parser();

		// The phpbb parser must get the mocked s9e parser
		$parser->expects($this->once())
			->method('get_parser')
			->will($this->returnValue($mock));

		// Assign $event data
		$event = new \phpbb\event\data([
			'mode'		=> $mode,
			'parser'	=> $parser,
		]);

		// Get the listener and call the signature methods
		$listener = $this->get_listener();
		$listener->check_signature($event);
		$listener->disable_media_embed($event);
	}

	/**
	 * Data for test_check_magic_urls
	 *
	 * @return array
	 */
	public function check_magic_urls_data()
	{
		return [
			[false, false, 1],
			[false, true, 1],
			[true, false, 1],
			[true, true, 0],
		];
	}

	/**
	 * Test the check_magic_urls method
	 *
	 * @param bool   $u_allowed  Are magic urls allowed by the user during posting?
	 * @param bool   $g_allowed  Are magic urls allowed globally by the ACP settings?
	 * @param int    $expected The expected times parser plugin methods are called
	 *
	 * @dataProvider check_magic_urls_data
	 */
	public function test_check_magic_urls($u_allowed, $g_allowed, $expected)
	{
		// Set the allow sigs config value with test data
		$this->config['media_embed_parse_urls'] = $g_allowed;

		// Must use a mock of the s9e parser
		$mock = $this->mock_s9e_parser();

		// Test the expected parser method is called
		$mock->expects($this->exactly($expected))
			->method('disablePlugin')
			->with('MediaEmbed');

		// Must use a mock of the phpbb parser to pass to the event
		$parser = $this->mock_phpbb_parser();

		// The phpbb parser must get the mocked s9e parser
		$parser->expects($this->once())
			->method('get_parser')
			->will($this->returnValue($mock));

		// Assign $event data
		$event = new \phpbb\event\data([
			'allow_magic_url'	=> $u_allowed,
			'parser'			=> $parser,
		]);

		// Get the listener and call the methods
		$listener = $this->get_listener();
		$listener->check_magic_urls($event);
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
			[2, 'f_mediaembed', false, 1],
			[3, 'f_mediaembed', true, 0],
			[0, 'u_pm_mediaembed', false, 1],
			[0, 'u_pm_mediaembed', true, 0],
		];
	}

	/**
	 * Test the check permissions methods
	 *
	 * @param bool   $forum_id   Forum id?
	 * @param string $permission The permission name
	 * @param bool   $allowed    Allowed?
	 * @param int    $expected   The expected times parser plugin methods are called
	 *
	 * @dataProvider check_permissions_data
	 */
	public function test_check_permissions($forum_id, $permission, $allowed, $expected)
	{
		$acl_map = [[$permission, $forum_id, $allowed]];

		$this->auth->expects($this->once())
			->method('acl_get')
			->with($this->stringContains('_'), $this->anything())
			->will($this->returnValueMap($acl_map));

		// Must use a mock of the s9e parser
		$mock = $this->mock_s9e_parser();

		// Test the expected parser method is called
		$mock->expects($this->exactly($expected))
			->method('disablePlugin')
			->with('MediaEmbed');

		// Must use a mock of the phpbb parser to pass to the event
		$parser = $this->mock_phpbb_parser();

		// The phpbb parser must get the mocked s9e parser
		$parser->expects($this->once())
			->method('get_parser')
			->will($this->returnValue($mock));

		// Get the listener and call the methods
		$listener = $this->get_listener();
		switch ($permission)
		{
			case 'f_mediaembed':
				$listener->check_forum_permission(new \phpbb\event\data(['forum_id' => $forum_id]));
			break;

			case 'u_pm_mediaembed':
				$listener->check_pm_permission();
			break;
		}
		$listener->disable_media_embed(new \phpbb\event\data(['parser' => $parser]));
	}

	/**
	 * Test the setup_media_bbcode method
	 */
	public function test_setup_media_bbcode()
	{
		$listener = $this->get_listener();

		$this->template->expects($this->once())
			->method('assign_var')
			->with('S_BBCODE_MEDIA', $this->config['media_embed_bbcode']);

		$listener->setup_media_bbcode();
	}

	public function test_media_embed_help()
	{
		// Test template methods and lang vars are called as expected
		$this->template->expects($this->exactly(2))
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
