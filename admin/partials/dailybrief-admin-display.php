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
$options  = $dc->get_options(); // get_option( $this->plugin_name ); // .
$date_now = get_date_from_gmt( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' );
$date_one = date( 'Y-m-d H:i:s', strtotime( $options['start_date'] ) );
if ( 'day' !== $options['period'] ) {
	$date_two = date( 'Y-m-d H:i:s', strtotime( $options['end_date'] ) );
} else {
	$date_two = date( 'Y-m-d 23:59:59', strtotime( $options['start_date'] ) );
}
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
		if ( $options['post_category'] == $category->cat_ID ) {
			$debug_current_category_name = $category->name;
		}
		$category_select .= '<option ' . ( $options['post_category'] == $category->cat_ID ? 'SELECTED' : '' ) . ' value="' . $category->cat_ID . '">' . $category->name . '</option>';
	}
}
// The Focus on Categories loop.
if ( ! empty( $categories ) && is_array( $categories ) ) {
	foreach ( $categories as $category ) {
		if ( $options['focus'] == $category->cat_ID ) {
			$debug_current_focus_category_name = $category->name;
		}
		$category_focus_select .= '<option ' . ( $options['focus'] == $category->cat_ID ? 'SELECTED' : '' ) . ' value="' . $category->cat_ID . '">' . $category->name . '</option>';
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
					class = "nav-tab <?php echo 'options' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'Options', 'dailybrief' ); ?></a>
			<a href = "options-general.php?page=dailybrief&tab=preview"
					class = "nav-tab <?php echo 'preview' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'Preview', 'dailybrief' ); ?></a>
			<a href = "options-general.php?page=dailybrief&tab=support"
					class = "nav-tab <?php echo 'support' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'Support', 'dailybrief' ); ?></a>
		</h2>
		<?php
		if ( 'publish' === $active_tab ) {
			$dc_result = $dc->dailybrief_do_daily_event( true );
			if ( ! isset( $dc_result['error'] ) ) {
				echo '<h2>' . _e( 'Your Daily Brief is done!', 'dailybrief' ) . '</h2>';
				echo '<h3>' . _e( 'Published', 'dailybrief' ) . ': ' . $dc_result['post_title'] . '</h3>';
				echo '<a href = "options-general.php?page=dailybrief&tab=preview">' . _e( 'Go back', 'dailybrief' ) . '</a>';
			} else {
				echo '<h3>' . _e( 'An error occurred during manual publish', 'dailybrief' ) . ':</h3><pre>' . print_r( $dc_result['error'], true ) . '</pre>';
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
					<h2><img src = "<?php echo plugin_dir_url( __FILE__ ); ?>/images/white_icon_dailybrief.png" width = "70" height = "70"> <?php _e( 'dailybrief (48) in news • 12 hours ago', 'dailybrief' ); ?>  </h2>
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
					<a <a href = "options-general.php?page=dailybrief&tab=publish"><button class="dailybrief_preview_generate"><?php _e( 'Manually Generate Brief Now!', 'dailybrief' ); ?></button></a>
					<p><?php _e( 'This will create the Brief immediately with the contents in the preview.', 'dailybrief' ); ?></p>
				</div>
			</div>

		<?php } // end if preview ?>
		<?php if ( 'options' === $active_tab ) { ?>
			<form method = "post" class = "settings-form" name = "cleanup_options" action = "options.php">
				<?php settings_fields( $this->plugin_name ); ?>
				<fieldset>
					<table>
						<tr><td colspan="2"><h3><?php _e( 'CRON Control', 'dailybrief' ); ?></h3></td></tr>
						<tr><td><?php _e( 'CRON Publish', 'dailybrief' ); ?></td><td><input type = "radio"
									value = "1"
										id = "cron_publish_on"
									name = "<?php echo $this->plugin_name; ?>[cron_publish]" <?php echo( '1' === $options['cron_publish'] ? 'checked' : '' ); ?> /><label for="cron_publish_on"><?php _e( 'On (Default)', 'dailybrief' ); ?></label> <input type = "radio"
									value = "0"
										id = "cron_publish_off"
									name = "<?php echo $this->plugin_name; ?>[cron_publish]" <?php echo( ( '0' === $options['cron_publish'] || empty( $options['cron_publish'] ) ) ? 'checked' : '' ); ?> /><label for="cron_publish_off"><?php _e( 'Off', 'dailybrief' ); ?></label>
								<br><em><?php _e( '( If on; create the post but do not publish it )', 'dailybrief' ); ?></em> </td>
						</tr>
						<tr><td><?php _e( 'CRON Pause', 'dailybrief' ); ?></td><td><input type = "radio"
										value = "1"
										id = "cron_pause_on"
										name = "<?php echo $this->plugin_name; ?>[cron_pause]" <?php echo( '1' === $options['cron_pause'] ? 'checked' : '' ); ?> /><label for="cron_pause_on"><?php _e( 'On', 'dailybrief' ); ?></label> <input type = "radio"
										value = "0"
										id = "cron_pause_off"
										name = "<?php echo $this->plugin_name; ?>[cron_pause]" <?php echo( ( '0' === $options['cron_pause'] || empty( $options['cron_pause'] ) ) ? 'checked' : '' ); ?> /><label for="cron_pause_off"><?php _e( 'Off (Default)', 'dailybrief' ); ?></label>
								<br><em><?php _e( '( Will disable post creation by internal CRON )', 'dailybrief' ); ?></em> </td>
						</tr>
						<tr><td colspan="2"><h3><?php _e( 'Who, what & where?', 'dailybrief' ); ?></h3></td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-author_id"><?php _e( 'User ID to Post as', 'dailybrief' ); ?> :</label>
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
								<label for="<?php echo $this->plugin_name; ?>-post_category"><?php _e( 'Category to Post to', 'dailybrief' ); ?> :</label>
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
								<label for="<?php echo $this->plugin_name; ?>-focus"><?php _e( 'Focus on a single Category', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<select id = "<?php echo $this->plugin_name; ?>-focus"
										name = "<?php echo $this->plugin_name; ?>[focus]">
									<option value = "-1"><?php _e( 'No Focus Category (Default)', 'dailybrief' ); ?></option>
									<?php echo $category_focus_select; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-post_title"><?php _e( 'Post Title', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-post_title"
										name = "<?php echo $this->plugin_name; ?>[post_title]"
										value = "<?php echo htmlspecialchars( ( '' === $options['post_title'] ? $dc->get_post_title() : $options['post_title'] ), ENT_QUOTES ); ?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-slug"><?php _e( 'Post Slug', 'dailybrief' ); ?> :</label>
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
								<label for="<?php echo $this->plugin_name; ?>-post_tags"><?php _e( 'Post Tags', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-post_tags"
										name = "<?php echo $this->plugin_name; ?>[post_tags]"
										value = "<?php echo htmlspecialchars( ( '' === $options['post_tags'] ? $dc->get_post_tags() : $options['post_tags'] ), ENT_QUOTES ); ?>"/>
								<br><em>( <?php _e( 'Max 5', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-url_suffix"><?php _e( 'Url Suffix', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-url_suffix"
										name = "<?php echo $this->plugin_name; ?>[url_suffix]"
										value = "<?php echo( '' === $options['url_suffix'] ? $dc->get_url_suffix() : $options['url_suffix'] ); ?>"/>
								<br><em>( <?php _e( 'Append to outbound links in your post', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-excerpt_words"><?php _e( 'Excerpt Words', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "number" class = "regular-text" maxlength = "4"
										id = "<?php echo $this->plugin_name; ?>-excerpt_words"
										name = "<?php echo $this->plugin_name; ?>[excerpt_words]"
										value = "<?php echo htmlspecialchars( ( '' === $options['excerpt_words'] ? $dc->get_excerpt_words() : $options['excerpt_words'] ), ENT_QUOTES ); ?>"/>
								<br><em>( <?php _e( 'How many words to include', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e( 'Use Excerpts', 'dailybrief' ); ?> :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "use_excerpts_on"
										name = "<?php echo $this->plugin_name; ?>[use_excerpts]" <?php echo( '1' === $dc->get_use_excerpts() ? 'checked' : '' ); ?>>
								<label for="use_excerpts_on"><?php _e( 'On', 'dailybrief' ); ?></label>
								<input type = "radio"
										value = "0"
										id = "use_excerpts_off"
										name = "<?php echo $this->plugin_name; ?>[use_excerpts]" <?php echo( ( '0' === $dc->get_use_excerpts() || empty( $dc->get_use_excerpts() ) ) ? 'checked' : '' ); ?>>
								<label for="use_excerpts_off"><?php _e( 'Off (Default)', 'dailybrief' ); ?></label>
								<br><em>( <?php _e( 'Use existing excerpts or generate our own (safest)', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_delimiter"><?php _e( 'Article delimiter', 'dailybrief' ); ?> :</label>
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
								<label for="<?php echo $this->plugin_name; ?>-article_continue"><?php _e( 'Article Continue Prompt', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_continue"
										name = "<?php echo $this->plugin_name; ?>[article_continue]"
										value = "<?php echo htmlspecialchars( '' === $options['article_continue'] ? $dc->get_article_continue() : $options['article_continue'], ENT_COMPAT | ENT_HTML401, 'UTF-8', false ); ?>"/>
							</td>
						</tr>
						<tr><td colspan="2"><h3><?php _e( 'Debugging', 'dailybrief' ); ?></h3></td></tr>
						<tr>
							<td>
								<?php _e( 'Debugging', 'dailybrief' ); ?> :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "debug_on"
										name = "<?php echo $this->plugin_name; ?>[debug]" <?php echo( '1' === $options['debug'] ? 'checked' : '' ); ?>> <label for="debug_on"><?php _e( 'On', 'dailybrief' ); ?></label>
								<input type = "radio"
										value = "0"
										id = "debug_off"
										name = "<?php echo $this->plugin_name; ?>[debug]" <?php echo( ( '0' === $options['debug'] || empty( $options['debug'] ) ) ? 'checked' : '' ); ?>> <label for="debug_off"><?php _e( 'Off (Default)', 'dailybrief' ); ?></label>
							</td>
						</tr>
						<tr><td colspan="2"><h3><?php _e( 'Table of Contents', 'dailybrief' ); ?></h3></td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-toc_header"><?php _e( 'Table of Contents Header', 'dailybrief' ); ?> :</label>
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
								<?php _e( 'Include Table of Contents', 'dailybrief' ); ?> :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "toc_on"
										name = "<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( '1' === $dc->get_include_toc() ? 'checked' : '' ); ?>>
								<label for="toc_on"><?php _e( 'On (Default)', 'dailybrief' ); ?></label>
								<input type = "radio"
										value = "0"
										id = "toc_off"
										name = "<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( ( '0' === $dc->get_include_toc() || empty( $dc->get_include_toc() ) ) ? 'checked' : '' ); ?>>
								<label for="toc_off"><?php _e( 'Off', 'dailybrief' ); ?></label>
							</td>
						</tr>
						<tr>
							<td>
								<?php _e( 'Local HREFs in TOC', 'dailybrief' ); ?> :
							</td>
							<td>
								<input type = "radio"
										value = "1"
										id = "localhrefs_on"
										name = "<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( '1' === $dc->get_include_toc_local_hrefs() ? 'checked' : '' ); ?>>
								<label for="localhrefs_on"><?php _e( 'On (Default)', 'dailybrief' ); ?></label>
								<input type = "radio"
										value = "0"
										id = "localhrefs_off"
										name = "<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( ( '0' === $dc->get_include_toc_local_hrefs() || empty( $dc->get_include_toc_local_hrefs() ) ) ? 'checked' : '' ); ?>>
								<label for="localhrefs_off"><?php _e( 'Off', 'dailybrief' ); ?></label>
							</td>
						</tr>
						<tr><td colspan="2"><h3><?php _e( 'Statistics', 'dailybrief' ); ?></h3><em><?php _e( 'These section headers are used if you do not specify any of the replacement tags in the header or footer texts.', 'dailybrief' ); ?></em></p></td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_stats_txt"><?php _e( 'Number of articles in Brief :', 'dailybrief' ); ?></label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_stats_txt"
										name = "<?php echo $this->plugin_name; ?>[article_stats_txt]"
										value = "<?php echo( '' === $options['article_stats_txt'] ? $dc->get_article_stats_txt() : $options['article_stats_txt'] ); ?>"/>
								<br/><em>( <?php _e( 'Replaced by {article_count}', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_stats_cats_txt"><?php _e( 'Categories in Brief', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_stats_cats_txt"
										name = "<?php echo $this->plugin_name; ?>[article_stats_cats_txt]"
										value = "<?php echo( '' === $options['article_stats_cats_txt'] ? $dc->get_article_stats_cats_txt() : $options['article_stats_cats_txt'] ); ?>"/>
								<br/><em>( <?php _e( 'Replaced by {article_categories}', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-article_stats_tags_txt"><?php _e( 'Tags in Brief', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-article_stats_tags_txt"
										name = "<?php echo $this->plugin_name; ?>[article_stats_tags_txt]"
										value = "<?php echo( '' === $options['article_stats_tags_txt'] ? $dc->get_article_stats_tags_txt() : $options['article_stats_tags_txt'] ); ?>"/>
								<br/><em>( <?php _e( 'Replaced by {article_tags}', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr><td colspan="2"><h3><?php _e( 'Header & Footer', 'dailybrief' ); ?></h3></td></tr>
						<tr>
							<td colspan="2">
								<label for="headereditor"><strong><?php _e( 'Header text', 'dailybrief' ); ?> :</strong></label><br>
								<?php
								$settings = array(
									'textarea_rows' => 5,
									'textarea_name' => $this->plugin_name . '[header]',
								);
								wp_editor( wpautop( '' === $options['header'] ? $dc->get_header() : $options['header'], true ), 'headereditor', $settings );
								?>
								<br><?php _e( 'The replacement tags {article_count}, {article_categories} and {article_tags} will be replaced by the count, categories and tags respectively covered by the articles included in the daily briefs.', 'dailybrief' ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label for="footereditor"><strong><?php _e( 'Footer text', 'dailybrief' ); ?> :</strong><br></label>
								<?php
								$settings = array(
									'textarea_rows' => 5,
									'textarea_name' => $this->plugin_name . '[footer]',
								);
								wp_editor( wpautop( '' === $options['footer'] ? $dc->get_footer() : $options['footer'], true ), 'footereditor', $settings );
								?>
								<br><?php _e( 'The replacement tags {article_count}, {article_categories} and {article_tags} will be replaced by the count, categories and tags respectively covered by the articles included in the daily briefs.', 'dailybrief' ); ?>
							</td>
						</tr>
						<tr><td colspan="2" style="white-space: normal;"><h4><?php _e( 'Times', 'dailybrief' ); ?></h4>
								<?php
								/* translators: The three strings are dates, where the last two specifies a range. */
								printf( __( 'The below settings currently means that if you generated the brief now ( %1$s ) it would collect articles between; %2$s and %3$s.', 'dailybrief' ), $date_now, $date_one, $date_two );
								?>
							</td></tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-period"><?php _e( 'Period', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<select id = "<?php echo $this->plugin_name; ?>-period"
										name = "<?php echo $this->plugin_name; ?>[period]">
									<option value = "day" <?php echo( 'day' === $options['period'] ? 'SELECTED' : '' ); ?>><?php _e( 'Single day', 'dailybrief' ); ?></option>
									<option value = "range" <?php echo( 'range' === $options['period'] ? 'SELECTED' : '' ); ?>><?php _e( 'Range of days', 'dailybrief' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-start_date"><?php _e( 'Start Date', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-start_date"
										name = "<?php echo $this->plugin_name; ?>[start_date]"
										value = "<?php echo( '' === $options['start_date'] ? $dc->get_end_date() : $options['start_date'] ); ?>"/>
								<br/><em>( <?php _e( 'Include articles after this date', 'dailybrief' ); ?> )</em>
							</td>
						</tr>
						<tr>
							<td>
								<label for="<?php echo $this->plugin_name; ?>-end_date"><?php _e( 'End Date', 'dailybrief' ); ?> :</label>
							</td>
							<td>
								<input type = "text" class = "regular-text" maxlength = "50"
										id = "<?php echo $this->plugin_name; ?>-end_date"
										name = "<?php echo $this->plugin_name; ?>[end_date]"
										value = "<?php echo( '' === $options['end_date'] ? $dc->get_start_date() : $options['end_date'] ); ?>"/>
								<br/><em>( <?php _e( 'Include articles before this date', 'dailybrief' ); ?> )</em>
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
		<p><?php _e( 'Join us on the discord server', 'dailybrief' ); ?> : <a href="https://discord.gg/W2KyAbm">https://discord.gg/W2KyAbm</a> <?php _e( 'and talk to', 'dailybrief' ); ?> Danny</p>
		<?php
		if ( $options['debug'] ) {
			?>
			<h4><?php _e( 'Debuging Information', 'dailybrief' ); ?>:</h4>
			<p><?php _e( 'Internal CRON is', 'dailybrief' ); ?>:
				<br/><?php echo( wp_get_schedule( 'dailybrief_daily_event' ) ? 'Scheduled to run on ' . get_date_from_gmt( $date->format( 'Y-m-d H:m:s T' ) ) . ' ' . $timezone->getName() : '<strong>Not</strong> scheduled' ); ?>
			</p>
			<p>
			<?php
			/* translators: The three strings are dates, where the last two specifies a range. */
			printf( __( 'The below settings currently means that if you generated the brief now ( %1$s ) it would collect articles between; %2$s and %3$s.', 'dailybrief' ), $date_now, $date_one, $date_two );
			?>
			</p>
			<p><?php _e( 'Brief author', 'dailybrief' ); ?> : <?php echo $debug_current_author_name; ?></p>
			<p><?php _e( 'Posted to category', 'dailybrief' ); ?> : <?php echo $debug_current_category_name; ?></p>
			<p><?php _e( 'Focusing on category', 'dailybrief' ); ?> : <?php echo( $debug_current_focus_category_name ?: 'None' ); ?></p>
			<?php
		}
		?>
	</aside>
</div>
