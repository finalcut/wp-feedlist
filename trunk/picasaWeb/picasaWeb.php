<?php

/*
	Plugin Name: PicasaWeb
	Plugin URI: 
	Description: Display thumbnails from one of your picasaweb feeds.
	Author: Bill Rawlinson
	Author URI: http://blog.rawlinson.us/
	Version: 1



		Has seen very limited testing with non UTF-8 encoding.
*/


	// get the magpie libary
if (file_exists(dirname(__FILE__).'/../../wp-includes/rss-functions.php')) {
	require_once(dirname(__FILE__).'/../../wp-includes/rss-functions.php');
} else {
	require_once(dirname(__FILE__).'/../../wp-includes/rss.php');
}

	
	
	function picasaWeb($url){
		$list = "";
		
		if($images = fetch_rss($url)){
			$images = $images->items;


			foreach ($images as $image) {

				
				$list .= '<a href="'.$image['link'].'"><img src="'.$image['photo']['thumbnail'].'" alt="'.$image['title'].'" /></a>';
			}
		}

		print $list;

	}

	
	

?>