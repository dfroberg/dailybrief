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
	'fields'  => array( 'id', 'user_login', 'user_nicename' ),
);

// The User Query.
$user_query  = new WP_User_Query( $args );
$user_select = '';
// The User Loop.
if ( ! empty( $user_query->results ) ) {
	foreach ( $user_query->results as $user ) {
		$user_select .= '<option ' . ( $options['author_id'] == $user->id ? 'SELECTED' : '' ) . ' value="' . $user->id . '">' . $user->user_nicename . '</option>';
	}
}
// Get the categories.
$categories      = get_categories( array( 'hide_empty' => false ) );
$category_select = '';
// The Categories loop.
if ( ! empty( $categories ) && is_array( $categories ) ) {
	foreach ( $categories as $category ) {
		$category_select .= '<option ' . ( $options['post_category'] == $category->cat_ID ? 'SELECTED' : '' ) . ' value="' . $category->cat_ID . '">' . $category->name . '</option>';
	}
}

// Figure out what tab we're on.
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'preview';

?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<h2 class="nav-tab-wrapper">
		<a href = "options-general.php?page=dailybrief&tab=options"
				class = "nav-tab <?php echo 'options' === $active_tab ? 'nav-tab-active' : ''; ?>">Options</a>
		<a  href="options-general.php?page=dailybrief&tab=preview"
			class="nav-tab <?php echo 'preview' === $active_tab ? 'nav-tab-active' : ''; ?>">Preview</a>
		<a  href="options-general.php?page=dailybrief&tab=support"
			class="nav-tab <?php echo 'support' === $active_tab ? 'nav-tab-active' : ''; ?>">Support</a>
	</h2>
	<?php
	if ( 'support' === $active_tab ) {
		?>
		<p>Join us on the discord server : https://discord.gg/W2KyAbm and talk to Danny</p>
		<p>This is <?php echo $this->plugin_name; ?> version <?php echo $this->version; ?></p>
	<?php } // end if support ?>
	<?php
	if ( 'preview' === $active_tab ) {
		// Generate preview.
		$sample = $dc->create(
			array(
				'preview' => true,
				'period'  => 'range',
				'start'   => date( 'Y-m-d', strtotime( 'yesterday' ) ),
				'end'     => date( 'Y-m-d', strtotime( 'today' ) ),
			)
		)
		?>
		<div id = "dailybrief-preview-post" class = "dailybrief-preview-post">
			<h1><?php echo $sample['post_title'] . ' ' . $dc->get_date_suffix(); ?></h1>
			<p></p>
			<?php
			echo '<center><img src="' . $dc->get_temp_featured_image_url() . '" width="640"></center>';
			?>
			<br>
			<p></p>
			<p>
			<?php
			echo $sample['content'];
			?>
			</p>
		</div>

	<?php } // end if preview ?>
	<?php if ( 'options' === $active_tab ) { ?>

	<form method="post" name="cleanup_options" action="options.php">
		<?php settings_fields( $this->plugin_name ); ?>
		<br/>
		<label>User ID to Post as :<br/>
			<select id="<?php echo $this->plugin_name; ?>-author_id"
					name="<?php echo $this->plugin_name; ?>[author_id]">
				<?php echo $user_select; ?>
			</select></label>
		<br/>
		<label>Category ID to Post to :<br/>
			<select id="<?php echo $this->plugin_name; ?>-post_category"
					name="<?php echo $this->plugin_name; ?>[post_category]">
				<?php echo $category_select; ?>
			</select></label>
		<br/>
		<label>Post Title :<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-post_title"
					name="<?php echo $this->plugin_name; ?>[post_title]"
					value="<?php echo htmlspecialchars( ( '' === $options['post_title'] ? $dc->get_post_title() : $options['post_title'] ), ENT_QUOTES ); ?>"/>
			<br/></label>
		<label>Post Tags : (Max 5)<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-post_tags"
					name="<?php echo $this->plugin_name; ?>[post_tags]"
					value="<?php echo htmlspecialchars( ( '' === $options['post_tags'] ? $dc->get_post_tags() : $options['post_tags'] ), ENT_QUOTES ); ?>"/>
			<br/></label>
		<label>Url Suffix (Append to outbound links in your post)<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-url_suffix"
					name="<?php echo $this->plugin_name; ?>[url_suffix]"
					value="<?php echo( '' === $options['url_suffix'] ? $dc->get_url_suffix() : $options['url_suffix'] ); ?>"/>
			<br/></label>
		<label>Excerpt Words (How many words to include)<br/>
			<input  type="number" class="regular-text" maxlength="4" id="<?php echo $this->plugin_name; ?>-excerpt_words"
					name="<?php echo $this->plugin_name; ?>[excerpt_words]"
					value="<?php echo htmlspecialchars( ( '' === $options['excerpt_words'] ? $dc->get_excerpt_words() : $options['excerpt_words'] ), ENT_QUOTES ); ?>"/>
			<br/></label>
		<label>Post Slug :<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-slug"
					name="<?php echo $this->plugin_name; ?>[slug]"
					value="<?php echo htmlspecialchars( ( '' === $options['slug'] ? $dc->get_slug() : $options['slug'] ), ENT_QUOTES ); ?>"/>
			<br/> </label>
		<label>Article delimiter :<br/>
			<input  type="text" class="regular-text" maxlength="50"
					id="<?php echo $this->plugin_name; ?>-article_delimiter"
					name="<?php echo $this->plugin_name; ?>[article_delimiter]"
					value="<?php echo( '' === $options['article_delimiter'] ? $dc->get_article_delimiter() : $options['article_delimiter'] ); ?>"/>
			<br/> </label>

		<label>Debugging :
			<label><input type = "radio"
						value = "1"
						name = "<?php echo $this->plugin_name; ?>[debug]" <?php echo( '1' === $options['debug'] ? 'checked' : '' ); ?>>
				On</label>
			<label><input type = "radio"
						value = "0"
						name = "<?php echo $this->plugin_name; ?>[debug]" <?php echo( ( '0' === $options['debug'] || empty( $options['debug'] ) ) ? 'checked' : '' ); ?>>
				Off</label>
			<br></label>
		<label>Table of Contents Header :<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-toc_header"
					name="<?php echo $this->plugin_name; ?>[toc_header]"
					value="<?php echo htmlspecialchars( ( '' === $options['toc_header'] ? $dc->get_toc_header() : $options['toc_header'] ) ); ?>"/>
			<br/> </label>
		<label>Include Table of Contents :
			<label><input type = "radio"
						value = "1"
						name = "<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( '1' === $options['include_toc'] ? 'checked' : '' ); ?>>
				On</label>
			<label><input type = "radio"
						value = "0"
						name = "<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( ( '0' === $options['include_toc'] || empty( $options['include_toc'] ) ) ? 'checked' : '' ); ?>>
				Off</label>
			<br> </label>
		<label>Local HREFs in TOC :
			<label><input type = "radio"
						value = "1"
						name = "<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( '1' === $options['include_toc_local_hrefs'] ? 'checked' : '' ); ?>>
				On</label>
			<label><input type = "radio"
						value = "0"
						name = "<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( ( '0' === $options['include_toc_local_hrefs'] || empty( $options['include_toc_local_hrefs'] ) ) ? 'checked' : '' ); ?>>
				Off</label>
			<br></label>
		<label><strong>Header text :</strong> <br> the tags {article_count}, {article_categories} and {article_tags}
			will be replaced by the count, categories and tags respectively covered by the articles included in the
			daily briefs. </label>
		<?php
		$settings = array(
			'textarea_rows' => 5,
			'textarea_name' => $this->plugin_name . '[header]',
		);
		wp_editor( ( '' === $options['header'] ? $dc->get_header() : $options['header'] ), 'headereditor', $settings );
		?>
		<label><strong>Footer text :</strong> <br> the tags {article_count}, {article_categories} and {article_tags}
			will be replaced by the count, categories and tags respectively covered by the articles included in the
			daily briefs. </label>
		<?php
		$settings = array(
			'textarea_rows' => 5,
			'textarea_name' => $this->plugin_name . '[footer]',
		);
		wp_editor( ( '' === $options['footer'] ? $dc->get_footer() : $options['footer'] ), 'footereditor', $settings );
		?>
		<?php


		submit_button( 'Save all changes', 'primary', 'submit', true );
		?>
	</form>
	<?php } // end if display_options ?>
</div>
