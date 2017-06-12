<?php
/**
 * Include deprecated functions
 */

/**
* Function check- for old plugin version
* @deprecated 1.2.1
* @todo Remove function after 25.11.2017
*/
if ( ! function_exists( 'rltdpstsplgn_update_old_options' ) ) {
	function rltdpstsplgn_update_old_options() {
		global $rltdpstsplgn_options;
		
		/* Related plugin */
		if ( isset( $rltdpstsplgn_options['head'] ) ) {
			$rltdpstsplgn_options['related_title'] = $rltdpstsplgn_options['head'];
			unset( $rltdpstsplgn_options['head'] );
		}

		if ( isset( $rltdpstsplgn_options['number_post'] ) ) {
			$rltdpstsplgn_options['related_posts_count'] = $rltdpstsplgn_options['number_post'];
			unset( $rltdpstsplgn_options['number_post'] );
		}

		if ( isset( $rltdpstsplgn_options['show_thumbnail'] ) ) {
			$rltdpstsplgn_options['related_show_thumbnail'] = $rltdpstsplgn_options['show_thumbnail'];
			unset( $rltdpstsplgn_options['show_thumbnail'] );
		}

		if ( isset( $rltdpstsplgn_options['add_for_page'] ) ) {
			$rltdpstsplgn_options['related_add_for_page'] = $rltdpstsplgn_options['add_for_page'];
			unset( $rltdpstsplgn_options['add_for_page'] );
		}

		if ( isset( $rltdpstsplgn_options['criteria'] ) ) {
			$rltdpstsplgn_options['related_criteria'] = $rltdpstsplgn_options['criteria'];
			unset( $rltdpstsplgn_options['criteria'] );
		}

		if ( isset( $rltdpstsplgn_options['no_posts'] ) ) {
			$rltdpstsplgn_options['related_no_posts_message'] = $rltdpstsplgn_options['no_posts'];
			unset( $rltdpstsplgn_options['no_posts'] );
		}

		if ( isset( $rltdpstsplgn_options['index_show'] ) ) {
			unset( $rltdpstsplgn_options['index_show'] );
		}
	}
}

/**
* Function for deactivation Featured\Latest\Popular Posts
* @deprecated 1.2.1
* @todo Remove function after 25.11.2017
*/
if ( ! function_exists( 'rltdpstsplgn_deactivate_plugins' ) ) {
	function rltdpstsplgn_deactivate_plugins() {
		global $wpdb;

		/* deactivate plugins - Featured\Latest\Popular Posts */
		$this_plugin = 'relevant/related-posts-plugin.php';
		$plugins = array( 'bws-featured-posts/bws-featured-posts.php', 'bws-latest-posts/bws-latest-posts.php', 'bws-popular-posts/bws-popular-posts.php' );
		$flag    = false;
		foreach ( $plugins as $plugin ) {
			if ( is_plugin_active_for_network( $plugin ) ) {
				$free = $plugin;
				$flag = true;
				break;
			}
		}

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( ! is_plugin_active_for_network( $this_plugin ) )
				$deactivate_not_for_all_network = true;
		}

		if ( isset( $deactivate_not_for_all_network ) && $flag ) {
			global $wpdb;
			deactivate_plugins( $plugins );

			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				activate_plugin( $free );
			}
			switch_to_blog( $old_blog );
		} else {
			deactivate_plugins( $plugins );
		}
	}
}

