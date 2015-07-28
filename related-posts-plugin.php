<?php
/*
Plugin Name: Relevant - Related Posts by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Related Posts Plugin intended to display related posts by category, by tag, by title or by meta key. The result can be displayed as a widget and as a shortocode.
Author: BestWebSoft
Version: 1.1.3
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*
	© Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

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
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		add_submenu_page( 'bws_plugins', __( 'Related Posts Plugin Settings', 'related_posts_plugin' ), 'Related Posts Plugin', 'manage_options', 'related-posts-plugin.php', 'rltdpstsplgn_settings_page' );
	}
}

if ( ! function_exists ( 'rltdpstsplgn_plugin_init' ) ) {
	function rltdpstsplgn_plugin_init() {
		global $rltdpstsplgn_plugin_info;
		/* Internationalization */
		load_plugin_textdomain( 'related_posts_plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_functions.php' );
		
		if ( empty( $rltdpstsplgn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$rltdpstsplgn_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version  */
		bws_wp_version_check( plugin_basename( __FILE__ ), $rltdpstsplgn_plugin_info, '3.0' );
	}
}

if ( ! function_exists( 'rltdpstsplgn_admin_init' ) ) {
	function rltdpstsplgn_admin_init() {
		global $bws_plugin_info, $rltdpstsplgn_plugin_info;

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '100', 'version' => $rltdpstsplgn_plugin_info["Version"] );		

		/* Call register settings function */
		if ( isset( $_GET['page'] ) && "related-posts-plugin.php" == $_GET['page'] )
			rltdpstsplgn_set_options();
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
		$rltdpstsplgn_options = array();
		$rltdpstsplgn_options_defaults	=	array(
			'plugin_option_version'	=>	$rltdpstsplgn_plugin_info["Version"],
			'criteria'				=>	'category',
			'number_post'			=>	'5',
			'head'					=>	__( 'Related Posts Plugin', 'related_posts_plugin' ),
			'no_posts'				=>	__( 'No related posts found...', 'related_posts_plugin' ),
			'index_show'			=>	0,
			'show_thumbnail'		=>	0
		);

		if ( ! get_option( 'rltdpstsplgn_options' ) )
			add_option( 'rltdpstsplgn_options', $rltdpstsplgn_options_defaults );

		$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $rltdpstsplgn_options['plugin_option_version'] ) || $rltdpstsplgn_options['plugin_option_version'] != $rltdpstsplgn_plugin_info["Version"] ) {
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

/* Connecting Stylesheet and Scripts */
if ( ! function_exists ( 'rltdpstsplgn_admin_style' ) ) {
	function rltdpstsplgn_admin_style() {
		if ( isset( $_GET['page'] ) && "related-posts-plugin.php" == $_GET['page'] ) {
			wp_enqueue_script( 'rltdpstsplgn_script', plugins_url( 'js/script.js', __FILE__ ) );
		}
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
				$settings_link = '<a href="admin.php?page=related-posts-plugin.php">' . __( 'Settings', 'related_posts_plugin' ) . '</a>';
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
				$links[]	=	'<a href="admin.php?page=related-posts-plugin.php">' . __( 'Settings', 'related_posts_plugin' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/related-posts-plugin/faq/" target="_blank">' . __( 'FAQ', 'related_posts_plugin' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'related_posts_plugin' ) . '</a>';
		}
		return $links;
	}
}

/* Add meta box "meta_key" for posts */
if ( ! function_exists( 'rltdpstsplgn_add_box' ) ) {
	function rltdpstsplgn_add_box() {
		add_meta_box( 'rltdpstsplgn_sectionid', __( 'Related Posts Plugin', 'related_posts_plugin' ), 'rltdpstsplgn_meta_box', 'post' );
	}
}

/* Create meta box */
if ( ! function_exists( 'rltdpstsplgn_meta_box' ) ) {
	function rltdpstsplgn_meta_box( $post ) {
		$rltdpstsplgn_meta = get_post_meta( $post->ID, 'rltdpstsplgn_meta_key', 1 ); ?>
		<p><?php _e( 'Check "Key" if you want to display this post with Related Posts Plugin sorted by Meta Key:', 'related_posts_plugin' ); ?></p>
		<p>
			<label><input type="radio" name="extra[rltdpstsplgn_meta_key]" value="key" <?php checked( $rltdpstsplgn_meta, 'key' ); ?> /> <?php _e( "Key", "related_posts_plugin" ); ?></label>
			<label><input type="radio" name="extra[rltdpstsplgn_meta_key]" value="" <?php checked( $rltdpstsplgn_meta, '' ); ?> /> <?php _e( "None", "related_posts_plugin" ); ?></label>
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

/* Our widget to output result of user request */
if ( ! class_exists( 'rltdpstsplgn_widget' ) ) {
	class rltdpstsplgn_widget extends WP_Widget {
		function __construct() {
			parent::__construct(
				'rltdpstsplgnwidget',
				__( 'Related Posts Plugin', 'related_posts_plugin' ),
				array(
					'classname'		=>	'rltdpstsplgnwidget',
					'description'	=>	__( 'A widget that displays related posts depending on your choice of sorting criteria', 'related_posts_plugin' )
				)
			);
		}

		/* Display Widget */
		function widget( $args, $instance ) {
			$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );
			extract ( $args, EXTR_SKIP );
			$title = ( isset( $instance['title'] ) ) ? $instance['title'] : __( 'Related Posts Plugin', 'related_posts_plugin' );
			$rltdpstsplgn_type = get_post_type();
			if ( is_single() || ( ! is_single() && 'page' != $rltdpstsplgn_type && "1" == $rltdpstsplgn_options['index_show'] ) ) {
				echo $before_widget;
				echo $before_title . $title . $after_title;
				rltdpstsplgn_loop();
				echo $after_widget;
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
			$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'Related Posts Plugin', 'related_posts_plugin' ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'related_posts_plugin' ); ?></label>
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
		global $rltdpstsplgn_options, $rltdpstsplgn_plugin_info, $rltdpstsplgn_options_defaults;
		$error = $message =	"";
		/* Save data for settings page */
		if ( isset( $_REQUEST['rltdpstsplgn_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'rltdpstsplgn_nonce_name' ) ) {
			/* Save checked radio and number of posts */
			foreach ( $_POST['rltdpstsplgn_options'] as $key => $value ) {
				$rltdpstsplgn_options[ $key ] = stripslashes( esc_html( $_POST['rltdpstsplgn_options'][ $key ] ) );
			}
			if ( isset( $rltdpstsplgn_options ) && ! empty( $rltdpstsplgn_options['number_post'] ) && preg_match( '|^[\d]*$|', $rltdpstsplgn_options['number_post'] ) ) {
				$rltdpstsplgn_options['index_show'] = isset( $_POST['rltdpstsplgn_options']['index_show'] ) ? 1 : 0;
				$rltdpstsplgn_options['show_thumbnail'] = isset( $_POST['rltdpstsplgn_options']['show_thumbnail'] ) ? 1 : 0;
				$rltdpstsplgn_options['plugin_option_version'] = $rltdpstsplgn_plugin_info["Version"];
				update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
				$message = __( "Settings saved.", 'related_posts_plugin' );
			} else {
				$error = __( "Wrong type of displayed posts, please use number", 'related_posts_plugin' );
			}
		}
		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
			$rltdpstsplgn_options = $rltdpstsplgn_options_defaults;
			update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
			$message = __( 'All plugin settings were restored.', 'related_posts_plugin' );
		} /* end */

		/* Display form on the setting page */ ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php _e( 'Related Posts Plugin Settings', 'related_posts_plugin' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="admin.php?page=related-posts-plugin.php"><?php _e( 'Settings', 'related_posts_plugin' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/related-posts/faq/" target="_blank"><?php _e( 'FAQ', 'related_posts_plugin' ); ?></a>
			</h2>			
			<div class="updated fade" <?php if ( $message == "" || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="rltdpstsplgn_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'related_posts_plugin' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'related_posts_plugin' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! isset( $_GET['action'] ) ) { 
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( plugin_basename( __FILE__ ) );
				} else { ?>
					<form method="post" action="admin.php?page=related-posts-plugin.php" id="rltdpstsplgn_settings_form">
						<p><?php _e( 'If you would like to display related posts with widget, you must add widget "Related Posts Plugin" in the tab Widgets', 'related_posts_plugin' ); ?></p>
						<p><?php _e( 'If you would like to display related posts on the end of your post, just copy and put this shortcode onto your post or page: [bws_related_posts]', 'related_posts_plugin' ); ?></p>
						<table class="form-table">
							<tr>
								<th><?php _e( 'Heading the list of related posts', 'related_posts_plugin' ); ?></th>
								<td>
									<input style="width:300px" type="text" name="rltdpstsplgn_options[head]" maxlength="250" value="<?php echo strip_tags( $rltdpstsplgn_options['head'] ); ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e( 'How many posts to display', 'related_posts_plugin' ); ?></th>
								<td>
									<input type="number" name="rltdpstsplgn_options[number_post]" min="1" max="10000" step="1" value="<?php echo $rltdpstsplgn_options['number_post']; ?>"/>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Message if do not have related posts', 'related_posts_plugin' ); ?></th>
								<td>
									<input style="width:300px" type="text" name="rltdpstsplgn_options[no_posts]" maxlength="250" value="<?php echo strip_tags( $rltdpstsplgn_options['no_posts'] ); ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e( 'An option on which will be searched for related words in the posts', 'related_posts_plugin' ); ?></th>
								<td>
									<label>
										<input type="radio" name="rltdpstsplgn_options[criteria]" value="category"<?php checked( $rltdpstsplgn_options['criteria'], 'category' ); ?> />
										<?php _e( 'Category', 'related_posts_plugin' ); ?>
									</label><br />
									<label>
										<input type="radio" name="rltdpstsplgn_options[criteria]" value="tags"<?php checked( $rltdpstsplgn_options['criteria'], 'tags' ); ?> />
										<?php _e( 'Tags', 'related_posts_plugin' ); ?>
									</label><br />
									<label>
										<input type="radio" name="rltdpstsplgn_options[criteria]" value="title"<?php checked( $rltdpstsplgn_options['criteria'], 'title' ); ?> />
										<?php _e( 'Title', 'related_posts_plugin' ); ?>
									</label><br />
									<label>
										<input type="radio" name="rltdpstsplgn_options[criteria]" value="meta"<?php checked( $rltdpstsplgn_options['criteria'], 'meta' ); ?> />
										<?php _e( 'Meta Key', 'related_posts_plugin' ); ?>
									</label>
									<span class="bws_info"><?php _e( '(If you want to display related posts by meta key you should first check the "Key" in meta box "Related post plugin" in the post that you want to display)', 'related_posts_plugin' ) ?></span>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Show Related Posts Widget not only in the post (category/tag/index/home page)', 'related_posts_plugin' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="rltdpstsplgn_options[index_show]" value="1" <?php checked( $rltdpstsplgn_options['index_show'], 1 ); ?> />
									</label>
									<span class="bws_info"><?php _e( '(By default related posts will be displayed for the first post in the list of posts.)', 'related_posts_plugin' ) ?></span>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Show posts thumbnails', 'related_posts_plugin' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="rltdpstsplgn_options[show_thumbnail]" value="1" <?php checked( $rltdpstsplgn_options['show_thumbnail'], 1 ); ?> />
									</label>
								</td>
							</tr>
						</table>
						<input type="hidden" name="rltdpstsplgn_form_submit" value="submit" />
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'related_posts_plugin' ); ?>" />
						</p>
						<?php wp_nonce_field( plugin_basename( __FILE__ ), 'rltdpstsplgn_nonce_name' ); ?>
					</form>
					<?php bws_form_restore_default_settings( plugin_basename( __FILE__ ) );
				}
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
		} elseif ( ! is_single() ) {
			$rltdpstsplgn_post_ID = $wp_query->posts[0]->ID;
		}
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
			}
		} elseif ( 'meta' == $rltdpstsplgn_options['criteria'] ) { /* Sort by meta key */
			$args = array(
				'meta_key'				=>	'rltdpstsplgn_meta_key',
				'post__not_in'			=>	array( $rltdpstsplgn_post_ID ),
				'showposts'				=>	$rltdpstsplgn_options['number_post'],
		 		'ignore_sticky_posts'	=>	1
			);
		} elseif ( 'tags' == $rltdpstsplgn_options['criteria'] && ! empty( $post ) ) { /* Sort by tag */
			$tags = wp_get_post_tags( $rltdpstsplgn_post_ID );
			if ( $tags ) {
				$tag_ids = array();
				foreach( $tags as $individual_tag ) {
					$tag_ids[] = $individual_tag->term_id;
				}
				$args = array(
					'tag__in' 				=>	$tag_ids,
					'post__not_in'			=>	array( $rltdpstsplgn_post_ID ),
					'showposts'				=>	$rltdpstsplgn_options['number_post'],
					'ignore_sticky_posts'	=>	1
				);
			}
		} elseif ( 'title' == $rltdpstsplgn_options['criteria'] && ! empty( $post ) ) { /* Sort by title */
			$rltdpstsplgn_prep = get_the_title( $rltdpstsplgn_post_ID );
			if ( "" != $rltdpstsplgn_prep ) {
				$rltdpstsplgn_titles = explode( " ", $rltdpstsplgn_prep );
				foreach ( $rltdpstsplgn_titles as $key ) {
					$rltdpstsplgn_res = $wpdb->get_results( "SELECT ID FROM wp_posts WHERE post_title LIKE '%$key%' AND post_status = 'publish' AND ID != $rltdpstsplgn_post_ID" );
					if ( $rltdpstsplgn_res ) {
						foreach ( $rltdpstsplgn_res as $rltdpstsplgn_title ) {
							$title_ids[] = $rltdpstsplgn_title->ID;
						}
						$args = array(
							'post_type'				=>	'post',
							'post__in'				=>	$title_ids,
							'post__not_in'			=>	array( $rltdpstsplgn_post_ID ),
							'showposts'				=>	$rltdpstsplgn_options['number_post'],
							'ignore_sticky_posts'	=>	1
						);
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
			wp_reset_query();
			} else {
				echo '<p>' . strip_tags( $rltdpstsplgn_options['no_posts'] ) . '</p>';
			}
		} else {
			echo '<p>' . strip_tags( $rltdpstsplgn_options['no_posts'] ) . '</p>';
		}
	}
} /* End of the loop */

/* Uninstall options */
if ( ! function_exists( 'rltdpstsplgn_uninstall' ) ) {
	function rltdpstsplgn_uninstall() {
		delete_option( 'rltdpstsplgn_options' );
	}
}

add_action( 'admin_menu', 'add_rltdpstsplgn_admin_menu' );
add_action( 'init', 'rltdpstsplgn_plugin_init' );
add_action( 'admin_init', 'rltdpstsplgn_admin_init' );
add_action( 'admin_enqueue_scripts', 'rltdpstsplgn_admin_style' );
/* Add meta box for Posts */
add_action( 'add_meta_boxes', 'rltdpstsplgn_add_box' );
/* Save our own meta_key */
add_action( 'save_post', 'rltdpstsplgn_save_postdata' );
add_action( 'widgets_init', 'rltdpstsplgn_widget_init' );

/* Adds "Settings" link to the plugin action page */
add_filter( 'plugin_action_links', 'rltdpstsplgn_plugin_action_links', 10, 2 );
/* Additional links on the plugin page */
add_filter( 'plugin_row_meta', 'rltdpstsplgn_register_plugin_links', 10, 2 );

add_shortcode( 'bws_related_posts', 'rltdpstsplgn_output' );

register_uninstall_hook( __FILE__, 'rltdpstsplgn_uninstall' );