<?php
/*
Plugin Name: Relevant - Related, Featured, Latest, and Popular Posts by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/related-posts/
Description: Add related, featured, latest, and popular posts to your WordPress website. Connect your blog readers with a relevant content.
Author: BestWebSoft
Text Domain: relevant
Domain Path: /languages
Version: 1.4.1
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
*/

/*
	© Copyright 2021  BestWebSoft  ( https://support.bestwebsoft.com )

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

require_once( dirname( __FILE__ ) . '/includes/widgets.php' );

/* Add our own menu */
if ( ! function_exists( 'add_rltdpstsplgn_admin_menu' ) ) {
	function add_rltdpstsplgn_admin_menu() {
		$settings = add_menu_page( __( 'Relevant Posts Settings', 'relevant' ), 'Relevant Posts', 'manage_options', 'related-posts-plugin.php', 'rltdpstsplgn_settings_page', 'none' );
		add_submenu_page( 'related-posts-plugin.php', __( 'Relevant Posts Settings', 'relevant' ), __( 'Settings', 'relevant' ), 'manage_options', 'related-posts-plugin.php', 'rltdpstsplgn_settings_page' );
		add_submenu_page( 'related-posts-plugin.php', 'BWS Panel', 'BWS Panel', 'manage_options', 'rltdpstsplgn-bws-panel', 'bws_add_menu_render' );
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

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $rltdpstsplgn_plugin_info, '4.5' );

		/* tag support */
		rltdpstsplgn_tags_support_all();

		add_image_size( 'popular-post-featured-image', 60, 60, true );
	}
}

if ( ! function_exists( 'rltdpstsplgn_admin_init' ) ) {
	function rltdpstsplgn_admin_init() {
		global $bws_plugin_info, $rltdpstsplgn_plugin_info, $bws_shortcode_list, $pagenow;

		if ( empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array( 'id' => '100', 'version' => $rltdpstsplgn_plugin_info["Version"] );
		}
		/* Call register settings function */
		$admin_pages = array( 'widgets.php', 'plugins.php' );
		if ( in_array( $pagenow, $admin_pages ) || ( isset( $_GET['page'] ) && 'related-posts-plugin.php' == $_GET['page'] ) ) {
			rltdpstsplgn_set_options();
		}

		/* add Relevant to global $bws_shortcode_list */
		$bws_shortcode_list['rltdpstsplgn'] = array( 'name' => 'Relevant Posts', 'js_function' => 'rltdpstsplgn_shortcode_init' );
	}
}

/* Plugin activate */
if ( ! function_exists( 'rltdpstsplgn_plugin_activate' ) ) {
	function rltdpstsplgn_plugin_activate() {
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			register_uninstall_hook( __FILE__, 'rltdpstsplgn_uninstall' );
			restore_current_blog();
		} else {
			register_uninstall_hook( __FILE__, 'rltdpstsplgn_uninstall' );
		}
	}
}

/* Setting options */
if ( ! function_exists( 'rltdpstsplgn_set_options' ) ) {
	function rltdpstsplgn_set_options() {
		global $rltdpstsplgn_options, $rltdpstsplgn_plugin_info;

		if ( ! get_option( 'rltdpstsplgn_options' ) ) {
			$options_default = rltdpstsplgn_get_options_default();
			add_option( 'rltdpstsplgn_options', $options_default );
		}

		$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $rltdpstsplgn_options['plugin_option_version'] ) || $rltdpstsplgn_options['plugin_option_version'] != $rltdpstsplgn_plugin_info["Version"] ) {
			rltdpstsplgn_plugin_activate();

			/**
			 * @deprecated since 1.4.0
			 * @todo remove after 21.04.2021
			 */
			if ( isset( $rltdpstsplgn_options['plugin_option_version'] ) && version_compare( $rltdpstsplgn_options['plugin_option_version'] , '1.4.0', '<' ) ) {
				$rltdpstsplgn_options['featured_block_width_remark'] = ( substr_count( $rltdpstsplgn_options['featured_block_width'], 'px' ) ) ? 'px' : '%';
				$rltdpstsplgn_options['featured_block_width'] = intval( $rltdpstsplgn_options['featured_block_width'] );
				$rltdpstsplgn_options['featured_text_block_width_remark'] = ( substr_count( $rltdpstsplgn_options['featured_text_block_width'], 'px' ) ) ? 'px' : '%';
				$rltdpstsplgn_options['featured_text_block_width'] = intval( $rltdpstsplgn_options['featured_text_block_width'] );
			}
			/* end deprecated */

			$options_default = rltdpstsplgn_get_options_default();
			$rltdpstsplgn_options = array_merge( $options_default, $rltdpstsplgn_options );
			$rltdpstsplgn_options['plugin_option_version'] = $rltdpstsplgn_plugin_info["Version"];

			update_option( 'rltdpstsplgn_options', $rltdpstsplgn_options );
		}
	}
}

