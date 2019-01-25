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
        <p> Header text : <br>  the tag {article_categories} and {article_tags} will be replaced by the categories and tags respectively covered by the articles included in the daily briefs. </p>
        <br/>
        <textarea maxlength="30000" type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>_header" name="<?php echo $this->plugin_name; ?>[header]"><?php echo ($options["header"] == '' ? '<p>This is the header, this summary contains {article_count} articles about {article_categories}.</p>' : $options['header']) ?></textarea>
        <br />
        <div id="preview-box-header"><div class="comment-by">Live Preview</div><div id="live-preview-header"></div></div>
        <p> Footer text : <br>  the tag {article_categories} and {article_tags} will be replaced by the categories and tags respectively covered by the articles included in the daily briefs. </p>
        <br/>
        <textarea maxlength="30000" type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>_footer" name="<?php echo $this->plugin_name; ?>[footer]"><?php echo ($options["footer"] == '' ? '<p>This is the footer {article_tags}.</p>' : $options['footer']) ?></textarea>
        <br />
        <div id="preview-box-footer"><div class="comment-by">Live Preview</div><div id="live-preview-footer"></div></div>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                let $<?php echo $this->plugin_name; ?>_header = '';
                jQuery('#<?php echo $this->plugin_name; ?>_header').keyup(function() {
                    $<?php echo $this->plugin_name; ?>_header = jQuery(this).val();
                    $<?php echo $this->plugin_name; ?>_header = $<?php echo $this->plugin_name; ?>_header.replace(/\n/g, "<br />").replace(/\n\n+/g, '<br /><br />');
                    jQuery('#live-preview-header').html( <?php echo $this->plugin_name; ?>_header );
                });

                let $<?php echo $this->plugin_name; ?>_footer = '';
                jQuery('#<?php echo $this->plugin_name; ?>_footer').keyup(function() {
                    $<?php echo $this->plugin_name; ?>_footer = jQuery(this).val();
                    $<?php echo $this->plugin_name; ?>_footer = $<?php echo $this->plugin_name; ?>_footer.replace(/\n/g, "<br />").replace(/\n\n+/g, '<br /><br />');
                    jQuery('#live-preview-footer').html( $<?php echo $this->plugin_name; ?>_footer );
                });
            });
        </script>
		<?php


		submit_button( 'Save all changes', 'primary', 'submit', true ); ?>
    </form>
</div>