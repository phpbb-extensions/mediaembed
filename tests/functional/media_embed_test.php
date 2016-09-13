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
	static protected function setup_extensions()
	{
		return array('phpbb/mediaembed');
	}

	public function test_posting_media_bbcode()
	{
		$this->login();

		$youtubeId = 'PHzShhtkzEk';

		$post = $this->create_topic(2, 'Media Embed Test Topic 1', "[media]https://youtu.be/{$youtubeId}[/media]");
		$crawler = self::request('GET', "viewtopic.php?t={$post['topic_id']}&sid={$this->sid}");
		static::assertContains("//www.youtube.com/embed/{$youtubeId}", $crawler->filter("#post_content{$post['topic_id']} iframe")->attr('src'));
	}
}
