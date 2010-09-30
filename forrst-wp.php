<?php
/*
Plugin Name: Forrst WP
Plugin URI: http://www.igeek.co.za/
Description: Add a Forrst Widget to your WordPress site displaying your latest Forrst Posts.
Author: Gerhard Potgieter
Version: 1.0
Author URI: http://www.igeek.co.za/
*/

//initialize the widget
add_action('widgets_init', 'forrst_load_widget');

function forrst_load_widget() {
	register_widget('Forrst_Widget');
}

//Forrst Widget Class
class Forrst_Widget extends WP_Widget {
	
	function Forrst_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'forrst', 'description' => 'An widget that displays a users latest Forrst posts.' );

		/* Widget control settings. */
		$control_ops = array('id_base' => 'forrst-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'forrst-widget', 'Forrst Widget', $widget_ops, $control_ops );
	}
	
	//Display the Widget
	function widget($args, $instance) {
		extract($args);

		/* settings */
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['username'];
		$nrposts = $instance['nrposts'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Go fetch Forrst posts for user. */
		require_once "forrst.php";
		$forrst = new Forrst($name,$nrposts);
		$forrstResults =  $forrst->getPosts();
		
		$forrstCounter = 0;
		/* Check if everything was okay before continuing */
		if(strtolower($forrstResults->resp->stat) == 'ok') {
			/* Loop through all users posts */
			foreach($forrstResults->resp->posts as $forrstPost) {
				$forrstCounter++;
				if($forrstCounter > $nrposts)
					break;
				//Shorten the title a bit
				if(strlen($forrstPost->page_title) > 30)
					$forrstTitle = substr($forrstPost->page_title,0,27)."...";
				else
					$forrstTitle = $forrstPost->page_title;
				
				//Shorten the description a bit
				if($forrstPost->content)
					$theDesc = $forrstPost->content;
				elseif($forrstPost->description)
					$theDesc = $forrstPost->description;
					
				if(strlen($theDesc) > 150)
					$forrstDesc = substr($theDesc,0,147)."...";
				else
					$forrstDesc = $theDesc;
				
				echo "<div class=\"forrst_post\">";
				echo "<h4><a href=\"http://forrst.com".$forrstPost->post_url."\">".$forrstTitle."</a></h4>";
				echo "<p>".$forrstDesc."</p></div>";
			}
		} else {
			echo "<p>Error fetching latest Forrst posts: ".$forrstResults->resp->reason."</p>";
		}


		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	//Handle widget settings update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['nrposts'] = $new_instance['nrposts'];

		return $instance;
	}
	
	//The widget settings form
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'My Latest Forrst Posts', 'username'=>'kyle', 'nrposts' => 5);
		$instance = wp_parse_args( (array) $instance, $defaults ); 
?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>">Forrst Username:</label>
			<input id="<?php echo $this->get_field_id( 'username' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'nrposts' ); ?>">Nr of posts to show:</label>
			<input id="<?php echo $this->get_field_id( 'nrposts' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'nrposts' ); ?>" value="<?php echo $instance['nrposts']; ?>" style="width:100%;" />
		</p>
<?php
	}
}
?>