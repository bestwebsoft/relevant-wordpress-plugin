<?php
/**
 * Include all plugin widgets
 */

/* Registing Widget */
if ( ! function_exists( 'rltdpstsplgn_widget_init' ) ) {
	function rltdpstsplgn_widget_init() {
		register_widget( 'Rltdpstsplgn_Widget' );
		register_widget( 'Bws_Latest_Posts' );
		register_widget( 'PopularPosts' );
	}
}
/* Related Posts Widget */
if ( ! class_exists( 'Rltdpstsplgn_Widget' ) ) {
	class Rltdpstsplgn_Widget extends WP_Widget {
		function __construct() {
			parent::__construct(
				'rltdpstsplgnwidget',
				__( 'Related Posts', 'relevant' ),
				array(
					'classname'		=> 'rltdpstsplgnwidget',
					'description'	=> __( 'A widget that displays related posts depending on your choice of sorting criteria.', 'relevant' )
				)
			);
		}

		/* Display Widget */
		function widget( $args, $instance ) {
			global $rltdpstsplgn_options, $widget_title;

			$widget_title             = ( ! empty( $instance['widget_title'] ) != ( is_home() || is_front_page() ) ) ? apply_filters( 'widget_title', $instance['widget_title'], $instance, $this->id_base ) : '';
			$rltdpstsplgn_options_old = $rltdpstsplgn_options;

			if ( isset( $instance['count'] ) ) {
				$rltdpstsplgn_options['related_posts_count'] = $instance['count'];
			}

			if ( isset( $instance['height'] ) ) {
				$rltdpstsplgn_options['related_image_height'] = $instance['height'];
			}

			if ( isset( $instance['width'] ) ) {
				$rltdpstsplgn_options['related_image_width'] = $instance['width'];
			}
			if ( isset( $instance['excerpt_length'] ) ) {
				$rltdpstsplgn_options['related_excerpt_length'] = intval( $instance['excerpt_length'] );
			}
			if ( isset( $instance['excerpt_more'] ) ) {
				$rltdpstsplgn_options['related_excerpt_more'] = stripslashes( esc_html( $instance['excerpt_more'] ) );
			}

			if ( isset( $instance['no_preview_img'] ) ) {
				$rltdpstsplgn_options['related_no_preview_img'] = $instance['no_preview_img'];
			}

			if ( isset( $instance['search_in'] ) ) {
				$rltdpstsplgn_options['related_criteria'] = $instance['search_in'];
			}

			if ( isset( $instance['no_posts'] ) ) {
				$rltdpstsplgn_options['related_no_posts_message'] = $instance['no_posts'];
			}
			$rltdpstsplgn_options['related_show_comments']			= isset( $instance['show_comments'] ) ? $instance['show_comments'] : 1;
			$rltdpstsplgn_options['related_show_date']				= isset( $instance['show_date'] ) ? $instance['show_date'] : 1;
			$rltdpstsplgn_options['related_show_author']			= isset( $instance['show_author'] ) ? $instance['show_author'] : 1;
			$rltdpstsplgn_options['related_show_reading_time']		= isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : 1;
			$rltdpstsplgn_options['related_show_thumbnail']			= isset( $instance['show_image'] ) ? $instance['show_image'] : 1;
			$rltdpstsplgn_options['related_show_excerpt']			= isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : 1;
			$rltdpstsplgn_options['related_add_for_page']			= array();

			if ( isset( $instance['search_category'] ) ) {
				array_push( $rltdpstsplgn_options['related_add_for_page'], $instance['search_category'] );
			}
			if ( isset( $instance['search_tags'] ) ) {
				array_push( $rltdpstsplgn_options['related_add_for_page'], $instance['search_tags'] );
			}
			if ( isset( $instance['search_meta'] ) ) {
				array_push( $rltdpstsplgn_options['related_add_for_page'], $instance['search_meta'] );
			}
			if ( isset( $instance['search_title'] ) ) {
				array_push( $rltdpstsplgn_options['related_add_for_page'], $instance['search_title'] );
			}

			echo $args['before_widget'];
			if ( ! empty( $widget_title ) ) {
				echo $args['before_title'] . $widget_title . $args['after_title'];
			}
			$post_title_tag = $this->get_post_title_tag( $args['before_title'] );
			$number = $this->number;
			echo rltdpstsplgn_related_posts_output( true, $number );
			echo $args['after_widget'];
			$rltdpstsplgn_options = $rltdpstsplgn_options_old;
		}

		/*  Display Widget Control Form */
		function form( $instance ) {
			global $rltdpstsplgn_related_posts_excerpt_length, $rltdpstsplgn_related_excerpt_more, $rltdpstsplgn_options;

			$widget_title		= isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $rltdpstsplgn_options['related_title'];
			$count				= isset( $instance['count'] ) ? intval( $instance['count'] ) : $rltdpstsplgn_options['related_posts_count'];
			$excerpt_length		= $rltdpstsplgn_related_posts_excerpt_length = isset( $instance['excerpt_length'] ) ? intval( $instance['excerpt_length'] ) : $rltdpstsplgn_options['related_excerpt_length'];
			$excerpt_more		= $rltdpstsplgn_related_excerpt_more = isset( $instance['excerpt_more'] ) ? stripslashes( esc_html( $instance['excerpt_more'] ) ) : $rltdpstsplgn_options['related_excerpt_more'];
			$no_preview_img		= isset( $instance['no_preview_img'] ) ? $instance['no_preview_img'] : $rltdpstsplgn_options['related_no_preview_img'];
			$show_comments		= isset( $instance['show_comments'] ) ? $instance['show_comments'] : $rltdpstsplgn_options['related_show_comments'];
			$show_date			= isset( $instance['show_date'] ) ? $instance['show_date'] : $rltdpstsplgn_options['related_show_date'];
			$show_author		= isset( $instance['show_author'] ) ? $instance['show_author'] : $rltdpstsplgn_options['related_show_author'];
			$show_reading_time	= isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : $rltdpstsplgn_options['related_show_reading_time'];
			$show_image			= isset( $instance['show_image'] ) ? $instance['show_image'] : $rltdpstsplgn_options['related_show_thumbnail'];
			$height				= isset( $instance['height'] ) ? $instance['height'] : $rltdpstsplgn_options['related_image_height'];
			$width				= isset( $instance['width'] ) ? $instance['width'] : $rltdpstsplgn_options['related_image_width'];
			$search_in			= isset( $instance['search_in'] ) ? $instance['search_in'] : $rltdpstsplgn_options['related_criteria'];
			$no_posts			= isset( $instance['no_posts'] ) ? $instance['no_posts'] : $rltdpstsplgn_options['related_no_posts_message'];

			$search_category	= isset( $instance['search_category'] ) ? $instance['search_category'] : 0;
			$search_tags		= isset( $instance['search_tags'] ) ? $instance['search_tags'] : 0;
			$search_meta		= isset( $instance['search_meta'] ) ? $instance['search_meta'] : 0;
			$search_title		= isset( $instance['search_title'] ) ? $instance['search_title'] : 0;

			$show_excerpt		= isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : $rltdpstsplgn_options['related_show_excerpt']; ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Title', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $widget_title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of posts', 'relevant' ); ?>:</label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" min="1" max="1000" value="<?php echo esc_attr( $count ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt length', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" min="1" max="10000" value="<?php echo esc_attr( $excerpt_length ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_more' ); ?>"><?php _e( 'Read More Link Text', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_more' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_more' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $excerpt_more ); ?>"/>
			</p>
			<p>
				<?php _e( 'Show', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_date ); ?> />
					<?php _e( 'Post Date', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_author ); ?> />
					<?php _e( 'Author', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_reading_time' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_reading_time' ); ?>" name="<?php echo $this->get_field_name( 'show_reading_time' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_reading_time ); ?> />
					<?php _e( 'Reading time', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_comments' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_comments ); ?> />
					<?php _e( 'Comments number', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_excerpt ); ?> />
					<?php _e( 'Excerpt', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_image' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_image ); ?> />
					<?php _e( 'Featured image', 'relevant' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'no_preview_img' ); ?>"><?php _e( 'Featured Image Placeholder URL', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'no_preview_img' ); ?>" name="<?php echo $this->get_field_name( 'no_preview_img' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $no_preview_img ); ?>"/><br />
				<small><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></small>
			</p>
			<p>
				<?php _e( 'Featured image size', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'height' ); ?>">
					<?php _e( 'height', 'relevant' ); ?>
					<input class="tiny-text rltdpstsplgnwidget_image_size" id="<?php echo $this->get_field_id( 'height' ); ?>" type="number" min="40" max="240" step="20" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr( $height ); ?>"/>px
					<span class="bws_info">( <?php _e( 'Choose the size from 40px to 240px', 'relevant' ); ?> )</span>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'width' ); ?>">
					<?php _e( 'width', 'relevant' ); echo '&nbsp'; ?>
					<input class="tiny-text rltdpstsplgnwidget_image_size" id="<?php echo $this->get_field_id( 'width' ); ?>" type="number" min="40" max="240" step="20" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo esc_attr( $width ); ?>"/>px
					<span class="bws_info">( <?php _e( 'Choose the size from 40px to 240px', 'relevant' ); ?> )</span>
				</label>
			</p>
			<p>
				<?php _e( 'Search Related Words in', 'relevant' ); ?>:<br />
				<label>
					<input name="<?php echo $this->get_field_name( 'search_in' ); ?>" type="radio" value="category" <?php checked( 'category', esc_attr( $search_in ) ); ?> /> <?php _e( 'Categories', 'relevant' ); ?>
				</label><br />
				<label>
					<input name="<?php echo $this->get_field_name( 'search_in' ); ?>" type="radio" value="tags" <?php checked( 'tags', esc_attr( $search_in ) ); ?> /> <?php _e( 'Tags', 'relevant' ); ?>
				</label><br />
				<label>
					<input name="<?php echo $this->get_field_name( 'search_in' ); ?>" type="radio" value="title" <?php checked( 'title', esc_attr( $search_in ) ); ?> /> <?php _e( 'Titles', 'relevant' ); ?>
				</label><br />
				<label>
					<input name="<?php echo $this->get_field_name( 'search_in' ); ?>" type="radio" value="meta" <?php checked( 'meta', esc_attr( $search_in ) ); ?> /> <?php _e( 'Meta Key', 'relevant' ); ?>
					<span class="bws_info">( <?php _e( 'Enable "Key" in the "Related Post" block which is located in the post you want to display.', 'relevant' ); ?> )</span>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'no_posts' ); ?>"><?php _e( '"No Posts Found" Message', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'no_posts' ); ?>" name="<?php echo $this->get_field_name( 'no_posts' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $no_posts ); ?>"/>
			</p>
			<p>
				<?php _e( 'Search on Pages', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'search_category' ); ?>">
					<input id="<?php echo $this->get_field_id( 'search_category' ); ?>" name="<?php echo $this->get_field_name( 'search_category' ); ?>" type="checkbox" value="1"<?php checked( 1, $search_category ); ?> />
					<?php _e( 'Categories', 'relevant' ); ?>
					<span class="bws_info">( <?php _e( 'Post categories will be available for pages.', 'relevant' ); ?> )</span>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'search_tags' ); ?>">
					<input id="<?php echo $this->get_field_id( 'search_tags' ); ?>" name="<?php echo $this->get_field_name( 'search_tags' ); ?>" type="checkbox" value="1"<?php checked( 1, $search_tags ); ?> />
					<?php _e( 'Tags', 'relevant' ); ?>
					<span class="bws_info">( <?php _e( 'Post tags will be available for pages.', 'relevant' ) ?> )</span>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'search_title' ); ?>">
					<input id="<?php echo $this->get_field_id( 'search_title' ); ?>" name="<?php echo $this->get_field_name( 'search_title' ); ?>" type="checkbox" value="1"<?php checked( 1, $search_title ); ?> />
					<?php _e( 'Title', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'search_meta' ); ?>">
					<input id="<?php echo $this->get_field_id( 'search_meta' ); ?>" name="<?php echo $this->get_field_name( 'search_meta' ); ?>" type="checkbox" value="1"<?php checked( 1, $search_meta ); ?> />
					<?php _e( 'Meta Key', 'relevant' ); ?>
				</label>
				<div class="bws_info"><?php _e( 'Enable to search related words on pages.', 'relevant' ); ?></div>
			</p>
		<?php }

		function update( $new_instance, $old_instance ) {
			global $rltdpstsplgn_options;

			$instance = array();
			$instance['widget_title']	= ( isset( $new_instance['widget_title'] ) ) ? stripslashes( esc_html( $new_instance['widget_title'] ) ) : $rltdpstsplgn_options['related_title'];
			$instance['count']			= ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : $rltdpstsplgn_options['related_posts_count'];
			$instance['excerpt_length']	= ( ! empty( $new_instance['excerpt_length'] ) ) ? intval( $new_instance['excerpt_length'] ) : $rltdpstsplgn_options['related_excerpt_length'];
			$instance['excerpt_more']	= ( ! empty( $new_instance['excerpt_more'] ) ) ? stripslashes( esc_html( $new_instance['excerpt_more'] ) ) : $rltdpstsplgn_options['related_excerpt_more'];
			$instance['category']		= ( ! empty( $new_instance['category'] ) ) ? intval( $new_instance['category'] ) : 0;
			$instance['search_in']		= ( ! empty( $new_instance['search_in'] ) ) ? $new_instance['search_in'] : $rltdpstsplgn_options['related_criteria'];
			$instance['no_posts']		= ( ! empty( $new_instance['no_posts'] ) ) ? $new_instance['no_posts'] : $rltdpstsplgn_options['related_no_posts_message'];
			$instance['height']			= ( ! empty( $new_instance['height'] ) ) ? intval( $new_instance['height'] ) : $rltdpstsplgn_options['related_image_height'];
			$instance['width']			= ( ! empty( $new_instance['width'] ) ) ? intval( $new_instance['width'] ) : $rltdpstsplgn_options['related_image_width'];

			if ( ! empty( $new_instance['no_preview_img'] ) && rltdpstsplgn_is_200( $new_instance['no_preview_img'] ) && getimagesize( $new_instance['no_preview_img'] ) ) {
				$instance['no_preview_img']		= $new_instance['no_preview_img'];
			} else {
				$instance['no_preview_img']		= $rltdpstsplgn_options['popular_no_preview_img'];
			}

			$show_options = array( 'comments', 'date', 'author', 'reading_time', 'image', 'excerpt' );
			foreach ( $show_options as $item )
				$instance["show_{$item}"]		= isset( $new_instance["show_{$item}"] ) ? absint( $new_instance["show_{$item}"] ) : 0;

			$search_option = array( 'category', 'tags', 'meta', 'title' );
			foreach ( $search_option as $item ) {
				$instance["search_{$item}"]		= isset( $new_instance["search_{$item}"] ) ? absint( $new_instance["search_{$item}"] ) : 0;
			}
			return $instance;
		}

		function get_post_title_tag( $widget_tag ) {
			preg_match( '/h[1-5]{1}/', $widget_tag, $matches );
			if ( empty( $matches ) ) {
				return 'h1';
			}
			$number = absint( preg_replace( '/h/', '', $matches[0] ) );
			$number ++;
			return "h{$number}";
		}
	}
}