if ( ! function_exists( 'rltdpstsplgn_get_options_default' ) ) {
	function rltdpstsplgn_get_options_default() {
		global $rltdpstsplgn_plugin_info;

		if ( empty( $rltdpstsplgn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$rltdpstsplgn_plugin_info = get_plugin_data( __FILE__ );
		}

		$default_options = array(
			/* general options */
			'plugin_option_version'				=> $rltdpstsplgn_plugin_info["Version"],
			'display_settings_notice'			=> 1,
			'suggest_feature_banner'			=> 1,
			/* related posts options */
			'related_display'					=> array(),
			'related_title'						=> __( 'Related Posts', 'relevant' ),
			'related_posts_count'				=> 5,
			'related_criteria'					=> 'category',
			'related_no_posts_message'			=> __( 'No related posts found...', 'relevant' ),
			'related_show_thumbnail'			=> 1,
			'related_use_category'				=> 0,
			'related_show_date'					=> 1,
			'related_show_author'				=> 1,
			'related_show_reading_time'			=> 1,
			'related_show_comments' 			=> 1,
			'related_show_excerpt'				=> 1,
			'related_add_for_page'				=> array(),
			'related_excerpt_length'			=> 10,
			'related_excerpt_more'				=> '...',
			'related_no_preview_img'			=> plugins_url( 'images/no_preview.jpg', __FILE__ ),
			'related_image_height'				=> 80,
			'related_image_width'				=> 80,
			'display_related_posts'				=> 'All',
			/* featured posts options */
			'featured_display'					=> array( 'after' ),
			'featured_block_width'		        => '100',
			'featured_block_width_remark'		=> '%',
			'featured_text_block_width'	        => '960',
			'featured_text_block_width_remark'	=> 'px',
			'featured_posts_count'				=> 1,
			'featured_theme_style'				=> 1,
			'featured_use_category'				=> 0,
			'featured_background_color_block'	=> '#f3f3f3',
			'featured_background_color_text'	=> '#f3f3f3',
			'featured_color_text'				=> '#777b7e',
			'featured_color_header'				=> '#777b7e',
			'featured_color_link'				=> '#777b7e',
			'featured_show_thumbnail'			=> 1,
			'featured_show_date'				=> 1,
			'featured_show_author'				=> 1,
			'featured_show_reading_time'		=> 1,
			'featured_show_comments'			=> 1,
			'featured_show_excerpt'				=> 1,
			'featured_excerpt_length'			=> 10,
			'featured_excerpt_more'				=> '...',
			'featured_no_preview_img'			=> plugins_url( 'images/no_preview.jpg', __FILE__ ),
			'featured_image_height'				=> 80,
			'featured_image_width'				=> 80,
			'display_featured_posts'			=> 'All',
			/* Latest posts options */
			'latest_display'					=> array(),
			'latest_title'						=> __( 'Latest Posts', 'relevant' ),
			'latest_posts_count'				=> 3,
			'latest_excerpt_length'				=> 10,
			'latest_excerpt_more'				=> '...',
			'latest_no_preview_img'				=> plugins_url( 'images/no_preview.jpg', __FILE__ ),
			'latest_show_date'					=> 1,
			'latest_show_author'				=> 1,
			'latest_show_reading_time'			=> 1,
			'latest_show_comments'				=> 1,
			'latest_show_thumbnail'				=> 1,
			'latest_use_category'			    => 0,
			'latest_show_excerpt'				=> 1,
			'latest_image_height'				=> 80,
			'latest_image_width'				=> 80,
			/* Popular posts options */
			'popular_display'					=> array(),
			'popular_title'						=> __( 'Popular Posts', 'relevant' ),
			'popular_posts_count'				=> 5,
			'popular_excerpt_length'			=> 10,
			'popular_excerpt_more'				=> '...',
			'popular_no_preview_img'			=> plugins_url( 'images/no_preview.jpg', __FILE__ ),
			'popular_order_by'					=> 'comment_count',
			'popular_show_views'				=> 1,
			'popular_show_excerpt'				=> 1,
			'popular_show_date'					=> 1,
			'popular_show_author'				=> 1,
			'popular_show_thumbnail'			=> 1,
			'popular_show_reading_time'			=> 1,
			'popular_show_comments'				=> 1,
			'popular_use_category'				=> 0,
			'popular_min_posts_count'			=> 0,
			'popular_image_height'				=> 80,
			'popular_image_width'				=> 80,
			'display_popular_posts'				=> 'All',
		);

		return $default_options;
	}
}

/* Options of settings page */
if ( ! function_exists( 'rltdpstsplgn_settings_page' ) ) {
	function rltdpstsplgn_settings_page() {
		if ( ! class_exists( 'Bws_Settings_Tabs' ) )
			require_once( dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php' );
		require_once( dirname( __FILE__ ) . '/includes/class-rltdpstsplgn-settings.php' );
		$page = new Rltdpstsplgn_Settings_Tabs( plugin_basename( __FILE__ ) ); 
		if ( method_exists( $page, 'add_request_feature' ) ) {
			$page->add_request_feature();
		} ?>
		<div class="wrap">
			<h1>Relevant Posts <?php _e( 'Settings', 'relevant' ); ?></h1>
			<?php $page->display_content(); ?>
		</div>
	<?php }
}

/* Add meta box "meta_key" for posts */
if ( ! function_exists( 'rltdpstsplgn_add_box' ) ) {
	function rltdpstsplgn_add_box() {
		global $rltdpstsplgn_options;

		if ( empty( $rltdpstsplgn_options ) ) {
			rltdpstsplgn_set_options();
		}
		add_meta_box( 'rltdpstsplgn_sectionid', __( 'Related Posts', 'relevant' ), 'rltdpstsplgn_meta_box', 'post' );

		if ( in_array( 'meta', $rltdpstsplgn_options['related_add_for_page'] ) ) {
			add_meta_box( 'rltdpstsplgn_sectionid', __( 'Related Posts', 'relevant' ), 'rltdpstsplgn_meta_box', 'page' );
		}
		/*
		 * Add a box to the main column on the Post and Page edit screens.
		 */
		$screens = array( 'post', 'page' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'showonfeaturedpost',
				__( 'Featured Post', 'relevant' ),
				'rltdpstsplgn_featured_post_inner_custom_box',
				$screen
			);
		}
	}
}

/* Create meta box */
if ( ! function_exists( 'rltdpstsplgn_meta_box' ) ) {
	function rltdpstsplgn_meta_box( $post ) {
		$meta_data = get_post_meta( $post->ID, 'rltdpstsplgn_meta_key', 1 ); ?>
		<p><?php _e( 'Check "Key" if you want to display this post with Related Posts sorted by Meta Key:', 'relevant' ); ?></p>
		<p>
			<label><input type="radio" name="extra[rltdpstsplgn_meta_key]" value="key" <?php checked( $meta_data, 'key' ); ?> /><?php _e( 'Key', 'relevant' ); ?></label>
			<label><input type="radio" name="extra[rltdpstsplgn_meta_key]" value="" <?php checked( $meta_data, '' ); ?> /><?php _e( 'None', 'relevant' ); ?></label>
		</p>
	<?php }
}

/* Save meta_key */
if ( ! function_exists( 'rltdpstsplgn_save_postdata' ) ) {
	function rltdpstsplgn_save_postdata( $post_id ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		/* If this is an autosave, our form has not been submitted, so we don't want to do anything. */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		/* Related Posts */
		if ( isset( $_POST['extra'] ) && is_array( $_POST['extra'] ) ) {
			foreach ( $_POST['extra'] as $key => $value ) {
				if ( empty( $value ) ) {
					delete_post_meta( $post_id, $key ); /* Delete meta_key if value is empty */
				} else {
					update_post_meta( $post_id, $key, $value ); /* Add meta_key in wp_postmeta */
				}
			}
		}
		/* Featured Post */
		if ( ! isset( $_POST['rltdpstsplgn_featured_post_inner_custom_box_nonce'] ) ) {
			return $post_id;
		} else {
			$nonce = $_POST['rltdpstsplgn_featured_post_inner_custom_box_nonce'];
			/* Verify that the nonce is valid. */
			if ( ! wp_verify_nonce( $nonce, 'rltdpstsplgn_featured_post_inner_custom_box' ) ) {
				return $post_id;
			}
		}
		if ( isset( $_POST['rltdpstsplgn_featured_post_inner_custom_box_nonce' ] ) ) {
			$featured_post_checkbox = isset( $_POST['rltdpstsplgn_featured_post_checkbox'] ) ? 1 : 0;
			/* Update the meta field in the database. */
			update_post_meta( $post_id, '_ftrdpsts_add_to_featured_post', $featured_post_checkbox );
		}
	}
}

/**
* Registers the taxonomy terms for the post type
*/
if ( ! function_exists( 'rltdpstsplgn_tags_support_all' ) ) {
	function rltdpstsplgn_tags_support_all() {
		global $rltdpstsplgn_options;

		if ( empty( $rltdpstsplgn_options ) ) {
			rltdpstsplgn_set_options();
		}
		if ( in_array( 'tags', $rltdpstsplgn_options['related_add_for_page'] ) ) {
			register_taxonomy_for_object_type( 'post_tag', 'page' );
		}
		if ( in_array( 'category', $rltdpstsplgn_options['related_add_for_page'] ) ) {
			register_taxonomy_for_object_type( 'category', 'page' );
		}
	}
}

/**
* ensure all tags are included in queries
*/
if ( ! function_exists( 'rltdpstsplgn_tags_support_query' ) ) {
	function rltdpstsplgn_tags_support_query( $wp_query ) {
		global $rltdpstsplgn_options;
		if ( ! is_admin() ) {
			if ( empty( $rltdpstsplgn_options ) ) {
				rltdpstsplgn_set_options();
			}
			if ( in_array( 'tags', $rltdpstsplgn_options['related_add_for_page'] ) ) {
				if ( $wp_query->get( 'tag' ) ) {
					$wp_query->set( 'post_type', 'any' );
				}
			}
			if ( in_array( 'category', $rltdpstsplgn_options['related_add_for_page'] ) ) {
				if ( $wp_query->get( 'category_name' ) || $wp_query->get( 'cat' ) ) {
					$wp_query->set( 'post_type', 'any' );
				}
			}
		}
	}
}

if ( ! function_exists( 'rltdpstsplgn_loop_start' ) ) {
	function rltdpstsplgn_loop_start( $query ) {
		global $wp_query, $rltdpstsplgn_is_main_query;
		if ( is_main_query() && $query === $wp_query ) {
			$rltdpstsplgn_is_main_query = true;
		}
	}
}

if ( ! function_exists( 'rltdpstsplgn_loop_end' ) ) {
	function rltdpstsplgn_loop_end( $query ) {
		global $rltdpstsplgn_is_main_query;
		$rltdpstsplgn_is_main_query = false;
	}
}

/**
 * Display Block with Featured Post/Related Posts/Latest Posts/Popular Posts
 */
if ( ! function_exists( 'rltdpstsplgn_display_blocks' ) ) {
	function rltdpstsplgn_display_blocks( $content ) {
		global $rltdpstsplgn_options, $rltdpstsplgn_is_main_query, $is_rltdpstsplgn_query;

		if ( is_feed() || ! empty( $is_rltdpstsplgn_query ) ) {
			return $content;
		}

		if ( ( is_single() || is_page() ) && $rltdpstsplgn_is_main_query ) {

			$before_content = $after_content = '';

			/* Related Post */
			if ( ! empty( $rltdpstsplgn_options['related_display'] ) ) {
				$related_block = rltdpstsplgn_related_posts_block();
				if ( in_array( 'before', $rltdpstsplgn_options['related_display'] ) ) {
					$before_content .= $related_block;
				}
				if ( in_array( 'after', $rltdpstsplgn_options['related_display'] ) ) {
					$after_content .= $related_block;
				}
			}
			/* Featured Post */
			if ( ! empty( $rltdpstsplgn_options['featured_display'] ) ) {
				$featured_block = rltdpstsplgn_featured_posts( true );
				if ( in_array( 'before', $rltdpstsplgn_options['featured_display'] ) ) {
					$before_content .= $featured_block;
				}
				if ( in_array( 'after', $rltdpstsplgn_options['featured_display'] ) ) {
					$after_content .= $featured_block;
				}
			}
			/* Latest Post */
			if ( ! empty( $rltdpstsplgn_options['latest_display'] ) ) {
				$latest_block = rltdpstsplgn_latest_posts_block();
				if ( ! empty( $latest_block ) ) {
					$latest_block = '<h4 class="rltdpstsplgn-latest-title">' . $rltdpstsplgn_options['latest_title'] . '</h4>' . $latest_block;
				}
				if ( in_array( 'before', $rltdpstsplgn_options['latest_display'] ) ) {
					$before_content .= $latest_block;
				}
				if ( in_array( 'after', $rltdpstsplgn_options['latest_display'] ) ) {
					$after_content .= $latest_block;
				}
			}
			/* Popular Post */
			if ( ! empty( $rltdpstsplgn_options['popular_display'] ) ) {
				$popular_block = rltdpstsplgn_popular_posts_block();
				if ( ! empty( $popular_block ) ) {
					$popular_block = '<h4 class="rltdpstsplgn-popular-title">' . $rltdpstsplgn_options['popular_title'] . '</h4>' . $popular_block;
				}
				if ( in_array( 'before', $rltdpstsplgn_options['popular_display'] ) ) {
					$before_content .= $popular_block;
				}
				if ( in_array( 'after', $rltdpstsplgn_options['popular_display'] ) ) {
					$after_content .= $popular_block;
				}
			}
			return $before_content . $content . $after_content;
		}
		return $content;
	}
}

/**
 * Function render view Plugin.
 * @param $slug string, $post_title_tag string, $flag bool, $number int
 */
if ( ! function_exists( 'rltdpstsplgn_render_view' ) ) {
	function rltdpstsplgn_render_view( $slug = '', $post_title_tag = 'h3', $flag = false, $number = 0 ) {
		global $rltdpstsplgn_options, $post; ?>
		<article class="post type-post format-standard">
			<header class="entry-header">
				<?php echo "<{$post_title_tag} class=\"rltdpstsplgn_posts_title\">"; ?>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php echo "</{$post_title_tag}>";
					if ( $rltdpstsplgn_options[ $slug . '_show_date' ] || $rltdpstsplgn_options[ $slug . '_show_author' ] || $rltdpstsplgn_options[ $slug . '_show_comments' ] || $rltdpstsplgn_options[ $slug . '_show_reading_time' ] || ( isset( $rltdpstsplgn_options[ $slug . '_show_views' ] ) && $rltdpstsplgn_options[ $slug . '_show_views' ] ) ) { ?>
						<div class="entry-meta">
							<?php if ( 1 == $rltdpstsplgn_options[ $slug . '_show_date' ] ) { ?>
								<span class="rltdpstsplgn_date entry-date">
									<?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'relevant' ); ?>
								</span>
							<?php }
								if ( 1 == $rltdpstsplgn_options[ $slug . '_show_author' ] ) { ?>
									<span class="rltdpstsplgn-author"><?php _e( 'by', 'relevant' ) ?>
										<span class="author vcard">
											<a class="url fn n" rel="author" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a>
										</span>
									</span>
								<?php }
								if ( 1 == $rltdpstsplgn_options[ $slug . '_show_reading_time' ] ) {
									$word	= str_word_count( strip_tags( get_the_content() ) );
									$min	= floor( $word / 200 );
									$sec	= floor( $word % 200 / ( 200 / 60 ) ); ?>
									<span class="rltdpstsplgn-reading-time">
											<?php if ( 0 == $min && 30 >= $sec ) {
												echo __( 'less than 1 min read', 'relevant' );
											} elseif ( 60 < $min ) {
												echo __( 'more than 1 hour read', 'relevant' );
											} else {
												if ( 0 != $sec ) {
													$min ++;
												}
												printf( __( '%s min read', 'relevant' ), $min );
											} ?>
										</span>
								<?php }
								if ( 1 == $rltdpstsplgn_options[ $slug . '_show_comments' ] ) { ?>
									<span class="rltdpstsplgn-comments-count">
										<?php comments_number( __( 'No comments', 'relevant' ), __( '1 Comment', 'relevant' ), __( '% Comments', 'relevant' ) ); ?>
									</span>
								<?php }
								if ( 'popular' == $slug && 1 == $rltdpstsplgn_options[ $slug . '_show_views'] ) {
									$views_count = get_post_meta( $post->ID, 'pplrpsts_post_views_count' );
									$views_count = $views_count ? $views_count[0] : 0; ?>
									<span class="rltdpstsplgn-post-count"><?php printf( _n( '%s view', '%s views', $views_count, 'relevant' ), $views_count ); ?></span>
								<?php } ?>
						</div><!-- .entry-meta -->
					<?php } ?>
			</header>
			<?php if ( $rltdpstsplgn_options[ $slug . '_show_thumbnail' ] || $rltdpstsplgn_options[ $slug . '_show_excerpt' ] ) { ?>
				<div class="entry-content">
					<?php if ( 1 == $rltdpstsplgn_options[ $slug . '_show_thumbnail'] ) {

						$number     = ( $flag ) ? $number : '';
						$thumb_size = ( $flag ) ? 'thumb_size_widget_' : 'thumb_size_';
						$size       = rltdpstsplgn_get_image_sizes( $thumb_size . $slug . $number );

						if ( ! has_post_thumbnail() && ! empty( $rltdpstsplgn_options[ $slug . '_no_preview_img' ] ) ) {
							$format = '<img class="attachment-thumbnail wp-post-image" width="%s" height="%s" src="%s"/>';
							$thumbnail = sprintf(
                                $format,
                                $size['width'],
                                $size['height'],
                                $rltdpstsplgn_options[ $slug . '_no_preview_img' ]
                            );
                        } else {
							$thumbnail = get_the_post_thumbnail( $post->ID, array( $size['width'], $size['height'] ) );
                        } ?>
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                            <?php echo $thumbnail; ?>
                        </a>
					<?php }
					if ( 1 == $rltdpstsplgn_options[ $slug . '_show_excerpt' ] ) {
						the_excerpt();
					} ?>
					<div class="clear"></div>
				</div><!-- .entry-content -->
			<?php } ?>
		</article><!-- .post -->
	<?php }
}

