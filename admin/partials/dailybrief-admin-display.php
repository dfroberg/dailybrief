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
        <p>Header : </p>
        <input type="text" class="regular-text" maxlength="250" id="<?php echo $this->plugin_name; ?>-header"
               name="<?php echo $this->plugin_name; ?>[header]"
               value="<?php echo htmlspecialchars( $options["header"], ENT_QUOTES ); ?>"/>
        <br/>
        <p>Footer : </p>
        <input type="text" class="regular-text" maxlength="250" id="<?php echo $this->plugin_name; ?>-footer"
               name="<?php echo $this->plugin_name; ?>[footer]"
               value="<?php echo htmlspecialchars( $options["footer"], ENT_QUOTES ); ?>"/>
        <br/>
		<?php


		submit_button( 'Save all changes', 'primary', 'submit', true ); ?>
    </form>
</div>