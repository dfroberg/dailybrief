<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.froberg.org
 * @since      1.0.0
 *
 * @package    Dailybrief
 * @subpackage Dailybrief/admin/partials
 */

//Grab all options
$options = get_option( $this->plugin_name );
// avoid undefined errors when running it for the first time :
if (!isset($options['test'])) { $options['test¨'] = ''; }

// WP_User_Query arguments
$args = array(
    'role'           => '',
    'orderby'        => 'id',
    'who'            => 'authors',
    'fields'         => array( 'id', 'user_login', 'user_nicename' ),
);

// The User Query
$user_query = new WP_User_Query( $args );
$user_select = '';
// The User Loop
if ( ! empty( $user_query->results ) ) {
    foreach ( $user_query->results as $user ) {
        $user_select .= '<option '.($options["author_id"] == $user->id ? 'SELECTED' : '').' value="'.$user->id.'">'.$user->user_nicename.'</option>';
    }
}
// Get the categories
$categories = get_categories(array('hide_empty' => FALSE));
$category_select = '';
// The Categories loop
if ( ! empty( $categories ) && is_array($categories) ) {
    foreach ( $categories as $category ) {
        $category_select .= '<option '.($options['post_category'] == $category->cat_ID ? 'SELECTED' : '').' value="'.$category->cat_ID.'">'.$category->name.'</option>';
    }
}

if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_options';
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <h2 class="nav-tab-wrapper">
        <a href="options-general.php?page=dailybrief&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">Display Options</a>
        <a href="options-general.php?page=dailybrief&tab=preview" class="nav-tab <?php echo $active_tab == 'preview' ? 'nav-tab-active' : ''; ?>">Preview</a>
    </h2>
    <?php if( $active_tab == 'preview' ) { ?>
    <div style="float: right; margin-right: 30%"><h2>Preview </h2>
    <h1><?php echo $options["post_title"]; ?></h1>
        <p><?php echo $options["header"]; ?></p>
        <p><?php echo $options["footer"]; ?></p>
    </div>

    <?php } // end if preview ?>
    <?php if( $active_tab == 'display_options' ) { ?>
    <p>Join us on the discord server : https://discord.gg/W2KyAbm </p>
    <form method="post" name="cleanup_options" action="options.php">
		<?php settings_fields( $this->plugin_name ); ?>
        <br/>
        <p>User ID to Post as : </p>
        <select id="<?php echo $this->plugin_name; ?>-author_id"
        name="<?php echo $this->plugin_name; ?>[author_id]">
            <?php echo $user_select; ?>
        </select>
        <p>Category ID to Post to : </p>
        <select id="<?php echo $this->plugin_name; ?>-post_category"
                name="<?php echo $this->plugin_name; ?>[post_category]">
            <?php echo $category_select; ?>
        </select>
        <br/>
        <p>Post Title : </p>
        <input type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-post_title"
               name="<?php echo $this->plugin_name; ?>[post_title]"
               value="<?php echo htmlspecialchars( ($options["post_title"] == '' ? 'The Your Site Daily Brief' : $options["post_title"] ), ENT_QUOTES ); ?>"/>
        <br/>
        <p>Post Tags : (Max 5)</p>
        <input type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-post_tags"
               name="<?php echo $this->plugin_name; ?>[post_tags]"
               value="<?php echo htmlspecialchars( ($options["post_tags"] == '' ? 'news-blog,life' : $options["post_tags"] ), ENT_QUOTES ); ?>"/>
        <br/>
        <p>Url Suffix (Append to outbound links in your post)</p>
        <input type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-url_suffix"
               name="<?php echo $this->plugin_name; ?>[url_suffix]"
               value="<?php echo ( ($options["url_suffix"] == '' ? '?campaign=steempress&amp;utm=dailybrief' : $options["url_suffix"] )); ?>"/>
        <br/>
        <p>Excerpt Words (How many words to include)</p>
        <input type="number" class="regular-text" maxlength="4" id="<?php echo $this->plugin_name; ?>-excerpt_words"
               name="<?php echo $this->plugin_name; ?>[excerpt_words]"
               value="<?php echo htmlspecialchars( ($options["excerpt_words"] == '' ? '100' : $options["excerpt_words"] ), ENT_QUOTES ); ?>"/>
        <br/>
        <p>Post Slug : </p>
        <input type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-slug"
               name="<?php echo $this->plugin_name; ?>[slug]"
               value="<?php echo htmlspecialchars( ($options["slug"] == '' ? 'the-daily-brief' : $options["slug"] ), ENT_QUOTES ); ?>"/>
        <br/>
        <p>Article delimiter : </p>
        <input type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-article_delimiter"
               name="<?php echo $this->plugin_name; ?>[article_delimiter]"
               value="<?php echo ( ($options["article_delimiter"] == '' ? '<hr>' : $options["article_delimiter"] )); ?>"/>
        <br/>

        <p>Debugging : </p>
        <label><input type="radio" name="<?php echo $this->plugin_name; ?>[debug]" <?php echo ( ($options["debug"] == '1' ? 'checked' : '' )); ?>> On</label>
        <label><input type="radio" name="<?php echo $this->plugin_name; ?>[debug]" <?php echo ( (($options["debug"] == '0' || empty($options["debug"])) ? 'checked' : '' )); ?>> Off</label>
        <br>
        <p>Include Table of Contents : </p>
        <label><input type="radio" name="<?php echo $this->plugin_name; ?>[include_toc]" <?php echo ( ($options["include_toc"] == '1' ? 'checked' : '' )); ?>> On</label>
        <label><input type="radio" name="<?php echo $this->plugin_name; ?>[include_toc]" <?php echo ( (($options["include_toc"] == '0' || empty($options["include_toc"])) ? 'checked' : '' )); ?>> Off</label>
        <br>
        <p>Local HREFs in TOC : </p>
        <label><input type="radio" name="<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo ( ($options["include_toc_local_hrefs"] == '1' ? 'checked' : '' )); ?>> On</label>
        <label><input type="radio" name="<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo ( (($options["include_toc_local_hrefs"] == '0' || empty($options["include_toc_local_hrefs"])) ? 'checked' : '' )); ?>> Off</label>
        <br>
        <p> Header text : <br> the tags {article_count}, {article_categories} and {article_tags} will be replaced by the count, categories and tags respectively covered by the articles included in the daily briefs. </p>
<?php
$settings = array( 'textarea_rows'=>5,'textarea_name' => $this->plugin_name.'[header]' );
wp_editor(  ($options["header"] == '' ? '<p>This daily summary contains <strong>{article_count}</strong> articles about; <em>{article_tags}</em> in the following categories; <em>{article_categories}</em>.</p>' : $options['header']), 'headereditor', $settings );
?>
        <p> Footer text : <br> the tags {article_count}, {article_categories} and {article_tags} will be replaced by the count, categories and tags respectively covered by the articles included in the daily briefs. </p>
<?php
$settings = array( 'textarea_rows'=>5,'textarea_name' => $this->plugin_name.'[footer]' );
wp_editor(  ($options["footer"] == '' ? '<center><h2>Thank you for following our coverage.</h2></center>' : $options['footer']), 'footereditor', $settings );
?>
        <?php } // end if display_options ?>
		<?php


		submit_button( 'Save all changes', 'primary', 'submit', true ); ?>
    </form>
</div>