# dailybrief WP-CLI

## Description
Plugin with WP_CLI support to generate a simple daily daily brief with the posts of the day

## Installation

This section describes how to install the plugin and get it working.

```wp plugin install --activate https://gitlab.froberg.org/dfroberg/dailybrief/-/archive/master/dailybrief-master.zip```

## Frequently Asked Questions

#### Note:
This is still alpha grade code, and subject to change!
 
## Setup & Run via WP CLI:

The dailybrief command is intended to be run as a CRON task.
(Suggestion, you might want to run WP CRON via WP_CLI too)

```wp cron event run --due-now```

## Configuration via WP CLI:
All static configuration is done from the WP_CLI command line using *wp dailybrief set &lt;option&gt; &lt;value&gt;*
### Basic Settings:
Configure the post author id;

First find the user ID you wish to post as;
````
> wp user list --fields="ID,display_name"
+----+----------------------+
| ID | display_name         |
+----+----------------------+
| 1  | Your Displayname     |
+----+----------------------+
````
Then use the dailybrief set command to set it.
````
> wp dailybrief set author_id 1
Updated author_id = 1
````
Find the category you want to post to;
```
   > wp term list category --fields=term_id,name --name="Your Category"
   +---------+---------------+
   | term_id | name          |
   +---------+---------------+
   | 34000   | Your Category |
   +---------+---------------+
```
Then use the dailybrief set command again to set it.
```   
   > wp dailybrief set post_category 34000
   Updated post_category = 34663
```
That is it, the bare minimum is done. You probably want to personalize it more so here are some more examples for you. Check out the options table and play with the options until you find a combination that suits you. (Just avoid posting to steem as that messes up your account and sends some people into a frenzy)

    wp dailybrief set header '<h2>This is the header</h1>This summary contains {article_count} articles about {article_categories} and {article_tags}.</h1>'
    wp dailybrief set footer '<p>This is the footer.</p>'
    wp dailybrief set post_title 'The Your Site Daily Brief'
    wp dailybrief set post_tags 'news-blog,life'

### Options available:

|: Option        | Value type | Default Value     | Example - Description                     |
|:--------------|:-----------|:------------------|:------------------------------------------|
| debug         | numeric    | 1                 | 1 = on / 0 = Off                          |
| include_toc   | numeric    | 1                 | 1 = on / 0 = Off - Include a table of contents |
| include_toc_localhrefs | numeric    | 1                 | 1 = on / 0 = Off - Include TOC local links (steemit bug) |
| toc_header    | string     | "Table of Contents" | Table of Contents header text |
| url_suffix    | string     | ""                | "?campaign=steempress&amp;utm=dailybrief" |
| excerpt_words | numeric    | 100               | Numer of words in excerpt                 |
| post_title    | string     | "The Daily Brief" | A date 2018-11-15 will be suffixed to this|
| author_id     | numeric    | 1                 | User ID that will be the author           |
| post_category | list       | 1                 | Category ID(s) to post to single or comma separated |
| post_tags     | list       | ""                | Tags to add i.e. news,life,blog will create tags if nonexistent          |
| always_skip_category | list | post_category | Always skips the brief category |
| slug | string | "the-daily-brief" | A date i.e. 2018-11-15 will be suffixed to this |
| comment_status | string | "open" | open or closed for comments |
| ping_status | string | "closed" | open or closed for pings | 
| post_status | string | "draft" | Is set to "draft" to only create post but not publish, quick override available with --publish flag |
| post_type   | string | "post" | Only "post" is supported and tested |
| article_delimiter | string | "&lt;hr&gt;" | Delimited between summarized articles |
| article_continue | string | "Continue&nbsp;-&gt;" | Read more text after excerpt |
| article_stats_txt | string | "&lt;hr&gt;Articles in this brief: " | Article part of stats section |
| article_stats_cats_txt | string | '&lt;br&gt;Categories in this brief: '| Categories part of stats section |
| article_stats_tags_txt | string | '&lt;br&gt;Tags in this brief: '| Tags part of stats section |
| featured_image_url | string | "" | Fixed Image URL to use as featured image, if not set first summariez article posts image will be used. |

## Formating headers and footers
There are a few keywords that you can use in your header;

So for example if you set your header to `wp dailybrief set header 'This is a summary of {article_count} article(s) about {article_categories} containing {article_tags}'` it will be included in your post as `'This is a summary of 5 article(s) about Holliday, Photos containing life,blog,news`

You can use HTML to format the header and footer.

## Running from WP CLI
Create list of posts with dates between before and after dates

### OPTIONS & FLAGS:

[--post]
: Create the post in Wordpress
* default: false

[--period=<day|range>]
: Indicate what type of summary
* default: day

[--start=<strtime>]
: Begin brief with posts on day i.e. "-1 day" / "yesterday"

[--end=<strtime>]
: End brief with posts on day i.e. "-1 day" / "yesterday"

[--use-excerpts] [--no-use-excerpts]
: Do you want to use the excepts of the summarized Wordpress posts
* default: true

[--publish]
: Set the post_status to 'Publish' Wordpress posts
* default: false

[--days=<days>]
: Days back from where to get the posts to summarize 'today' / '-1 day' / '-2 days'
* default: today

### Examples:
To dump an preview to the console;

`wp dailybrief create --days="-1 day" --no-use-excerpts`

To produce a draft post;

`wp dailybrief create --days="2018-10-15" --use-excerpts --post`

To create and publish a post;

`wp dailybrief create --days="today" --post --publish`  

Will generate one post with summaries of all articles from the day before using the body to create the excerpt.

`wp dailybrief brief --days='-1 day' --post --publish --no-use-excerpts`
 
# Combining with SteemPress
Although the dailybrief command can be used with any type of social media or newsletter plugin to distribute your Daily Brief it was written and intended to be use as a companion application to SteemPress, to enable high frequency Wordpress authors not to be hammered by numerous spambots and "holier than thau" self professed "I AM THE COMMUNITY!!!" steem cops and simply post ONE (or max 4) briefs per day.   
## SteemPress specific setup
* In Posts -> Categories
: Create a new category named i.e. "Daily Brief", make a note of the ID once created.
* In Settings -> SteemPress
: Select to ignore all categories but the newly created "Daily Brief" category and save the selection.
* You can easily get the Category ID on command line run using WP CLI; 
```
   > wp term list category --fields=term_id,name --name="Daily Brief"
   +---------+-------------+
   | term_id | name        |
   +---------+-------------+
   | 34663   | Daily Brief |
   +---------+-------------+
   
   > wp dailybrief set post_category 34663
   Updated post_category = 34663
```
  
# Roadmap:

#### General:
General improvements planned for the plugin;
* Better preview system.
* More configurable options and formating.
* Pre-made styling templates.

#### Steem / SteemPpress:
Improvements planned specific to Steem & SteemPress integration;
* A VERY high frequency version allowing for 4 posts a day splitting the day into 6 hour segments. (it will skip a segment if no articles has been produced or you have less than 
  `wp dailbrief set article_treshold X` ) where X is an integer representing the number of articles that is considered to few to publish in it's own segment.   
* Create a brief for individual users posts and allow SteemPress to publish these to the individual steem accounts.

  
  
