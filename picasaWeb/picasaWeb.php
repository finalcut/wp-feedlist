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
if (file_exists(dirname(__FILE__).'/../../wp-includes/rss.php')) {
	require_once(dirname(__FILE__).'/../../wp-includes/rss.php');
} else {
	require_once(dirname(__FILE__).'/../../wp-includes/rss-functions.php');
}

	
if ( !in_array('PicasaWeb', get_declared_classes() ) ) :

class WP_PicasaWeb
{
	
		var $p=array();
		$p['url']="";
		$p['random']=false;
		$p['num']=0;
		$p['size']=160;
		$p['username']="";
		$p['albumid']="";

	
	function WP_PicasaWeb(){
	}



	function display($args){


		if (is_array($args)){
			if(!isset($args['url'])){

				if(!isset($args['username'])){
					print "No URL Provided";
					return false;
				} else {
					$p['username'] = $args['username'];
					$category='album';
					$p['url'] = "http://picasaweb.google.com/data/feed/api/user/" . $p['username'] ."/";
				}

				if(isset($args['albumid'])){
					$p['albumid'] = $args['albumid'];
					$category='photo';
					$p['url'] .= "albumid/".$p['albumid'];
				}

				$p['url'] .= "?category=". $category ."&alt=rss";

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
			if(isset($args['size']) && is_numeric($args['size']) && ($args['size'] == 144 || $args['size'] == 160 || $args['size'] == 288  || $args['size'] == 576 || $args['size'] == 720 )){
				$p['size'] = $args['size'];
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
				$imgUrl = $image['photo']['imgsrc'].'?imgmax='. $p['size'];
				if($p['size'] == 160){
					$imgUrl .= '&crop=1';
				}
																																//?imgmax=160&crop=1
				
				$list .= '<a href="'.$image['link'].'"><img src="' . $imgUrl .'" alt="'.$image['title'].'" /></a>';
			}
		}

		print $list;

	}

	
	
}

//add_action('plugins_loaded', create_function('$wmr', 'global $wp_picasaWeb; $wp_picasaWeb = new WP_PicasaWeb;'));

global $wp_picasaWeb;
$wp_picasaWeb = new WP_PicasaWeb;

endif;


?>
