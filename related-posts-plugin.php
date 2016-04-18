<?php
/*
Plugin Name: Relevant - Related Posts by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Related Posts Plugin intended to display related posts by category, by tag, by title or by meta key. The result can be displayed as a widget and as a shortocode.
Author: BestWebSoft
Text Domain: relevant
Domain Path: /languages
Version: 1.1.6
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*
	© Copyright 2016  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add our own menu */
if ( ! function_exists( 'add_rltdpstsplgn_admin_menu' ) ) {
	function add_rltdpstsplgn_admin_menu() {
		bws_general_menu();
		$settings = add_submenu_page( 'bws_plugins', __( 'Related Posts Plugin Settings', 'relevant' ), 'Related Posts Plugin', 'manage_options', 'related-posts-plugin.php', 'rltdpstsplgn_settings_page' );
		add_action( 'load-' . $settings, 'rltdpstsplgn_add_tabs' );
	}
}

if ( ! function_exists( 'rltdpstsplgn_plugins_loaded' ) ) {
	function rltdpstsplgn_plugins_loaded() {
		/* Internationalization */
		load_plugin_textdomain( 'relevant', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'rltdpstsplgn_plugin_init' ) ) {
	function rltdpstsplgn_plugin_init() {
		global $rltdpstsplgn_plugin_info;		

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		
		if ( empty( $rltdpstsplgn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$rltdpstsplgn_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version  */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $rltdpstsplgn_plugin_info, '3.8', '3.1' );

		/* tag support */
		rltdpstsplgn_tags_support_all();
	}
}

if ( ! function_exists( 'rltdpstsplgn_admin_init' ) ) {
	function rltdpstsplgn_admin_init() {
		global $bws_plugin_info, $rltdpstsplgn_plugin_info, $bws_shortcode_list;

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '100', 'version' => $rltdpstsplgn_plugin_info["Version"] );		

		/* Call register settings function */
		if ( isset( $_GET['page'] ) && 'related-posts-plugin.php' == $_GET['page'] )
			rltdpstsplgn_set_options();

		/* add Relevant to global $bws_shortcode_list  */
		$bws_shortcode_list['rltdpstsplgn'] = array( 'name' => 'Related Posts' );
	}
}

/* Setting options */
if ( ! function_exists( 'rltdpstsplgn_set_options' ) ) {
	function rltdpstsplgn_set_options() {
		global $rltdpstsplgn_options, $rltdpstsplgn_plugin_info, $rltdpstsplgn_options_defaults;

		/*
		Defaults checked radio button => "category", post to show with plugin => "5",
		heading the list of related posts, message if no related posts
		*/
		$rltdpstsplgn_options_defaults	= array(
			'plugin_option_version'		=> $rltdpstsplgn_plugin_info["Version"],
			'criteria'					=> 'category',
			'number_post'				=> '5',
			'head'						=> __( 'Related Posts Plugin', 'relevant' ),
			'no_posts'					=> __( 'No related posts found...', 'relevant' ),
			'index_show'				=> 0,
			'show_thumbnail'			=> 0,
			'add_for_page'				=> array(),
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1
		);

		if ( ! get_option( 'rltdpstsplgn_options' ) )
			add_option( 'rltdpstsplgn_options', $rltdpstsplgn_options_defaults );

		$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $rltdpstsplgn_options['plugin_option_version'] ) || $rltdpstsplgn_options['plugin_option_version'] != $rltdpstsplgn_plugin_info["Version"] ) {
			$rltdpstsplgn_options_defaults['display_settings_notice'] = 0;
			$rltdpstsplgn_options = array_merge( $rltdpstsplgn_options_defaults, $rltdpstsplgn_options );

			foreach ( $rltdpstsplgn_options_defaults as $key => $value ) {
				if ( isset( $rltdpstsplgn_options['rltdpstsplgn_' . $key ] ) ) {
					$rltdpstsplgn_options[ $key ] = $rltdpstsplgn_options['rltdpstsplgn_' . $key ];
					unset( $rltdpstsplgn_options['rltdpstsplgn_' . $key ] );
				}
			}

			$rltdpstsplgn_options['plugin_option_version'] = $rltdpstsplgn_plugin_info["Version"];
			update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
		}
	}
}

