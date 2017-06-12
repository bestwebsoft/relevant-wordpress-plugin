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
					'classname'		=>	'rltdpstsplgnwidget',
					'description'	=>	__( 'A widget that displays related posts depending on your choice of sorting criteria.', 'relevant' )
				)
			);
		}

		/* Display Widget */
		function widget( $args, $instance ) {
			global $rltdpstsplgn_options;

			if ( empty( $rltdpstsplgn_options ) )
				rltdpstsplgn_set_options();

			extract( $args, EXTR_SKIP );

			$title = ( isset( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';
			if ( is_single() || ( is_page() && ! empty( $rltdpstsplgn_options['related_add_for_page'] ) ) ) {
				echo $args['before_widget'];
				echo $args['before_title'] . $title . $args['after_title'];
				echo rltdpstsplgn_loop();
				echo $args['after_widget'];
			}
		}

		/* When Widget Control Form Is Posted */
		function update( $new_instance, $old_instance ) {
			$instance			= $old_instance;
			$instance['title']	= strip_tags( $new_instance['title'] );
			return $instance;
		}

		/*  Display Widget Control Form */
		function form( $instance ) {
			/* We appropriate the value of the name Widget */
			$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Related Posts', 'relevant' ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'relevant' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
		<?php }
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

			$widget_title 		= ( ! empty( $instance['widget_title'] ) ) ? apply_filters( 'widget_title', $instance['widget_title'], $instance, $this->id_base ) : '';
			$category			= isset( $instance['category'] ) ? $instance['category'] : 0;

			$rltdpstsplgn_options_old = $rltdpstsplgn_options;

			if ( isset( $instance['count'] ) )
				$rltdpstsplgn_options['latest_posts_count'] = $instance['count'];

			$rltdpstsplgn_options['latest_show_comments'] = isset( $instance['show_comments'] ) ? $instance['show_comments'] : 1;
			$rltdpstsplgn_options['latest_show_date'] = isset( $instance['show_date'] ) ? $instance['show_date'] : 1;
			$rltdpstsplgn_options['latest_show_author'] = isset( $instance['show_author'] ) ? $instance['show_author'] : 1;
			$rltdpstsplgn_options['latest_show_reading_time'] = isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : 1;
			$rltdpstsplgn_options['latest_show_thumbnail'] = isset( $instance['show_image'] ) ? $instance['show_image'] : 1;
			$rltdpstsplgn_options['latest_show_excerpt'] = isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : 1;			

			echo $args['before_widget'];
			if ( ! empty( $widget_title ) ) {
				if ( ! empty( $category ) )
					echo '<a href="' . esc_url( get_category_link( $category ) ) . '">';
				echo $args['before_title'] . $widget_title . $args['after_title'];
				if ( ! empty( $category ) )
					echo '</a>';
			}
			$post_title_tag = $this->get_post_title_tag( $args['before_title'] );
			echo rltdpstsplgn_latest_posts_block( $post_title_tag );
			echo $args['after_widget'];

			$rltdpstsplgn_options = $rltdpstsplgn_options_old;
		}

		/* Outputs the options form on admin */
		function form( $instance ) {
			global $rltdpstsplgn_options;

			$widget_title		= isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $rltdpstsplgn_options['latest_title'];
			$count				= isset( $instance['count'] ) ? intval( $instance['count'] ) : $rltdpstsplgn_options['latest_posts_count'];
			$category			= isset( $instance['category'] ) ? $instance['category'] : 0;

			$show_comments		= isset( $instance['show_comments'] ) ? $instance['show_comments'] : $rltdpstsplgn_options['latest_show_comments'];
			$show_date			= isset( $instance['show_date'] ) ? $instance['show_date'] : $rltdpstsplgn_options['latest_show_date'];
			$show_author		= isset( $instance['show_author'] ) ? $instance['show_author'] : $rltdpstsplgn_options['latest_show_author'];
			$show_reading_time	= isset( $instance['show_reading_time'] ) ? $instance['show_reading_time'] : $rltdpstsplgn_options['latest_show_reading_time'];
			$show_image			= isset( $instance['show_image'] ) ? $instance['show_image'] : $rltdpstsplgn_options['latest_show_thumbnail'];
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
				<?php _e( 'Show', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_date ); ?> />
					<?php _e( 'post date', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_author ); ?> />
					<?php _e( 'author', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_reading_time' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_reading_time' ); ?>" name="<?php echo $this->get_field_name( 'show_reading_time' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_reading_time ); ?> />
					<?php _e( 'reading time', 'relevant' ); ?>
				</label>
				<br />			
				<label for="<?php echo $this->get_field_id( 'show_comments' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_comments ); ?> />
					<?php _e( 'comments number', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_image' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_image ); ?> />
					<?php _e( 'featured image', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_excerpt ); ?> />
					<?php _e( 'excerpt', 'relevant' ); ?>
				</label>				
			</p>
		<?php }

		/* Processing widget options on save */
		function update( $new_instance, $old_instance ) {
			global $rltdpstsplgn_options;

			$instance = array();
			$instance['widget_title']	= ( isset( $new_instance['widget_title'] ) ) ? stripslashes( esc_html( $new_instance['widget_title'] ) ) : $rltdpstsplgn_options['latest_title'];
			$instance['count']			= ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : $rltdpstsplgn_options['latest_posts_count'];
			$instance['category']		= ( ! empty( $new_instance['category'] ) ) ? intval( $new_instance['category'] ) : 0;

			$show_options = array( 'comments', 'date', 'author', 'reading_time', 'image', 'excerpt' );
			foreach ( $show_options as $item )
				$instance["show_{$item}"] = isset( $new_instance["show_{$item}"] ) ? absint( $new_instance["show_{$item}"] ) : 0;

			return $instance;
		}

		function get_post_title_tag( $widget_tag ) {
			preg_match( '/h[1-5]{1}/', $widget_tag, $matches );

			if ( empty( $matches ) )
				return 'h1';

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
			
			if ( isset( $instance['count'] ) )
				$rltdpstsplgn_options['popular_posts_count'] = intval( $instance['count'] );
			if ( isset( $instance['excerpt_length'] ) )
				$rltdpstsplgn_options['popular_excerpt_length'] = intval( $instance['excerpt_length'] );
			if ( isset( $instance['excerpt_more'] ) )
				$rltdpstsplgn_options['popular_excerpt_more'] = stripslashes( esc_html( $instance['excerpt_more'] ) );
			if ( isset( $instance['no_preview_img'] ) )
				$rltdpstsplgn_options['popular_no_preview_img'] = $instance['no_preview_img'];
			if ( isset( $instance['order_by'] ) )
				$rltdpstsplgn_options['popular_order_by'] = $instance['order_by'];
			if ( isset( $instance['min_count'] ) )
				$rltdpstsplgn_options['popular_min_posts_count'] = intval( $instance['min_count'] );

			$rltdpstsplgn_options['popular_show_views'] = isset( $instance['show_views'] ) ? $instance['show_views'] : 1;
			$rltdpstsplgn_options['popular_show_date'] = isset( $instance['show_date'] ) ? $instance['show_date'] : 1;
			$rltdpstsplgn_options['popular_show_author'] = isset( $instance['show_author'] ) ? $instance['show_author'] : 1;
			$rltdpstsplgn_options['popular_show_thumbnail'] = isset( $instance['show_image'] ) ? $instance['show_image'] : 1;
			$rltdpstsplgn_options['popular_use_category'] = isset( $instance['use_category'] ) ? $instance['use_category'] : 1;

			echo $args['before_widget'];
			if ( ! empty( $widget_title ) )
				echo $args['before_title'] . $widget_title . $args['after_title'];
			$post_title_tag = $this->get_post_title_tag( $args['before_title'] );
			echo rltdpstsplgn_popular_posts_block( $post_title_tag );
			echo $args['after_widget'];

			$rltdpstsplgn_options = $rltdpstsplgn_options_old;
		}

		/* Outputs the options form on admin */
		function form( $instance ) {
			global $rltdpstsplgn_popular_excerpt_length, $rltdpstsplgn_popular_excerpt_more, $rltdpstsplgn_options;

			$widget_title	= isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $rltdpstsplgn_options['popular_title'];
			$count			= isset( $instance['count'] ) ? intval( $instance['count'] ) : $rltdpstsplgn_options['popular_posts_count'];
			$min_count		= isset( $instance['min_count'] ) ? absint( $instance['min_count'] ) : $rltdpstsplgn_options['popular_min_posts_count'];
			$excerpt_length = $rltdpstsplgn_popular_excerpt_length = isset( $instance['excerpt_length'] ) ? intval( $instance['excerpt_length'] ) : $rltdpstsplgn_options['popular_excerpt_length'];
			$excerpt_more 	= $rltdpstsplgn_popular_excerpt_more = isset( $instance['excerpt_more'] ) ? stripslashes( esc_html( $instance['excerpt_more'] ) ) : $rltdpstsplgn_options['popular_excerpt_more'];
			$no_preview_img = isset( $instance['no_preview_img'] ) ? $instance['no_preview_img'] : $rltdpstsplgn_options['popular_no_preview_img'];
			$order_by		= isset( $instance['order_by'] ) ? $instance['order_by'] : $rltdpstsplgn_options['popular_order_by'];
			$show_views		= isset( $instance['show_views'] ) ? $instance['show_views'] : $rltdpstsplgn_options['popular_show_views'];
			$show_date		= isset( $instance['show_date'] ) ? $instance['show_date'] : $rltdpstsplgn_options['popular_show_date'];
			$show_author	= isset( $instance['show_author'] ) ? $instance['show_author'] : $rltdpstsplgn_options['popular_show_author'];
			$show_image		= isset( $instance['show_image'] ) ? $instance['show_image'] : $rltdpstsplgn_options['popular_show_thumbnail'];
			$use_category	= isset( $instance['use_category'] ) ? $instance['use_category'] : $rltdpstsplgn_options['popular_use_category']; ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Title', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $widget_title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of posts', 'relevant' ); ?>: </label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" min="1" max="10000" value="<?php echo esc_attr( $count ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'min_count' ); ?>"><?php _e( 'Min posts number', 'relevant' ); ?>: </label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'min_count' ); ?>" name="<?php echo $this->get_field_name( 'min_count' ); ?>" type="number" min="0" max="9999" value="<?php echo esc_attr( $min_count ); ?>"/>
				<br />
				<small><?php _e( 'Hide Popular Posts block if posts count is less than specified.', 'relevant' ); ?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt length', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" min="1" max="10000" value="<?php echo esc_attr( $excerpt_length ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_more' ); ?>"><?php _e( '"Read more" text', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_more' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_more' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $excerpt_more ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'no_preview_img' ); ?>"><?php _e( 'Placeholder image URL', 'relevant' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'no_preview_img' ); ?>" name="<?php echo $this->get_field_name( 'no_preview_img' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $no_preview_img ); ?>"/>
				<br />
				<small><?php _e( 'Displayed if there is no featured image available.', 'relevant' ); ?></small>
			</p>
			<p>
				<?php _e( 'Show', 'relevant' ); ?>:<br />
				<label for="<?php echo $this->get_field_id( 'show_views' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_views' ); ?>" name="<?php echo $this->get_field_name( 'show_views' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_views ); ?> />
					<?php _e( 'views number', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_date ); ?> />
					<?php _e( 'post date', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_author ); ?> />
					<?php _e( 'author', 'relevant' ); ?>
				</label>
				<br />
				<label for="<?php echo $this->get_field_id( 'show_image' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" type="checkbox" value="1"<?php checked( 1, $show_image ); ?> />
					<?php _e( 'featured image', 'relevant' ); ?>
				</label>				
			</p>
			<p>
				<?php _e( 'Order posts by number of', 'relevant' ); ?>:<br />
				<label>
					<input name="<?php echo $this->get_field_name( 'order_by' ); ?>" type="radio" value="comment_count" <?php checked( 'comment_count', esc_attr( $order_by ) ); ?> /> <?php _e( 'comments', 'relevant' ); ?>
				</label>
				<br />
				<label>
					<input name="<?php echo $this->get_field_name( 'order_by' ); ?>" type="radio" value="views_count" <?php checked( 'views_count', esc_attr( $order_by ) ); ?> /> <?php _e( 'views', 'relevant' ); ?>
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

			$instance = array();
			$instance['widget_title']	= ( isset( $new_instance['widget_title'] ) ) ? stripslashes( esc_html( $new_instance['widget_title'] ) ) : $rltdpstsplgn_options['popular_title'];
			$instance['count']			= ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : $rltdpstsplgn_options['popular_posts_count'];
			$instance['min_count']		= ( ! empty( $new_instance['min_count'] ) ) ? intval( $new_instance['min_count'] ) : $rltdpstsplgn_options['popular_min_posts_count'];
			$instance['excerpt_length'] = ( ! empty( $new_instance['excerpt_length'] ) ) ? intval( $new_instance['excerpt_length'] ) : $rltdpstsplgn_options['popular_excerpt_length'];
			$instance['excerpt_more']	= ( ! empty( $new_instance['excerpt_more'] ) ) ? stripslashes( esc_html( $new_instance['excerpt_more'] ) ) : $rltdpstsplgn_options['popular_excerpt_more'];
			$instance["use_category"]	= isset( $new_instance["use_category"] ) ? absint( $new_instance["use_category"] ) : 0;

			$show_options = array( 'views', 'date', 'author', 'image' );
			foreach ( $show_options as $item )
				$instance["show_{$item}"] = isset( $new_instance["show_{$item}"] ) ? absint( $new_instance["show_{$item}"] ) : 0;

			if ( ! empty( $new_instance['no_preview_img'] ) && rltdpstsplgn_is_200( $new_instance['no_preview_img'] ) && getimagesize( $new_instance['no_preview_img'] ) )
				$instance['no_preview_img'] = $new_instance['no_preview_img'];
			else
				$instance['no_preview_img'] = $rltdpstsplgn_options['popular_no_preview_img'];
			$instance['order_by'] 		= ( ! empty( $new_instance['order_by'] ) ) ? $new_instance['order_by'] : $rltdpstsplgn_options['popular_order_by'];
			return $instance;
		}

		function get_post_title_tag( $widget_tag ) {
			preg_match( '/h[1-5]{1}/', $widget_tag, $matches );

			if ( empty( $matches ) )
				return 'h1';

			$number = absint( preg_replace( '/h/', '', $matches[0] ) );
			$number ++;
			return "h{$number}";
			return 1;
		}
	}
}


add_action( 'widgets_init', 'rltdpstsplgn_widget_init' );