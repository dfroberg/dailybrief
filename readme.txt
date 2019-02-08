=== FakerPress ===
Contributors:      dfroberg
Tags:              content generation, generation, steem, blockchain, steemit, briefs
Requires at least: 4.7
Tested up to:      5.0.4
Stable tag:        trunk
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

DailyBrief is a WordPress plugin that provides a post summary of all WordPress posts written a during a day.

== Description ==

DailyBrief is designed to solve a fundamental problem many new WordPress users find themselves with when they first begin to publish on the steem blockchain using SteemPress; The dreaded SPAM label. Producing too many posts daily on steem hits a cultural bias many anti-abuse teams adhere to "More than 4 posts a day is always SPAM".

DailyBrief compiles a summary of all posts made during a particular day complete with featured images, meta information, and excerpts. It also creates a table of contents, inserts headers and footers with optional macros that auto-fill in article counts, tags and categories used in the processed articles.

> **Important: it's primarily intended to be used alongside [SteemPress](https://wordpress.org/plugins/steempress/) the WordPress to Steem integration solution. **
> **Note: This plugin requires PHP 5.6 or higher to be activated.**
> **Note: Automated tests for PHP 5.6, 7.0, 7.1, 7.2 are made on each commit.**

---

= Features =

* Daily automated brief generated for previous day (if any posts was published that day).
* Automatic integration with your SteemPress installation.

= Languages =
We use GlotPress on WordPress.org, so if you want to translate FakerPress to your language please [follow this guidelines](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/#translating-themes-plugins).

= See room for improvement? =

Great! There are several ways you can get involved to help make Dailybrief better:

1. **Report Bugs:** If you find a bug, error or other problem, please report it! You can do this by [creating a new topic](http://wordpress.org/support/plugin/dailybrief) in the plugin forum. Once a developer can verify the bug by reproducing it, they will create an official bug report in GitHub where the bug will be worked on.
2. **Suggest New Features:** Have an awesome idea? Please share it! Simply [create a new topic](http://wordpress.org/support/plugin/dailybrief) in the plugin forum to express your thoughts on why the feature should be included and get a discussion going around your idea.

Thank you for wanting to make FakerPress better for everyone! [We salute you](https://www.youtube.com/watch?v=8fPf6L0XNvM).

= Frequently Asked Questions =



== Changelog ==
= 1.0.17 &mdash; 8 of February, 2019 =

* Fix: Ensure that the category you post Daily Briefs to is always skipped from daily brief sourcing.
* Fix: User names now full names for readability on settings screens.

= 1.0.16 &mdash; 7 of February, 2019 =

* Improvement: Added Admin GUI preview for Dailybrief posts (steemit:ish version).

= 1.0.0 &mdash; 4 of January, 2019 =

* Improvement: Added Admin GUI for settings.

= 0.0.9 &mdash; 17 of December, 2018 =

* First initial WP_CLI version.