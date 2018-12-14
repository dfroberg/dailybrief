# dailybrief

## Description:
WP_CLI command plugin to generate a simple daily daily brief with the posts of the day

#### Note:
This is still alpha grade code, and subject to change!
 
## Setup:
Install by issuing;

```wp plugin install --activate https://gitlab.froberg.org/dfroberg/dailybrief/-/archive/master/dailybrief-master.zip```

The dailybrief command is intended to be run as a CRON task.
(Suggestion, you might want to run WP CRON via WP_CLI too)

``` wp cron event run --due-now```

## Configuration:
All static configuration is done from the WP_CLI command line using *wp dailybrief set &lt;option&gt; &lt;value&gt;*
### Examples:

    wp dailybrief set header '<h1>This is the header, this summary contains {article_count} articles about {article_categories}.</h1>'
    wp dailybrief set footer '<h1>This is the footer.</h1>'
    wp dailybrief set post_title 'The Your Site Daily Brief'
    wp dailybrief set post_status 'draft'

### Options available:

| Option        | Value type | Default Value     | Example - Description                     |
|:--------------|:-----------|:------------------|:------------------------------------------|
| debug         | numeric    | 1                 | 1 = on / 0 = Off                          |
| include_toc   | numeric    | 1                 | 1 = on / 0 = Off - Include a table of contents |
| include_toc_localhrefs | numeric    | 1                 | 1 = on / 0 = Off - Include TOC local links (steemit bug) |
| url_suffix    | string     | ""                | "?campaign=steempress&amp;utm=dailybrief" |
| excerpt_words | numeric    | 100               | Numer of words in excerpt                 |
| post_title    | string     | "The Daily Brief" | A date 2018-11-15 will be suffixed to this|
| author_id     | numeric    | 1                 | User ID that will be the author           |
| post_category | list       | 1                 | Category ID(s) to post to single or comma separated |
| post_tags     | list       | ""                | Tags to add i.e. news,life,blog will create tags if nonexistent          |
| always_skip_category | list | post_category | Always skips the brief category |
| slug | string | "the-daily-brief" | A date 2018-11-15 will be suffixed to this |
| comment_status | string | "open" | open or closed for comments |
| ping_status | string | "closed" | open or closed for pings | 
| post_status | string | "draft" | Is set to "draft" to only create post but not publish, quick override available with --publish flag |
| post_type   | string | "post" | Only "post" is supported and tested |
| article_delimiter | string | "&lt;hr&gt;" | Delimited between summarized articles |
| article_continue | string | "Continue&nbsp;-&gt;" | Read more text after excerpt |
| article_stats_txt | string | "&lt;hr&gt;Articles in this brief: " | Article part of stats section |
| article_stats_cats_txt | string | &lt;br&gt;Categories in this brief: | Categories part of stats section |
| featured_image_url | string | "" | Fixed Image URL to use as featured image, if not set first summariez article posts image will be used. |

### Flags
[--post]
: Create the post in Wordpress

[--use-excerpts] [--no-use-excerpts]
: Do you want to use the excepts of the summarized Wordpress posts
 default: true

[--days=<days>]
: Days back from where to get the posts to summarize 'today' / '-1 day' / '-2 days'
default: today

## Formating headers and footers
There are a few keywords that you can use in your header;

So for example if you set your header to `wp dailybrief set header 'This is a summary of {article_count} article(s) about {article_categories} containing {article_tags}'` it will be included in your post as `'This is a summary of 5 article(s) about Holliday, Photos containing life,blog,news`

You can use HTML to format the header and footer.

## Running
Create list of posts with dates between before and after dates

#### OPTIONS:

[--post]
: Create the post in Wordpress
* default: false

[--use-excerpts]
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
* On command line run;
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
* STEEM SPECIFIC:
: A VERY high frequency version allowing for 4 posts a day splitting the day into 6 hour segments. (it will skip a segment if no articles has been produced or you have less than 
  `wp dailbrief set article_treshold X` )
  
