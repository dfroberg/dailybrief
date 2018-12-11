# dailybrief

## Description:
WP_CLI command plugin to generate a simple daily daily brief with the posts of the day

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
| url_suffix    | string     | ""                | "?campaign=steempress&amp;utm=dailybrief" |
| excerpt_words | numeric    | 100               | Numer of words in excerpt                 |
| post_title    | string     | "The Daily Brief" | A date 2018-11-15 will be suffixed to this|
| author_id     | numeric    | 1                 | User ID that will be the author           |
| post_category | list       | 1                 | Category ID(s) to post to single or comma separated |
| always_skip_category | list | post_category | Always skips the brief category |
| slug | string | "the-daily-brief" | A date 2018-11-15 will be suffixed to this |
| comment_status | string | "open" | open or closed for comments |
| ping_status | string | "closed" | open or closed for pings | 
| post_status | string | "publish" | Set to "draft" to only create post but not publish |
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