if ( ! function_exists( 'rltdpstsplgn_add_thumb_custom_size' ) ) {
	function rltdpstsplgn_add_thumb_custom_size() {
		add_theme_support( 'post-thumbnails' );

		$rltdpstsplgn_options = get_option( 'widget_pplrpsts_popular_posts_widget' );
		foreach ( ( array ) $rltdpstsplgn_options as $key => $value ) {
			if ( is_int( $key ) && isset( $value['width'] ) && isset( $value['height'] ) ) {
				add_image_size( 'thumb_size_widget_popular' . $key, $value['width'], $value['height'], array( 'center', 'center' ) );
			}
		}
		$rltdpstsplgn_options = get_option( 'widget_ltstpsts_latest_posts_widget' );
		foreach ( ( array ) $rltdpstsplgn_options as $key => $value ) {
			if ( is_int( $key ) && isset( $value['width'] ) && isset( $value['height'] ) ) {
				add_image_size( 'thumb_size_widget_latest' . $key, $value['width'], $value['height'], array( 'center', 'center' ) );
			}
		}
		$rltdpstsplgn_options = get_option( 'widget_rltdpstsplgnwidget' );
		foreach ( ( array ) $rltdpstsplgn_options as $key => $value ) {
			if ( is_int( $key ) && isset( $value['width'] ) && isset( $value['height'] ) ) {
				add_image_size( 'thumb_size_widget_related' . $key, $value['width'], $value['height'], array( 'center', 'center' ) );
			}
		}

		if ( ! get_option( 'rltdpstsplgn_options' ) ) {
			rltdpstsplgn_set_options();
		}
		$rltdpstsplgn_options = get_option( 'rltdpstsplgn_options' );
		add_image_size( 'thumb_size_popular', $rltdpstsplgn_options['popular_image_width'], $rltdpstsplgn_options['popular_image_height'], array( 'center', 'center' ) );
		add_image_size( 'thumb_size_latest', $rltdpstsplgn_options['latest_image_width'], $rltdpstsplgn_options['latest_image_height'], array( 'center', 'center' ) );
		add_image_size( 'thumb_size_related', $rltdpstsplgn_options['related_image_width'], $rltdpstsplgn_options['related_image_height'], array( 'center', 'center' ) );
		add_image_size( 'thumb_size_featured', $rltdpstsplgn_options['featured_image_width'], $rltdpstsplgn_options['featured_image_height'], array( 'center', 'center' ) );
	}
}
if ( ! function_exists( 'rltdpstsplgn_get_image_sizes' ) ) {
	function rltdpstsplgn_get_image_sizes( $size ) {
		global $_wp_additional_image_sizes;
		$sizes = array();
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = ( bool ) get_option( "{$_size}_crop" );
			}
			if ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		return $sizes[ $size ];
	}
}

