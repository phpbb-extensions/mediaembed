# phpBB Media Embed PlugIn

An extension for phpBB 3.2 that allows the user to embed content from allowed sites using a [media] BBCode, or from simply posting a supported URL in plain text.

[![Build Status](https://travis-ci.org/phpbb-extensions/mediaembed.svg?branch=master)](https://travis-ci.org/phpbb-extensions/mediaembed)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpbb-extensions/mediaembed/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpbb-extensions/mediaembed/?branch=master)

## Requirements:

* phpBB 3.2.0-RC2 or higher

## Install

1. [Download the latest release](https://github.com/phpbb-extensions/mediaembed/releases).
2. Unzip the downloaded release and copy it to the `ext` directory of your phpBB board.
3. Navigate in the ACP to `Customise -> Manage extensions`.
4. Look for `phpBB Media Embed PlugIn` under the Disabled Extensions list, and click its `Enable` link.
5. Configuration settings are in the ACP at `Posting` -> `Media Embed`.

## Uninstall

1. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
2. Look for `phpBB Media Embed PlugIn` under the Enabled Extensions list, and click its `Disable` link.
3. To permanently uninstall, click `Delete Data` and then delete the `/ext/phpbb/mediaembed` directory.

## Usage Information

A note about existing BBCodes with the same name as a media site ID name (i.e. youtube): 
If you already have one of these, then your existing BBCode will take precedence over 
the one in this plugin. You may continue using your existing BBCode and this plugin will 
ignore any links from that site. 

You can delete the existing BBCode if you want this plugin to handle links from that site
instead. After you delete your old BBCode you must go to `Media Embed` -> 
`Manage Sites` and enable the site.

Note that by deleting the old BBCode your old posts may no longer render the videos 
and will display the deleted BBCode tags as text. Experiment with this on a local 
offline backup of your board first.

A full list of supported media sites can be found [here](http://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/).

## Support

* **Important: Only official release versions validated by the phpBB Extensions Team should be installed on a live forum. Pre-release (beta, RC) versions downloaded from this repository are only to be used for testing on offline/development forums and are not officially supported.**
* Report bugs and other issues to our [Issue Tracker](https://github.com/phpbb-extensions/mediaembed/issues).
* Support requests should be posted and discussed in the [Development topic at phpBB.com](https://www.phpbb.com/community/viewtopic.php?f=456&t=2386631).

## Translations

Translations will not be accepted until after the first official stable release.

## Contributing

Please fork this repository and submit a pull request to contribute to phpBB Media Embed PlugIn

## License

[GPLv2](license.txt)