/* Add meta box "meta_key" for posts */
if ( ! function_exists( 'rltdpstsplgn_add_box' ) ) {
	function rltdpstsplgn_add_box() {
		global $rltdpstsplgn_options;
		if ( ! $rltdpstsplgn_options )
			$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );

		add_meta_box( 'rltdpstsplgn_sectionid', __( 'Related Posts Plugin', 'relevant' ), 'rltdpstsplgn_meta_box', 'post' );

		if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'meta', $rltdpstsplgn_options['add_for_page'] ) )
			add_meta_box( 'rltdpstsplgn_sectionid', __( 'Related Posts Plugin', 'relevant' ), 'rltdpstsplgn_meta_box', 'page' );
	}
}

/* Create meta box */
if ( ! function_exists( 'rltdpstsplgn_meta_box' ) ) {
	function rltdpstsplgn_meta_box( $post ) {
		$rltdpstsplgn_meta = get_post_meta( $post->ID, 'rltdpstsplgn_meta_key', 1 ); ?>
		<p><?php _e( 'Check "Key" if you want to display this post with Related Posts Plugin sorted by Meta Key:', 'relevant' ); ?></p>
		<p>
			<label><input type="radio" name="extra[rltdpstsplgn_meta_key]" value="key" <?php checked( $rltdpstsplgn_meta, 'key' ); ?> /> <?php _e( "Key", "relevant" ); ?></label>
			<label><input type="radio" name="extra[rltdpstsplgn_meta_key]" value="" <?php checked( $rltdpstsplgn_meta, '' ); ?> /> <?php _e( "None", "relevant" ); ?></label>
		</p>
	<?php }
}

/* Save meta_key */
if ( ! function_exists( 'rltdpstsplgn_save_postdata' ) ) {
	function rltdpstsplgn_save_postdata( $post_id ) {
		if ( isset( $_POST['extra'] ) && is_array( $_POST['extra'] ) ) {
			foreach ( $_POST['extra'] as $key => $value ) {
				if ( empty( $value ) )
					delete_post_meta( $post_id, $key ); /* Delete meta_key if value is empty */
				else
					update_post_meta( $post_id, $key, $value ); /* Add meta_key in  wp_postmeta */
			}
		}
	}
} /* End of the meta_key for posts */

/* Show shortcode under content */
if ( ! function_exists( 'rltdpstsplgn_output' ) ) {
	function rltdpstsplgn_output() {
		$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );
		ob_start();
		if ( ! is_home() ) {
			echo '<h2>' . strip_tags( $rltdpstsplgn_options['head'] ) . '</h2>';
			rltdpstsplgn_loop();
		}
		$rltdpstsplgn_output_string = ob_get_contents();
		ob_end_clean();
		return $rltdpstsplgn_output_string;
	}
}

/* add shortcode content  */
if ( ! function_exists( 'rltdpstsplgn_shortcode_button_content' ) ) {
	function rltdpstsplgn_shortcode_button_content( $content ) {
		global $wp_version, $post; ?>
		<div id="rltdpstsplgn" style="display:none;">
			<fieldset style="white-space: normal;">
				<?php _e( 'Insert the shortcode to display related posts.', 'relevant' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_related_posts]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/* Our widget to output result of user request */
if ( ! class_exists( 'rltdpstsplgn_widget' ) ) {
	class rltdpstsplgn_widget extends WP_Widget {
		function __construct() {
			parent::__construct(
				'rltdpstsplgnwidget',
				__( 'Related Posts Plugin', 'relevant' ),
				array(
					'classname'		=>	'rltdpstsplgnwidget',
					'description'	=>	__( 'A widget that displays related posts depending on your choice of sorting criteria', 'relevant' )
				)
			);
		}

		/* Display Widget */
		function widget( $args, $instance ) {
			$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );
			extract ( $args, EXTR_SKIP );
			$title = ( isset( $instance['title'] ) ) ? $instance['title'] : __( 'Related Posts Plugin', 'relevant' );
			$rltdpstsplgn_type = get_post_type();			
			if ( is_single() || ( ! is_single() && "1" == $rltdpstsplgn_options['index_show'] ) ) {
				if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) || ( empty( $rltdpstsplgn_options['add_for_page'] ) && 'page' != $rltdpstsplgn_type ) ) {
					echo $before_widget;
					echo $before_title . $title . $after_title;
					rltdpstsplgn_loop();
					echo $after_widget;
				}
			}
		}

		/* When Widget Control Form Is Posted */
		function update( $new_instance, $old_instance ) {
			$instance			=	$old_instance;
			$instance['title']	=	strip_tags( $new_instance['title'] );
			return $instance;
		}

		/*  Display Widget Control Form */
		function form( $instance ) {
			/* We appropriate the value of the name Widget */
			$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'Related Posts Plugin', 'relevant' ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
		<?php }
	}
} /* End of the widget */