if ( ! function_exists( 'rltdpstsplgn_featured_get_the_excerpt' ) ) {
	function rltdpstsplgn_featured_get_the_excerpt( $content ) {
		$charlength			= 100;
		$content			= wp_strip_all_tags( $content );
		if ( strlen( $content ) > $charlength ) {
			$subex			= substr( $content, 0, $charlength-5 );
			$exwords		= explode( " ", $subex );
			$excut			= - ( strlen( $exwords [ count( $exwords ) - 1 ] ) );
			$new_content	= ( $excut < 0 ) ? substr( $subex, 0, $excut ) : $subex;
			$new_content	.= "...";
			return $new_content;
		} else {
			return $content;
		}
	}
}

/* Show shortcode Related Posts */
if ( ! function_exists( 'rltdpstsplgn_related_posts_output' ) ) {
	function rltdpstsplgn_related_posts_output() {
		global $rltdpstsplgn_options;
		if ( empty( $rltdpstsplgn_options ) ) {
			rltdpstsplgn_set_options();
		}
		$return = rltdpstsplgn_related_posts_block();
		if ( ! empty( $return ) ) {
			$return = '<h4 class="rltdpstsplgn-related-title">' . $rltdpstsplgn_options['related_title'] . '</h4>' . $return;
		}
		return '<div class="rltdpstsplgn-related-post-block">' . $return . '</div>';
	}
}

