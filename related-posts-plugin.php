<?php
/*
Plugin Name: Relevant - Related Posts Plugin
Plugin URI: http://bestwebsoft.com/plugin/
Description: Related Posts Plugin intended to display related posts by category, by tag, by title or by meta key. The result can be displayed as a widget and as a shortocode.
Author: BestWebSoft
Version: 1.0.5
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*
	© Copyright 2014  BestWebSoft  ( http://support.bestwebsoft.com )

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

require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );

/* Add our own menu */
if( ! function_exists( 'add_rltdpstsplgn_admin_menu' ) ) {
	function add_rltdpstsplgn_admin_menu() {
		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( 'images/px.png', __FILE__ ), 1001 );
		add_submenu_page( 'bws_plugins', __( 'Related Posts Plugin Settings', 'related_posts_plugin' ), __( 'Related Posts Plugin', 'related_posts_plugin' ), 'manage_options', "related-posts-plugin.php", 'rltdpstsplgn_settings_page' );
	}
}

/* Setting options */
if( ! function_exists( 'rltdpstsplgn_set_options' ) ) {
	function rltdpstsplgn_set_options() {
		global $rltdpstsplgn_options, $wpmu, $bws_plugin_info;

		if ( function_exists( 'get_plugin_data' ) && ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) ) ) {
			$plugin_info = get_plugin_data( __FILE__ );	
			$bws_plugin_info = array( 'id' => '100', 'version' => $plugin_info["Version"] );
		};
		/*
		Defaults checked radio button => "category", post to show with plugin => "5",
		heading the list of related posts, message if no related posts
		*/
		$rltdpstsplgn_options = array();
		$rltdpstsplgn_options_defaults	=	array(
			'criteria'		=>	'category',
			'number_post'	=>	'5',
			'head'			=>	__( '<h2>Related Posts Plugin</h2>', 'related_posts_plugin' ),
			'no_posts'		=>	__( '<p>No related posts found...</p>', 'related_posts_plugin' ),
			'index_show'	=>	0
		);
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'rltdpstsplgn_options' ) ) {
				add_site_option( 'rltdpstsplgn_options', $rltdpstsplgn_option_defaults, '', 'yes' );
			} else {
				$rltdpstsplgn_options = get_site_option( 'rltdpstsplgn_options' );
				foreach ( $rltdpstsplgn_options_defaults as $key => $value) {
					if ( isset( $rltdpstsplgn_options['rltdpstsplgn_' . $key] ) ) {
						$rltdpstsplgn_options[$key] = $rltdpstsplgn_options['rltdpstsplgn_' . $key];
						unset( $rltdpstsplgn_options['rltdpstsplgn_' . $key] );
					}
				}
			}
		} else {
			if ( ! get_option( 'rltdpstsplgn_options' ) ) {
				add_option( 'rltdpstsplgn_options', $rltdpstsplgn_options_defaults, '', 'yes' );
			} else {
				$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );
				foreach ( $rltdpstsplgn_options_defaults as $key => $value) {
					if ( isset( $rltdpstsplgn_options['rltdpstsplgn_' . $key] ) ) {
						$rltdpstsplgn_options[$key] = $rltdpstsplgn_options['rltdpstsplgn_' . $key];
						unset( $rltdpstsplgn_options['rltdpstsplgn_' . $key] );
					}
				}
			}
		}
		$rltdpstsplgn_options = array_merge( $rltdpstsplgn_options_defaults, $rltdpstsplgn_options );
		update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
	}
}

if ( ! function_exists ( 'rltdpstsplgn_plugin_init' ) ) {
	function rltdpstsplgn_plugin_init() {
		/* Internationalization */
		load_plugin_textdomain( 'related_posts_plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'rltdpstsplgn_plugin_version_check' ) ) {
	function rltdpstsplgn_plugin_version_check() {
		global $wp_version;
		$plugin_data	=	get_plugin_data( __FILE__, false );
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( __FILE__ );
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $plugin_data['Name'] . " </strong> " . __( 'requires', 'related_posts_plugin' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'related_posts_plugin') . "<br /><br />" . __( 'Back to the WordPress', 'related_posts_plugin') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'related_posts_plugin') . "</a>." );
			}
		}
	}
}