/* Registing Widget */
if ( ! function_exists( 'rltdpstsplgn_widget_init' ) ) {
	function rltdpstsplgn_widget_init() {
		register_widget( 'rltdpstsplgn_widget' );
	}
}

/* Options of settings page */
if ( ! function_exists( 'rltdpstsplgn_settings_page' ) ) {
	function rltdpstsplgn_settings_page() {
		global $rltdpstsplgn_options, $rltdpstsplgn_plugin_info, $rltdpstsplgn_options_defaults, $wpdb;
		$error = $message =	"";

		/* Save data for settings page */
		if ( isset( $_REQUEST['rltdpstsplgn_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'rltdpstsplgn_nonce_name' ) ) {
			/* Save checked radio and number of posts */
			foreach ( $_POST['rltdpstsplgn_options'] as $key => $value ) {
				$rltdpstsplgn_options_new[ $key ] = esc_html( $_POST['rltdpstsplgn_options'][ $key ] );
			}

			$rltdpstsplgn_options_new = array_map( 'stripslashes_deep', $rltdpstsplgn_options_new );
			
			if ( isset( $rltdpstsplgn_options_new ) && ! empty( $rltdpstsplgn_options_new['number_post'] ) && preg_match( '|^[\d]*$|', $rltdpstsplgn_options_new['number_post'] ) ) {
				
				$rltdpstsplgn_options_new['index_show'] 	= isset( $_POST['rltdpstsplgn_options']['index_show'] ) ? 1 : 0;
				$rltdpstsplgn_options_new['show_thumbnail'] = isset( $_POST['rltdpstsplgn_options']['show_thumbnail'] ) ? 1 : 0;
				
				$rltdpstsplgn_options_new['add_for_page'] = array();
				if ( isset( $_POST['rltdpstsplgn_options']['add_for_page_categories'] ) )
					$rltdpstsplgn_options_new['add_for_page'][] = 'category';
				elseif ( in_array( 'category', $rltdpstsplgn_options['add_for_page'] ) ) {
					$delete[] = 'category';
				}
				if ( isset( $_POST['rltdpstsplgn_options']['add_for_page_tags'] ) )
					$rltdpstsplgn_options_new['add_for_page'][] = 'tags';
				elseif ( in_array( 'tags', $rltdpstsplgn_options['add_for_page'] ) ) {
					$delete[] = 'post_tag';
				}
				if ( isset( $_POST['rltdpstsplgn_options']['add_for_page_meta'] ) )
					$rltdpstsplgn_options_new['add_for_page'][] = 'meta';
				if ( isset( $_POST['rltdpstsplgn_options']['add_for_page_title'] ) )
					$rltdpstsplgn_options_new['add_for_page'][] = 'title';

				if ( ! empty( $delete ) ) {
					$taxonomies = implode( ',', $delete );

					$relationships = $wpdb->get_results(
						"SELECT r.object_id FROM $wpdb->terms AS t
						INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
						INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
						INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
						WHERE p.post_type = 'page' AND tt.taxonomy IN ('" . $taxonomies . "')
						GROUP BY t.term_id", ARRAY_A						
					);
					foreach ( $relationships as $key => $value ) {
						wp_delete_object_term_relationships( $value['object_id'], $delete );
					}
				}

				$rltdpstsplgn_options = $rltdpstsplgn_options_new;
				
				update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
				$message = __( "Settings saved.", 'relevant' );
			} else {
				$error = __( "Wrong type of displayed posts, please use number", 'relevant' );
			}
		}
		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
			$rltdpstsplgn_options = $rltdpstsplgn_options_defaults;
			update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
			$message = __( 'All plugin settings were restored.', 'relevant' );
		} /* end */

		/* Display form on the setting page */ ?>
		<div class="wrap">
			<h1><?php _e( 'Related Posts Plugin Settings', 'relevant' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>"  href="admin.php?page=related-posts-plugin.php"><?php _e( 'Settings', 'relevant' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=related-posts-plugin.php&amp;action=custom_code"><?php _e( 'Custom code', 'relevant' ); ?></a>
			</h2>
			<?php bws_show_settings_notice(); ?>			
			<div class="updated fade below-h2" <?php if ( $message == "" || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error below-h2" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! isset( $_GET['action'] ) ) { 
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( plugin_basename( __FILE__ ) );
				} else { ?>
					<p><?php _e( 'If you would like to display related posts with widget, you must add widget "Related Posts Plugin" in the tab Widgets', 'relevant' ); ?></p>
					<div>
						<?php printf( __( "If you would like to add related posts to your post, please use %s button", 'relevant' ), 
							'<code><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt=""/></code>'
						); ?>
						<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 260px;">
								<?php printf( 
									__( "You can add related posts to your page by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s.", 'relevant' ),
									'<code><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt="" /></code>',
									'<code>[bws_related_posts]</code>'
								); ?>
							</div>
						</div>						
					</div>
					<form class="bws_form" method="post" action="admin.php?page=related-posts-plugin.php">
						<table class="form-table">
							<tr>
								<th><?php _e( 'Heading the list of related posts', 'relevant' ); ?></th>
								<td>
									<input style="width:300px" type="text" name="rltdpstsplgn_options[head]" maxlength="250" value="<?php echo $rltdpstsplgn_options['head']; ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e( 'How many posts to display', 'relevant' ); ?></th>
								<td>
									<input type="number" name="rltdpstsplgn_options[number_post]" min="1" max="10000" step="1" value="<?php echo $rltdpstsplgn_options['number_post']; ?>"/>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Message if do not have related posts', 'relevant' ); ?></th>
								<td>
									<input style="width:300px" type="text" name="rltdpstsplgn_options[no_posts]" maxlength="250" value="<?php echo $rltdpstsplgn_options['no_posts']; ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e( 'An option on which will be searched for related words in the posts', 'relevant' ); ?></th>
								<td>
									<fieldset>
										<label>
											<input type="radio" name="rltdpstsplgn_options[criteria]" value="category"<?php checked( $rltdpstsplgn_options['criteria'], 'category' ); ?> />
											<?php _e( 'Categories', 'relevant' ); ?>
										</label><br />
										<label>
											<input type="radio" name="rltdpstsplgn_options[criteria]" value="tags"<?php checked( $rltdpstsplgn_options['criteria'], 'tags' ); ?> />
											<?php _e( 'Tags', 'relevant' ); ?>
										</label><br />
										<label>
											<input type="radio" name="rltdpstsplgn_options[criteria]" value="title"<?php checked( $rltdpstsplgn_options['criteria'], 'title' ); ?> />
											<?php _e( 'Title', 'relevant' ); ?>
										</label><br />
										<label>
											<input type="radio" name="rltdpstsplgn_options[criteria]" value="meta"<?php checked( $rltdpstsplgn_options['criteria'], 'meta' ); ?> />
											<?php _e( 'Meta Key', 'relevant' ); ?>
										</label>
										<span class="bws_info">(<?php _e( 'If you want to display related posts by meta key you should first check the "Key" in meta box "Related post plugin" in the post that you want to display', 'relevant' ) ?>)</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Share into pages', 'relevant' ); ?></th>
								<td>
									<fieldset>
										<label>
											<input type="checkbox" name="rltdpstsplgn_options[add_for_page_categories]" value="1"<?php checked( in_array( 'category', $rltdpstsplgn_options['add_for_page'] ) ); ?> />
											<?php _e( 'Categories', 'relevant' ); ?> 
											<span class="bws_info">(<?php _e( 'Default posts categories will be available for pages', 'relevant' ) ?>)</span>
										</label><br />
										<label>
											<input type="checkbox" name="rltdpstsplgn_options[add_for_page_tags]" value="1"<?php checked( in_array( 'tags', $rltdpstsplgn_options['add_for_page'] ) ); ?> />
											<?php _e( 'Tags', 'relevant' ); ?> 
											<span class="bws_info">(<?php _e( 'Default posts tags will be available for pages', 'relevant' ) ?>)</span>
										</label><br />
										<label>
											<input type="checkbox" name="rltdpstsplgn_options[add_for_page_title]" value="title"<?php checked( in_array( 'title', $rltdpstsplgn_options['add_for_page'] ) ); ?> />
											<?php _e( 'Title', 'relevant' ); ?>
										</label><br />
										<label>
											<input type="checkbox" name="rltdpstsplgn_options[add_for_page_meta]" value="1"<?php checked( in_array( 'meta', $rltdpstsplgn_options['add_for_page'] ) ); ?> />
											<?php _e( 'Meta Key', 'relevant' ); ?>
										</label><br />
										<span class="bws_info">(<?php _e( 'If you want that the plugin search not only related posts, but related pages too', 'relevant' ) ?>)</span>
									</fieldset>
								</td>
							</tr>							
							<tr>
								<th><?php _e( 'Show Related Posts Widget not only in the post (category/tag/index/home page)', 'relevant' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="rltdpstsplgn_options[index_show]" value="1" <?php checked( $rltdpstsplgn_options['index_show'], 1 ); ?> />
									</label>
									<span class="bws_info">(<?php _e( 'By default related posts will be displayed for the first post in the list of posts.', 'relevant' ) ?>)</span>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Show posts thumbnails', 'relevant' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="rltdpstsplgn_options[show_thumbnail]" value="1" <?php checked( $rltdpstsplgn_options['show_thumbnail'], 1 ); ?> />
									</label>
								</td>
							</tr>
						</table>
						<input type="hidden" name="rltdpstsplgn_form_submit" value="submit" />
						<p class="submit">
							<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'relevant' ); ?>" />
						</p>
						<?php wp_nonce_field( plugin_basename( __FILE__ ), 'rltdpstsplgn_nonce_name' ); ?>
					</form>
					<?php bws_form_restore_default_settings( plugin_basename( __FILE__ ) );
				}
			} else {
				bws_custom_code_tab();
			}
			bws_plugin_reviews_block( $rltdpstsplgn_plugin_info['Name'], 'relevant' ); ?>
		</div>
	<?php }
} /* End of the options of settings page */

/* Loop */
if ( ! function_exists( 'rltdpstsplgn_loop' ) ) {
	function rltdpstsplgn_loop() {
		global $post, $wpdb, $args, $wp_query;
		$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );
		if ( is_single() ) {
			$rltdpstsplgn_post_ID = $post->ID;
		} elseif ( ! is_single() && isset( $wp_query->posts[0] ) ) {
			$rltdpstsplgn_post_ID = $wp_query->posts[0]->ID;
		}
		
		if ( isset( $rltdpstsplgn_post_ID ) ) {
			/* Sort by category */
			if ( 'category' == $rltdpstsplgn_options['criteria'] && ! empty( $post ) ) {
				$categories = get_the_category( $rltdpstsplgn_post_ID );
				
				if ( $categories ) {
					$category_ids = array();
					foreach ( $categories as $individual_category ) {
						$category_ids[] = $individual_category->term_id;
					}
					$args = array(
						'category__in'			=>	$category_ids,
						'post__not_in'			=>	array( $rltdpstsplgn_post_ID ),
						'showposts'				=>	$rltdpstsplgn_options['number_post'],
						'ignore_sticky_posts'	=>	1
				    );
					if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'category', $rltdpstsplgn_options['add_for_page'] ) )
						$args['post_type'] = array( 'post', 'page' );
					else
						$args['post_type'] = 'post';
				}
			} elseif ( 'meta' == $rltdpstsplgn_options['criteria'] ) { /* Sort by meta key */
				$args = array(
					'meta_key'				=>	'rltdpstsplgn_meta_key',
					'post__not_in'			=>	array( $rltdpstsplgn_post_ID ),
					'showposts'				=>	$rltdpstsplgn_options['number_post'],
			 		'ignore_sticky_posts'	=>	1
				);

				if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'meta', $rltdpstsplgn_options['add_for_page'] ) )
					$args['post_type'] = array( 'post', 'page' );
				else
					$args['post_type'] = 'post';

			} elseif ( 'tags' == $rltdpstsplgn_options['criteria'] && ! empty( $post ) ) { /* Sort by tag */
				$tags = wp_get_post_tags( $rltdpstsplgn_post_ID );
				if ( $tags ) {
					$tag_ids = array();
					foreach ( $tags as $individual_tag ) {
						$tag_ids[] = $individual_tag->term_id;
					}
					$args = array(
						'tag__in' 				=>	$tag_ids,
						'post__not_in'			=>	array( $rltdpstsplgn_post_ID ),
						'showposts'				=>	$rltdpstsplgn_options['number_post'],
						'ignore_sticky_posts'	=>	1
					);
					if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'tags', $rltdpstsplgn_options['add_for_page'] ) )
						$args['post_type'] = array( 'post', 'page' );
					else
						$args['post_type'] = 'post';
				}
			} elseif ( 'title' == $rltdpstsplgn_options['criteria'] && ! empty( $post ) ) { /* Sort by title */
				$rltdpstsplgn_prep = get_the_title( $rltdpstsplgn_post_ID );
				if ( "" != $rltdpstsplgn_prep ) {
					$rltdpstsplgn_titles = explode( " ", $rltdpstsplgn_prep );
					foreach ( $rltdpstsplgn_titles as $key ) {
						$rltdpstsplgn_res = $wpdb->get_results( "SELECT `ID` FROM $wpdb->posts WHERE `post_title` LIKE '%$key%' AND `post_status` = 'publish' AND `ID` != $rltdpstsplgn_post_ID" );
						if ( $rltdpstsplgn_res ) {
							foreach ( $rltdpstsplgn_res as $rltdpstsplgn_title ) {
								$title_ids[] = $rltdpstsplgn_title->ID;
							}
							$args = array(
								'post__in'				=>	$title_ids,
								'post__not_in'			=>	array( $rltdpstsplgn_post_ID ),
								'showposts'				=>	$rltdpstsplgn_options['number_post'],
								'ignore_sticky_posts'	=>	1
							);
							if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'title', $rltdpstsplgn_options['add_for_page'] ) )
								$args['post_type'] = array( 'post', 'page' );
							else
								$args['post_type'] = 'post';
						}
					}
				}
			}
			/* Starting of the loop */
			if ( $args != NULL ) {
				$rltdpstsplgn_query = new WP_Query( $args );
				if ( $rltdpstsplgn_query->have_posts() ) {
					while ( $rltdpstsplgn_query->have_posts() ) {
						$rltdpstsplgn_query->the_post();
						echo '<div class="rltdpstsplgn_content">';					
						echo '<a href="' .  get_permalink( get_the_ID() ) . '"><h3>' . get_the_title() . '</h3>';
						if ( 1 == $rltdpstsplgn_options['show_thumbnail'] && has_post_thumbnail( get_the_ID() ) )
							echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
						echo '</a>';
						echo '</div>';
					}					
				} else {
					echo '<p>' . strip_tags( $rltdpstsplgn_options['no_posts'] ) . '</p>';
				}
				wp_reset_query();
			} else {
				echo '<p>' . strip_tags( $rltdpstsplgn_options['no_posts'] ) . '</p>';
			}
		} else {
			echo '<p>' . strip_tags( $rltdpstsplgn_options['no_posts'] ) . '</p>';
		}
	}
} /* End of the loop */

