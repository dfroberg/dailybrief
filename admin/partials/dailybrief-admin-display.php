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

// Grab all options.
$dc = new Dailybrief();
$dc->update_globals();
$options = $dc->get_options(); // get_option( $this->plugin_name ); // .

// Avoid undefined errors when running it for the first time.
if ( ! isset( $options['test'] ) ) {
	$options['test¨'] = '';
}

// WP_User_Query arguments.
$args = array(
	'role'    => '',
	'orderby' => 'id',
	'who'     => 'authors',
	'fields'  => array( 'id', 'user_login', 'user_nicename', 'display_name' ),
);

// The User Query.
$user_query  = new WP_User_Query( $args );
$user_select = '';
// The User Loop.
if ( ! empty( $user_query->results ) ) {
	foreach ( $user_query->results as $user ) {
		if ( $options['author_id'] === $user->id ) {
			$debug_current_author_name = $user->display_name;
		}
		$user_select .= '<option ' . ( $options['author_id'] == $user->id ? 'SELECTED' : '' ) . ' value="' . $user->id . '">' . $user->display_name . '</option>';
	}
}
// Get the categories.
$categories      = get_categories( array( 'hide_empty' => false ) );
$category_select = '';
// The Categories loop.
if ( ! empty( $categories ) && is_array( $categories ) ) {
	foreach ( $categories as $category ) {
		$tmp_selected_current_category = false;
		if ( $dc->get_post_category() == $category->cat_ID ) {
			$debug_current_category_name   = $category->name;
			$debug_current_category_id     = $category->cat_ID;
			$tmp_selected_current_category = true;
		}
		$category_select .= '<option ' . ( $tmp_selected_current_category ? 'SELECTED' : '' ) . ' value="' . $category->cat_ID . '">' . $category->name . '</option>';
	}
}
// The Focus on Categories loop.
$category_focus_select = '';
if ( ! empty( $categories ) && is_array( $categories ) ) {
	foreach ( $categories as $category ) {
		if ( 1 === $category->cat_ID ) {
			break;
		}
		$tmp_selected_current_focus_category = false;
		if ( in_array( $category->cat_ID, explode( ',', $options['focus'] ) ) ) {
			$debug_current_focus_category_name[] = $category->name;
			$tmp_selected_current_focus_category = true;
		}
		$category_focus_select .= '<option ' . ( $tmp_selected_current_focus_category ? 'SELECTED' : '' ) . ' value="' . $category->cat_ID . '">' . $category->name . '</option>';
	}
}
// Figure out what tab we're on.
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'preview';

