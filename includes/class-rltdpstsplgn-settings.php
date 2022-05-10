<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Rltdpstsplgn_Settings_Tabs' ) ) {
	class Rltdpstsplgn_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $rltdpstsplgn_options, $rltdpstsplgn_plugin_info;

			$tabs = array(
				'related-posts'		=> array( 'label' => __( 'Related Posts', 'relevant' ) ),
				'featured-posts'	=> array( 'label' => __( 'Featured Posts', 'relevant' ) ),
				'latest-posts'		=> array( 'label' => __( 'Latest Posts', 'relevant' ) ),
				'popular-posts'		=> array( 'label' => __( 'Popular Posts', 'relevant' ) ),
				'misc'				=> array( 'label' => __( 'Misc', 'relevant' ) ),
				'custom_code'		=> array( 'label' => __( 'Custom Code', 'relevant' ) )
			);

			parent::__construct( array(
				'plugin_basename'	=> $plugin_basename,
				'plugins_info'		=> $rltdpstsplgn_plugin_info,
				'prefix'			=> 'rltdpstsplgn',
				'default_options'	=> rltdpstsplgn_get_options_default(),
				'options'			=> $rltdpstsplgn_options,
				'tabs'				=> $tabs,
				'wp_slug'			=> 'relevant',
				'doc_link'			=> 'https://bestwebsoft.com/documentation/relevant/relevant-user-guide/'
			) );

			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param void
		 * @return array The action results
		 */
		public function save_options() {
			global $wpdb;
			$message = $notice = $error = '';

			/* related-posts */
			$this->options['related_display']          = isset( $_POST['rltdpstsplgn_related_display'] ) ? $_POST['rltdpstsplgn_related_display'] : array();
			$this->options['related_title']            = sanitize_text_field( $_POST['rltdpstsplgn_related_title'] );
			$this->options['related_posts_count']      = empty( $_POST['rltdpstsplgn_related_posts_count'] ) ? 1 : intval( $_POST['rltdpstsplgn_related_posts_count'] );
			$this->options['related_criteria']         = in_array( $_POST['rltdpstsplgn_related_criteria'], array( 'category', 'tags', 'title', 'meta') ) ? $_POST['rltdpstsplgn_related_criteria'] : 'category';
			$this->options['related_no_posts_message'] = sanitize_text_field( $_POST['rltdpstsplgn_related_no_posts_message'] );
			$this->options['related_show_thumbnail']   = ( isset( $_POST['rltdpstsplgn_related_show_thumbnail'] ) ) ? 1 : 0;
			$this->options['related_image_height']     = intval( $_POST['rltdpstsplgn_related_image_size_height'] );
			$this->options['related_image_width']      = intval( $_POST['rltdpstsplgn_related_image_size_width'] );
			$this->options['display_related_posts']	   = in_array( $_POST['rltdpstsplgn_display_related_posts'], array( 'All', '3 days ago', '5 days ago', '7 days ago', '1 month ago', '3 month ago', '6 month ago' ) ) ? $_POST['rltdpstsplgn_display_related_posts'] : 'All';
			$this->options['related_use_category'] = isset( $_POST['rltdpstsplgn_related_use_category'] ) ? 1 : 0;

			$delete = $related_add_for_page = array();
			if ( ! empty( $_POST['rltdpstsplgn_related_add_for_page'] ) && in_array( 'category', $_POST['rltdpstsplgn_related_add_for_page'] ) ) {
				$related_add_for_page[] = 'category';
			} elseif ( in_array( 'category', $this->options['related_add_for_page'] ) ) {
				$delete[] = 'category';
			}
			if ( ! empty( $_POST['rltdpstsplgn_related_add_for_page'] ) && in_array( 'tags', $_POST['rltdpstsplgn_related_add_for_page'] ) ) {
				$related_add_for_page[] = 'tags';
			} elseif ( in_array( 'tags', $this->options['related_add_for_page'] ) ) {
				$delete[] = 'post_tag';
			}
			if ( ! empty( $_POST['rltdpstsplgn_related_add_for_page'] ) && in_array( 'meta', $_POST['rltdpstsplgn_related_add_for_page'] ) ) {
				$related_add_for_page[] = 'meta';
			}
			if ( ! empty( $_POST['rltdpstsplgn_related_add_for_page'] ) && in_array( 'title', $_POST['rltdpstsplgn_related_add_for_page'] ) ) {
				$related_add_for_page[] = 'title';
			}
			$this->options['related_add_for_page'] = $related_add_for_page;

			if ( ! empty( $delete ) ) {
				$taxonomies = implode( ',', $delete );

				$relationships = $wpdb->get_results(
					"SELECT r.object_id FROM $wpdb->terms AS t
					INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
					INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
					INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
					WHERE p.post_type = 'page' AND tt.taxonomy IN ( '" . $taxonomies . "' )
					GROUP BY t.term_id", ARRAY_A
				);
				foreach ( $relationships as $key => $value ) {
					wp_delete_object_term_relationships( $value['object_id'], $delete );
				}
			}

			$this->options['related_excerpt_length'] = intval( $_POST['rltdpstsplgn_related_excerpt_length'] );
			$this->options['related_excerpt_more']   = sanitize_text_field( $_POST['rltdpstsplgn_related_excerpt_more'] );

			if ( empty( $this->options['related_excerpt_more'] ) ) {
				$this->options['related_excerpt_more'] = '...';
			}
			if ( ! empty( $_POST['rltdpstsplgn_related_no_preview_img'] ) && rltdpstsplgn_is_200( $_POST['rltdpstsplgn_related_no_preview_img'] ) && getimagesize( $_POST['rltdpstsplgn_related_no_preview_img'] ) ) {
				$this->options['related_no_preview_img'] = $_POST['rltdpstsplgn_related_no_preview_img'];
			} else {
				$this->options['related_no_preview_img'] = $this->default_options['related_no_preview_img'];
			}

			$related_show_options = array( 'comments', 'date', 'author', 'reading_time', 'thumbnail', 'excerpt' );
			foreach ( $related_show_options as $item ) {
				$this->options["related_show_{$item}"] = isset( $_POST["rltdpstsplgn_related_show_{$item}"] ) ? 1 : 0;
			}

			/* featured-posts */
			$this->options['featured_display']     = isset( $_POST['rltdpstsplgn_featured_display'] ) ? $_POST['rltdpstsplgn_featured_display'] : array();
			$this->options['featured_posts_count'] = empty( $_POST['rltdpstsplgn_featured_posts_count'] ) ? 1 : intval( $_POST['rltdpstsplgn_featured_posts_count'] );
			$this->options['display_featured_posts'] = in_array( $_POST['rltdpstsplgn_display_featured_posts'], array( 'All', '3 days ago', '5 days ago', '7 days ago', '1 month ago', '3 month ago', '6 month ago' ) ) ? $_POST['rltdpstsplgn_display_featured_posts'] : 'All';

			/*Block Width*/
			if ( 0 < $_POST['rltdpstsplgn_featured_block_width'] ) {
				$this->options['featured_block_width_remark'] = in_array( $_POST['rltdpstsplgn_featured_block_unit'], array( 'px', '%' ) ) ? $_POST['rltdpstsplgn_featured_block_unit'] : '%';
				$this->options['featured_block_width'] = ( '%' == $this->options['featured_block_width_remark'] && 100 < $_POST['rltdpstsplgn_featured_block_width'] ) ? '100' : absint( $_POST['rltdpstsplgn_featured_block_width'] );
			} else {
				$error .= __( "Invalid value for 'Block Width'.", 'relevant' ) . '<br />';
			}

			/*Content Block Width*/
			if ( 0 < $_POST['rltdpstsplgn_featured_text_block_width'] ) {
				$this->options['featured_text_block_width_remark'] = in_array( $_POST['rltdpstsplgn_featured_text_block_unit'], array( 'px', '%' ) ) ? $_POST['rltdpstsplgn_featured_text_block_unit'] : '%';
				$this->options['featured_text_block_width'] = ( '%' == $this->options['featured_text_block_width_remark'] && 100 < $_POST['rltdpstsplgn_featured_text_block_width'] ) ? '100' : absint( $_POST['rltdpstsplgn_featured_text_block_width'] );
			} else {
				$error .= __( "Invalid value for 'Content Block Width'.", 'relevant' ) . '<br />';
			}

			$this->options['featured_theme_style']            = ( isset( $_POST['rltdpstsplgn_featured_theme_style'] ) ) ? 1 : 0;
			$this->options['featured_background_color_block'] = sanitize_text_field( $_POST['rltdpstsplgn_featured_background_color_block'] );
			$this->options['featured_background_color_text']  = sanitize_text_field( $_POST['rltdpstsplgn_featured_background_color_text'] );
			$this->options['featured_color_text']             = sanitize_text_field( $_POST['rltdpstsplgn_featured_color_text'] );
			$this->options['featured_color_header']           = sanitize_text_field( $_POST['rltdpstsplgn_featured_color_header'] );
			$this->options['featured_color_link']             = sanitize_text_field( $_POST['rltdpstsplgn_featured_color_link'] );
			$this->options['featured_image_height']           = intval( $_POST['rltdpstsplgn_featured_image_size_height'] );
			$this->options['featured_image_width']            = intval( $_POST['rltdpstsplgn_featured_image_size_width'] );

			$this->options['featured_use_category'] = isset( $_POST['rltdpstsplgn_featured_use_category'] ) ? 1 : 0;
			$this->options['featured_excerpt_length'] = intval( $_POST['rltdpstsplgn_featured_excerpt_length'] );
			$this->options['featured_excerpt_more']   = sanitize_text_field( $_POST['rltdpstsplgn_featured_excerpt_more'] );

			if ( empty( $this->options['featured_excerpt_more'] ) ) {
				$this->options['featured_excerpt_more'] = '...';
			}

			if ( ! empty( $_POST['rltdpstsplgn_featured_no_preview_img'] ) && rltdpstsplgn_is_200( $_POST['rltdpstsplgn_featured_no_preview_img'] ) && getimagesize( $_POST['rltdpstsplgn_featured_no_preview_img'] ) ) {
				$this->options['featured_no_preview_img'] = $_POST['rltdpstsplgn_featured_no_preview_img'];
			} else {
				$this->options['featured_no_preview_img'] = $this->default_options['featured_no_preview_img'];
			}

			$featured_show_options = array( 'comments', 'date', 'author', 'reading_time', 'thumbnail', 'excerpt' );
			foreach ( $featured_show_options as $item ) {
				$this->options["featured_show_{$item}"] = isset( $_POST["rltdpstsplgn_featured_show_{$item}"] ) ? 1 : 0;
			}

			/* Latest posts options */
			$this->options['latest_display']        = isset( $_POST['rltdpstsplgn_latest_display'] ) ? $_POST['rltdpstsplgn_latest_display'] : array();
			$this->options['latest_title']          = sanitize_text_field( $_POST['rltdpstsplgn_latest_title'] );
			$this->options['latest_posts_count']    = empty( $_POST['rltdpstsplgn_latest_posts_count'] ) ? 1 : intval( $_POST['rltdpstsplgn_latest_posts_count'] );
			$this->options['latest_excerpt_length'] = intval( $_POST['rltdpstsplgn_latest_excerpt_length'] );
			$this->options['latest_excerpt_more']   = sanitize_text_field( $_POST['rltdpstsplgn_latest_excerpt_more'] );
			$this->options['latest_image_height']   = intval( $_POST['rltdpstsplgn_latest_image_size_height'] );
			$this->options['latest_image_width']    = intval( $_POST['rltdpstsplgn_latest_image_size_width'] );
			$this->options['latest_use_category'] = isset( $_POST['rltdpstsplgn_latest_use_category'] ) ? 1 : 0;
			if ( empty( $this->options['latest_excerpt_more'] ) ) {
				$this->options['latest_excerpt_more'] = '...';
			}

			$latest_show_options = array( 'comments', 'date', 'author', 'reading_time', 'thumbnail', 'excerpt' );
			foreach ( $latest_show_options as $item ) {
				$this->options["latest_show_{$item}"] = isset( $_POST["rltdpstsplgn_latest_show_{$item}"] ) ? 1 : 0;
			}

			if ( ! empty( $_POST['rltdpstsplgn_latest_no_preview_img'] ) && rltdpstsplgn_is_200( $_POST['rltdpstsplgn_latest_no_preview_img'] ) && getimagesize( $_POST['rltdpstsplgn_latest_no_preview_img'] ) ) {
				$this->options['latest_no_preview_img'] = $_POST['rltdpstsplgn_latest_no_preview_img'];
			} else {
				$this->options['latest_no_preview_img'] = $this->default_options['latest_no_preview_img'];
			}

			/* Popular posts options */
			$this->options['popular_display']         = isset( $_POST['rltdpstsplgn_popular_display'] ) ? $_POST['rltdpstsplgn_popular_display'] : array();
			$this->options['popular_title']           = sanitize_text_field( $_POST['rltdpstsplgn_popular_title'] );
			$this->options['popular_posts_count']     = empty( $_POST['rltdpstsplgn_popular_posts_count'] ) ? 1 : absint( $_POST['rltdpstsplgn_popular_posts_count'] );
			$this->options['popular_min_posts_count'] = absint( $_POST['rltdpstsplgn_popular_min_posts_count'] );
			$this->options['popular_excerpt_length']  = absint( $_POST['rltdpstsplgn_popular_excerpt_length'] );
			$this->options['popular_excerpt_more']    = sanitize_text_field( $_POST['rltdpstsplgn_popular_excerpt_more'] );
			$this->options['popular_image_height']    = intval( $_POST['rltdpstsplgn_popular_image_size_height'] );
			$this->options['popular_image_width']     = intval( $_POST['rltdpstsplgn_popular_image_size_width'] );
			$this->options['display_popular_posts']	  = in_array( $_POST['rltdpstsplgn_display_popular_posts'], array( 'All', '3 days ago', '5 days ago', '7 days ago', '1 month ago', '3 month ago', '6 month ago' ) ) ? $_POST['rltdpstsplgn_display_popular_posts'] : 'All';

			if ( empty( $this->options['popular_excerpt_more'] ) ) {
				$this->options['popular_excerpt_more'] = '...';
			}

			$this->options['popular_use_category'] = isset( $_POST['rltdpstsplgn_popular_use_category'] ) ? 1 : 0;
			$this->options['popular_order_by']     = $_POST['rltdpstsplgn_popular_order_by'];

			$show_options = array( 'views', 'excerpt', 'date', 'author', 'thumbnail', 'comments', 'reading_time' );
			foreach ( $show_options as $item ) {
				$this->options["popular_show_{$item}"] = isset( $_POST["rltdpstsplgn_popular_show_{$item}"] ) ? 1 : 0;
			}

			if ( ! empty( $_POST['rltdpstsplgn_popular_no_preview_img'] ) && rltdpstsplgn_is_200( $_POST['rltdpstsplgn_popular_no_preview_img'] ) && getimagesize( $_POST['rltdpstsplgn_popular_no_preview_img'] ) ) {
				$this->options['popular_no_preview_img'] = $_POST['rltdpstsplgn_popular_no_preview_img'];
			} else {
				$this->options['popular_no_preview_img'] = $this->default_options['popular_no_preview_img'];
			};

			if ( empty( $error ) ) {
				/* Update options in the database */
				update_option( 'rltdpstsplgn_options', $this->options );
				$message = __( "Settings saved.", 'relevant' );
			}
			return compact( 'message', 'notice', 'error' );
		}

		public function tab_related_posts() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Related Posts Settings', 'relevant' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table">
				<tr>
					<th><?php _e( 'Title', 'relevant' ); ?></th>
					<td>
						<input type="text" name="rltdpstsplgn_related_title" maxlength="250" value="<?php echo $this->options['related_title']; ?>" />
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Number of Posts', 'relevant' ); ?></th>
					<td>
						<input type="number" name="rltdpstsplgn_related_posts_count" min="1" max="10000" step="1" value="<?php echo $this->options['related_posts_count']; ?>" />
						<div class="bws_info"><?php _e( 'Number of posts displayed in Related Posts block.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Date Range', 'relevant' ); ?></th>
					<td>
						<select name="rltdpstsplgn_display_related_posts" >
							<option value="All" id="selectedMonth" <?php selected( 'All' == $this->options["display_related_posts"] ); ?>><?php _e( 'All', 'relevant' ); ?></option>
							<option value="3 days ago" id="selectedMonth" <?php selected( '3 days ago' == $this->options["display_related_posts"] ); ?>>3 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="5 days ago" id="selectedMonth" <?php selected( '5 days ago' == $this->options["display_related_posts"] ); ?>>5 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="7 days ago" id="selectedMonth" <?php selected( '7 days ago' == $this->options["display_related_posts"] ); ?>>7 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="1 month ago" id="selectedMonth" <?php selected( '1 month ago' == $this->options["display_related_posts"] ); ?>>1 <?php _e( 'month', 'relevant' ); ?></option>
							<option value="3 month ago" id="selectedMonth" <?php selected( '3 month ago' == $this->options["display_related_posts"] ); ?>>3 <?php _e( 'months', 'relevant' ); ?></option>
							<option value="6 month ago" id="selectedMonth" <?php selected( '6 month ago' == $this->options["display_related_posts"] ); ?>>6 <?php _e( 'months', 'relevant' ); ?></option>
						</select>
						<div class="bws_info"><?php _e( 'Show only posts not older than the indicated time period.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Display', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<?php $related_show_options = array(
									'thumbnail'		=> __( 'Featured image', 'relevant' ),
									'excerpt'		=> __( 'Excerpt', 'relevant' ),
									'date'			=> __( 'Post date', 'relevant' ),
									'author'		=> __( 'Author', 'relevant' ),
									'reading_time'	=> __( 'Reading time', 'relevant' ),
									'comments'		=> __( 'Comments number', 'relevant' )
								);
								foreach ( $related_show_options as $item => $label ) { ?>
									<label>
										<input name="rltdpstsplgn_related_show_<?php echo $item; ?>" type="checkbox" value="1" <?php checked( 1, $this->options["related_show_{$item}"] ); ?> /> <?php echo $label; ?>
									</label><br />
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Placeholder URL', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_related_no_preview_img" type="text" maxlength="250" value="<?php echo $this->options['related_no_preview_img']; ?>"/>
						<div class="bws_info"><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Size', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input class="small-text" name="rltdpstsplgn_related_image_size_height" type="number" min="40" max="240" step="20" value="<?php echo $this->options['related_image_height']; ?>"/> x
							</label>
							<label>
								<input class="small-text" name="rltdpstsplgn_related_image_size_width" type="number" min="40" max="240" step="20" value="<?php echo $this->options['related_image_width']; ?>"/> px
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Excerpt Length', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_related_excerpt_length" type="number" min="1" max="1000" value="<?php echo $this->options['related_excerpt_length']; ?>"/>
						<?php _e( 'Symbol(s)', 'relevant' ); ?>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Read More Link Text', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_related_excerpt_more" type="text" maxlength="250" value="<?php echo $this->options['related_excerpt_more']; ?>"/>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Search Related Words in', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="rltdpstsplgn_related_criteria" value="category"<?php checked( $this->options['related_criteria'], 'category' ); ?> />
								<?php _e( 'Categories', 'relevant' ); ?>
							</label><br />
							<label>
								<input type="radio" name="rltdpstsplgn_related_criteria" value="tags"<?php checked( $this->options['related_criteria'], 'tags' ); ?> />
								<?php _e( 'Tags', 'relevant' ); ?>
							</label><br />
							<label>
								<input type="radio" name="rltdpstsplgn_related_criteria" value="title"<?php checked( $this->options['related_criteria'], 'title' ); ?> />
								<?php _e( 'Titles', 'relevant' ); ?>
							</label><br />
							<label>
								<input type="radio" name="rltdpstsplgn_related_criteria" value="meta"<?php checked( $this->options['related_criteria'], 'meta' ); ?> />
								<?php _e( 'Meta Key', 'relevant' ); ?>
								<span class="bws_info">(<?php _e( 'Enable "Key" in the "Related Post" block which is located in the post you want to display.', 'relevant' ); ?>)</span>
							</label>
						</fieldset>
						<span class="bws_info"><?php _e( 'Search related words on posts.', 'relevant' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Current Category', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_related_use_category" type="checkbox" value="1" <?php checked( 1, $this->options["related_use_category"] ); ?>/> <span class="bws_info"><?php _e( 'Enable to display posts from the current category only.', 'relevant' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Block Position', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" value="before" name="rltdpstsplgn_related_display[]" <?php if ( in_array( 'before', $this->options['related_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'Before content', 'relevant' ); ?>
							</label><br />
							<label>
								<input type="checkbox" value="after" name="rltdpstsplgn_related_display[]" <?php if ( in_array( 'after', $this->options['related_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'After content', 'relevant' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( '"No Posts Found" Message', 'relevant' ); ?></th>
					<td>
						<input type="text" name="rltdpstsplgn_related_no_posts_message" maxlength="250" value="<?php echo $this->options['related_no_posts_message']; ?>" />
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Search on Pages', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="rltdpstsplgn_related_add_for_page[]" value="category" <?php checked( in_array( 'category', $this->options['related_add_for_page'] ) ); ?> />
								<?php _e( 'Categories', 'relevant' ); ?>
								<span class="bws_info">(<?php _e( 'Post categories will be available for pages.', 'relevant' ); ?>)</span>
							</label><br />
							<label>
								<input type="checkbox" name="rltdpstsplgn_related_add_for_page[]" value="tags" <?php checked( in_array( 'tags', $this->options['related_add_for_page'] ) ); ?> />
								<?php _e( 'Tags', 'relevant' ); ?>
								<span class="bws_info">(<?php _e( 'Post tags will be available for pages.', 'relevant' ) ?>)</span>
							</label><br />
							<label>
								<input type="checkbox" name="rltdpstsplgn_related_add_for_page[]" value="title" <?php checked( in_array( 'title', $this->options['related_add_for_page'] ) ); ?> />
								<?php _e( 'Title', 'relevant' ); ?>
							</label><br />
							<label>
								<input type="checkbox" name="rltdpstsplgn_related_add_for_page[]" value="meta" <?php checked( in_array( 'meta', $this->options['related_add_for_page'] ) ); ?> />
								<?php _e( 'Meta Key', 'relevant' ); ?>
							</label>
							<div class="bws_info"><?php _e( 'Enable to search related words on pages.', 'relevant' ); ?></div>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		public function tab_featured_posts() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Featured Posts Settings', 'relevant' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<p><?php _e( 'Navigate to a single post or page and enable Featured Posts option.', 'relevant' ); ?></p>
			<table class="form-table">
				<tr>
					<th><?php _e( 'Number of Posts', 'relevant' ); ?></th>
					<td>
						<input type="number" min="1" max="999" value="<?php echo $this->options['featured_posts_count']; ?>" name="rltdpstsplgn_featured_posts_count" />
						<div class="bws_info"><?php _e( 'Number of posts displayed in Featured Posts block.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Date Range', 'relevant' ); ?></th>
					<td>
						<select name="rltdpstsplgn_display_featured_posts" >
							<option value="All" id="selectedMonth" <?php selected( 'All' == $this->options["display_featured_posts"] ); ?>><?php _e( 'All', 'relevant' ); ?></option>
							<option value="3 days ago" id="selectedMonth" <?php selected( '3 days ago' == $this->options["display_featured_posts"] ); ?>>3 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="5 days ago" id="selectedMonth" <?php selected( '5 days ago' == $this->options["display_featured_posts"] ); ?>>5 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="7 days ago" id="selectedMonth" <?php selected( '7 days ago' == $this->options["display_featured_posts"] ); ?>>7 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="1 month ago" id="selectedMonth" <?php selected( '1 month ago' == $this->options["display_featured_posts"] ); ?>>1 <?php _e( 'month', 'relevant' ); ?></option>
							<option value="3 month ago" id="selectedMonth" <?php selected( '3 month ago' == $this->options["display_featured_posts"] ); ?>>3 <?php _e( 'months', 'relevant' ); ?></option>
							<option value="6 month ago" id="selectedMonth" <?php selected( '6 month ago' == $this->options["display_featured_posts"] ); ?>>6 <?php _e( 'months', 'relevant' ); ?></option>
						</select>
						<div class="bws_info"><?php _e( 'Show only posts not older than the indicated time period.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Display', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<?php $featured_show_options = array(
								'excerpt'		=> __( 'Excerpt', 'relevant' ),
								'date'			=> __( 'Post date', 'relevant' ),
								'author'		=> __( 'Author', 'relevant' ),
								'reading_time'	=> __( 'Reading time', 'relevant' ),
								'comments'		=> __( 'Comments number', 'relevant' ),
								'thumbnail'		=> __( 'Featured image', 'relevant' ),
							);
							foreach ( $featured_show_options as $item => $label ) { ?>
								<label>
									<input name="rltdpstsplgn_featured_show_<?php echo $item; ?>" type="checkbox" value="1" <?php checked( 1, $this->options["featured_show_{$item}"] ); ?> /> <?php echo $label; ?>
								</label><br />
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Placeholder URL', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_featured_no_preview_img" type="text" maxlength="250" value="<?php echo $this->options['featured_no_preview_img']; ?>"/>
						<div class="bws_info"><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Size', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input class="small-text" name="rltdpstsplgn_featured_image_size_height" type="number" min="40" max="240" step="20" value="<?php echo $this->options['featured_image_height']; ?>"/> x
							</label>
							<label>
								<input class="small-text" name="rltdpstsplgn_featured_image_size_width" type="number" min="40" max="240" step="20" value="<?php echo $this->options['featured_image_width']; ?>"/> px
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Excerpt Length', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_featured_excerpt_length" type="number" min="1" max="1000" value="<?php echo $this->options['featured_excerpt_length']; ?>"/>
						<?php _e( 'Symbol(s)', 'relevant' ); ?>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Read More Link Text', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_featured_excerpt_more" type="text" maxlength="250" value="<?php echo $this->options['featured_excerpt_more']; ?>"/>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Current Category', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_featured_use_category" type="checkbox" value="1" <?php checked( 1, $this->options["featured_use_category"] ); ?>/> <span class="bws_info"><?php _e( 'Enable to display posts from the current category only.', 'relevant' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Block Position', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" value="before" name="rltdpstsplgn_featured_display[]" <?php if ( in_array( 'before', $this->options['featured_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'Before content', 'relevant' ); ?>
							</label><br />
							<label>
								<input type="checkbox" value="after" name="rltdpstsplgn_featured_display[]" <?php if ( in_array( 'after', $this->options['featured_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'After content', 'relevant' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Block Width', 'relevant' ); ?></th>
					<td>
						<input id="block_width_id" type="number" min="10" value="<?php echo $this->options['featured_block_width']; ?>" name="rltdpstsplgn_featured_block_width">
						<select class = "rltdpstsplgn_block_unit" name="rltdpstsplgn_featured_block_unit">						
						    <option value='%' <?php selected( '%', $this->options['featured_block_width_remark'] ); ?> >%</option>
						    <option value='px' <?php selected( 'px', $this->options['featured_block_width_remark'] ); ?> >px</option>
						</select>
                        <div class="bws_info"><?php printf( __( 'Enter the value in %s or %s, for example, %s or %s.', 'relevant' ), '&#37;', 'px', '100&#37;', '960px' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Content Block Width', 'relevant' ); ?></th>
					<td>
						<input id="text_width_id" type="number" min="10" value="<?php echo $this->options['featured_text_block_width']; ?>" name="rltdpstsplgn_featured_text_block_width" />
						<select class = "rltdpstsplgn_text_unit" name="rltdpstsplgn_featured_text_block_unit">
                            <option value='%' <?php selected( '%', $this->options['featured_text_block_width_remark'] ); ?> >%</option>
                            <option value='px' <?php selected( 'px', $this->options['featured_text_block_width_remark'] ); ?> >px</option>
						</select>
						<div class="bws_info"><?php printf( __( 'Enter the value in %s or %s, for example, %s or %s.', 'relevant' ), '&#37;', 'px', '100&#37;', '960px' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Custom Style', 'relevant' ); ?></th>
					<td>
						<label>
							<input type="checkbox" value="1" name="rltdpstsplgn_featured_theme_style" <?php checked( $this->options['featured_theme_style'], 1 ); ?> class="bws_option_affect" data-affect-show=".rltdpstsplgn_theme_style" />
							<span class="bws_info"><?php _e( 'Enable to add custom styles for Featured Posts block.', 'relevant' ); ?></span>
						</label>
					</td>
				</tr>
				<tr class="rltdpstsplgn_theme_style">
					<th><?php _e( 'Block Background Color', 'relevant' ); ?></th>
					<td>
						<input type="text" value="<?php echo $this->options['featured_background_color_block']; ?>" name="rltdpstsplgn_featured_background_color_block" maxlength="7" class="rltdpstsplgn_colorpicker" />
					</td>
				</tr>
				<tr class="rltdpstsplgn_theme_style">
					<th><?php _e( 'Text Background Color', 'relevant' ); ?></th>
					<td>
						<input type="text" value="<?php echo $this->options['featured_background_color_text']; ?>" name="rltdpstsplgn_featured_background_color_text" maxlength="7" class="rltdpstsplgn_colorpicker" />
					</td>
				</tr>
				<tr class="rltdpstsplgn_theme_style">
					<th><?php _e( 'Title Color', 'relevant' ); ?></th>
					<td>
						<input type="text" value="<?php echo $this->options['featured_color_header']; ?>" name="rltdpstsplgn_featured_color_header" maxlength="7" class="rltdpstsplgn_colorpicker" />
					</td>
				</tr>
				<tr class="rltdpstsplgn_theme_style">
					<th><?php _e( 'Text Color', 'relevant' ); ?></th>
					<td>
						<input type="text" value="<?php echo $this->options['featured_color_text']; ?>" name="rltdpstsplgn_featured_color_text" maxlength="7" class="rltdpstsplgn_colorpicker" />
					</td>
				</tr>
				<tr class="rltdpstsplgn_theme_style">
					<th><?php _e( 'Read More Link Text Color', 'relevant' ); ?></th>
					<td>
						<input type="text" value="<?php echo $this->options['featured_color_link']; ?>" name="rltdpstsplgn_featured_color_link" maxlength="7" class="rltdpstsplgn_colorpicker" />
					</td>
				</tr>
			</table>
		<?php }

		public function tab_latest_posts() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Latest Posts Settings', 'relevant' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table">
				<tr>
					<th><?php _e( 'Title', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_latest_title" type="text" maxlength="250" value="<?php echo $this->options['latest_title']; ?>"/>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Number of Posts', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_latest_posts_count" type="number" min="1" max="1000" value="<?php echo $this->options['latest_posts_count']; ?>" />
						<div class="bws_info"><?php _e( 'Number of posts displayed in Latest Posts block.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Display', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<?php $latest_show_options = array(
								'thumbnail'		=> __( 'Featured image', 'relevant' ),
								'excerpt'		=> __( 'Excerpt', 'relevant' ),
								'date'			=> __( 'Post date', 'relevant' ),
								'author'		=> __( 'Author', 'relevant' ),
								'reading_time'	=> __( 'Reading time', 'relevant' ),
								'comments'		=> __( 'Comments number', 'relevant' )
							);
							foreach ( $latest_show_options as $item => $label ) { ?>
								<label>
									<input name="rltdpstsplgn_latest_show_<?php echo $item; ?>" type="checkbox" value="1" <?php checked( 1, $this->options["latest_show_{$item}"] ); ?> /> <?php echo $label; ?>
								</label>
								<br />
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Placeholder URL', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_latest_no_preview_img" type="text" maxlength="250" value="<?php echo $this->options['latest_no_preview_img']; ?>"/>
						<div class="bws_info"><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Size', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input class="small-text" name="rltdpstsplgn_latest_image_size_height" type="number" min="40" max="240" step="20" value="<?php echo $this->options['latest_image_height']; ?>"/> x
							</label>
							<label>
								<input class="small-text" name="rltdpstsplgn_latest_image_size_width" type="number" min="40" max="240" step="20" value="<?php echo $this->options['latest_image_width']; ?>"/> px
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Excerpt Length', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_latest_excerpt_length" type="number" min="1" max="1000" value="<?php echo $this->options['latest_excerpt_length']; ?>"/>
						<?php _e( 'Symbol(s)', 'relevant' ); ?>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Read More Link Text', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_latest_excerpt_more" type="text" maxlength="250" value="<?php echo $this->options['latest_excerpt_more']; ?>"/>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Current Category', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_latest_use_category" type="checkbox" value="1" <?php checked( 1, $this->options["latest_use_category"] ); ?>/> <span class="bws_info"><?php _e( 'Enable to display posts from the current category only.', 'relevant' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Block Position', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" value="before" name="rltdpstsplgn_latest_display[]" <?php if ( in_array( 'before', $this->options['latest_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'Before content', 'relevant' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" value="after" name="rltdpstsplgn_latest_display[]" <?php if ( in_array( 'after', $this->options['latest_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'After content', 'relevant' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		public function tab_popular_posts() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Popular Posts Settings', 'relevant' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table">
				<tr>
					<th><?php _e( 'Title', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_popular_title" type="text" maxlength="250" value="<?php echo $this->options['popular_title']; ?>"/>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Number of Posts', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_popular_posts_count" type="number" min="1" max="1000" value="<?php echo $this->options['popular_posts_count']; ?>"/>
						<div class="bws_info"><?php _e( 'Number of posts displayed in Popular Posts block.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Date Range', 'relevant' ); ?></th>
					<td>
						<select name="rltdpstsplgn_display_popular_posts" >
							<option value="All" id="selectedMonth" <?php selected( 'All' == $this->options["display_popular_posts"] ); ?>><?php _e( 'All', 'relevant' ); ?></option>
							<option value="3 days ago" id="selectedMonth" <?php selected( '3 days ago' == $this->options["display_popular_posts"] ); ?>>3 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="5 days ago" id="selectedMonth" <?php selected( '5 days ago' == $this->options["display_popular_posts"] ); ?>>5 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="7 days ago" id="selectedMonth" <?php selected( '7 days ago' == $this->options["display_popular_posts"] ); ?>>7 <?php _e( 'days', 'relevant' ); ?></option>
							<option value="1 month ago" id="selectedMonth" <?php selected( '1 month ago' == $this->options["display_popular_posts"] ); ?>>1 <?php _e( 'month', 'relevant' ); ?></option>
							<option value="3 month ago" id="selectedMonth" <?php selected( '3 month ago' == $this->options["display_popular_posts"] ); ?>>3 <?php _e( 'months', 'relevant' ); ?></option>
							<option value="6 month ago" id="selectedMonth" <?php selected( '6 month ago' == $this->options["display_popular_posts"] ); ?>>6 <?php _e( 'months', 'relevant' ); ?></option>
						</select>
						<div class="bws_info"><?php _e( 'Show only posts not older than the indicated time period.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Display', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<?php $show_options = array(
									'views'			=> __( 'Views number', 'relevant' ),
									'thumbnail'		=> __( 'Featured image', 'relevant' ),
									'excerpt'		=> __( 'Excerpt', 'relevant' ),
									'date'			=> __( 'Post date', 'relevant' ),
									'author'		=> __( 'Author', 'relevant' ),
									'reading_time'	=> __( 'Reading time', 'relevant' ),
									'comments'		=> __( 'Comments number', 'relevant' )
								);
								foreach ( $show_options as $item => $label ) { ?>
									<label>
										<input name="rltdpstsplgn_popular_show_<?php echo $item; ?>" type="checkbox" value="1" <?php checked( 1, $this->options["popular_show_{$item}"] ); ?> /><?php echo $label; ?>
									</label><br />
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Placeholder URL', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_popular_no_preview_img" type="text" maxlength="250" value="<?php echo $this->options['popular_no_preview_img']; ?>"/>
						<div class="bws_info"><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Featured Image Size', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input class="small-text" name="rltdpstsplgn_popular_image_size_height" type="number" min="40" max="240" step="20" value="<?php echo $this->options['popular_image_height']; ?>"/> x
							</label>
							<label>
								<input class="small-text" name="rltdpstsplgn_popular_image_size_width" type="number" min="40" max="240" step="20" value="<?php echo $this->options['popular_image_width']; ?>"/> px
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Min Posts Number', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_popular_min_posts_count" type="number" min="0" max="9999" step="1" value="<?php echo $this->options['popular_min_posts_count']; ?>" />
						<div class="bws_info"><?php _e( 'Hide Popular Posts block if posts count is less than specified.', 'relevant' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Excerpt Length', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_popular_excerpt_length" type="number" min="1" max="10000" value="<?php echo $this->options['popular_excerpt_length']; ?>"/>
						<?php _e( 'Symbol(s)', 'relevant' ); ?>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Read More Link Text', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_popular_excerpt_more" type="text" maxlength="250" value="<?php echo $this->options['popular_excerpt_more']; ?>"/>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Sort Posts by Number of', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label><input name="rltdpstsplgn_popular_order_by" type="radio" value="comment_count" <?php checked( 'comment_count', $this->options['popular_order_by'] ); ?> /><?php _e( 'Comments', 'relevant' ); ?></label>
							<br />
							<label><input name="rltdpstsplgn_popular_order_by" type="radio" value="views_count" <?php checked( 'views_count', $this->options['popular_order_by'] ); ?> /><?php _e( 'Views', 'relevant' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Current Category', 'relevant' ); ?></th>
					<td>
						<input name="rltdpstsplgn_popular_use_category" type="checkbox" value="1" <?php checked( 1, $this->options["popular_use_category"] ); ?>/> <span class="bws_info"><?php _e( 'Enable to display posts from the current category only.', 'relevant' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Block Position', 'relevant' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" value="before" name="rltdpstsplgn_popular_display[]" <?php if ( in_array( 'before', $this->options['popular_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'Before content', 'relevant' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" value="after" name="rltdpstsplgn_popular_display[]" <?php if ( in_array( 'after', $this->options['popular_display'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'After content', 'relevant' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		/**
		 * Display custom metabox
		 * @access public
		 * @param void
		 * @return array The action results
		 */
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Relevant Posts Shortcodes', 'relevant' ); ?>
				</h3>
				<div class="inside">
					<p><?php _e( 'Add "Related Posts", "Latest Posts" or "Popular Posts" to a widget.', 'relevant' ); ?> <a href="widgets.php"><?php _e( 'Navigate to Widgets', 'relevant' ); ?></a></p>
					<div class="bws_margined_box">
						<?php _e( "Add related posts to your posts, pages or custom post types by using the following shortcode:", 'relevant' ); ?>
						<?php bws_shortcode_output( '[bws_related_posts]' ); ?>
					</div>
					<div class="bws_margined_box">
						<?php _e( "Add featured posts to your posts, pages or custom post types by using the following shortcode:", 'relevant' ); ?>
						<?php bws_shortcode_output( '[bws_featured_post]' ); ?>
					</div>
					<div class="bws_margined_box">
						<?php _e( "Add latest posts to your posts, pages or custom post types by using the following shortcode:", 'relevant' ); ?>
						<?php bws_shortcode_output( '[bws_latest_posts]' ); ?>
					</div>
					<div class="bws_margined_box">
						<?php _e( "Add popular posts to your posts, pages or custom post types by using the following shortcode:", 'relevant' ); ?>
						<?php bws_shortcode_output( '[bws_popular_posts]' ); ?>
					</div>
					<p><?php _e( 'Add featured posts to PHP template files by using the following code', 'relevant' ); ?>:</p>
					<code>&lt;?php do_action( 'ftrdpsts_featured_posts' ); ?&gt;</code>
				</div>
			</div>
		<?php }
	}
}
