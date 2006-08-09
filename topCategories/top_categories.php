<?php
/*
Plugin Name: Top Categories
Plugin URI: http://rawlinson.us/blog/articles/top-categories-plugin/
Description: Display your top x categories by post where x is the number of categories you want to display.  Can be seen in use at: <a href="http://rawlinson.us/blog/archives/">Top Categories</a>.
Version: 1.0
Author: Bill Rawlinson
Author URI: http://blog.rawlinson.us/
*/
function top_categories($displaynum=10)
{


		$defaults = array('type' => 'post', 'child_of' => 0, 'orderby' => 'count', 'order' => 'DESC',
		'hide_empty' => false, 'include_last_update_time' => false, 'hierarchical' => 0, $exclude => '', $include => '',
		'number' => $displaynum);



		$cats = get_categories($defaults);


		$catcount = 0;
		$output="";

        foreach ($cats as $cat)
        {
				$output .= '<li class="categories"><a href="' . get_category_link($cat->cat_id) . $cat->category_nicename . '/">' . $cat->cat_name . '</a> (' . $cat->category_count . ')</li>';
        }


		// order from lowest to highest
		//natsort($counts);


		// get the last ten,which have the highest counts ..
		//$counts = array_slice($counts,$displaynum,$displaynum);

		// reverse the order so we go from highest to lowest
		//$counts = array_reverse($counts);

/*
		print "<ul>";
        foreach ($counts as $catname => $count)
        {
                $catlink = $catlinks{$catname};
                if (strstr($catlink, "http:") == FALSE) {
                        print "<li><a href=\"$myurl$category_base/$catlink\" rel=\"tag\" title=\"$count entries\">$catname</a></li>";
                } else {
                        print "<li><a href=\"$catlink\" rel=\"tag\" title=\"$count entries\">$catname</a></li>";
                }
        }
		print "</ul>";
	*/
	print $output;
}
?>