if ( ! function_exists( 'rltdpstsplgn_related_posts_block' ) ) {
	function rltdpstsplgn_related_posts_block( $post_title_tag = 'h3', $flag = false, $number = 0 ) {
		global $post, $wpdb, $args, $wp_query, $rltdpstsplgn_options, $is_rltdpstsplgn_query;

		if ( empty( $rltdpstsplgn_options ) ) {
			rltdpstsplgn_set_options();
		}
		if ( is_single() ) {
			$post_ID = $post->ID;
		} elseif ( ! is_single() && isset( $wp_query->posts[0] ) ) {
			$post_ID = $wp_query->posts[0]->ID;
		}
		$html = '';

		if ( isset( $post_ID ) ) {
			/* Creating array with search criteria related post/page */
            $criterias['post'][] = $rltdpstsplgn_options['related_criteria'];
			$criterias['page'] = $rltdpstsplgn_options['related_add_for_page'];
			$criterias = array_merge( $criterias['post'] ,$criterias['page'] );
			$criterias = array_unique( $criterias );
			$related_query_arr = array();

			foreach ( $criterias as $criteria ) {
                if ( 'category' == $criteria && ! empty( $post ) ) {
                    $categories = get_the_category( $post_ID );

                    if ( $categories ) {
                        $category_ids = array();
                        foreach ( $categories as $individual_category ) {
                            $category_ids[] = $individual_category->term_id;
                        }
                        $args = array(
                            'category__in'			=> $category_ids,
                            'post__not_in'			=> array( $post_ID ),
                            'showposts'				=> $rltdpstsplgn_options['related_posts_count'],
                            'ignore_sticky_posts'	=> 1,
                        );
                    }
                } elseif ( 'meta' == $criteria ) { /* Sort by meta key */
                    $args = array(
                        'meta_key'				=> 'rltdpstsplgn_meta_key',
                        'post__not_in'			=> array( $post_ID ),
                        'showposts'				=> $rltdpstsplgn_options['related_posts_count'],
                        'ignore_sticky_posts'	=> 1,
                    );
                } elseif ( 'tags' == $criteria && ! empty( $post ) ) { /* Sort by tag */
                    $tags = wp_get_post_tags( $post_ID );
                    if ( $tags ) {
                        $tag_ids = array();
                        foreach ( $tags as $individual_tag ) {
                            $tag_ids[] = $individual_tag->term_id;
                        }
                        $args = array(
                            'tag__in'				=> $tag_ids,
                            'post__not_in'			=> array( $post_ID ),
                            'showposts'				=> $rltdpstsplgn_options['related_posts_count'],
                            'ignore_sticky_posts'	=> 1
                        );
                    }
                } elseif ( 'title' == $criteria && ! empty( $post ) ) { /* Sort by title */
                    $title_prepare = get_the_title( $post_ID );
                    if ( '' != $title_prepare ) {
                        $all_titles = explode( ' ', $title_prepare );
                        $title_ids = array();

                        foreach ( $all_titles as $key ) {
                            $results = $wpdb->get_col( "SELECT `ID` FROM $wpdb->posts WHERE `post_title` LIKE '%$key%' AND `post_status` = 'publish' AND `ID` != $post_ID" );

                            if ( $results ) {
                                $title_ids = array_merge( $title_ids, $results );
                            }
                        }
                        if ( ! empty( $title_ids ) ) {
                            $args = array(
                                'post__in'				=> $title_ids,
                                'post__not_in'			=> array( $post_ID ),
                                'showposts'				=> $rltdpstsplgn_options['related_posts_count'],
                                'ignore_sticky_posts'	=> 1,
                            );
                        }
                    }
                }
                switch ( $rltdpstsplgn_options['display_related_posts'] ) {
                    case '1 month ago':
                        $date_query = array(
                            array(
                                'after'     => '1 month ago',
                                'inclusive' => true,
                            ),
                        );
                        $args['date_query'] = $date_query;
                        break;
                    case '3 month ago':
                        $date_query = array(
                            array(
                                'after'     => '3 month ago',
                                'inclusive' => true,
                            ),
                        );
                        $args['date_query'] = $date_query;
                        break;
                    case '6 month ago':
                        $date_query = array(
                            array(
                                'after'     => '6 month ago',
                                'inclusive' => true,
                            ),
                        );
                        $args['date_query'] = $date_query;
                        break;

                    default:
                        break;
                }

                if ( $rltdpstsplgn_options['related_criteria'] == $criteria && in_array( $criteria, $rltdpstsplgn_options['related_add_for_page'] ) ) {
	                $args['post_type'] = array( 'post', 'page' );
                } elseif ( $rltdpstsplgn_options['related_criteria'] == $criteria ) {
					$args['post_type'] = 'post';
				} elseif ( in_array( $criteria, $rltdpstsplgn_options['related_add_for_page'] ) ) {
	                $args['post_type'] = 'page';
                }

                /* Exclude current post from the list */
				if ( is_singular() && isset( $post->ID ) ) {
					$query_args['post__not_in'] = array( $post->ID );
				}

				if ( ! empty ( $rltdpstsplgn_options['related_use_category'] ) && ( is_category() || is_singular() ) ) { 
					$category = rltdpstsplgn_get_category_for_posts_block();
					if ( isset( $category ) ) {
						$args['category__in'] = $category;
						
					}
				}

				if ( $args != NULL ) {
					$related_query_arr[] = new WP_Query( $args );
				}
            }

			$related_query = new WP_Query();
			$related_query->posts = $unique_posts = array();
			/* Merging results multiple WP_Query */
			foreach ( $related_query_arr as $item ) {
				$related_query->posts = array_merge( $related_query->posts, $item->posts );
            }
			$related_query->post_count = count( $related_query->posts );

			/* The Loop */ 
			if ( ! empty ( $related_query ) ) {
				if ( $related_query->have_posts() ) {
					ob_start();
					$is_rltdpstsplgn_query = 1;
					add_filter( 'excerpt_length', 'rltdpstsplgn_related_posts_excerpt_length' );
					add_filter( 'excerpt_more', 'rltdpstsplgn_related_posts_excerpt_more' );
					if ( ! $flag ) {
						if ( ! empty( $rltdpstsplgn_options['related_title'] ) ) {
							echo '<h4 class="rltdpstsplgn-latest-title">' . $rltdpstsplgn_options['related_title'] . '</h4>';
						}
					} ?>
					<div class="rltdpstsplgn-related-posts">
						<?php while ( $related_query->have_posts() ) {
							$related_query->the_post();
							/* Check for duplicate posts */
							if ( ! empty ( $unique_posts[ $post->ID ] ) ) {
							    continue;
                            }
							/* Forming array with unique posts id */
							$unique_posts[ $post->ID ] = true; ?>
							<div class="clear"></div>
							<?php rltdpstsplgn_render_view( 'related', $post_title_tag, $flag, $number );
						} ?>
					</div><!-- .rltdpstsplgn-related-posts -->
				<?php remove_filter( 'excerpt_length', 'rltdpstsplgn_related_posts_excerpt_length' );
					remove_filter( 'excerpt_more', 'rltdpstsplgn_related_posts_excerpt_more' );
					$is_rltdpstsplgn_query = 0;
					wp_reset_postdata();
					$html = ob_get_contents();
					ob_end_clean();
				}
			}
		}
		if( '' == $html ) {
			$html .= '<p>' . strip_tags( $rltdpstsplgn_options['related_no_posts_message'] ) . '</p>';
		}
		
		if ( ! function_exists ( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active( 'custom-fields-search-pro/custom-fields-search-pro.php' ) || is_plugin_active( 'custom-fields-search/custom-fields-search.php' ) ) {
			$cstmfldssrch_is_active = true;
			remove_filter( 'posts_join', 'cstmfldssrch_join' );
			remove_filter( 'posts_where', 'cstmfldssrch_request' );
		}

		return $html;
	}
}

if ( ! function_exists( 'rltdpstsplgn_get_category_for_posts_block' ) ) {
	function rltdpstsplgn_get_category_for_posts_block() {
		global $post;
		/* We get post category */
		$cat_ids = array();
		if ( is_singular() && isset( $post->ID ) ) {
			$categories = get_the_category( $post->ID );
			if ( ! empty( $categories ) ) {
				foreach( $categories as $category )
					$cat_ids[] = $category->cat_ID;
			}
		} elseif ( is_category() ) {
			$category = get_category( get_query_var( 'cat' ) );
			$cat_ids[] = $category->cat_ID;
		}
		if ( ! empty( $cat_ids ) ) {
			return $cat_ids;
		}
	}
}

if ( ! function_exists( 'rltdpstsplgn_popular_posts_block' ) ) {
	function rltdpstsplgn_popular_posts_block( $post_title_tag = 'h3', $flag = false, $number = 0 ) {
		global $post, $rltdpstsplgn_options, $is_rltdpstsplgn_query;

		if ( 'comment_count' == $rltdpstsplgn_options['popular_order_by'] ) {
			$query_args = array(
				'post_type'				=> 'post',
				'post_status'			=> 'publish',
				'meta_key'				=> 'pplrpsts_post_views_count',
				'orderby'				=> 'comment_count',
				'order'					=> 'DESC',
				'posts_per_page'		=> $rltdpstsplgn_options['popular_posts_count'],
				'ignore_sticky_posts'	=> 1,
			);
		} else {
			$query_args = array(
				'post_type'				=> 'post',
				'post_status'			=> 'publish',
				'meta_key'				=> 'pplrpsts_post_views_count',
				'orderby'				=> 'meta_value_num',
				'order'					=> 'DESC',
				'posts_per_page'		=> $rltdpstsplgn_options['popular_posts_count'],
				'ignore_sticky_posts'	=> 1
			);
		}
		switch ( $rltdpstsplgn_options['display_popular_posts'] ) {
			case '1 month ago':
				$date_query = array(
					array(
						'after'     => '1 month ago',
						'inclusive' => true,
					),
				);
				$query_args['date_query'] = $date_query;
				break;
			case '3 month ago':
				$date_query = array(
					array(
						'after'     => '3 month ago',
						'inclusive' => true,
					),
				);
				$query_args['date_query'] = $date_query;
				break;
			case '6 month ago':
				$date_query = array(
					array(
						'after'     => '6 month ago',
						'inclusive' => true,
					),
				);
				$query_args['date_query'] = $date_query;
				break;
			
			default:
				break;
		}
		$the_query = new WP_Query( $query_args );
		/* Exclude current post from the list */
		if ( is_singular() && isset( $post->ID ) ) {
			$query_args['post__not_in'] = array( $post->ID );
		}

		if ( ! empty( $rltdpstsplgn_options['popular_use_category'] ) && ( is_category() || is_singular() ) ) {
			$category = rltdpstsplgn_get_category_for_posts_block();
			if ( isset( $category ) ) {
				$query_args['category__in'] = $category;
			}
		}
		if ( ! function_exists ( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active( 'custom-fields-search-pro/custom-fields-search-pro.php' ) || is_plugin_active( 'custom-fields-search/custom-fields-search.php' ) ) {
			$cstmfldssrch_is_active = true;
			remove_filter( 'posts_join', 'cstmfldssrch_join' );
			remove_filter( 'posts_where', 'cstmfldssrch_request' );
		}

		$the_query = new WP_Query( $query_args );

		/* The Loop */
		ob_start();
		if ( $the_query->have_posts() && absint( $the_query->found_posts ) >= absint( $rltdpstsplgn_options['popular_min_posts_count'] ) ) {
			$is_rltdpstsplgn_query = 1;
			add_filter( 'excerpt_length', 'rltdpstsplgn_popular_posts_excerpt_length' );
			add_filter( 'excerpt_more', 'rltdpstsplgn_popular_posts_excerpt_more' ); ?>
			<div class="rltdpstsplgn-popular-posts">
				<?php while ( $the_query->have_posts() ) {
					$the_query->the_post(); ?>
					<div class="clear"></div>
				<?php rltdpstsplgn_render_view( 'popular', $post_title_tag, $flag, $number ); } ?>
			</div><!-- .pplrpsts-popular-posts -->
			<?php	remove_filter( 'excerpt_length', 'rltdpstsplgn_popular_posts_excerpt_length' );
					remove_filter( 'excerpt_more', 'rltdpstsplgn_popular_posts_excerpt_more' );
					$is_rltdpstsplgn_query = 0;
		}
		wp_reset_postdata();
		$output_string = ob_get_contents();
		ob_end_clean();

		/* Restore original Post Data */
		if ( isset( $cstmfldssrch_is_active ) ) {
			add_filter( 'posts_join', 'cstmfldssrch_join' );
			add_filter( 'posts_where', 'cstmfldssrch_request' );
		}
		return $output_string;
	}
}

if ( ! function_exists( 'rltdpstsplgn_latest_posts_block' ) ) {
	function rltdpstsplgn_latest_posts_block( $post_title_tag = 'h3', $flag = false, $number = 0, $category = 0 ) {
		global $post, $rltdpstsplgn_options, $is_rltdpstsplgn_query;

		$query_args = array(
			'post_type'				=> 'post',
			'post_status'			=> 'publish',
			'orderby'				=> 'date',
			'order'					=> 'DESC',
			'posts_per_page'		=> $rltdpstsplgn_options['latest_posts_count'],
			'ignore_sticky_posts'	=> 1
		);
		/* Exclude current post from the list */
		if ( is_singular() && isset( $post->ID ) ) {
			$query_args['post__not_in'] = array( $post->ID );
		}

		if ( ! empty( $category ) && empty( $rltdpstsplgn_options['latest_use_category'] ) ) {
			$query_args['cat'] = $category;
		} elseif ( ! empty( $rltdpstsplgn_options['latest_use_category'] ) && ( is_category() || is_singular() ) ) {
			$category = rltdpstsplgn_get_category_for_posts_block();
			if ( isset( $category ) ) {
				$query_args['category__in'] = $category;
			}
		}

		$second_query = new WP_Query( $query_args );

		ob_start();
		/* The Loop */
		if ( $second_query->have_posts() ) {
			$is_rltdpstsplgn_query = 1;
			add_filter( 'excerpt_length', 'rltdpstsplgn_latest_posts_excerpt_length' );
			add_filter( 'excerpt_more', 'rltdpstsplgn_latest_posts_excerpt_more' ); ?>
			<div class="rltdpstsplgn-latest-posts">
				<?php while ( $second_query->have_posts() ) {
					$second_query->the_post(); ?>
					<div class="clear"></div>
				<?php rltdpstsplgn_render_view( 'latest', $post_title_tag, $flag, $number ); } ?>
			</div><!-- .ltstpsts-latest-posts -->
			<?php	remove_filter( 'excerpt_length', 'rltdpstsplgn_latest_posts_excerpt_length' );
					remove_filter( 'excerpt_more', 'rltdpstsplgn_latest_posts_excerpt_more' );
					$is_rltdpstsplgn_query = 0;
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		$output_string = ob_get_contents();
		ob_end_clean();

		return $output_string;
	}
}

/**
 * Display Featured Post shortcode
 * @return Featured Post block
 */
if ( ! function_exists( 'rltdpstsplgn_featured_posts_shortcode' ) ) {
	function rltdpstsplgn_featured_posts_shortcode() {
		return rltdpstsplgn_featured_posts( true );
	}
}

/**
 * Display Popular Post shortcode
 * @return Popular Post block
 */
if ( ! function_exists( 'rltdpstsplgn_popular_posts_output' ) ) {
	function rltdpstsplgn_popular_posts_output() {
		global $rltdpstsplgn_options;

		if ( empty( $rltdpstsplgn_options ) ) {
			rltdpstsplgn_set_options();
		}
		$return = rltdpstsplgn_popular_posts_block();
		if ( ! empty( $return ) ) {
			$return = '<h4 class="rltdpstsplgn-popular-title">' . $rltdpstsplgn_options['popular_title'] . '</h4>' . $return;
		}
		return '<div class="rltdpstsplgn-popular-post-block">' . $return . '</div>';
	}
}

/**
 * Display Latest Post shortcode
 * @return Latest Post block
 */
if ( ! function_exists( 'rltdpstsplgn_latest_posts_output' ) ) {
	function rltdpstsplgn_latest_posts_output() {
		global $rltdpstsplgn_options;

		if ( empty( $rltdpstsplgn_options ) ) {
			rltdpstsplgn_set_options();
		}
		$return = rltdpstsplgn_latest_posts_block();
		if ( ! empty( $return ) ) {
			$return = '<h4 class="rltdpstsplgn-latest-title">' . $rltdpstsplgn_options['latest_title'] . '</h4>' . $return;
		}
		return '<div class="rltdpstsplgn-latest-post-block">' . $return . '</div>';
	}
}

/**
 * Display Featured Post
 * @return echo Featured Post block
 */
if ( ! function_exists( 'rltdpstsplgn_featured_posts' ) ) {
	function rltdpstsplgn_featured_posts( $return = false ) {
		global $rltdpstsplgn_options, $is_rltdpstsplgn_query, $post;

		if ( empty( $rltdpstsplgn_options ) )
			rltdpstsplgn_set_options();

		$post__not_in = array();
		if ( isset( $post->ID ) ) {
			$post__not_in[] = $post->ID;
		}
		$query_args = array(
			'post_type'				=> array( 'post', 'page' ),
			'meta_key'				=> '_ftrdpsts_add_to_featured_post',
			'meta_value'			=> '1',
			'posts_per_page'		=> $rltdpstsplgn_options['featured_posts_count'],
			'orderby'				=> 'rand',
			'ignore_sticky_posts'	=> 1,
			'post__not_in'			=> $post__not_in,
		);
		switch ( $rltdpstsplgn_options['display_featured_posts'] ) {
			case '1 month ago':
				$date_query = array(
					array(
						'after'     => '1 month ago',
						'inclusive' => true,
					),
				);
				$query_args['date_query'] = $date_query;
				break;
			case '3 month ago':
				$date_query = array(
					array(
						'after'     => '3 month ago',
						'inclusive' => true,
					),
				);
				$query_args['date_query'] = $date_query;
				break;
			case '6 month ago':
				$date_query = array(
					array(
						'after'     => '6 month ago',
						'inclusive' => true,
					),
				);
				$query_args['date_query'] = $date_query;
				break;
			
			default:
				break;
		}
		/* Exclude current post from the list */
		if ( is_singular() ) {
			$query_args['post__not_in'] = array( $post->ID );
		}

		if ( ! empty( $rltdpstsplgn_options['featured_use_category'] ) && ( is_category() || is_singular() ) ) {
			$category = rltdpstsplgn_get_category_for_posts_block();
			if ( isset( $category ) ) {
				$query_args['category__in'] = $category;
			}
		}
		$the_query = new WP_Query( $query_args );
		ob_start();
		/* The Loop */
		if ( $the_query->have_posts() ) {
			$is_rltdpstsplgn_query = 1;
			add_filter( 'excerpt_length', 'rltdpstsplgn_featured_posts_excerpt_length' );
			add_filter( 'excerpt_more', 'rltdpstsplgn_featured_posts_excerpt_more' ); ?>
			<div class="rltdpstsplgn-featured-posts">
			<?php while ( $the_query->have_posts() ) {
				$the_query->the_post(); ?>
				<div class="clear"></div>
				<?php rltdpstsplgn_render_view( 'featured' );
			} ?>
			</div><!-- .rltdpstsplgn-featured-posts -->
		<?php	remove_filter( 'excerpt_length', 'rltdpstsplgn_featured_posts_excerpt_length' );
				remove_filter( 'excerpt_more', 'rltdpstsplgn_featured_posts_excerpt_more' );
				$is_rltdpstsplgn_query = 0; }
			/* Restore original Post Data */
			wp_reset_postdata();
			$result = ob_get_contents();
			ob_end_clean();
			if ( true === $return ) {
				return '<div class="rltdpstsplgn-featured-post-block">' . $result . '</div>';
			} else {
				echo $result;
		}
	}
}

/**
 * Prints the meta box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
if ( ! function_exists( 'rltdpstsplgn_featured_post_inner_custom_box' ) ) {
	function rltdpstsplgn_featured_post_inner_custom_box( $post ) {
		/* Add an nonce field so we can check for it later. */
		wp_nonce_field( 'rltdpstsplgn_featured_post_inner_custom_box', 'rltdpstsplgn_featured_post_inner_custom_box_nonce' );
		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$is_check = get_post_meta( $post->ID, '_ftrdpsts_add_to_featured_post', true ); ?>
		<div class="check-to-display">
			<label>
				<input type="checkbox" name="rltdpstsplgn_featured_post_checkbox" <?php if ( true == $is_check ) echo 'checked="checked"'; ?> value="1" />
				<?php _e( "Enable to display this post in the Featured Posts block.", 'relevant' ); ?>
			</label>
		</div>
	<?php }
}

/**
* Add style for featured posts block
*/
if ( ! function_exists( 'rltdpstsplgn_wp_enqueue_scripts' ) ) {
	function rltdpstsplgn_wp_enqueue_scripts() {
		global $rltdpstsplgn_options;

		wp_enqueue_style( 'rltdpstsplgn_stylesheet', plugins_url( 'css/style.css', __FILE__ ) ); ?>
		<style type="text/css">
			.rltdpstsplgn-featured-posts {
				width: <?php echo $rltdpstsplgn_options['featured_block_width'] . $rltdpstsplgn_options['featured_block_width_remark'] ?>;
			}
			.rltdpstsplgn-featured-post-block .rltdpstsplgn-featured-posts article {
				width: <?php echo $rltdpstsplgn_options['featured_text_block_width'] . $rltdpstsplgn_options['featured_text_block_width_remark']; ?>;
			}
			<?php if ( 1 == $rltdpstsplgn_options['featured_theme_style'] ) { ?>
				.rltdpstsplgn-featured-posts {
					background-color: <?php echo $rltdpstsplgn_options['featured_background_color_block']; ?> !important;
				}
				.rltdpstsplgn-featured-posts article {
					background-color: <?php echo $rltdpstsplgn_options['featured_background_color_text']; ?> !important;
				}
				.rltdpstsplgn-featured-posts article h3 a {
					color: <?php echo $rltdpstsplgn_options['featured_color_header']; ?> !important;
				}
				.rltdpstsplgn-featured-posts article p {
					color: <?php echo $rltdpstsplgn_options['featured_color_text']; ?> !important;
				}
				.rltdpstsplgn-featured-posts .more-link {
					color: <?php echo $rltdpstsplgn_options['featured_color_link']; ?> !important;
				}
			<?php } ?>
		</style>
	<?php }
}

/* Filter the number of words in an excerpt */
if ( ! function_exists( 'rltdpstsplgn_related_posts_excerpt_length' ) ) {
	function rltdpstsplgn_related_posts_excerpt_length( $length ) {
		global $rltdpstsplgn_options;
		return $rltdpstsplgn_options['related_excerpt_length'];
	}
}

/* Filter the string in the "more" link displayed after a trimmed excerpt */
if ( ! function_exists( 'rltdpstsplgn_related_posts_excerpt_more' ) ) {
	function rltdpstsplgn_related_posts_excerpt_more( $more ) {
		global $rltdpstsplgn_options;
		if ( ! empty( $rltdpstsplgn_options['related_excerpt_more'] ) ) {
			return rltdpstsplgn_get_more_link( 'related' );
		}
		return $more;
	}
}

/* Filter the number of words in an excerpt */
if ( ! function_exists( 'rltdpstsplgn_featured_posts_excerpt_length' ) ) {
	function rltdpstsplgn_featured_posts_excerpt_length( $length ) {
		global $rltdpstsplgn_options;
		return $rltdpstsplgn_options['featured_excerpt_length'];
	}
}

/* Filter the string in the "more" link displayed after a trimmed excerpt */
if ( ! function_exists( 'rltdpstsplgn_featured_posts_excerpt_more' ) ) {
	function rltdpstsplgn_featured_posts_excerpt_more( $more ) {
		global $rltdpstsplgn_options;
		if ( ! empty( $rltdpstsplgn_options['featured_excerpt_more'] ) ) {
			return rltdpstsplgn_get_more_link( 'featured' );
		}
		return $more;
	}
}

/* Filter the number of words in an excerpt */
if ( ! function_exists( 'rltdpstsplgn_latest_posts_excerpt_length' ) ) {
	function rltdpstsplgn_latest_posts_excerpt_length( $length ) {
		global $rltdpstsplgn_options;
		return $rltdpstsplgn_options['latest_excerpt_length'];
	}
}

/* Filter the string in the "more" link displayed after a trimmed excerpt */
if ( ! function_exists( 'rltdpstsplgn_latest_posts_excerpt_more' ) ) {
	function rltdpstsplgn_latest_posts_excerpt_more( $more ) {
		global $rltdpstsplgn_options;
		if ( ! empty( $rltdpstsplgn_options['latest_excerpt_more'] ) ) {
			return rltdpstsplgn_get_more_link( 'latest' );
		}
		return $more;
	}
}

/* Filter the number of words in an excerpt */
if ( ! function_exists( 'rltdpstsplgn_popular_posts_excerpt_length' ) ) {
	function rltdpstsplgn_popular_posts_excerpt_length( $length ) {
		global $rltdpstsplgn_options;
		return $rltdpstsplgn_options['popular_excerpt_length'];
	}
}

/* Filter the string in the "more" link displayed after a trimmed excerpt */
if ( ! function_exists ( 'rltdpstsplgn_popular_posts_excerpt_more' ) ) {
	function rltdpstsplgn_popular_posts_excerpt_more( $more ) {
		global $rltdpstsplgn_options;
		if ( ! empty( $rltdpstsplgn_options['popular_excerpt_more'] ) ) {
			return rltdpstsplgn_get_more_link( 'popular' );
		}
		return $more;
	}
}

if ( ! function_exists ( 'rltdpstsplgn_get_more_link' ) ) {
	function rltdpstsplgn_get_more_link( $slug = 'popular' ) {
		global $rltdpstsplgn_options;
		return '<a class="more-link" href="' . get_permalink() . '">' . $rltdpstsplgn_options[ $slug . '_excerpt_more' ] . '</a>';
	}
}

/* Function for to gather information about viewing posts */
if ( ! function_exists ( 'rltdpstsplgn_set_post_views' ) ) {
	function rltdpstsplgn_set_post_views( $popular_post_ID ) {
		global $post;

		if ( empty( $popular_post_ID ) && ! empty( $post ) ) {
			$popular_post_ID = $post->ID;
		}

		/* Check post type */
		if ( get_post_type( $popular_post_ID ) != 'post' && is_home() ) {
			return;
		}

		$count = absint( get_post_meta( $popular_post_ID, 'pplrpsts_post_views_count', true ) );
		$count++;
		update_post_meta( $popular_post_ID, 'pplrpsts_post_views_count', $count );
	}
}

/**
* Check if image status = 200
*/
if ( ! function_exists( 'rltdpstsplgn_is_200' ) ) {
	function rltdpstsplgn_is_200( $url ) {
		if ( filter_var( $url, FILTER_VALIDATE_URL ) === FALSE ) {
			return false;
		}
		$options['http'] = array(
			'method' => "HEAD",
			'ignore_errors' => 1,
			'max_redirects' => 0
		);
		$code = 0;
		$body = @file_get_contents( $url, NULL, stream_context_create( $options ) );
		if ( isset( $http_response_header ) ) {
			sscanf( $http_response_header[0], 'HTTP/%*d.%*d %d', $code );
		}
		return $code === 200;
	}
}

/* add shortcode content */
if ( ! function_exists( 'rltdpstsplgn_shortcode_button_content' ) ) {
	function rltdpstsplgn_shortcode_button_content( $content ) { ?>
		<div id="rltdpstsplgn" style="display:none;">
			<fieldset>
				<label>
					<input type="radio" name="rltdpstsplgn_shortcode" value="bws_related_posts" checked="checked" />
					<?php _e( 'Related Posts', 'relevant' ) ?>
				</label>
				<br />
				<label>
					<input type="radio" name="rltdpstsplgn_shortcode" value="bws_featured_post" />
					<?php _e( 'Featured Posts', 'relevant' ) ?>
				</label>
				<br />
				<label>
					<input type="radio" name="rltdpstsplgn_shortcode" value="bws_latest_posts" />
					<?php _e( 'Latest Posts', 'relevant' ) ?>
				</label>
				<br />
				<label>
					<input type="radio" name="rltdpstsplgn_shortcode" value="bws_popular_posts" />
					<?php _e( 'Popular Posts', 'relevant' ) ?>
				</label>
				<input class="bws_default_shortcode" type="hidden" name="default" value='[bws_related_posts]' />
				<div class="clear"></div>
			</fieldset>
		</div>
		<script type="text/javascript">
			function rltdpstsplgn_shortcode_init() {
				( function( $ ) {
					$( '.mce-reset input[name="rltdpstsplgn_shortcode"]' ).change( function() {
						$( '.mce-reset #bws_shortcode_display' ).text( '[' + $( this ).val() + ']' );
					} );
				} ) ( jQuery );
			}
		</script>
	<?php }
}
/* add a class with theme name */
if ( ! function_exists ( 'rltdpstsplgn_theme_body_classes' ) ) {
	function rltdpstsplgn_theme_body_classes( $classes ) {
		if ( function_exists( 'wp_get_theme' ) ) {
			$current_theme = wp_get_theme();
			$classes[] = 'rltdpstsplgn_' . basename( $current_theme->get( 'ThemeURI' ) );
		}
		return $classes;
	}
}

/* Add CSS and JS for plugin */
if ( ! function_exists ( 'rltdpstsplgn_admin_enqueue_scripts' ) ) {
	function rltdpstsplgn_admin_enqueue_scripts() {
		global $pagenow;
		wp_enqueue_style( 'rltdpstsplgn_stylesheet', plugins_url( 'css/admin-icon.css', __FILE__ ) );

		if ( ( isset( $_GET['page'] ) && 'related-posts-plugin.php' == $_GET['page'] ) || $pagenow == 'widgets.php' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'rltdpstsplgn_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ) );

			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
	}
}

/* add help tab */
if ( ! function_exists( 'rltdpstsplgn_add_tabs' ) ) {
	function rltdpstsplgn_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id'		=> 'rltdpstsplgn',
			'section'	=> '200538689'
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
			if ( ! $this_plugin ) {
				$this_plugin = plugin_basename( __FILE__ );
			}
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
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() ) {
				$links[] = '<a href="admin.php?page=related-posts-plugin.php">' . __( 'Settings', 'relevant' ) . '</a>';
			}
			$links[] = '<a href="https://support.bestwebsoft.com/hc/en-us/sections/200538689/" target="_blank">' . __( 'FAQ', 'relevant' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'relevant' ) . '</a>';
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

/* Get posts data objects */
if ( ! function_exists( 'rltdpstsplgn_get_data_objects' ) ) {
    function rltdpstsplgn_get_data_objects( $relevant_posts = '', $number = '' ) {
		global $rltdpstsplgn_options;

		if ( empty ( $rltdpstsplgn_options ) ) {
			rltdpstsplgn_set_options();
        }

		if ( '' == $relevant_posts ) {
			$relevant_posts = array( 'related', 'featured', 'latest', 'popular' );
		}

		if ( '' == $number ) {
			$number = array( 'all', 'all', 'all', 'all' );
		}

		$relevant_objects = array();
		foreach ( $rltdpstsplgn_options as $key => $val ) {
		    foreach ( $relevant_posts as $item ) {
				if ( 0 === strpos( $key, $item ) ) {
					$relevant_objects[ $item ]['relevant_options'][ $key ] = $val;
				}
            }
        }

		$relevant_posts = array_combine( $relevant_posts, $number );

        foreach ( $relevant_posts as $key => $val ) {

            switch ( $key ) {
                case 'related' :
                    $query_args = array(
                        'post_type'				=> 'post',
                        'post_status'			=> 'publish',
                        'meta_key'				=> 'pplrpsts_post_views_count',
                        'orderby'				=> 'meta_value_num',
                        'order'					=> 'DESC',
                        'posts_per_page'		=> ( 'all' == $val ) ? $rltdpstsplgn_options['related_posts_count'] : $val,
                        'ignore_sticky_posts'	=> 1
                    );
                    switch ( $rltdpstsplgn_options['display_related_posts'] ) {
                        case '1 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '1 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;
                        case '3 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '3 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;
                        case '6 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '6 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;
                    }
                    $relevant_objects[ $key ]['relevant_posts'] = get_posts( $query_args );
                    break;

                case 'featured' :
                    $query_args = array(
                        'post_type'				=> array( 'post', 'page' ),
                        'meta_key'				=> '_ftrdpsts_add_to_featured_post',
                        'meta_value'			=> '1',
                        'posts_per_page'		=> ( 'all' == $val ) ? $rltdpstsplgn_options['featured_posts_count'] : $val,
                        'orderby'				=> 'rand',
                        'ignore_sticky_posts'	=> 1,
                        'post__not_in'			=> ( isset( $post->ID ) ) ? $post->ID : array()
                    );
                    switch ( $rltdpstsplgn_options['display_featured_posts'] ) {
                        case '1 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '1 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;
                        case '3 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '3 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;
                        case '6 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '6 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;
                    }
                    $relevant_objects[ $key ]['relevant_posts'] = get_posts( $query_args );
                    break;

                case 'latest' :
                    $query_args = array(
                        'post_type'				=> 'post',
                        'post_status'			=> 'publish',
                        'orderby'				=> 'date',
                        'order'					=> 'DESC',
                        'posts_per_page'		=> ( 'all' == $val ) ? $rltdpstsplgn_options['latest_posts_count'] : $val,
                        'ignore_sticky_posts'	=> 1
                    );

                    if ( ! empty( $category ) ) {
                        $query_args['cat'] = $category;
                    }
                    $relevant_objects[ $key ]['relevant_posts'] = get_posts( $query_args );
                    break;

                case 'popular' :

                    if ( 'comment_count' == $rltdpstsplgn_options['popular_order_by'] ) {
                        $order_by = 'comment_count';
                    } else {
                        $order_by = 'meta_value_num';
                    }

                    $query_args = array(
                        'post_type'				=> 'post',
                        'post_status'			=> 'publish',
                        'meta_key'				=> 'pplrpsts_post_views_count',
                        'orderby'				=> $order_by,
                        'order'					=> 'DESC',
                        'posts_per_page'		=> ( 'all' == $val ) ? $rltdpstsplgn_options['popular_posts_count'] : $val,
                        'ignore_sticky_posts'	=> 1,
                    );
                    switch ( $rltdpstsplgn_options['display_popular_posts'] ) {

                        case '1 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '1 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;

                        case '3 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '3 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;

                        case '6 month ago':
                            $date_query = array(
                                array(
                                    'after'     => '6 month ago',
                                    'inclusive' => true,
                                ),
                            );
                            $query_args['date_query'] = $date_query;
                            break;
                    }
                    $relevant_objects[$key]['relevant_posts'] = get_posts( $query_args );
                    break;
            }
        }
		return $relevant_objects;
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
				delete_option( 'widget_rltdpstsplgnwidget' );
				delete_option( 'widget_pplrpsts_popular_posts_widget' );
				delete_option( 'widget_ltstpsts_latest_posts_widget' );
				$allposts = get_posts( 'meta_key=_ftrdpsts_add_to_featured_post' );
				foreach( $allposts as $postinfo ) {
					delete_post_meta( $postinfo->ID, '_ftrdpsts_add_to_featured_post' );
				}

				$allposts = get_posts( 'meta_key=pplrpsts_post_views_count' );
				foreach ( $allposts as $postinfo ) {
					delete_post_meta( $postinfo->ID, 'pplrpsts_post_views_count' );
				}
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'rltdpstsplgn_options' );
			delete_option( 'widget_rltdpstsplgnwidget' );
			delete_option( 'widget_pplrpsts_popular_posts_widget' );
			delete_option( 'widget_ltstpsts_latest_posts_widget' );

			$allposts = get_posts( 'meta_key=_ftrdpsts_add_to_featured_post' );
			foreach ( $allposts as $postinfo ) {
				delete_post_meta( $postinfo->ID, '_ftrdpsts_add_to_featured_post' );
			}

			$allposts = get_posts( 'meta_key=pplrpsts_post_views_count' );
			foreach ( $allposts as $postinfo ) {
				delete_post_meta( $postinfo->ID, 'pplrpsts_post_views_count' );
			}
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

register_activation_hook( __FILE__, 'rltdpstsplgn_plugin_activate' );
add_action( 'admin_menu', 'add_rltdpstsplgn_admin_menu' );

add_action( 'init', 'rltdpstsplgn_plugin_init' );
add_action( 'admin_init', 'rltdpstsplgn_admin_init' );

add_action( 'plugins_loaded', 'rltdpstsplgn_plugins_loaded' );

/* Add meta box for Posts */
add_action( 'add_meta_boxes', 'rltdpstsplgn_add_box' );
/* Save our own meta_key */
add_action( 'save_post', 'rltdpstsplgn_save_postdata' );

add_action( 'after_setup_theme', 'rltdpstsplgn_add_thumb_custom_size' );

add_action( 'admin_enqueue_scripts', 'rltdpstsplgn_admin_enqueue_scripts' );
/* Add theme name as class to body tag */
add_filter( 'body_class', 'rltdpstsplgn_theme_body_classes' );
/* Add style for Featured Posts block */
add_action( 'wp_enqueue_scripts', 'rltdpstsplgn_wp_enqueue_scripts' );

/* Display Featured Post */
add_action( 'ftrdpsts_featured_posts', 'rltdpstsplgn_featured_posts' );
add_action( 'loop_start', 'rltdpstsplgn_loop_start' );
add_filter( 'the_content', 'rltdpstsplgn_display_blocks' );
add_action( 'loop_end', 'rltdpstsplgn_loop_end' );

/* Function for to gather information about viewing posts - for popular posts */
add_action( 'wp_head', 'rltdpstsplgn_set_post_views' );

add_shortcode( 'bws_featured_post', 'rltdpstsplgn_featured_posts_shortcode' );
add_shortcode( 'bws_related_posts', 'rltdpstsplgn_related_posts_output' );
add_shortcode( 'bws_popular_posts', 'rltdpstsplgn_popular_posts_output' );
add_shortcode( 'bws_latest_posts', 'rltdpstsplgn_latest_posts_output' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'rltdpstsplgn_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'rltdpstsplgn_register_plugin_links', 10, 2 );

add_action( 'admin_notices', 'rltdpstsplgn_admin_notices' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'rltdpstsplgn_shortcode_button_content' );