/**
* Registers the taxonomy terms for the post type
*/
if ( ! function_exists( 'rltdpstsplgn_tags_support_all' ) ) {
	function rltdpstsplgn_tags_support_all() {
		global $rltdpstsplgn_options;
		if ( ! $rltdpstsplgn_options )
			$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );

		if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'tags', $rltdpstsplgn_options['add_for_page']  ) )
			register_taxonomy_for_object_type( 'post_tag', 'page' );
		if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'category', $rltdpstsplgn_options['add_for_page'] ) )
			register_taxonomy_for_object_type( 'category', 'page' );
	}
}
		
/**
* ensure all tags are included in queries
*/
if ( ! function_exists( 'rltdpstsplgn_tags_support_query' ) ) {
	function rltdpstsplgn_tags_support_query( $wp_query ) {
		global $rltdpstsplgn_options;
		if ( ! is_admin() ) {
			global $rltdpstsplgn_options;
			if ( ! $rltdpstsplgn_options )
				$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );

			if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'tags', $rltdpstsplgn_options['add_for_page'] ) ) {
				if ( $wp_query->get( 'tag' ) )
					$wp_query->set( 'post_type', 'any' );
			}

			if ( ! empty( $rltdpstsplgn_options['add_for_page'] ) && in_array( 'category', $rltdpstsplgn_options['add_for_page'] ) ) {
				if ( $wp_query->get( 'category_name' ) || $wp_query->get( 'cat' ) )
					$wp_query->set( 'post_type', 'any' );
			}
		}
	}
}

