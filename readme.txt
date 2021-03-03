=== DailyBrief ===
Contributors:      dfroberg
Donation Link:     https://steemit.com/@dfroberg
Tags:              content+generation, generation, steem, blockchain, hive, briefs, monetization
Requires at least: 4.7
Tested up to:      5.7
Stable tag:        trunk
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

DailyBrief is a WordPress plugin that provides a post summary of all WordPress posts written a during a day.

== Description ==

DailyBrief is designed to solve a fundamental problem many new WordPress users find themselves with when they first begin to publish on the steem blockchain using SteemPress; The dreaded SPAM label. Producing too many posts daily on steem hits a cultural bias many anti-abuse teams adhere to "More than 4 posts a day is always SPAM".

DailyBrief compiles a summary of all posts made during a particular day complete with featured images, meta information, and excerpts. It also creates a table of contents, inserts headers and footers with optional macros that auto-fill in article counts, tags and categories used in the processed articles.

> **Important: The plugin is primarily intended to be used alongside [SteemPress](https://wordpress.org/plugins/steempress/) the WordPress to Steem integration solution. If you want to publish your daily briefs to steem you need to install [SteemPress](https://wordpress.org/plugins/steempress/) first.**
> **Note: This plugin requires PHP 5.6 or higher to be activated.**
> **Note: Automated tests for PHP 5.6, 7.0, 7.1, 7.2 are made on each commit.**
> [![Build Status](https://gitlab.froberg.org/dfroberg/dailybrief/badges/master/build.svg)](https://github.com/dfroberg/dailybrief)

---


== Features ==

* Daily automated brief generated for previous day (if any posts was published that day).
* Automatic integration with your SteemPress installation.


== Screenshots ==

1. Automatic Preview gives you a pretty good overview on how your post will look on Steem.
2. Table of Contents.
3. Footer and Manual Brief Generation button.
4. Options 1
5. Options 2
6. Options 3


== Languages ==

We plan to use GlotPress on WordPress.org, so if you want to translate DailyBrief to your language please [follow this guidelines](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/=translating-themes-plugins).


== See room for improvement? ==

Great! There are several ways you can get involved to help make Dailybrief better:

1. **Report Bugs:** If you find a bug, error or other problem, please report it! You can do this by [creating a new topic](http://wordpress.org/support/plugin/dailybrief) in the plugin forum. Once a developer can verify the bug by reproducing it, they will create an official bug report in GitHub where the bug will be worked on.
2. **Suggest New Features:** Have an awesome idea? Please share it! Simply [create a new topic](http://wordpress.org/support/plugin/dailybrief) in the plugin forum to express your thoughts on why the feature should be included and get a discussion going around your idea.
3. **Check out our Github:** Take a look at the code; [GitHub](https://github.com/dfroberg/dailybrief)

Thank you for wanting to make DailyBrief better for everyone! [We salute you](https://www.youtube.com/watch?v=8fPf6L0XNvM).


== Frequently Asked Questions ==

= Can daily briefs be generated via WP CLI? =

Yes, check [WP-CLI-README](https://github.com/dfroberg/dailybrief/blob/master/WP-CLI-README.md) to learn how.

= Can I temporarily disable generation of daily briefs? =

Yes, go to settings and set pause to Yes.

= Can I generate daily briefs as drafts? =

Yes, go to settings and set publish to No.

= If I haven't written any posts will Daily Brief publish empty briefs? =

No, if it detects that there is nothing to summarize it will skip post creation.

= Can I create the Briefs manually and not rely on CRON jobs? =

Yes, Set "CRON Pause" to on and use the preview windows "Generate Now" button to create the briefs.

= How do I fix 404 / page not found when clicking on a post link in a brief? =

Take a look at the URL Suffix '?campaign=xxxxx' etc and prefix it i.e. '?utm_campaign=xxxxxx' and see if it helps, there is a slew of reserved parameters that is a terrible idea to use; [Reserved Terms](https://codex.wordpress.org/Reserved_Terms), many plugins also take ownership of specific parameters, so a bit of trial and error might be required. **Test links in your Preview**.


== Changelog ==
= 1.0.40 &mdash; 3 of March, 2021 =
* Compatibility check with WP 5.5

= 1.0.39 &mdash; 24 of August, 2020 =
* Compatibility check with WP 5.5 and SteemPress 2.6.3.

= 1.0.38 &mdash; 11 of June, 2020 =
* Compatibility check with WP 5.4.2 and SteemPress 2.6.3.

= 1.0.37 &mdash; 8 of January, 2020 =
* Compatibility check for WP 5.3 and SteemPress 2.6.

= 1.0.36 &mdash; 30 of September, 2019 =
* Compatibility check for WP 5.2.3 and SteemPress 2.6.

= 1.0.35 &mdash; 10 of June, 2019 =
* Compatibility check for WP 5.2.1 and SteemPress 2.4.1.

= 1.0.34 &mdash; 5 of April, 2019 =
* Fix: More workarounds setting featured image of the post dynamically while using CDN etc.

= 1.0.33 &mdash; 4 of April, 2019 =
* Fix: Workaround for catching and setting featured image of the post dynamically while using CDN etc.

= 1.0.32 &mdash; 3 of April, 2019 =
* Fix: Show warning in debug screen if steempress is detected and post is too large.

= 1.0.31 &mdash; 1 of April, 2019 =
* Fix: Ensure character limit is only respected if SteemPress is installed and active, as there are other uses for DailyBrief.
* Add: Enable or Disable Article title links.
* Fix: Strip shortcodes from excerpts.
* Fix: Some display fixes in preview and generated posts.
* Fix: Make sure your text is smaller than 65280 characters.

= 1.0.30 &mdash; 18 of March, 2019 =
* Fix: Debug aside covered options screens on small screen devices.
* Fix: Fixed select2 JS error on multiple=true and removed it since it's not needed.

= 1.0.29 &mdash; 7 of March, 2019 =
* New: Enabled select2 & multiple focus categories.

= 1.0.28 &mdash; 26 of February, 2019 =
* Fix: Filled out some more information on how to get support.

= 1.0.27 &mdash; 26 of February, 2019 =
* Fix: To avoid conflicts change the default url suffix from campaign to utm_campaign, there seems to conflicts where campaign parameter causes 404's.

= 1.0.26 &mdash; 22 of February, 2019 =
* Fix: Add extra checks for missing CRON job, plugin not activated properly or upgraded without activate/deactivate.

= 1.0.25 &mdash; 22 of February, 2019 =
* Fix: Ensuring compatibility with Windows 5.1
* Fix: More preview window style fixes.
* New: Added generate manually now button to Preview window for those that really want to do things manually.
* Fix: An logical error in CRON pause resolved.

= 1.0.24 &mdash; 21 of February, 2019 =
* Fix: Rework Admin GUI & Try getting the timezone to display properly.
* Add: Make sidebar display debug information of Debug option is on.

= 1.0.23 &mdash; 20 of February, 2019 =
* Fix: Date selection for periods and single day to include hours
* Fix: Reorder Period selection fields plus adding more verbose debug information.
* Fix: Category exclusions
* Fix: Add new options to Internal CRON job.

= 1.0.22 &mdash; 19 of February, 2019 =
* Fix: Admin GUI layout and position of options.
* Fix: WP Cron activator / de-activator.
* New: Make cron_pause an option, pause post creation, this will skip post creation entirely.

= 1.0.21 &mdash; 14 of February, 2019 =
* Fix: Add try catch for scheduling timestamp in cron.
* New: Make cron_publish an option, this is practical if you're not quite ready with your setup or wish to pause creation of daily briefs.

= 1.0.20 &mdash; 13 of February, 2019 =
* Fix: Set internal CRON to fire "tomorrow" after midnight taking WP timezone into consideration.

= 1.0.19 &mdash; 8 of February, 2019 =
* New: Implement internal CRON to fire once a day for now. Should suffice for basic usage.

= 1.0.18 &mdash; 8 of February, 2019 =
* New: Implement focus category setting; Enable briefs about a particular subject.

= 1.0.17 &mdash; 8 of February, 2019 =
* Fix: Ensure that the category you post Daily Briefs to is always skipped from daily brief sourcing.
* Fix: User names now full names for readability on settings screens.

= 1.0.16 &mdash; 7 of February, 2019 =
* Improvement: Added Admin GUI preview for Dailybrief posts (steemit:ish version).

= 1.0.0 &mdash; 4 of January, 2019 =
* Improvement: Added Admin GUI for settings.

= 0.0.9 &mdash; 17 of December, 2018 =
* First initial WP_CLI version.


== Roadmap ==

* Multi-user functionality to work with StemPress 2.3+
* Multi-focus categories.
* Multi-period support; daily, weekly, monthly briefs.
* Ajaxify the options panel to make things a lot easier!!!
* High frequency post sites support for sites that requires multiple briefs per day.