/* Create Latest_Posts widget */
if ( ! class_exists( 'Bws_Latest_Posts' ) ) {
	class Bws_Latest_Posts extends WP_Widget {

		function __construct() {
			/* Instantiate the parent object */
			parent::__construct(
				'ltstpsts_latest_posts_widget',
				__( 'Latest Posts', 'relevant' ),
				array( 'description' => __( 'Widget for Latest Posts displaying.', 'relevant' ) )
			);
		}

		/* Outputs the content of the widget */
		function widget( $args, $instance ) {
			global $rltdpstsplgn_options;

			$widget_title	= ( ! empty( $instance['widget_title'] ) ) ? apply_filters( 'widget_title', $instance['widget_title'], $instance, $this->id_base ) : '';
			$category		= isset( $instance['category'] ) ? $instance['category'] : 0;

			$rltdpstsplgn_options_old = $rltdpstsplgn_options;

			if ( isset( $instance['count'] ) ) {
				$rltdpstsplgn_options['latest_posts_count']		= $instance['count'];
			}
			if ( isset( $instance['height'] ) ) {
				$rltdpstsplgn_options['latest_image_height']	= $instance['height'];
			}
			if ( isset( $instance['width'] ) ) {
				$rltdpstsplgn_options['latest_image_width']		= $instance['width'];
			}
			if ( isset( $instance['excerpt_length'] ) ) {
				$rltdpstsplgn_options['latest_excerpt_length']	= intval( $instance['excerpt_length'] );
			}
			if ( isset( $instance['excerpt_more'] ) ) {
				$rltdpstsplgn_options['latest_excerpt_more']	= stripslashes( esc_html( $instance['excerpt_more'] ) );
			}
			if ( isset( $instance['no_preview_img'] ) ) {
				$rltdpstsplgn_options['latest_no_preview_img']	= $instance['no_preview_img'];
			}

			$rltdpstsplgn_options['latest_show_comments']		= isset( $instance['show_comments'] ) ? $instance['show_comments'] : 1;
			$rltdpstsplgn_options['latest_show_date']			= isset( $instance['show_date'] ) ? $instance['show_date'] : 1;
			$rltdpstsplgn_options['latest_show_author']			= isset( $instance['show_author'] ) ? $instance['show_author'] : 1;
			$rltdpstsplgn_options['latest_show_reading_time']	= isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : 1;
			$rltdpstsplgn_options['latest_show_thumbnail']		= isset( $instance['show_image'] ) ? $instance['show_image'] : 1;
			$rltdpstsplgn_options['latest_show_excerpt']		= isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : 1;

			echo $args['before_widget'];
			if ( ! empty( $widget_title ) ) {
				if ( ! empty( $category ) ) {
					echo '<a href="' . esc_url( get_category_link( $category ) ) . '">';
				}
				echo $args['before_title'] . $widget_title . $args['after_title'];
				if ( ! empty( $category ) ) {
					echo '</a>';
				}
			}
			$post_title_tag = $this->get_post_title_tag( $args['before_title'] );
			$number = $this->number;
			echo rltdpstsplgn_latest_posts_block( $post_title_tag, true, $number, $category );
			echo $args['after_widget'];

			$rltdpstsplgn_options = $rltdpstsplgn_options_old;
		}

		/* Outputs the options form on admin */
		function form( $instance ) {
			global $rltdpstsplgn_latest_posts_excerpt_length, $rltdpstsplgn_latest_excerpt_more, $rltdpstsplgn_options;

			$widget_title		= isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $rltdpstsplgn_options['latest_title'];
			$count				= isset( $instance['count'] ) ? intval( $instance['count'] ) : $rltdpstsplgn_options['latest_posts_count'];
			$excerpt_length		= $rltdpstsplgn_latest_posts_excerpt_length	= isset( $instance['excerpt_length'] ) ? intval( $instance['excerpt_length'] ) : $rltdpstsplgn_options['latest_excerpt_length'];
			$excerpt_more 		= $rltdpstsplgn_latest_excerpt_more = isset( $instance['excerpt_more'] ) ? stripslashes( esc_html( $instance['excerpt_more'] ) ) : $rltdpstsplgn_options['latest_excerpt_more'];
			$no_preview_img		= isset( $instance['no_preview_img'] ) ? $instance['no_preview_img'] : $rltdpstsplgn_options['latest_no_preview_img'];
			$category			= isset( $instance['category'] ) ? $instance['category'] : 0;
			$show_comments		= isset( $instance['show_comments'] ) ? $instance['show_comments'] : $rltdpstsplgn_options['latest_show_comments'];
			$show_date			= isset( $instance['show_date'] ) ? $instance['show_date'] : $rltdpstsplgn_options['latest_show_date'];
			$show_author		= isset( $instance['show_author'] ) ? $instance['show_author'] : $rltdpstsplgn_options['latest_show_author'];
			$show_reading_time	= isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : $rltdpstsplgn_options['latest_show_reading_time'];
			$show_image			= isset( $instance['show_image'] ) ? $instance['show_image'] : $rltdpstsplgn_options['latest_show_thumbnail'];
			$height				= isset( $instance['height'] ) ? $instance['height'] : $rltdpstsplgn_options['latest_image_height'];
			$width				= isset( $instance['width'] ) ? $instance['width'] : $rltdpstsplgn_options['latest_image_width'];
			$show_excerpt		= isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : $rltdpstsplgn_options['latest_show_excerpt']; ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Title', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $widget_title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category', 'relevant' ); ?>: </label>
				<?php wp_dropdown_categories( array( 'show_option_all' => __( 'All categories', 'relevant' ), 'name' => $this->get_field_name( 'category' ), 'id' => $this->get_field_id( 'category' ), 'selected' => $category ) ); ?>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of posts', 'relevant' ); ?>:
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" min="1" max="1000" value="<?php echo esc_attr( $count ); ?>"/></label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt length', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" min="1" max="10000" value="<?php echo esc_attr( $excerpt_length ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_more' ); ?>"><?php _e( 'Read More Link Text', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_more' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_more' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $excerpt_more ); ?>"/>
			</p>
			<p>
				<?php _e( 'Show', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_date ); ?> />
					<?php _e( 'Post Date', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_author ); ?> />
					<?php _e( 'Author', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_reading_time' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_reading_time' ); ?>" name="<?php echo $this->get_field_name( 'show_reading_time' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_reading_time ); ?> />
					<?php _e( 'Reading time', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_comments' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_comments ); ?> />
					<?php _e( 'Comments number', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_excerpt ); ?> />
					<?php _e( 'Excerpt', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_image' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_image ); ?> />
					<?php _e( 'Featured image', 'relevant' ); ?>
				</label>
				<br />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'no_preview_img' ); ?>"><?php _e( 'Featured Image Placeholder URL', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'no_preview_img' ); ?>" name="<?php echo $this->get_field_name( 'no_preview_img' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $no_preview_img ); ?>"/><br />
				<small><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></small>
			</p>
			<p>
				<?php _e( 'Featured image size', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'height', 'relevant' ); ?></label>
				<input class="tiny-text rltdpstsplgnwidget_image_size" id="<?php echo $this->get_field_id( 'height' ); ?>" type="number" min="40" max="240" step="20" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr( $height ); ?>"/>px
				<span class="bws_info">( <?php _e( 'Choose the size from 40px to 240px', 'relevant' ); ?> )</span><br />
				<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'width', 'relevant' ); echo '&nbsp'; ?></label>
				<input class="tiny-text rltdpstsplgnwidget_image_size" id="<?php echo $this->get_field_id( 'width' ); ?>" type="number" min="40" max="240" step="20" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo esc_attr( $width ); ?>"/>px
				<span class="bws_info">( <?php _e( 'Choose the size from 40px to 240px', 'relevant' ); ?> )</span>
			</p>
		<?php }

		/* Processing widget options on save */
		function update( $new_instance, $old_instance ) {
			global $rltdpstsplgn_options;

			$instance = array();
			$instance['widget_title']	= ( isset( $new_instance['widget_title'] ) ) ? stripslashes( esc_html( $new_instance['widget_title'] ) ) : $rltdpstsplgn_options['latest_title'];
			$instance['count']			= ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : $rltdpstsplgn_options['latest_posts_count'];
			$instance['excerpt_length'] = ( ! empty( $new_instance['excerpt_length'] ) ) ? intval( $new_instance['excerpt_length'] ) : $rltdpstsplgn_options['latest_excerpt_length'];
			$instance['excerpt_more']	= ( ! empty( $new_instance['excerpt_more'] ) ) ? stripslashes( esc_html( $new_instance['excerpt_more'] ) ) : $rltdpstsplgn_options['latest_excerpt_more'];
			$instance['height']			= ( ! empty( $new_instance['height'] ) ) ? intval( $new_instance['height'] ) : $rltdpstsplgn_options['latest_image_height'];
			$instance['width']			= ( ! empty( $new_instance['width'] ) ) ? intval( $new_instance['width'] ) : $rltdpstsplgn_options['latest_image_width'];
			$instance['category']		= ( ! empty( $new_instance['category'] ) ) ? intval( $new_instance['category'] ) : 0;

			$show_options = array( 'comments', 'date', 'author', 'reading_time', 'image', 'excerpt' );
			if ( ! empty( $new_instance['no_preview_img'] ) && rltdpstsplgn_is_200( $new_instance['no_preview_img'] ) && getimagesize( $new_instance['no_preview_img'] ) )
				$instance['no_preview_img']	= $new_instance['no_preview_img'];
			else
				$instance['no_preview_img']	= $rltdpstsplgn_options['popular_no_preview_img'];
			foreach ( $show_options as $item )
				$instance["show_{$item}"]	= isset( $new_instance["show_{$item}"] ) ? absint( $new_instance["show_{$item}"] ) : 0;

			return $instance;
		}

		function get_post_title_tag( $widget_tag ) {
			preg_match( '/h[1-5]{1}/', $widget_tag, $matches );
			if ( empty( $matches ) ) {
				return 'h1';
			}
			$number = absint( preg_replace( '/h/', '', $matches[0] ) );
			$number ++;
			return "h{$number}";
		}
	}
}