?>
<div class="dailybrief">
	<div class = "dailybrief-settings">
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<h2 class = "nav-tab-wrapper">
			<a href = "options-general.php?page=dailybrief&tab=options"
					class = "nav-tab <?php echo 'options' === $active_tab ? 'nav-tab-active' : ''; ?>">Options</a>
			<a href = "options-general.php?page=dailybrief&tab=preview"
					class = "nav-tab <?php echo 'preview' === $active_tab ? 'nav-tab-active' : ''; ?>">Preview</a>
			<a href = "options-general.php?page=dailybrief&tab=support"
					class = "nav-tab <?php echo 'support' === $active_tab ? 'nav-tab-active' : ''; ?>">Support</a>
		</h2>
		<?php
		if ( 'support' === $active_tab ) {
			?>
			<p>For advice and some live support join <a href="https://discord.gg/W2KyAbm">https://discord.gg/W2KyAbm</a> and talk to Danny.</p>
			<p>To open a support topic you can go either to <a href="https://wordpress.org/support/plugin/dailybrief/" target="_blank">WordPress Support Forum</a> and create a new topic, or <a href="https://github.com/dfroberg/dailybrief/issues" target="_blank">GitHub Issues</a> and create an Issue.</p>
			<p>Make sure you're running the latest version before reporting issues.</p>
			<?php
		} // end if manual publish.
		if ( 'publish' === $active_tab ) {
			$dc_result = $dc->dailybrief_do_daily_event( true );
			if ( ! isset( $dc_result['error'] ) ) {
				echo '<h2>Your Daily Brief is done!</h2>';
				echo '<h3>Published: ' . $dc_result['post_title'] . '</h3>';
				echo '<a href = "options-general.php?page=dailybrief&tab=preview">Go back</a>';
			} else {
				echo '<h3>An Error occurred during Manual Publish:</h3><pre>' . print_r( $dc_result['error'], true ) . '</pre>';
			}
		} // end if manual publish.
		if ( 'preview' === $active_tab ) {
			// Generate preview.
			$sample = $dc->create(
				array(
					'preview'         => true,
					'period'          => $options['period'],
					'days'            => date( 'Y-m-d', strtotime( $options['start_date'] ) ),
					'start'           => date( 'Y-m-d H:i:s', strtotime( $options['start_date'] ) ),
					'end'             => date( 'Y-m-d H:i:s', strtotime( $options['end_date'] ) ),
					'use-excerpts'    => $options['use_excerpts'],
					'skip-categories' => $options['skip_categories'],
				)
			)
			?>
			<div id = "dailybrief-preview-post" class = "dailybrief-preview-post">
				<h1 style = "vertical-align: center; margin-bottom: 0;"><?php echo $sample['post_title'] . ' ' . $dc->get_date_suffix(); ?>
					<img src = "<?php echo plugin_dir_url( __FILE__ ); ?>/images/steemit.png" width = "27" height = "27"></h1>
				<div id="user-block" class="user-block">
					<h2><img src = "<?php echo plugin_dir_url( __FILE__ ); ?>/images/white_icon_dailybrief.png" width = "70" height = "70"> dailybrief (48) in news • 12 hours ago </h2>
				</div>
				<p></p>
				<div id="steem-body" class="steem-body">
				<?php
				echo '<center><img src="' . $dc->get_temp_featured_image_url() . '" width="640"></center>';
				?>
				<br>
				<p></p>
				<p>
					<?php
					echo wpautop( $sample['content'] );
					?>
				</p>
				</div>
				<hr>
				<div align="center">
					<?php
					if ( defined( 'DAILYBRIEF_DETECTED_STEEMPRESS' ) && _mb_strlen( $sample['content'] ) < 65280 ) {
						?>
						<a href = "options-general.php?page=dailybrief&tab=publish">
							<button class = "dailybrief_preview_generate">Manually Generate Brief Now!</button>
							<p>This will create the Brief immediately with the contents in the preview.</p>
						</a>
						<?php
					} else {
						?>
						<p>Make sure your text is smaller than <strong>65280</strong> characters.<br>It is now <?php echo _mb_strlen( $sample['content'] ); ?> characters.</p>
						<?php
					}
					?>
				</div>
			</div>

		<?php } // end if preview ?>
		<?php if ( 'options' === $active_tab ) { ?>
			<form method = "post" class = "settings-form" name = "cleanup_options" action = "options.php">
				<?php settings_fields( $this->plugin_name ); ?>
				<fieldset>
					<table>
						<tr><td colspan="2"><h3>CRON Control</h3></td></tr>
						<tr><td>CRON Publish</td><td><input type = "radio"
									value = "1"
										id = "cron_publish_on"
									name = "<?php echo $this->plugin_name; ?>[cron_publish]" <?php echo( '1' === $options['cron_publish'] ? 'checked' : '' ); ?> /><label for="cron_publish_on">On (Default)</label> <input type = "radio"
									value = "0"
										id = "cron_publish_off"
									name = "<?php echo $this->plugin_name; ?>[cron_publish]" <?php echo( ( '0' === $options['cron_publish'] || empty( $options['cron_publish'] ) ) ? 'checked' : '' ); ?> /><label for="cron_publish_off">Off</label>
								<br><em>( If on; create the post but do not publish it )</em> </td>
						</tr>
						<tr><td>CRON Pause</td><td><input type = "radio"
										value = "1"
										id = "cron_pause_on"
										name = "<?php echo $this->plugin_name; ?>[cron_pause]" <?php echo( '1' === $options['cron_pause'] ? 'checked' : '' ); ?> /><label for="cron_pause_on">On</label> <input type = "radio"
										value = "0"
										id = "cron_pause_off"
										name = "<?php echo $this->plugin_name; ?>[cron_pause]" <?php echo( ( '0' === $options['cron_pause'] || empty( $options['cron_pause'] ) ) ? 'checked' : '' ); ?> /><label for="cron_pause_off">Off (Default)</label>
								<br><em>( Will disable post creation by internal CRON )</em> </td>
						</tr>
						<tr><td colspan="2"><h3>Who, what & where?</h3></td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-author_id">User ID to Post as :</label>
							</td>
							<td>
								<select id = "<?php echo $this->plugin_name; ?>-author_id"
										name = "<?php echo $this->plugin_name; ?>[author_id]">
									<?php echo $user_select; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-post_category">Category to Post to :</label>
							</td>
							<td>
								<select id = "<?php echo $this->plugin_name; ?>-post_category"
										name = "<?php echo $this->plugin_name; ?>[post_category]">
									<?php echo $category_select; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-focus">Focus on Categories :</label>
							</td>
							<td>
								<select id = "<?php echo $this->plugin_name; ?>-focus"
										name = "<?php echo $this->plugin_name; ?>[focus][]" multiple="multiple">
									<?php echo $category_focus_select; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-post_title">Post Title :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-post_title"
										name = "<?php echo $this->plugin_name; ?>[post_title]"
										value = "<?php echo htmlspecialchars( ( '' === $options['post_title'] ? $dc->get_post_title() : $options['post_title'] ), ENT_QUOTES ); ?>"/>
							</td>
						</tr>
						<tr><td>Title Link</td><td><input type = "radio"
										value = "1"
										id = "title_link_on"
										name = "<?php echo $this->plugin_name; ?>[title_link]" <?php echo( '1' === $options['title_link'] ? 'checked' : '' ); ?> /><label for="title_link_on">On (Default)</label> <input type = "radio"
										value = "0"
										id = "title_link_off"
										name = "<?php echo $this->plugin_name; ?>[title_link]" <?php echo( ( '0' === $options['title_link'] || empty( $options['title_link'] ) ) ? 'checked' : '' ); ?> /><label for="title_link_off">Off</label>
								<br><em>( Make post title linked to original article )</em> </td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-slug">Post Slug :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-slug"
										name = "<?php echo $this->plugin_name; ?>[slug]"
										value = "<?php echo htmlspecialchars( ( '' === $options['slug'] ? $dc->get_slug() : $options['slug'] ), ENT_QUOTES ); ?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-post_tags">Post Tags :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-post_tags"
										name = "<?php echo $this->plugin_name; ?>[post_tags]"
										value = "<?php echo htmlspecialchars( ( '' === $options['post_tags'] ? $dc->get_post_tags() : $options['post_tags'] ), ENT_QUOTES ); ?>"/>
								<br><em>( Max 5 )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-url_suffix">Url Suffix :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-url_suffix"
										name = "<?php echo $this->plugin_name; ?>[url_suffix]"
										value = "<?php echo( '' === $options['url_suffix'] ? $dc->get_url_suffix() : $options['url_suffix'] ); ?>"/>
								<br><em>( Append to outbound links in your post )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-excerpt_words">Excerpt Words :</label>
							</td>
							<td>
								<input type = "number" class = "regular-text" maxlength = "4"
										id = "<?php echo $this->plugin_name; ?>-excerpt_words"
										name = "<?php echo $this->plugin_name; ?>[excerpt_words]"
										value = "<?php echo htmlspecialchars( ( '' === $options['excerpt_words'] ? $dc->get_excerpt_words() : $options['excerpt_words'] ), ENT_QUOTES ); ?>"/>
								<br><em>( How many words to include )</em>
							</td>
						</tr>
						<tr>
							<td>
								Use Excerpts :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "use_excerpts_on"
										name = "<?php echo $this->plugin_name; ?>[use_excerpts]" <?php echo( '1' === $dc->get_use_excerpts() ? 'checked' : '' ); ?>>
								<label for="use_excerpts_on">On</label>
								<input type = "radio"
										value = "0"
										id = "use_excerpts_off"
										name = "<?php echo $this->plugin_name; ?>[use_excerpts]" <?php echo( ( '0' === $dc->get_use_excerpts() || empty( $dc->get_use_excerpts() ) ) ? 'checked' : '' ); ?>>
								<label for="use_excerpts_off">Off (Default)</label>
								<br><em>( Use existing excerpts or generate our own (safest) )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_delimiter">Article delimiter :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_delimiter"
										name = "<?php echo $this->plugin_name; ?>[article_delimiter]"
										value = "<?php echo( '' === $options['article_delimiter'] ? $dc->get_article_delimiter() : $options['article_delimiter'] ); ?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_continue">Article Continue Prompt :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_continue"
										name = "<?php echo $this->plugin_name; ?>[article_continue]"
										value = "<?php echo htmlspecialchars( '' === $options['article_continue'] ? $dc->get_article_continue() : $options['article_continue'], ENT_COMPAT | ENT_HTML401, 'UTF-8', false ); ?>"/>
							</td>
						</tr>
						<tr><td colspan="2"><h3>Debugging</h3></td></tr>
						<tr>
							<td>
								Debugging :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "debug_on"
										name = "<?php echo $this->plugin_name; ?>[debug]" <?php echo( '1' === $options['debug'] ? 'checked' : '' ); ?>> <label for="debug_on">On</label>
								<input type = "radio"
										value = "0"
										id = "debug_off"
										name = "<?php echo $this->plugin_name; ?>[debug]" <?php echo( ( '0' === $options['debug'] || empty( $options['debug'] ) ) ? 'checked' : '' ); ?>> <label for="debug_off">Off (Default)</label>
							</td>
						</tr>
						<tr><td colspan="2"><h3>Table of Contents</h3></td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-toc_header">Table of Contents Header :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-toc_header"
										name = "<?php echo $this->plugin_name; ?>[toc_header]"
										value = "<?php echo htmlspecialchars( ( '' === $options['toc_header'] ? $dc->get_toc_header() : $options['toc_header'] ) ); ?>"/>
							</td>
						</tr>
						<tr>
							<td>
								Include Table of Contents :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "toc_on"
										name = "<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( '1' === $dc->get_include_toc() ? 'checked' : '' ); ?>>
								<label for="toc_on">On (Default)</label>
								<input type = "radio"
										value = "0"
										id = "toc_off"
										name = "<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( ( '0' === $dc->get_include_toc() || empty( $dc->get_include_toc() ) ) ? 'checked' : '' ); ?>>
								<label for="toc_off">Off</label>
							</td>
						</tr>
						<tr>
							<td>
								Local HREFs in TOC :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "localhrefs_on"
										name = "<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( '1' === $dc->get_include_toc_local_hrefs() ? 'checked' : '' ); ?>>
								<label for="localhrefs_on">On (Default)</label>
								<input type = "radio"
										value = "0"
										id = "localhrefs_off"
										name = "<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( ( '0' === $dc->get_include_toc_local_hrefs() || empty( $dc->get_include_toc_local_hrefs() ) ) ? 'checked' : '' ); ?>>
								<label for="localhrefs_off">Off</label>
							</td>
						</tr>
						<tr><td colspan="2"><h3>Statistics</h3><em>These section headers are used if you do not specify any of the replacement tags in the header or footer texts.</em></p></td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_stats_txt">Number of articles in Brief :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_stats_txt"
										name = "<?php echo $this->plugin_name; ?>[article_stats_txt]"
										value = "<?php echo( '' === $options['article_stats_txt'] ? $dc->get_article_stats_txt() : $options['article_stats_txt'] ); ?>"/>
								<br/><em>( Replaced by {article_count} )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_stats_cats_txt">Categories in Brief :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_stats_cats_txt"
										name = "<?php echo $this->plugin_name; ?>[article_stats_cats_txt]"
										value = "<?php echo( '' === $options['article_stats_cats_txt'] ? $dc->get_article_stats_cats_txt() : $options['article_stats_cats_txt'] ); ?>"/>
								<br/><em>( Replaced by {article_categories} )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_stats_tags_txt">Tags in Brief :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_stats_tags_txt"
										name = "<?php echo $this->plugin_name; ?>[article_stats_tags_txt]"
										value = "<?php echo( '' === $options['article_stats_tags_txt'] ? $dc->get_article_stats_tags_txt() : $options['article_stats_tags_txt'] ); ?>"/>
								<br/><em>( Replaced by {article_tags} )</em>
							</td>
						</tr>
						<tr><td colspan="2"><h3>Header & Footer</h3></td></tr>
						<tr>
							<td colspan="2">
								<label for="headereditor"><strong>Header text :</strong></label><br>
								<?php
								$settings = array(
									'textarea_rows' => 5,
									'textarea_name' => $this->plugin_name . '[header]',
								);
								wp_editor( wpautop( '' === $options['header'] ? $dc->get_header() : $options['header'], true ), 'headereditor', $settings );
								?>
								<br> The replacement tags {article_count}, {article_categories} and {article_tags} will be replaced by the count, categories and tags respectively covered by the articles included in the daily briefs.
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label for="footereditor"><strong>Footer text :</strong><br></label>
								<?php
								$settings = array(
									'textarea_rows' => 5,
									'textarea_name' => $this->plugin_name . '[footer]',
								);
								wp_editor( wpautop( '' === $options['footer'] ? $dc->get_footer() : $options['footer'], true ), 'footereditor', $settings );
								?>
								<br> The replacement tags {article_count}, {article_categories} and {article_tags} will be replaced by the count, categories and tags respectively covered by the articles included in the daily briefs.
							</td>
						</tr>
						<tr><td colspan="2" style="white-space: normal;"><h4>Times</h4>
								The below settings currently means that if you generated the brief now
								( <?php echo date( 'Y-m-d H:i:s' ); ?> ) it would collect articles
								between; <?php echo date( 'Y-m-d H:i:s', strtotime( $options['end_date'] ) ); ?>
								and <?php echo date( 'Y-m-d H:i:s', strtotime( $options['start_date'] ) ); ?>.</td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-period">Period :</label>
							</td>
							<td>
								<select id = "<?php echo $this->plugin_name; ?>-period"
										name = "<?php echo $this->plugin_name; ?>[period]">
									<option value = "day" <?php echo( 'day' === $options['period'] ? 'SELECTED' : '' ); ?>>Single day</option>
									<option value = "range" <?php echo( 'range' === $options['period'] ? 'SELECTED' : '' ); ?>>Range of days</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-start_date">Start Date :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-start_date"
										name = "<?php echo $this->plugin_name; ?>[start_date]"
										value = "<?php echo( '' === $options['start_date'] ? $dc->get_end_date() : $options['start_date'] ); ?>"/>
								<br/><em>( Include articles after this date )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-end_date">End Date :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-end_date"
										name = "<?php echo $this->plugin_name; ?>[end_date]"
										value = "<?php echo( '' === $options['end_date'] ? $dc->get_start_date() : $options['end_date'] ); ?>"/>
								<br/><em>( Include articles before this date )</em>
							</td>
						</tr>
						<tr>
							<td>

							</td>
							<td>

							</td>
						</tr>
						<tr>
							<td>

							</td>
							<td>

							</td>
						</tr>
					</table>
				</fieldset>
				<?php


				submit_button( 'Save all changes', 'primary', 'submit', true );
				?>
			</form>
		<?php } // end if display_options ?>

	</div>
	<aside class = "dailybrief-sidebar">
		<?php
		$cron_run  = wp_next_scheduled( 'dailybrief_daily_event' );
		$timezone  = WpDateTimeZone::getWpTimezone();
		$date      = new DateTime( ( $cron_run > 0 ? '@' . $cron_run : time() ), $timezone );
		$tz        = $date->getTimezone();
		$tz_name   = $tz->getName();
		$tz_offset = $tz->getOffset( $date );
		$date->setTimestamp( $cron_run + $tz_offset );
		?>
		<h1>DailyBrief v&nbsp;<?php echo $this->version; ?></h1>
		<p>Join us on the discord server : <a href="https://discord.gg/W2KyAbm">https://discord.gg/W2KyAbm</a> and talk to Danny</p>
		<?php
		if ( $options['debug'] ) {
			?>
			<h4>Debuging Information:</h4>
			<?php
			if ( defined( 'DAILYBRIEF_DETECTED_STEEMPRESS' ) && _mb_strlen( $sample['content'] ) < 65280 ) {
				?>
				<h3>Warning:</h3>
				<p>This post will be larger than 65280 characters, and can't be published to steem.</p>
				<?php
			}
			if ( defined( 'DAILYBRIEF_DETECTED_STEEMPRESS' ) ) {
				?>
				<p>SteemPress</p>
				<p>Is installed.</p>
				<?php
			}
			?>
			<p>Internal CRON is:
				<br/><?php echo( wp_get_schedule( 'dailybrief_daily_event' ) ? 'Scheduled to run on ' . get_date_from_gmt( $date->format( 'Y-m-d H:m:s T' ) ) . ' ' . $timezone->getName() : '<strong>Not</strong> scheduled' ); ?>
			</p>
			<p>The current settings currently means that if you generated the brief now
			( <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' ); ?> ) it would collect articles
			between; <?php echo date( 'Y-m-d H:i:s', strtotime( $options['start_date'] ) ); ?>
			and
				<?php
				if ( 'day' !== $options['period'] ) {
					echo date( 'Y-m-d H:i:s', strtotime( $options['end_date'] ) );
				} else {
					echo date( 'Y-m-d 23:59:59', strtotime( $options['start_date'] ) );
				}
				?>
			</p>
			<p>Briefs author: <?php echo $debug_current_author_name; ?></p>
			<p>Posted to category : <?php echo $debug_current_category_name; ?></p>
			<p>Focusing on categories :
				<?php
				if ( is_array( $debug_current_focus_category_name ) ) {
					echo implode( ', ', $debug_current_focus_category_name );
				} else {
					echo 'None';
				}
				?>
			</p>
			<?php
		}
		?>
	</aside>
</div>
