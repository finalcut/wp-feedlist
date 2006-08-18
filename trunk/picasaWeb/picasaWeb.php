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

	
	
	function picasaWeb($args){

		$p=array();
		$p['url']="";
		$p['random']=false;
		$p['num']=0;


		if (is_array($args)){
			if(!isset($args['url'])){
				print "No URL Provided";
				return false;
			}else{
				$p['url'] = $args['url'];
			}


			if (isset($args['random'])){

					if ($args['random'] == 'true' || $args['random'] == 1){
						$p['random'] = true;
					}else{
						$p['random'] = false;
					}

			}
			
			if (isset($args['num']) && is_numeric($args['num']))
			{
				$p['num'] = $args['num'];
			}

		} else {
			$p['url'] = $args;
		}



		$list = "";
		
		if($images = fetch_rss($p['url'])){
			$images = $images->items;
			if ($p['random'])
			{
				// We want a random selection, so lets shuffle it
				shuffle($images);
			}
			if ($p['num'] > 0)
			{
				// Slice off the number of items that we want:
				$images = array_slice($images, 0, $p['num']);
			}


			foreach ($images as $image) {

				
				$list .= '<a href="'.$image['link'].'"><img src="'.$image['photo']['thumbnail'].'" alt="'.$image['title'].'" /></a>';
			}
		}

		print $list;

	}

	
	

?>