<?php
/*
Plugin Name: Top Categories
Plugin URI: http://rawlinson.us/blog/articles/top-categories-plugin/
Description: Display your top x categories by post where x is the number of categories you want to display.  Can be seen in use at: <a href="http://rawlinson.us/blog/archives/">Top Categories</a>.
Version: 1.1
Author: Bill Rawlinson
Author URI: http://blog.rawlinson.us/
*/
function top_categories($args = '')
{

		if( is_numeric($args){ // in case user just passed in the number of categories to show (maintains backwards compatability)
			$r = array('number'=>$args);
		} else if ( is_array($args) ){ // if user defines the array to pass in (use $defaults for information the structure the array must match)
			$r = &$args;
		} else{
			parse_str($args, $r); // in case they pass in a "Url" type string of arguments
		}


		// $exclude and $include are comma separated lists of category ids such as '1,2,3,4'

		$defaults = array('type' => 'post', 'child_of' => 0, 'orderby' => 'count', 'order' => 'DESC',
		'hide_empty' => false, 'include_last_update_time' => false, 'hierarchical' => 0, $exclude => '', $include => '',
		'number' => 10);

		$r = array_merge($defaults, $r);


		$cats = get_categories($r);


		$catcount = 0;
		$output="";

        foreach ($cats as $cat)
        {
				$output .= '<li class="categories"><a href="' . get_category_link($cat->cat_id) . $cat->category_nicename . '/">' . $cat->cat_name . '</a> (' . $cat->category_count . ')</li>';
        }


	print $output;
}
?>