<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\tests\functional;

/**
 * @group functional
 */
class media_embed_test extends \phpbb_functional_test_case
{
	protected $youtubeId = 'PHzShhtkzEk';
	protected $ok_ru_Id = '549000643961';

	protected static function setup_extensions()
	{
		return ['phpbb/mediaembed'];
	}

	public function test_posting_media_bbcode()
	{
		$this->login();

		$post = $this->create_topic(2, 'Media Embed Test Topic 1', "[media]https://youtu.be/{$this->youtubeId}[/media]");
		$crawler = self::request('GET', "viewtopic.php?t={$post['topic_id']}&sid={$this->sid}");
		self::assertStringContainsString("//www.youtube.com/embed/{$this->youtubeId}", $crawler->filter("#post_content{$post['topic_id']} iframe")->attr('src'));
	}

	public function test_posting_custom_site()
	{
		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', "adm/index.php?i=\\phpbb\\mediaembed\\acp\\main_module&mode=manage&sid={$this->sid}");
		$this->assert_checkbox_is_unchecked($crawler, 'ok');
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$fields = $form->all();
		// Tick all the check boxes dang it, because unticked boxes can't be crawled alone
		foreach ($fields as $fieldname => $fieldobject)
		{
			if (preg_match('/mark\[(\d+)\]/', $fieldname, $matches))
			{
				$form['mark'][$matches[1]]->tick();
			}
		}
		self::submit($form);
		$crawler = self::request('GET', "adm/index.php?i=\\phpbb\\mediaembed\\acp\\main_module&mode=manage&sid={$this->sid}");
		$this->assert_checkbox_is_checked($crawler, 'ok');

		$post = $this->create_topic(2, 'Media Embed Custom Site Test Topic 1', "[media]https://ok.ru/video/{$this->ok_ru_Id}[/media]");
		$crawler = self::request('GET', "viewtopic.php?t={$post['topic_id']}&sid={$this->sid}");
		self::assertStringContainsString("//ok.ru/videoembed/{$this->ok_ru_Id}", $crawler->filter("#post_content{$post['topic_id']} iframe")->attr('src'));
	}

	public function signatures_data()
	{
		return [
			[false, 'UNAUTHORISED_BBCODE'],
			[true, "//www.youtube.com/embed/{$this->youtubeId}"],
		];
	}

	/**
	 * @dataProvider signatures_data
	 * @param bool   $allowed
	 * @param string $expected
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function test_signatures($allowed, $expected)
	{
		$this->add_lang(['ucp', 'posting']);

		$db = $this->get_db();
		$sql = 'UPDATE ' . CONFIG_TABLE . ' SET config_value = ' . (int) $allowed . " WHERE config_name = 'media_embed_allow_sig'";
		$db->sql_query($sql);
		$this->purge_cache();

		$this->login();

		$crawler = self::request('GET', 'ucp.php?i=ucp_profile&mode=signature');

		$form = $crawler->selectButton('Submit')->form([
			'signature'	=> "[media]https://youtu.be/{$this->youtubeId}[/media]",
		]);

		$crawler = self::submit($form);

		if ($allowed)
		{
			$this->assertContainsLang('PROFILE_UPDATED', $crawler->filter('#page-body')->text());
			$crawler = self::request('GET', 'ucp.php?i=ucp_profile&mode=signature');
			self::assertStringContainsString($expected, $crawler->filter('#postform iframe')->attr('src'));
		}
		else
		{
			self::assertStringContainsString($this->lang($expected, '[media]'), $crawler->filter('#postform')->text());
		}
	}

	public function test_posting_media_bbcode_wo_permission()
	{
		$this->add_lang(['posting', 'acp/permissions']);

		$this->login();
		$this->admin_login();

		// Set f_mediaembed to never
		$crawler = self::request('GET', "adm/index.php?i=acp_permissions&icat=16&mode=setting_forum_local&forum_id[0]=2&group_id[0]=2&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('APPLY_PERMISSIONS'))->form();
		$data = array('setting[2][2][f_mediaembed]' => ACL_NEVER);
		$form->setValues($data);
		self::submit($form);

		// Now verify the media bbcode can't be used to post
		$this->create_topic(2, 'Media Embed Test Topic 2', '[media]foo[/media]', [], $this->lang('UNAUTHORISED_BBCODE', '[media]'));
	}

	public function test_media_embed_help()
	{
		$this->add_lang_ext('phpbb/mediaembed', 'help');

		$crawler = self::request('GET', 'app.php/help/bbcode');
		$this->assertContainsLang('HELP_EMBEDDING_MEDIA', $crawler->filter('#faqlinks')->text());

		preg_match('/https:\/\/youtu\.be\/(.*)/', $this->lang('HELP_EMBEDDING_MEDIA_DEMO'), $matches);
		self::assertStringContainsString("//www.youtube.com/embed/{$matches[1]}", $crawler->filter('body iframe')->attr('src'));
	}

	public function test_acp_modules()
	{
		$this->add_lang_ext('phpbb/mediaembed', 'acp');

		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', "adm/index.php?i=\\phpbb\\mediaembed\\acp\\main_module&mode=settings&sid={$this->sid}");
		$this->assertContainsLang('ACP_MEDIA_SETTINGS', $crawler->filter('#main')->text(), 'The Media Embed settings module failed to load');

		$crawler = self::request('GET', "adm/index.php?i=\\phpbb\\mediaembed\\acp\\main_module&mode=manage&sid={$this->sid}");
		$this->assertContainsLang('ACP_MEDIA_MANAGE', $crawler->filter('#main')->text(), 'The Media Embed settings module failed to load');

		$this->assert_checkbox_is_checked($crawler, 'youtube');
	}

	/**
	 * Override original function to search by checkbox value instead of name
	 *
	 * {@inheritDoc}
	 */
	public function assert_find_one_checkbox($crawler, $name, $message = '')
	{
		$query = sprintf('//input[@type="checkbox" and @value="%s"]', $name);
		$result = $crawler->filterXPath($query);

		self::assertCount(
			1,
			$result,
			$message ?: 'Failed asserting that exactly one checkbox with name' .
				" $name exists in crawler scope."
		);

		return $result;
	}
}
