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


?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <p>Join us on the discord server : https://discord.gg/W2KyAbm </p>
    <form method="post" name="cleanup_options" action="options.php">
		<?php settings_fields( $this->plugin_name ); ?>
        <br/>
        <p>Test : </p>
        <input type="text" class="regular-text" maxlength="16" id="<?php echo $this->plugin_name; ?>-test"
               name="<?php echo $this->plugin_name; ?>[test]"
               value="<?php echo htmlspecialchars( $options["test"], ENT_QUOTES ); ?>"/>
        <br/>
        <p> Header text : <br> the tag {article_categories} and {article_tags} will be replaced by the categories and tags respectively covered by the articles included in the daily briefs. </p>
<?php
$settings = array( 'textarea_rows'=>5,'textarea_name' => $this->plugin_name.'[header]' );
wp_editor(  ($options["header"] == '' ? '<p>This is the header, this summary contains {article_count} articles about {article_categories}.</p>' : $options['header']), 'headereditor', $settings );
?>
        <p> Footer text : <br> the tag {article_categories} and {article_tags} will be replaced by the categories and tags respectively covered by the articles included in the daily briefs. </p>
<?php
$settings = array( 'textarea_rows'=>5,'textarea_name' => $this->plugin_name.'[footer]' );
wp_editor(  ($options["footer"] == '' ? '<p>This is the footer {article_tags}.</p>' : $options['footer']), 'footereditor', $settings );
?>
		<?php


		submit_button( 'Save all changes', 'primary', 'submit', true ); ?>
    </form>
</div>