/* Add CSS and JS for plugin */
if ( ! function_exists ( 'rltdpstsplgn_admin_enqueue_scripts' ) ) {
	function rltdpstsplgn_admin_enqueue_scripts() {
		if ( isset( $_GET['page'] ) && "related-posts-plugin.php" == $_GET['page'] && isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) {
			bws_plugins_include_codemirror();
		}		
	}
}

/* add help tab  */
if ( ! function_exists( 'rltdpstsplgn_add_tabs' ) ) {
	function rltdpstsplgn_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id' 			=> 'rltdpstsplgn',
			'section' 		=> '200538689'
		);
		bws_help_tab( $screen, $args );
	}
}

/* Way to the settings page */
if ( ! function_exists( 'rltdpstsplgn_plugin_action_links' ) ) {
	function rltdpstsplgn_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename( __FILE__ );
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=related-posts-plugin.php">' . __( 'Settings', 'relevant' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

/* Contacts data */
if ( ! function_exists( 'rltdpstsplgn_register_plugin_links' ) ) {
	function rltdpstsplgn_register_plugin_links( $links, $file ) {
		$base	=	plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=related-posts-plugin.php">' . __( 'Settings', 'relevant' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/related-posts-plugin/faq/" target="_blank">' . __( 'FAQ', 'relevant' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'relevant' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'rltdpstsplgn_admin_notices' ) ) {
	function rltdpstsplgn_admin_notices() {
		global $hook_suffix, $rltdpstsplgn_plugin_info;	
		if ( 'plugins.php' == $hook_suffix ) {
			bws_plugin_banner_to_settings( $rltdpstsplgn_plugin_info, 'rltdpstsplgn_options', 'relevant', 'admin.php?page=related-posts-plugin.php' );
		}
		if ( isset( $_GET['page'] ) && 'related-posts-plugin.php' == $_GET['page'] ) {
			bws_plugin_suggest_feature_banner( $rltdpstsplgn_plugin_info, 'rltdpstsplgn_options', 'relevant' );
		}
	}
}

/* Uninstall options */
if ( ! function_exists( 'rltdpstsplgn_uninstall' ) ) {
	function rltdpstsplgn_uninstall() {
		global $wpdb;
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'rltdpstsplgn_options' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'rltdpstsplgn_options' );
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

add_action( 'admin_menu', 'add_rltdpstsplgn_admin_menu' );

add_action( 'init', 'rltdpstsplgn_plugin_init' );
add_action( 'admin_init', 'rltdpstsplgn_admin_init' );

add_action( 'plugins_loaded', 'rltdpstsplgn_plugins_loaded' );

/* Add meta box for Posts */
add_action( 'add_meta_boxes', 'rltdpstsplgn_add_box' );
/* Save our own meta_key */
add_action( 'save_post', 'rltdpstsplgn_save_postdata' );
add_action( 'widgets_init', 'rltdpstsplgn_widget_init' );

add_action( 'admin_enqueue_scripts', 'rltdpstsplgn_admin_enqueue_scripts' );

/*add_action( 'pre_get_posts', 'rltdpstsplgn_tags_support_query' ); */

/* Adds "Settings" link to the plugin action page */
add_filter( 'plugin_action_links', 'rltdpstsplgn_plugin_action_links', 10, 2 );
/* Additional links on the plugin page */
add_filter( 'plugin_row_meta', 'rltdpstsplgn_register_plugin_links', 10, 2 );

add_action( 'admin_notices', 'rltdpstsplgn_admin_notices' );

add_shortcode( 'bws_related_posts', 'rltdpstsplgn_output' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'rltdpstsplgn_shortcode_button_content' );

register_uninstall_hook( __FILE__, 'rltdpstsplgn_uninstall' );