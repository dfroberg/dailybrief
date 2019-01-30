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
$options = get_option( $this->plugin_name );
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
		<a href="options-general.php?page=dailybrief&tab=options"
		   class="nav-tab <?php echo 'options' === $active_tab ? 'nav-tab-active' : ''; ?>">Options</a>
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
		// Sample data.
		$sample_posts = array(
			1 => array(
				'title'      => 'Nunc viverra tellus sed orci semper',
				'date'       => '2019-01-29 09:47:27',
				'author'     => 'Mr. A Guest',
				'categories' => array( 'Blog, Life, News' ),
				'body'       => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sed fringilla odio. Quisque ultrices justo sit amet lorem volutpat lacinia. Pellentesque tristique tellus turpis, sed varius tortor aliquam eu. Proin eget lacinia odio, in faucibus risus. Nullam est urna, mollis quis lorem id, ornare sollicitudin turpis. Pellentesque sit amet tempus diam, in volutpat quam. Curabitur tristique gravida nulla nec pulvinar. Fusce semper nisl in augue rutrum porta. Fusce aliquam imperdiet erat, laoreet viverra metus volutpat vel. Nam convallis id ligula nec tempor. Morbi ultrices a massa vitae sodales. Etiam eu risus non elit aliquet suscipit. Pellentesque efficitur pretium est ac varius. Aliquam vitae est id lacus malesuada facilisis. Sed eget lacus malesuada, ornare ante quis, consequat dolor. Mauris cursus accumsan ultricies. 

Aenean fringilla tempus sem et dapibus. Nunc viverra tellus sed orci semper, a mattis enim auctor. Mauris ut nibh imperdiet, dictum erat vitae, faucibus purus. Suspendisse in dictum augue. Praesent ut ullamcorper urna. Cras semper auctor pulvinar. Donec urna lectus, euismod in tincidunt nec, sagittis a massa. Donec ut facilisis elit. Vestibulum ac nibh eget lectus elementum varius ac sit amet felis. Nunc nibh magna, luctus eget faucibus vitae, cursus ac dui. Mauris in imperdiet justo, id pretium mi. Nullam ac sodales ipsum. Cras quis lectus eu arcu viverra pulvinar.

Nullam maximus, urna eget lacinia auctor, odio metus fermentum augue, et ullamcorper arcu quam a arcu. Etiam ornare est non neque scelerisque pretium. Integer massa arcu, luctus ac sapien vel, tincidunt luctus nibh. Maecenas volutpat placerat turpis, in laoreet felis convallis nec. Vivamus facilisis arcu leo, ut tristique arcu tempor in. Mauris facilisis tortor id purus varius facilisis. Morbi ut libero suscipit, sodales nisi pellentesque, feugiat elit. Fusce ullamcorper, nisl quis pretium porttitor, urna mauris eleifend tellus, et fermentum purus nisl eu sem. Phasellus convallis, massa eget ultrices sollicitudin, elit risus vulputate tortor, vitae semper odio lacus quis metus. Morbi nec nunc odio. Maecenas porta aliquam tellus, quis gravida metus dictum sodales.
</p>',
			),
			2 => array(
				'title'      => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				'date'       => '2019-01-29 09:47:27',
				'author'     => 'Mrs. M Rose',
				'categories' => array( 'Photography, Life' ),
				'body'       => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sed fringilla odio. Quisque ultrices justo sit amet lorem volutpat lacinia. Pellentesque tristique tellus turpis, sed varius tortor aliquam eu. Proin eget lacinia odio, in faucibus risus. Nullam est urna, mollis quis lorem id, ornare sollicitudin turpis. Pellentesque sit amet tempus diam, in volutpat quam. Curabitur tristique gravida nulla nec pulvinar. Fusce semper nisl in augue rutrum porta. Fusce aliquam imperdiet erat, laoreet viverra metus volutpat vel. Nam convallis id ligula nec tempor. Morbi ultrices a massa vitae sodales. Etiam eu risus non elit aliquet suscipit. Pellentesque efficitur pretium est ac varius. Aliquam vitae est id lacus malesuada facilisis. Sed eget lacus malesuada, ornare ante quis, consequat dolor. Mauris cursus accumsan ultricies. 
    
    Aenean fringilla tempus sem et dapibus. Nunc viverra tellus sed orci semper, a mattis enim auctor. Mauris ut nibh imperdiet, dictum erat vitae, faucibus purus. Suspendisse in dictum augue. Praesent ut ullamcorper urna. Cras semper auctor pulvinar. Donec urna lectus, euismod in tincidunt nec, sagittis a massa. Donec ut facilisis elit. Vestibulum ac nibh eget lectus elementum varius ac sit amet felis. Nunc nibh magna, luctus eget faucibus vitae, cursus ac dui. Mauris in imperdiet justo, id pretium mi. Nullam ac sodales ipsum. Cras quis lectus eu arcu viverra pulvinar.
    
    Nullam maximus, urna eget lacinia auctor, odio metus fermentum augue, et ullamcorper arcu quam a arcu. Etiam ornare est non neque scelerisque pretium. Integer massa arcu, luctus ac sapien vel, tincidunt luctus nibh. Maecenas volutpat placerat turpis, in laoreet felis convallis nec. Vivamus facilisis arcu leo, ut tristique arcu tempor in. Mauris facilisis tortor id purus varius facilisis. Morbi ut libero suscipit, sodales nisi pellentesque, feugiat elit. Fusce ullamcorper, nisl quis pretium porttitor, urna mauris eleifend tellus, et fermentum purus nisl eu sem. Phasellus convallis, massa eget ultrices sollicitudin, elit risus vulputate tortor, vitae semper odio lacus quis metus. Morbi nec nunc odio. Maecenas porta aliquam tellus, quis gravida metus dictum sodales.
    </p>',
			),
			3 => array(
				'title'      => 'Duis sed fringilla odio. Quisque ultrices justo sit amet lorem volutpat lacinia.',
				'date'       => '2019-01-29 09:47:27',
				'author'     => 'Mr. B Logger',
				'categories' => array( 'Blog, Life, Travel' ),
				'body'       => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sed fringilla odio. Quisque ultrices justo sit amet lorem volutpat lacinia. Pellentesque tristique tellus turpis, sed varius tortor aliquam eu. Proin eget lacinia odio, in faucibus risus. Nullam est urna, mollis quis lorem id, ornare sollicitudin turpis. Pellentesque sit amet tempus diam, in volutpat quam. Curabitur tristique gravida nulla nec pulvinar. Fusce semper nisl in augue rutrum porta. Fusce aliquam imperdiet erat, laoreet viverra metus volutpat vel. Nam convallis id ligula nec tempor. Morbi ultrices a massa vitae sodales. Etiam eu risus non elit aliquet suscipit. Pellentesque efficitur pretium est ac varius. Aliquam vitae est id lacus malesuada facilisis. Sed eget lacus malesuada, ornare ante quis, consequat dolor. Mauris cursus accumsan ultricies. 
        
        Aenean fringilla tempus sem et dapibus. Nunc viverra tellus sed orci semper, a mattis enim auctor. Mauris ut nibh imperdiet, dictum erat vitae, faucibus purus. Suspendisse in dictum augue. Praesent ut ullamcorper urna. Cras semper auctor pulvinar. Donec urna lectus, euismod in tincidunt nec, sagittis a massa. Donec ut facilisis elit. Vestibulum ac nibh eget lectus elementum varius ac sit amet felis. Nunc nibh magna, luctus eget faucibus vitae, cursus ac dui. Mauris in imperdiet justo, id pretium mi. Nullam ac sodales ipsum. Cras quis lectus eu arcu viverra pulvinar.
        
        Nullam maximus, urna eget lacinia auctor, odio metus fermentum augue, et ullamcorper arcu quam a arcu. Etiam ornare est non neque scelerisque pretium. Integer massa arcu, luctus ac sapien vel, tincidunt luctus nibh. Maecenas volutpat placerat turpis, in laoreet felis convallis nec. Vivamus facilisis arcu leo, ut tristique arcu tempor in. Mauris facilisis tortor id purus varius facilisis. Morbi ut libero suscipit, sodales nisi pellentesque, feugiat elit. Fusce ullamcorper, nisl quis pretium porttitor, urna mauris eleifend tellus, et fermentum purus nisl eu sem. Phasellus convallis, massa eget ultrices sollicitudin, elit risus vulputate tortor, vitae semper odio lacus quis metus. Morbi nec nunc odio. Maecenas porta aliquam tellus, quis gravida metus dictum sodales.
        </p>',
			),
		)
		?>
		<div style="float: right; margin-right: 30%"><h2>Preview </h2>
			<h1><?php echo $options['post_title']; ?></h1>
			<p><?php echo $options['header']; ?></p>
			<?php
			if ( '1' === $options['include_toc'] ) {
				?>
				<h2>Table of Contents:</h2>
				<ul>
					<?php
					foreach ( $sample_posts as $post ) {
						echo '<li>' . $post['title'] . '</li>';
					}
					?>
				</ul>
				<?php echo $options['article_delimiter']; ?>
				<?php
			}
			foreach ( $sample_posts as $post ) {
				?>

				<h2><?php echo $post['title']; ?></h2>
				<p><?php echo 'Published <strong>' . $post['date'] . '</strong> by <strong>' . $post['author'] . '</strong> in <strong>' . implode( ', ', $post['categories'] ) . '</strong>'; ?></p>
				<p><?php echo wp_trim_words( wp_strip_all_tags( $post['body'], true ), $options['excerpt_words'], '... <a href="#">' . $options['article_continue'] . '</a>' ); ?></p>
				<p><?php echo $options['article_delimiter']; ?></p>
			<?php } ?>
			<p><?php echo $options['footer']; ?></p>
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
					value="<?php echo htmlspecialchars( ( '' === $options['post_title'] ? 'The Your Site Daily Brief' : $options['post_title'] ), ENT_QUOTES ); ?>"/>
			<br/></label>
		<label>Post Tags : (Max 5)<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-post_tags"
					name="<?php echo $this->plugin_name; ?>[post_tags]"
					value="<?php echo htmlspecialchars( ( '' === $options['post_tags'] ? 'news-blog,life' : $options['post_tags'] ), ENT_QUOTES ); ?>"/>
			<br/></label>
		<label>Url Suffix (Append to outbound links in your post)<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-url_suffix"
					name="<?php echo $this->plugin_name; ?>[url_suffix]"
					value="<?php echo( '' === $options['url_suffix'] ? '?campaign=steempress&amp;utm=dailybrief' : $options['url_suffix'] ); ?>"/>
			<br/></label>
		<label>Excerpt Words (How many words to include)<br/>
			<input  type="number" class="regular-text" maxlength="4" id="<?php echo $this->plugin_name; ?>-excerpt_words"
					name="<?php echo $this->plugin_name; ?>[excerpt_words]"
					value="<?php echo htmlspecialchars( ( '' === $options['excerpt_words'] ? '100' : $options['excerpt_words'] ), ENT_QUOTES ); ?>"/>
			<br/></label>
		<label>Post Slug :<br/>
			<input  type="text" class="regular-text" maxlength="50" id="<?php echo $this->plugin_name; ?>-slug"
					name="<?php echo $this->plugin_name; ?>[slug]"
					value="<?php echo htmlspecialchars( ( '' === $options['slug'] ? 'the-daily-brief' : $options['slug'] ), ENT_QUOTES ); ?>"/>
			<br/> </label>
		<label>Article delimiter :<br/>
			<input  type="text" class="regular-text" maxlength="50"
					id="<?php echo $this->plugin_name; ?>-article_delimiter"
					name="<?php echo $this->plugin_name; ?>[article_delimiter]"
					value="<?php echo( '' === $options['article_delimiter'] ? '<hr>' : $options['article_delimiter'] ); ?>"/>
			<br/> </label>

		<label>Debugging :
			<label><input type="radio"
			              value="1"
			              name="<?php echo $this->plugin_name; ?>[debug]" <?php echo( '1' === $options['debug'] ? 'checked' : '' ); ?>>
				On</label>
			<label><input type="radio"
			              value="0"
			              name="<?php echo $this->plugin_name; ?>[debug]" <?php echo( ( '0' === $options['debug'] || empty( $options['debug'] ) ) ? 'checked' : '' ); ?>>
				Off</label>
			<br></label>
		<label>Include Table of Contents :
			<label><input type="radio"
			              value="1"
			              name="<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( '1' === $options['include_toc'] ? 'checked' : '' ); ?>>
				On</label>
			<label><input type="radio"
			              value="0"
			              name="<?php echo $this->plugin_name; ?>[include_toc]" <?php echo( ( '0' === $options['include_toc'] || empty( $options['include_toc'] ) ) ? 'checked' : '' ); ?>>
				Off</label>
			<br> </label>
		<label>Local HREFs in TOC :
			<label><input type="radio"
			              value="1"
			              name="<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( '1' === $options['include_toc_local_hrefs'] ? 'checked' : '' ); ?>>
				On</label>
			<label><input type="radio"
			              value="0"
			              name="<?php echo $this->plugin_name; ?>[include_toc_local_hrefs]" <?php echo( ( '0' === $options['include_toc_local_hrefs'] || empty( $options['include_toc_local_hrefs'] ) ) ? 'checked' : '' ); ?>>
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
		wp_editor( ( '' === $options['header'] ? '<p>This daily summary contains <strong>{article_count}</strong> articles about; <em>{article_tags}</em> in the following categories; <em>{article_categories}</em>.</p>' : $options['header'] ), 'headereditor', $settings );
		?>
		<label><strong>Footer text :</strong> <br> the tags {article_count}, {article_categories} and {article_tags}
			will be replaced by the count, categories and tags respectively covered by the articles included in the
			daily briefs. </label>
		<?php
		$settings = array(
			'textarea_rows' => 5,
			'textarea_name' => $this->plugin_name . '[footer]',
		);
		wp_editor( ( '' === $options['footer'] ? '<center><h2>Thank you for following our coverage.</h2></center>' : $options['footer'] ), 'footereditor', $settings );
		?>
		<?php } // end if display_options ?>
		<?php


		submit_button( 'Save all changes', 'primary', 'submit', true );
		?>
	</form>
</div>
