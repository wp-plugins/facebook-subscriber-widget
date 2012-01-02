<?php
/*
Plugin Name: Facebook Subscriber Widget
Plugin URI: http://wordpress.org/extend/plugins/facebook-subscriber-widget/
Description: Place a Facebook Subscribe button on your Wordpress blog as a widget and/or shortcode.
Version: 1.0
Author: Mallikarjun Yawalkar
Author URI: http://digitalfair.tk
*/

class FB_Subscribe_Widget extends WP_Widget {
	function FB_Subscribe_Widget() {
		$widget_ops = array( 'classname' => 'FB_Subscribe_Widget', 'description' => 'Place a Facebook Subscribe button on your Wordpress blog as a widget.' );
		$control_ops = array( 'id_base' => 'fb-subscribe-widget' );
		$this->WP_Widget( 'fb-subscribe-widget', 'Facebook subscribe Widget', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance) {
		extract( $args );
		$layout = empty($instance['layout']) ? 'standard' : $instance['layout'];
		$show_faces = ($instance['show_faces']) ? 'true' : 'false';
		$method = (empty($instance['method'])) ? 'iframe' : $instance['method'];
		if (!empty($instance['url'])) {
			$url = urlencode($instance['url']);
		} else {
			$url = urlencode('http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
		}
		echo $before_widget;
		if (!empty($instance['title'])) {	
			echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;	
		}		
		if ($method == 'iframe') {
			switch ($layout)
			{
				case 'box_count':
					$height = '65';
					break;
				case 'button_count':
					$height = '20';
					break;
				default:
					if ($instance['show_faces']) {
						$height = '80';	
					}
					else
					{		
						$height = '35';
					}
					break;	
			}
			echo "\n	<iframe src=\"http://www.facebook.com/plugins/subscribe.php?href=".$url."\"&amp;layout=".$layout."\"&amp;show_faces=$show_faces&amp;width=100%&amp;font&amp;colorscheme=".$instance['color']."&amp;height=".$height."px\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:100%; height:".$height."px;\" allowTransparency=\"true\"></iframe>";
		} else {
			echo "\n 	<fb:subscribe href=".$url."\" show_faces=\"true\" width=\"450\"></fb:subscribe>";
		}
		echo $after_widget;
	}
	
	function shortcode_handler( $atts, $content=null, $code="" ) {
		extract( shortcode_atts( array(
			'method' => 'iframe',
			'color' => 'light',
			'url' => ''
		), $atts ) );
		if ($url != '') {
			$url = urlencode($url);
		} else {
			$url = get_permalink();
		}
		if ($method == 'iframe') {
			$retval = "<iframe src=\"http://www.facebook.com/plugins/subscribe.php?href=".$url."\"&amp;layout=".$layout."\"&amp;show_faces=false&amp;width=100%&amp;font&amp;colorscheme=".$color."&amp;height=20px\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:100%; height:20px;\" allowTransparency=\"true\"></iframe>";
		} else {
			$retval = "<fb:subscribe href=".$url."\" show_faces=\"true\" width=\"450\"></fb:subscribe>"; 
			$retval .= " font=\"\"";
			if ($color != 'light') {
				$retval .= " colorscheme=\"".$color."\"";
			}
		}
		return $retval;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['layout'] = $new_instance['layout'];
		$instance['show_faces'] = isset($new_instance['show_faces']) ? true : false;
		$instance['color'] = $new_instance['color'];
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['method'] = $new_instance['method'];
		return $instance;
	}
	
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array('title' => '', 'layout' => 'standard', 'show_faces' => false, 'color' =>light, 'url' => '', 'method' => 'iframe'));
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title (<b>Optional</b> you may leave this empty):</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_faces'], true ); ?> id="<?php echo $this->get_field_id( 'show_faces' ); ?>" name="<?php echo $this->get_field_name( 'show_faces' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_faces' ); ?>">Show faces (only when using standard layout)</label>
		</p>	
		<p>
			<label for="<?php echo $this->get_field_id( 'color' ); ?>">Color scheme:</label>
			<select id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( "light" == $instance['color'] ) echo 'selected="selected"'; ?> value="light">Light</option>
				<option <?php if ( "dark" == $instance['color'] ) echo 'selected="selected"'; ?> value="dark">Dark</option>
			</select>
		</p>	
		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>">Facebook Profile URL </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" />
		</p>	

		<p><hr />
			<label>If you subscribe this plugin, Please contribute your subscribe on facebook:  </label><br />
<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FDigital-Fair%2F180655755348012&amp;send=false&amp;layout=button_count&amp;width=350&amp;show_faces=false&amp;action=subscribe&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:250px; height:21px;" allowTransparency="true"></iframe>		
	</p>	

		<?php
	}
}
add_shortcode( 'subscribe', array('FB_Subscribe_Widget', 'shortcode_handler') );
add_action('widgets_init', create_function('', 'return register_widget("FB_Subscribe_Widget");'));
?>