/* Create widget for plugin */
if ( ! class_exists( 'PopularPosts' ) ) {
	class PopularPosts extends WP_Widget {

		function __construct() {
			/* Instantiate the parent object */
			parent::__construct(
				'pplrpsts_popular_posts_widget',
				__( 'Popular Posts', 'relevant' ),
				array( 'description' => __( 'Widget for displaying Popular Posts by comments or views count.', 'relevant' ) )
			);
		}

		/* Outputs the content of the widget */
		function widget( $args, $instance ) {
			global $rltdpstsplgn_options;

			$widget_title = ( ! empty( $instance['widget_title'] ) ) ? apply_filters( 'widget_title', $instance['widget_title'], $instance, $this->id_base ) : '';

			$rltdpstsplgn_options_old = $rltdpstsplgn_options;

			if ( isset( $instance['count'] ) ) {
				$rltdpstsplgn_options['popular_posts_count']		= intval( $instance['count'] );
			}
			if ( isset( $instance['excerpt_length'] ) ) {
				$rltdpstsplgn_options['popular_excerpt_length']		= intval( $instance['excerpt_length'] );
			}
			if ( isset( $instance['excerpt_more'] ) ) {
				$rltdpstsplgn_options['popular_excerpt_more']		= stripslashes( esc_html( $instance['excerpt_more'] ) );
			}
			if ( isset( $instance['no_preview_img'] ) ) {
				$rltdpstsplgn_options['popular_no_preview_img']		= $instance['no_preview_img'];
			}
			if ( isset( $instance['order_by'] ) ) {
				$rltdpstsplgn_options['popular_order_by']			= $instance['order_by'];
			}
			if ( isset( $instance['min_count'] ) ) {
				$rltdpstsplgn_options['popular_min_posts_count']	= intval( $instance['min_count'] );
			}
			if ( isset( $instance['height'] ) ) {
				$rltdpstsplgn_options['popular_image_height']		= $instance['height'];
			}
			if ( isset( $instance['width'] ) ) {
				$rltdpstsplgn_options['popular_image_width']		= $instance['width'];
			}

			$rltdpstsplgn_options['popular_show_views']				= isset( $instance['show_views'] ) ? $instance['show_views'] : 1;
			$rltdpstsplgn_options['popular_show_date']				= isset( $instance['show_date'] ) ? $instance['show_date'] : 1;
			$rltdpstsplgn_options['popular_show_author']			= isset( $instance['show_author'] ) ? $instance['show_author'] : 1;
			$rltdpstsplgn_options['popular_show_thumbnail']			= isset( $instance['show_image'] ) ? $instance['show_image'] : 1;
			$rltdpstsplgn_options['popular_show_excerpt']			= isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : 1;
			$rltdpstsplgn_options['popular_show_reading_time']		= isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : 1;
			$rltdpstsplgn_options['popular_show_comments']			= isset( $instance['show_comments'] ) ? $instance['show_comments'] : 1;
			$rltdpstsplgn_options['popular_use_category']			= isset( $instance['use_category'] ) ? $instance['use_category'] : 1;

			echo $args['before_widget'];
			if ( ! empty( $widget_title ) ) {
				echo $args['before_title'] . $widget_title . $args['after_title'];
			}
			$post_title_tag = $this->get_post_title_tag( $args['before_title'] );
			$number = $this->number;
			echo rltdpstsplgn_popular_posts_block( $post_title_tag, true, $number );
			echo $args['after_widget'];

			$rltdpstsplgn_options = $rltdpstsplgn_options_old;
		}

		/* Outputs the options form on admin */
		function form( $instance ) {
			global $rltdpstsplgn_popular_excerpt_length, $rltdpstsplgn_popular_excerpt_more, $rltdpstsplgn_options;

			$widget_title		= isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $rltdpstsplgn_options['popular_title'];
			$count				= isset( $instance['count'] ) ? intval( $instance['count'] ) : $rltdpstsplgn_options['popular_posts_count'];
			$min_count			= isset( $instance['min_count'] ) ? absint( $instance['min_count'] ) : $rltdpstsplgn_options['popular_min_posts_count'];
			$excerpt_length		= $rltdpstsplgn_popular_excerpt_length = isset( $instance['excerpt_length'] ) ? intval( $instance['excerpt_length'] ) : $rltdpstsplgn_options['popular_excerpt_length'];
			$excerpt_more		= $rltdpstsplgn_popular_excerpt_more = isset( $instance['excerpt_more'] ) ? stripslashes( esc_html( $instance['excerpt_more'] ) ) : $rltdpstsplgn_options['popular_excerpt_more'];
			$no_preview_img		= isset( $instance['no_preview_img'] ) ? $instance['no_preview_img'] : $rltdpstsplgn_options['popular_no_preview_img'];
			$order_by			= isset( $instance['order_by'] ) ? $instance['order_by'] : $rltdpstsplgn_options['popular_order_by'];
			$show_views			= isset( $instance['show_views'] ) ? $instance['show_views'] : $rltdpstsplgn_options['popular_show_views'];
			$show_date			= isset( $instance['show_date'] ) ? $instance['show_date'] : $rltdpstsplgn_options['popular_show_date'];
			$show_author		= isset( $instance['show_author'] ) ? $instance['show_author'] : $rltdpstsplgn_options['popular_show_author'];
			$show_image			= isset( $instance['show_image'] ) ? $instance['show_image'] : $rltdpstsplgn_options['popular_show_thumbnail'];
			$show_excerpt		= isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : $rltdpstsplgn_options['popular_show_excerpt'];
			$show_reading_time	= isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : $rltdpstsplgn_options['popular_show_reading_time'];
			$show_comments		= isset( $instance['show_comments'] ) ? $instance['show_comments'] : $rltdpstsplgn_options['popular_show_comments'];
			$height				= isset( $instance['height'] ) ? $instance['height'] : $rltdpstsplgn_options['popular_image_height'];
			$width				= isset( $instance['width'] ) ? $instance['width'] : $rltdpstsplgn_options['popular_image_width'];
			$use_category		= isset( $instance['use_category'] ) ? $instance['use_category'] : $rltdpstsplgn_options['popular_use_category']; ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Title', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $widget_title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of posts', 'relevant' ); ?>:</label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" min="1" max="10000" value="<?php echo esc_attr( $count ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'min_count' ); ?>"><?php _e( 'Min posts number', 'relevant' ); ?>:</label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'min_count' ); ?>" name="<?php echo $this->get_field_name( 'min_count' ); ?>" type="number" min="0" max="9999" value="<?php echo esc_attr( $min_count ); ?>"/>
				<br />
				<small><?php _e( 'Hide Popular Posts block if posts count is less than specified.', 'relevant' ); ?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt length', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" min="1" max="10000" value="<?php echo esc_attr( $excerpt_length ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_more' ); ?>"><?php _e( 'Read More Link Text', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_more' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_more' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $excerpt_more ); ?>"/>
			</p>
			<p>
				<?php _e( 'Show', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'show_views' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_views' ); ?>" name="<?php echo $this->get_field_name( 'show_views' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_views ); ?> />
					<?php _e( 'Views number', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_date ); ?> />
					<?php _e( 'Post date', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_author ); ?> />
					<?php _e( 'Author', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_image' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_image ); ?> />
					<?php _e( 'Featured image', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_excerpt ); ?> />
					<?php _e( 'Excerpt', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_reading_time' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_reading_time' ); ?>" name="<?php echo $this->get_field_name( 'show_reading_time' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_reading_time ); ?> />
					<?php _e( 'Reading time', 'relevant' ); ?>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'show_comments' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_comments ); ?> />
					<?php _e( 'Comments number', 'relevant' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'no_preview_img' ); ?>"><?php _e( 'Featured Image Placeholder URL', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'no_preview_img' ); ?>" name="<?php echo $this->get_field_name( 'no_preview_img' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $no_preview_img ); ?>"/><br />
				<small><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></small>
			</p>
			<p>
				<?php _e( 'Featured image size', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'height', 'relevant' ); ?></label>
				<input class="tiny-text rltdpstsplgnwidget_image_size" id="<?php echo $this->get_field_id( 'height' ); ?>" type="number" min="40" max="240" step="20" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr( $height ); ?>"/>px
				<span class="bws_info">( <?php _e( 'Choose the size from 40px to 240px', 'relevant' ); ?> )</span><br />
				<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'width', 'relevant' ); echo '&nbsp'; ?></label>
				<input class="tiny-text rltdpstsplgnwidget_image_size" id="<?php echo $this->get_field_id( 'width' ); ?>" type="number" min="40" max="240" step="20" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo esc_attr( $width ); ?>"/>px
				<span class="bws_info">( <?php _e( 'Choose the size from 40px to 240px', 'relevant' ); ?> )</span>
			</p>
			<p>
				<?php _e( 'Order posts by number of', 'relevant' ); ?>:<br />
				<label>
					<input name="<?php echo $this->get_field_name( 'order_by' ); ?>" type="radio" value="comment_count" <?php checked( 'comment_count', esc_attr( $order_by ) ); ?> /><?php _e( 'Comments', 'relevant' ); ?>
				</label><br />
				<label>
					<input name="<?php echo $this->get_field_name( 'order_by' ); ?>" type="radio" value="views_count" <?php checked( 'views_count', esc_attr( $order_by ) ); ?> /><?php _e( 'Views', 'relevant' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'use_category' ); ?>">
					<input id="<?php echo $this->get_field_id( 'use_category' ); ?>" name="<?php echo $this->get_field_name( 'use_category' ); ?>" type="checkbox" value="1"<?php checked( 1, $use_category ); ?> /> <?php _e( 'Display posts from the current category only', 'relevant' ); ?>
				</label>
			</p>
		<?php }

		/* Processing widget options on save */
		function update( $new_instance, $old_instance ) {
			global $rltdpstsplgn_options;
			$instance                   = array();
			$instance['widget_title']   = isset( $new_instance['widget_title'] ) ? stripslashes( esc_html( $new_instance['widget_title'] ) ) : $rltdpstsplgn_options['popular_title'];
			$instance['count']          = ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : $rltdpstsplgn_options['popular_posts_count'];
			$instance['height']         = ( ! empty( $new_instance['height'] ) ) ? intval( $new_instance["height"] ) : $rltdpstsplgn_options['popular_image_height'];
			$instance['width']          = ( ! empty( $new_instance['width'] ) ) ? intval( $new_instance["width"] ) : $rltdpstsplgn_options['popular_image_width'];
			$instance['min_count']      = ( ! empty( $new_instance['min_count'] ) ) ? intval( $new_instance['min_count'] ) : $rltdpstsplgn_options['popular_min_posts_count'];
			$instance['excerpt_length'] = ( ! empty( $new_instance['excerpt_length'] ) ) ? intval( $new_instance['excerpt_length'] ) : $rltdpstsplgn_options['popular_excerpt_length'];
			$instance['excerpt_more']   = ( ! empty( $new_instance['excerpt_more'] ) ) ? stripslashes( esc_html( $new_instance['excerpt_more'] ) ) : $rltdpstsplgn_options['popular_excerpt_more'];
			$instance["use_category"]   = isset( $new_instance["use_category"] ) ? absint( $new_instance["use_category"] ) : 0;

			$show_options = array( 'views', 'date', 'author', 'image', 'excerpt', 'reading_time', 'comments' );
			foreach ( $show_options as $item ) {
				$instance["show_{$item}"] = isset( $new_instance["show_{$item}"] ) ? absint( $new_instance["show_{$item}"] ) : 0;
			}

			if ( ! empty( $new_instance['no_preview_img'] ) && rltdpstsplgn_is_200( $new_instance['no_preview_img'] ) && getimagesize( $new_instance['no_preview_img'] ) ) {
				$instance['no_preview_img'] = $new_instance['no_preview_img'];
			} else {
				$instance['no_preview_img'] = $rltdpstsplgn_options['popular_no_preview_img'];
			}

			$instance['order_by']			= ( ! empty( $new_instance['order_by'] ) ) ? $new_instance['order_by'] : $rltdpstsplgn_options['popular_order_by'];
			return $instance;
		}

		function get_post_title_tag( $widget_tag ) {
			preg_match( '/h[1-5]{1}/', $widget_tag, $matches );
			if ( empty( $matches ) ) {
				return 'h1';
			}
			$number = absint( preg_replace( '/h/', '', $matches[0] ) );
			$number ++;
			return "h{$number}";
		}
	}
}

add_action( 'widgets_init', 'rltdpstsplgn_widget_init' );