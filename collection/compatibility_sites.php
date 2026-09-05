<?php
/**
 * Hand-maintained compatibility overrides for generated MediaEmbed definitions.
 *
 * Keep this file PHP 7.1 compatible. Generated data lives in generated/upstream_sites.php.
 * Former phpBB 3 YAML overrides retain their established behavior here. PeerTube and
 * XenForo remove helper classes unavailable in TextFormatter 2.11.5; XenForo support
 * is consequently limited to xenforo.com.
 */

return [
	'applepodcasts' => [
		'name' => 'Apple Podcasts',
		'homepage' => 'https://podcasts.apple.com/',
		'host' => 'podcasts.apple.com',
		'example' => [
			'https://podcasts.apple.com/us/podcast/the-office-deep-dive-with-brian-baumgartner/id1550331348',
			'https://podcasts.apple.com/us/podcast/the-office-deep-dive-with-brian-baumgartner/id1550331348?i=1000514199106',
		],
		'extract' => [
			'@podcasts.apple.com/(?\'country\'\\w+)/podcast/[-\\w%]*/id(?\'podcast_id\'\\d+)(?:\\?i=(?\'episode_id\'\\d+))?@',
		],
		'choose' => [
			'when' => [
				'test' => '@episode_id',
				'iframe' => [
					'allow' => 'autoplay *;encrypted-media *',
					'width' => '100%',
					'height' => '175',
					'max-width' => '900',
					'src' => 'https://embed.podcasts.apple.com/{@country}/podcast/episode/id{@podcast_id}?i={@episode_id}',
				],
			],
			'otherwise' => [
				'iframe' => [
					'allow' => 'autoplay *;encrypted-media *',
					'width' => '100%',
					'height' => '450',
					'max-width' => '900',
					'src' => 'https://embed.podcasts.apple.com/{@country}/podcast/episode/id{@podcast_id}',
				],
			],
		],
		'tags' => 'podcasts',
	],
	'bluesky' => [
		'attributes' => [
			'embedder' => [
				'filterChain' => [],
				'required' => true,
			],
			'url' => [
				'filterChain' => 'urldecode',
				'required' => true,
			],
		],
		'example' => [
			'https://bsky.app/profile/bsky.app/post/3kkrqzuydho2v',
			'https://bsky.app/profile/bnewbold.net/post/3kxjq2auebs2f',
		],
		'extract' => '#^https://(?\'embedder\'[.\\w]+)/oembed.*?url=(?\'url\'[\\w%.]+)#',
		'homepage' => 'https://bsky.app/',
		'host' => 'bsky.app',
		'iframe' => [
			'data-s9e-livepreview-ignore-attrs' => 'style',
			'height' => 600,
			'onload' => 'let c=new MessageChannel;c.port1.onmessage=e=>this.style.height=e.data+\'px\';this.contentWindow.postMessage(\'s9e:init\',\'*\',[c.port2])',
			'src' => 'https://s9e.github.io/iframe/2/bluesky.min.html#<xsl:value-of select="@url"/>#<xsl:value-of select="@embedder"/>',
			'width' => 600,
		],
		'name' => 'Bluesky',
		'scrape' => [
			'extract' => '#https://(?\'embedder\'[.\\w]+)/oembed.*?url=(?\'url\'[\\w%.]+)#',
			'match' => '#/profile/[^/]+/post/.#',
		],
		'source' => 'https://embed.bsky.app/',
		'tags' => 'social',
	],
	'bunny' => [
		'name' => 'Bunny Stream',
		'host' => [
			'iframe.mediadelivery.net',
			'video.bunnycdn.com',
		],
		'example' => 'https://video.bunnycdn.com/play/759/eb1c4f77-0cda-46be-b47d-1118ad7c2ffe',
		'extract' => '!/(?:embed|play)/(?\'video_library_id\'\\d+)/(?\'video_id\'[-\\w]+)!',
		'iframe' => [
			'src' => '//iframe.mediadelivery.net/embed/{@video_library_id}/{@video_id}?autoplay=false',
		],
	],
	'facebook' => [
		'name' => 'Facebook',
		'homepage' => 'https://www.facebook.com/',
		'tags' => 'social',
		'host' => [
			'facebook.com',
			'fb.watch',
		],
		'example' => [
			'https://www.facebook.com/MetaforDevelopers/posts/451016937058647',
			'https://www.facebook.com/watch/?v=224353158889229',
			'https://fb.watch/3zYsXdnxjf/',
			'https://www.facebook.com/reel/873906321076441',
		],
		'extract' => [
			'@facebook.com/.*?(?:fbid=|/permalink/|\\?v=)(?\'id\'\\d+)@',
			'@facebook.com/(?\'user\'[.\\w]+)/(?\'type\'[pv])(?:ost|ideo)s?/(?:[-%.\\w]+/)?(?\'id\'\\d+)\\b@',
			'@facebook.com/video/(?=post|video)(?\'type\'[pv])@',
			'@facebook.com/events/(?\'id\'\\d+)\\b(?!/permalink)@',
			'@facebook.com/watch/?(?\'type\'[pv])=@',
			'@facebook.com/groups/[^/]*/(?\'type\'p)osts/(?\'id\'\\d+)@',
			'@facebook.com/(?\'user\'[.\\w]+)/posts/pfbid(?\'pfbid\'\\w+)@',
			'@facebook.com/permalink.php?story_fbid=(?:(?\'id\'\\d+)|pfbid(?\'pfbid\'\\w+))&id=(?\'page_id\'\\d+)@',
			'@facebook.com/(?\'type\'r)eel/(?\'id\'\\d+)@',
		],
		'scrape' => [
			[
				'extract' => '@facebook.com/(?\'user\'[.\\w]+)/(?\'type\'[pv])\\w+/(?\'id\'\\d+)(?!\\w)@',
				'header' => 'User-agent: PHP (not Mozilla)',
				'match' => '@facebook.com/[.\\w]+/posts/pfbid@',
				'url' => 'https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2F{@user}%2Fposts%2Fpfbid{@pfbid}',
			],
			[
				'extract' => '@story_fbid=(?\'id\'\\d+)@',
				'header' => 'User-agent: PHP (not Mozilla)',
				'match' => '@facebook.com/permalink.php?story_fbid=pfbid(?\'pfbid\'\\w+)&id=(?\'page_id\'\\d+)@',
				'url' => 'https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fpermalink.php%3Fstory_fbid%3Dpfbid{@pfbid}%26id%3D{@page_id}',
			],
			[
				'extract' => [
					'@facebook.com/watch/?(?\'type\'v)=(?\'id\'\\d+)@',
					'@facebook.com/(?\'user\'[.\\w]+)/(?\'type\'v)ideos/(?\'id\'\\d+)@',
				],
				'header' => 'User-agent: PHP (not Mozilla)',
				'match' => '@fb.watch/.@',
			],
			[
				'extract' => [
					'@facebook.com/\\w+/(?\'user\'[.\\w]+)/permalink/(?\'id\'\\d+)(?!\\w)@',
					'@og:url[^>]+facebook.com/(?\'user\'[.\\w]+)/(?\'type\'[pv])(?:ost|ideo)s?/(?:[-\\w%]+/)?(?\'id\'\\d+)\\b@',
				],
				'header' => 'User-agent: PHP (not Mozilla)',
				'match' => '@facebook.com/share/[pv]/\\w@',
			],
		],
		'iframe' => [
			'data-s9e-livepreview-ignore-attrs' => 'style',
			'onload' => 'let c=new MessageChannel;c.port1.onmessage=e=>this.style.height=e.data+\'px\';this.contentWindow.postMessage(\'s9e:init\',\'*\',[c.port2])',
			'src' => 'https://s9e.github.io/iframe/2/facebook.min.html#<xsl:choose><xsl:when test="@user"><xsl:value-of select="@user"/>/<xsl:choose><xsl:when test="@type=\'r\'or@type=\'v\'">video</xsl:when><xsl:otherwise>post</xsl:otherwise></xsl:choose>s/<xsl:choose><xsl:when test="@id"><xsl:value-of select="@id"/></xsl:when><xsl:otherwise>pfbid<xsl:value-of select="@pfbid"/></xsl:otherwise></xsl:choose></xsl:when><xsl:when test="@id"><xsl:value-of select="@type"/><xsl:value-of select="@id"/></xsl:when><xsl:otherwise>pfbid<xsl:value-of select="@pfbid"/></xsl:otherwise></xsl:choose>',
		],
		'amp' => [
			'custom-element' => 'amp-facebook',
			'src' => 'https://cdn.ampproject.org/v0/amp-facebook-0.1.js',
			'template' => '<amp-facebook layout="responsive" width="640" height="360"><xsl:if test="starts-with(@type,\'v\')"><xsl:attribute name="data-embed-as">video</xsl:attribute></xsl:if><xsl:attribute name="data-href">https://www.facebook.com/<xsl:choose><xsl:when test="@user"><xsl:value-of select="@user"/></xsl:when><xsl:otherwise>user</xsl:otherwise></xsl:choose>/<xsl:choose><xsl:when test="starts-with(@type,\'v\')">video</xsl:when><xsl:otherwise>post</xsl:otherwise></xsl:choose>s/<xsl:value-of select="@id"/></xsl:attribute></amp-facebook>',
		],
		'attributes' => [],
		'tracking_policy' => 'https://www.facebook.com/help/1896641480634370',
	],
	'mastodon' => [
		'name' => 'Mastodon',
		'host' => 'mastodon.social',
		'example' => 'https://mastodon.social/@HackerNewsBot/100181134752056592',
		'extract' => '!//(?\'host\'[-.\\w]+)/@(?\'name\'\\w+)/(?\'id\'\\d+)!',
		'oembed' => [
			'endpoint' => 'https://mastodon.social/api/oembed',
			'scheme' => 'https://mastodon.social/@{@name}/{@id}',
		],
		'scrape' => [
			[
				'extract' => '!"url":"https://(?\'host\'[-.\\w]+)/@(?\'name\'\\w+)/(?\'id\'\\d+)"!',
			],
			[
				'match' => '!^(?\'origin\'https://[^/]+)/@\\w+@[-.\\w]+/(?\'id\'\\d+)!',
			],
			[
				'url' => '{@origin}/api/v1/statuses/{@id}',
			],
		],
		'iframe' => [
			'data-s9e-livepreview-ignore-attrs' => 'style',
			'onload' => 'let c=new MessageChannel;c.port1.onmessage=e=>this.style.height=e.data+\'px\';this.contentWindow.postMessage(\'s9e:init\',\'*\',[c.port2])',
			'width' => '550',
			'height' => '300',
			'src' => 'https://s9e.github.io/iframe/2/mastodon.min.html#<xsl:value-of select="@name"/><xsl:if test="@host and@host!=\'mastodon.social\'">@<xsl:value-of select="@host"/></xsl:if>/<xsl:value-of select="@id"/>',
		],
	],
	'pastebin' => [
		'name' => 'Pastebin',
		'host' => 'pastebin.com',
		'example' => [
			'https://pastebin.com/9jEf44nc',
			'https://pastebin.com/9jEf44nc?theme=dark',
		],
		'extract' => '#pastebin\\.com/(?!u/)(?:\\w+(?:\\.php\\?i=|/))?(?\'id\'\\w+)(?\'dark\'\\?theme=dark)?#',
		'iframe' => [
			'scrolling' => 'yes',
			'width' => '100%',
			'height' => '300',
			'style' => [
				'border:none;resize:vertical;width:100%',
			],
			'src' => '//pastebin.com/embed_iframe/{@id}{@dark}',
		],
	],
	'threads' => [
		'name' => 'Threads',
		'host' => 'threads.net',
		'example' => 'https://www.threads.net/t/CuY2OYEAbJw',
		'extract' => '!threads\\.net/(?:@[-\\w.]+/pos)?t/(?\'id\'[-\\w]+)!',
		'iframe' => [
			'data-s9e-livepreview-ignore-attrs' => 'style',
			'onload' => 'let c=new MessageChannel;c.port1.onmessage=e=>this.style.height=e.data+\'px\';this.contentWindow.postMessage(\'s9e:init\',\'*\',[c.port2])',
			'width' => '550',
			'height' => '300',
			'src' => 'https://s9e.github.io/iframe/2/threads.min.html#{@id}',
		],
	],
	'twitter' => [
		'name' => 'Twitter',
		'host' => [
			'twitter.com',
			'x.com',
		],
		'example' => [
			'https://twitter.com/IJasonAlexander/statuses/526635414338023424',
			'https://mobile.twitter.com/DerekTVShow/status/463372588690202624',
			'https://twitter.com/#!/IJasonAlexander/status/526635414338023424',
		],
		'extract' => '@(?:twitter|x)\\.com/(?:#!/|i/)?\\w+/(?:status(?:es)?|tweet)/(?\'id\'\\d+)@',
		'oembed' => [
			'endpoint' => 'https://publish.twitter.com/oembed',
			'scheme' => 'https://twitter.com/user/status/{@id}',
		],
		'iframe' => [
			'data-s9e-livepreview-ignore-attrs' => 'style',
			'onload' => 'let c=new MessageChannel;c.port1.onmessage=e=>this.style.height=e.data+\'px\';this.contentWindow.postMessage(\'s9e:init\',\'*\',[c.port2])',
			'width' => '550',
			'height' => '350',
			'allow' => 'autoplay *',
			'src' => 'https://s9e.github.io/iframe/2/twitter.min.html#<xsl:value-of select="@id"/><xsl:if test="$MEDIAEMBED_THEME">#theme=<xsl:value-of select="$MEDIAEMBED_THEME"/></xsl:if>',
		],
		'amp' => [
			'custom-element' => 'amp-twitter',
			'src' => 'https://cdn.ampproject.org/v0/amp-twitter-0.1.js',
			'template' => '<amp-twitter layout="responsive" width="550" height="273" data-tweetid="{@id}"><blockquote placeholder=""><a href="https://twitter.com/user/status/{@id}">X</a></blockquote></amp-twitter>',
		],
	],
	'vk' => [
		'name' => 'VK',
		'homepage' => 'https://vk.com/',
		'tags' => '.ru',
		'host' => [
			'vk.com',
			'vkontakte.ru',
			'vkvideo.ru',
		],
		'example' => [
			'https://vk.com/video121599878_165723901?hash=e06b0878046e1d32',
			'https://vk.com/video_ext.php?oid=121599878&id=165723901&hash=e06b0878046e1d32',
		],
		'attributes' => [
			'oid' => [
				'required' => true,
			],
			'vid' => [
				'required' => true,
			],
		],
		'extract' => [
			'!video(?\'oid\'-?\\d+)_(?\'vid\'\\d+)!',
			'!video_ext\\.php\\?oid=(?\'oid\'-?\\d+)&id=(?\'vid\'\\d+)!',
			'!hash=(?\'hash\'[0-9a-f]+)!',
			'!hd=(?\'hd\'\\d)!',
		],
		'iframe' => [
			'src' => '//vk.com/video_ext.php?oid={@oid}&id={@vid}&hash={@hash}&hd={@hd}',
		],
		'scrape' => [
			'extract' => [
				'#meta property="og:video" content=".*?oid=(?\'oid\'-?\\d+).*?id=(?\'vid\'\\d+)#',
				'#meta property="og:video" content=".*?hash=(?\'hash\'[0-9a-f]+)#',
			],
			'match' => '#^(?!.*?hash=)#',
			'header' => [
				'User-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
				'Cookie: _ignoreAutoLogin=1',
			],
		],
	],
	'peertube' => [
		'attributes' => [
			'host' => [
				'required' => true,
			],
			'id' => [
				'filterChain' => [
					'#identifier',
				],
				'required' => true,
			],
			'start' => [
				'filterChain' => [
					'#timestamp',
				],
				'required' => false,
			],
		],
		'example' => 'https://peertube.tv/w/5JHc2MW1LS4HjDExModcwo',
		'extract' => [
			'!^https://(?\'host\'[-.\\w]+)/(?:videos/embed|w)/(?\'id\'[-\\w]+)(?:\\?start=(?\'start\'\\d[\\dhms]*))?!',
		],
		'homepage' => 'https://joinpeertube.org/',
		'host' => [
			'peertube.tv',
		],
		'iframe' => [
			'src' => 'https://<xsl:value-of select="@host"/>/videos/embed/<xsl:value-of select="@id"/><xsl:if test="@start">?start=<xsl:value-of select="@start"/></xsl:if>',
		],
		'name' => 'PeerTube',
		'scrape' => [
			[
				'extract' => [
					'!https://(?\'host\'[-.\\w]+)/videos/embed/(?\'id\'[-\\w]+)(?:\\?start=(?\'start\'\\d[\\dhms]*))?!',
				],
				'match' => [
					'!^https://(?\'host\'[-.\\w]+)/w/(?\'id\'\\w+)!',
				],
				'url' => 'https://{@host}/services/oembed?url=https%3A%2F%2F{@host}%2Fw%2F%2F{@id}',
			],
		],
		'tags' => [
			'videos',
		],
	],
	'xenforo' => [
		'attributes' => [
			'content_id' => [
				'filterChain' => [
					'#identifier',
				],
				'required' => false,
			],
			'post_id' => [
				'filterChain' => [
					'#uint',
				],
				'required' => false,
			],
			'profile_post_id' => [
				'filterChain' => [
					'#uint',
				],
				'required' => false,
			],
			'resource_id' => [
				'filterChain' => [
					'#uint',
				],
				'required' => false,
			],
			'thread_id' => [
				'filterChain' => [
					'#uint',
				],
				'required' => false,
			],
			'url' => [
				'filterChain' => [
					'#url',
				],
				'required' => true,
			],
			'xfmg_album_id' => [
				'filterChain' => [
					'#uint',
				],
				'required' => false,
			],
		],
		'example' => 'https://xenforo.com/community/threads/embed-your-content-anywhere.217381/',
		'extract' => [
			'!^(?\'url\'https://.*?/)media/albums/(?:[-\\w]+\\.)?(?\'xfmg_album_id\'\\d+)!',
			'!^(?\'url\'https://.*?/)(?:members/[-.\\w]+/#profile-post-|profile-posts/)(?\'profile_post_id\'\\d+)!',
			'!^(?\'url\'https://.*?/)resources/(?:[-\\w]+\\.)?(?\'resource_id\'\\d+)!',
			'!^(?\'url\'https://.*?/)threads/(?:[-\\w]+\\.)?(?\'thread_id\'\\d+)/(?:page-\\d+)?#?(?:post-(?\'post_id\'\\d+))?!',
			'!^(?\'url\'https://.*?/)embed\\.php\\?content=(?\'content_id\'[-\\w]+)!',
		],
		'host' => [
			'xenforo.com',
		],
		'iframe' => [
			'data-s9e-livepreview-ignore-attrs' => 'style',
			'height' => 300,
			'onload' => 'let c=new MessageChannel;c.port1.onmessage=e=>this.style.height=e.data+\'px\';this.contentWindow.postMessage(\'s9e:init\',\'*\',[c.port2])',
			'src' => 'https://s9e.github.io/iframe/2/xenforo.min.html#<xsl:value-of select="@url"/><xsl:choose><xsl:when test="@profile_post_id">profile-posts/<xsl:value-of select="@profile_post_id"/></xsl:when><xsl:when test="@resource_id">resources/<xsl:value-of select="@resource_id"/></xsl:when><xsl:when test="@xfmg_album_id">media/albums/<xsl:value-of select="@xfmg_album_id"/></xsl:when><xsl:otherwise>threads/<xsl:value-of select="@thread_id"/><xsl:if test="@post_id">/post-<xsl:value-of select="@post_id"/></xsl:if></xsl:otherwise></xsl:choose>',
			'width' => '100%',
		],
		'name' => 'XenForo',
		'scrape' => [],
		'tags' => [
			'social',
		],
	],
];