/* Connecting Stylesheet and Scripts */
if ( ! function_exists ( 'rltdpstsplgn_admin_style' ) ) {
	function rltdpstsplgn_admin_style() {
		global $wp_version;
		if ( $wp_version < 3.8 )
			wp_enqueue_style( 'rltdpstsplgnstylesheet', plugins_url( 'css/style_wp_before_3.8.css', __FILE__ ) );	
		else
			wp_enqueue_style( 'rltdpstsplgnstylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

if ( ! function_exists('rltdpstsplgn_admin_js') ) {
	function rltdpstsplgn_admin_js() {
		if ( isset( $_GET['page'] ) && "related-posts-plugin.php" == $_GET['page'] ) {
			/* add notice about changing in the settings page */
			?>
			<script type="text/javascript">
				(function($) {
					$(document).ready( function() {
						$( '#rltdpstsplgn_settings_form input' ).bind( "change click select", function() {
							if ( $( this ).attr( 'type' ) != 'submit' ) {
								$( '.updated.fade' ).css( 'display', 'none' );
								$( '#rltdpstsplgn_settings_notice' ).css( 'display', 'block' );
							};
						});
					});
				})(jQuery);
			</script>
		<?php }
	}
}

/* Way to the settings page */
if ( ! function_exists( 'rltdpstsplgn_plugin_action_links' ) ) {
	function rltdpstsplgn_plugin_action_links( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin )
			$this_plugin = plugin_basename( __FILE__ );
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="admin.php?page=related-posts-plugin.php">' . __( 'Settings', 'related_posts_plugin' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
}

/* Contacts data */
if ( ! function_exists( 'rltdpstsplgn_register_plugin_links' ) ) {
	function rltdpstsplgn_register_plugin_links( $links, $file ) {
		$base	=	plugin_basename( __FILE__ );
		if ( $file == $base ) {
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
	<?php
	}
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
			echo $rltdpstsplgn_options['head'];
			rltdpstsplgn_loop();
		}
		$rltdpstsplgn_output_string = ob_get_contents();
		ob_end_clean();
		return $rltdpstsplgn_output_string;
	}
}

/* Our widget to output result of user request */
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
		$title = ( $instance['title'] ) ? $instance['title'] : __( 'Related Posts Plugin', 'related_posts_plugin' );
		$rltdpstsplgn_type = get_post_type();
		if ( is_single() || ( ! is_single() && 'page' != $rltdpstsplgn_type && "1" == $rltdpstsplgn_options['index_show'] ) ) {
			echo $before_widget;
			echo $before_title . $title . $after_title;
			rltdpstsplgn_loop();
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
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
	<?php
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
		global $rltdpstsplgn_options;
		$error		=	"";
		$message	=	__( "Settings saved.", 'related_posts_plugin' );
		/* Save data for settings page */
		if ( isset( $_REQUEST['rltdpstsplgn_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'rltdpstsplgn_nonce_name' ) ) {
			/* Save checked radio and number of posts */
			$rltdpstsplgn_options = $_POST['rltdpstsplgn_options'];
			if ( isset( $rltdpstsplgn_options ) && ! empty( $rltdpstsplgn_options['number_post'] ) && preg_match( '|^[\d]*$|', $rltdpstsplgn_options['number_post'] ) ) {
				if ( ! isset( $_POST['rltdpstsplgn_options']['index_show'] ) ) {
					$rltdpstsplgn_options['index_show'] = 0;
				}
				update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
			} else {
				$error = __( "Wrong type of displayed posts, please use number", 'related_posts_plugin' );
			}
		}
		/* Display form on the setting page */ ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php _e( 'Related Posts Plugin Settings', 'related_posts_plugin' ); ?></h2>
			<div class="updated fade" <?php if ( ! isset( $_REQUEST['rltdpstsplgn_form_submit'] ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="rltdpstsplgn_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'related_posts_plugin' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'related_posts_plugin' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<form method="post" action="admin.php?page=related-posts-plugin.php" id="rltdpstsplgn_settings_form">
				<div id="rltdpstsplgn_options">
					<p><?php _e( 'If you would like to display related posts with widget, you must add widget "Releted Posts Plugin" in the tab Widgets', 'related_posts_plugin' ); ?></p>
					<p><?php _e( 'If you would like to display related posts on the end of your post, just copy and put this shortcode onto your post or page: [bws_related_posts]', 'related_posts_plugin' ); ?></p>
					<div id="rltdpstsplgn_sel"></div>
					<table id="rltdpstsplgn_sets">
						<tr>
							<th>
								<label><?php _e( 'Heading the list of related posts:', 'related_posts_plugin' ); ?>&nbsp;</label>
							</th>
							<td>
								<input class="rltdpstsplgn_text" type="text" name="rltdpstsplgn_options[head]" value="<?php echo $rltdpstsplgn_options['head'] ?>" />
							</td>
						</tr>
						<tr>
							<th>
								<label><?php _e( 'How many posts to display:', 'related_posts_plugin' ); ?>&nbsp;</label>
							</th>
							<td>
								<input id="rltdpstsplgn_number" type="number" name="rltdpstsplgn_options[number_post]" value="<?php echo $rltdpstsplgn_options['number_post'] ?>"/>
							</td>
						</tr>
						<tr>
							<th>
								<label><?php _e( 'Message if do not have related posts:', 'related_posts_plugin' ); ?></label>
							</th>
							<td>
								<input class="rltdpstsplgn_text" type="text" name="rltdpstsplgn_options[no_posts]" value="<?php echo $rltdpstsplgn_options['no_posts'] ?>" />
							</td>
						</tr>
						<tr class="rltdpstsplgn_search_block">
							<th>
								<?php _e( 'Select an option on which will be searched for related words in the posts:', 'related_posts_plugin' ); ?>
							</th>
							<td class="rltdpstsplgn_options">
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
								<span>
									<?php _e( '(If you want to display related posts by meta key you should first check the "Key" in meta box "Related post plugin" in the post that you want to display)', 'related_posts_plugin' ) ?>
								</span>
							</td>
						</tr>
						<tr>
							<th>
								<?php _e( 'Show Related Posts Widget not only in the post (category/tag/index/home page)', 'related_posts_plugin' ); ?>
							</th>
							<td class="rltdpstsplgn_checkbox">
								<label>
									<input class="rltdpstsplgn_text" type="checkbox" name="rltdpstsplgn_options[index_show]" value="1" <?php checked( $rltdpstsplgn_options['index_show'], 1 ); ?> />
								</label>
								<span><?php _e( '(By default related posts will be displayed for the first post in the list of posts.)', 'related_posts_plugin' ) ?></span>
							</td>
						</tr>
					</table>
				</div>
				<input type="hidden" name="rltdpstsplgn_form_submit" value="submit" />
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'related_posts_plugin' ); ?>" />
				</p>
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'rltdpstsplgn_nonce_name' ); ?>
			</form>
			<br />
			<div class="bws-plugin-reviews">
				<div class="bws-plugin-reviews-rate">
					<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'related_posts_plugin' ); ?>: 
					<a href="http://wordpress.org/support/view/plugin-reviews/relevant" target="_blank" title="Relevant - Related Posts Plugin"><?php _e( 'Rate the plugin', 'related_posts_plugin' ); ?></a>
				</div>
				<div class="bws-plugin-reviews-support">
					<?php _e( 'If there is something wrong about it, please contact us', 'related_posts_plugin' ); ?>: 
					<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
				</div>
			</div>
		</div>
	<?php
	}
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
						foreach( $rltdpstsplgn_res as $rltdpstsplgn_title ) {
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
					echo '<h3><a href="' .  get_permalink( get_the_ID() ) . '">';
					the_title();
					echo '</a></h3>';
					echo '</div>';
				}
			wp_reset_query();
			} else {
				echo $rltdpstsplgn_options['no_posts'];
			}
		} else {
			echo $rltdpstsplgn_options['no_posts'];
		}
	}
} /* End of the loop */

/* Uninstall options */
if ( ! function_exists( 'rltdpstsplgn_uninstall' ) ) {
	function rltdpstsplgn_uninstall() {
		delete_option( 'rltdpstsplgn_options' );
		delete_site_option( 'rltdpstsplgn_options' );
	}
}

add_action( 'admin_init', 'rltdpstsplgn_set_options' );
add_action( 'admin_init', 'rltdpstsplgn_plugin_init' );
add_action( 'admin_init', 'rltdpstsplgn_plugin_version_check' );
add_action( 'admin_menu', 'add_rltdpstsplgn_admin_menu' );
add_action( 'admin_enqueue_scripts', 'rltdpstsplgn_admin_style' );
add_action( 'wp_enqueue_scripts', 'rltdpstsplgn_admin_style' );
add_action( 'admin_head', 'rltdpstsplgn_admin_js' );
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
?>