/**
* Function check if plugins Featured\Latest\Popular Posts is/was active
* @deprecated 1.2.1
* @todo Remove function after 25.11.2017
*/
if ( ! function_exists( 'rltdpstsplgn_other_plugins_options' ) ) {
	function rltdpstsplgn_other_plugins_options() {
		global $rltdpstsplgn_options;

		/* featured posts options */
		if ( ! isset( $rltdpstsplgn_options['featured_display'] ) ) {
			$ftrdpsts_options = get_option( 'ftrdpsts_options' );
			if ( ! empty( $ftrdpsts_options ) ) {
				
				$rltdpstsplgn_options['featured_display'] = array();
				if ( 1 == $ftrdpsts_options['display_before_content'] )
					array_push( $rltdpstsplgn_options['featured_display'], 'before' );

				if ( 1 == $ftrdpsts_options['display_after_content'] )
					array_push( $rltdpstsplgn_options['featured_display'], 'after' );

				$rltdpstsplgn_options['featured_block_width'] = $ftrdpsts_options['block_width'];
				$rltdpstsplgn_options['featured_text_block_width'] = $ftrdpsts_options['text_block_width'];
				$rltdpstsplgn_options['featured_posts_count'] = $ftrdpsts_options['posts_count'];
				$rltdpstsplgn_options['featured_theme_style'] = $ftrdpsts_options['theme_style'];
				$rltdpstsplgn_options['featured_background_color_block'] = $ftrdpsts_options['background_color_block'];
				$rltdpstsplgn_options['featured_background_color_text'] = $ftrdpsts_options['background_color_text'];
				$rltdpstsplgn_options['featured_color_text'] = $ftrdpsts_options['color_text'];
				$rltdpstsplgn_options['featured_color_header'] = $ftrdpsts_options['color_header'];
				$rltdpstsplgn_options['featured_color_link'] = $ftrdpsts_options['color_link'];

				delete_option( 'ftrdpsts_options' );
			}
		}

		/* Latest posts options */
		if ( ! isset( $rltdpstsplgn_options['latest_title'] ) ) {
			$ltstpsts_options = get_option( 'ltstpsts_options' );
			if ( ! empty( $ltstpsts_options ) ) {

				$rltdpstsplgn_options['latest_title'] = $ltstpsts_options['title'];
				$rltdpstsplgn_options['latest_posts_count'] = $ltstpsts_options['count'];
				$rltdpstsplgn_options['latest_excerpt_length'] = $ltstpsts_options['excerpt_length'];
				$rltdpstsplgn_options['latest_excerpt_more'] = $ltstpsts_options['excerpt_more'];
				$rltdpstsplgn_options['latest_no_preview_img'] = $ltstpsts_options['no_preview_img'];
				$rltdpstsplgn_options['latest_show_date'] = $ltstpsts_options['show_date'];
				$rltdpstsplgn_options['latest_show_author'] = $ltstpsts_options['show_author'];
				$rltdpstsplgn_options['latest_show_reading_time'] = $ltstpsts_options['show_reading_time'];
				$rltdpstsplgn_options['latest_show_comments'] = $ltstpsts_options['show_comments'];
				$rltdpstsplgn_options['latest_show_thumbnail'] = $ltstpsts_options['show_image'];
				$rltdpstsplgn_options['latest_show_excerpt'] = $ltstpsts_options['show_excerpt'];

				delete_option( 'ltstpsts_options' );
			}
		}

		/* Popular posts options */
		if ( ! isset( $rltdpstsplgn_options['popular_title'] ) ) {
			$pplrpsts_options = get_option( 'pplrpsts_options' );
			if ( ! empty( $pplrpsts_options ) ) {

				$rltdpstsplgn_options['popular_title'] = $pplrpsts_options['widget_title'];
				$rltdpstsplgn_options['popular_posts_count'] = $pplrpsts_options['count'];
				$rltdpstsplgn_options['popular_excerpt_length'] = $pplrpsts_options['excerpt_length'];
				$rltdpstsplgn_options['popular_excerpt_more'] = $pplrpsts_options['excerpt_more'];
				$rltdpstsplgn_options['popular_no_preview_img'] = $pplrpsts_options['no_preview_img'];
				$rltdpstsplgn_options['popular_order_by'] = $pplrpsts_options['order_by'];
				$rltdpstsplgn_options['popular_show_views'] = $pplrpsts_options['show_views'];
				$rltdpstsplgn_options['popular_show_date'] = $pplrpsts_options['show_date'];
				$rltdpstsplgn_options['popular_show_author'] = $pplrpsts_options['show_author'];
				$rltdpstsplgn_options['popular_show_thumbnail'] = $pplrpsts_options['show_image'];
				$rltdpstsplgn_options['popular_use_category'] = $pplrpsts_options['use_category'];
				$rltdpstsplgn_options['popular_min_posts_count'] = $pplrpsts_options['min_count'];

				delete_option( 'pplrpsts_options' );
			}
		}
	}
}