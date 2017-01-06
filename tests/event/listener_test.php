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
		return array('phpbb/mediaembed');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/../../../../../../tests/text_formatter/s9e/fixtures/factory.xml');
	}

	/** @var ContainerInterface */
	protected $container;

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

		$this->config = new \phpbb\config\config([
			'media_embed_bbcode' => 1,
			'media_embed_allow_sig' => 0,
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
			'core.help_manager_add_block_before',
			'core.message_parser_check_message',
			'core.text_formatter_s9e_parser_setup',

		], array_keys(\phpbb\mediaembed\event\main_listener::getSubscribedEvents()));
	}

	/**
	 * Test the configure_media_embed method
	 */
	public function test_configure_media_embed()
	{
		// Get the s9e configurator
		$configurator = $this->container
			->get('text_formatter.s9e.factory')
			->get_configurator();

		// Add a BBCode. This will simulate an existing youtube bbcode,
		// which should therefore be ignored by the media embed plugin.
		$configurator->BBCodes->add('youtube');

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

		// Assert that sites are being processed by MediEmbed plugin with the MEDIA BBCode
		$this->assertContains('DAILYMOTION id="x222z1"', $parser->parse('[media]http://www.dailymotion.com/video/x222z1[/media]'));

		// Assert that sites are being processed by MediEmbed plugin as raw URLs
		$this->assertContains('FACEBOOK id="10100658170103643"', $parser->parse('https://www.facebook.com/video/video.php?v=10100658170103643'));

		// Assert that ignored sites are NOT being processed by MediEmbed plugin
		$this->assertNotContains('YOUTUBE id="-cEzsCAzTak"', $parser->parse('https://youtu.be/-cEzsCAzTak'));
	}

	/**
	 * Data for test_disable_in_signature
	 *
	 * @return array
	 */
	public function disable_in_signature_data()
	{
		return array(
			array('sig', false, 1),
			array('text_reparser.user_signature', false, 1),
			array('post', false, 0),
			array('post', true, 0),
		);
	}

	/**
	 * Test the disable_in_signature method
	 *
	 * @param string $mode     The post mode
	 * @param bool   $allowed  Config value for media_embed_allow_sig
	 * @param int    $expected The expected times parser plugin methods are called
	 *
	 * @dataProvider disable_in_signature_data
	 */
	public function test_disable_in_signature($mode, $allowed, $expected)
	{
		// Set the allow sigs config value with test data
		$this->config['media_embed_allow_sig'] = $allowed;

		// Must use a mock of the s9e parser
		$mock = $this->getMockBuilder('s9e\\TextFormatter\\Parser')
			->disableOriginalConstructor()
			->getMock();

		// Test the expected parser method is called
		$mock->expects($this->exactly($expected))
			->method('disablePlugin')
			->with('MediaEmbed');

		// Test the expected parser method is called
		$mock->expects($this->exactly($expected))
			->method('disableTag')
			->with('MEDIA');

		// Must use a mock of the phpbb parser to pass to the event
		$parser = $this->getMockBuilder('phpbb\\textformatter\\s9e\\parser')
			->disableOriginalConstructor()
			->getMock();

		// The phpbb parser must get the mocked s9e parser
		$parser->expects($this->exactly($expected))
			->method('get_parser')
			->will($this->returnValue($mock));

		// Assign $event data
		$event = new \phpbb\event\data([
			'mode'		=> $mode,
			'parser'	=> $parser,
		]);

		// Get the listener and call the signature methods
		$listener = $this->get_listener();
		$listener->set_signature($event);
		$listener->disable_in_signature($event);
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
}
