<?php

/*
	Plugin Name: PicasaWeb
	Plugin URI: 
	Description: Display thumbnails from one of your picasaweb feeds.
	Author: Bill Rawlinson
	Author URI: http://blog.rawlinson.us/
	Version: 1.1



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

		private function setArguments($args){
		$p=array();
		$p['url']="";
		$p['random']=false;
		$p['num']=0;
		$p['size']=160;
		$p['username']="";
		$p['albumid']="";
		$p['showRandomAlbum']=false;
		$p['linkToAlbum']=false;
		
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

					$p['random'] = $this->trueOrFalse($args['random']);

					$p['randomAlbumImage'] = false;

			} 
			
			if(isset($args['showRandomAlbum']) && !isset($args['albumid']) && !isset($args['url'])){ // only do this url='', and albumid = ''
				$p['showRandomAlbum'] = $this->trueOrFalse($args['showRandomAlbum']);
			} 
			
			if (isset($args['num']) && is_numeric($args['num']))
			{
				$p['num'] = $args['num'];
			}
			if(isset($args['size']) && is_numeric($args['size']) && ($args['size'] == 144 || $args['size'] == 160 || $args['size'] == 288  || $args['size'] == 576 || $args['size'] == 720 )){
				$p['size'] = $args['size'];
			}
			if (isset($args['linkToAlbum']))
			{
				$p['linkToAlbum'] = $this->trueOrFalse($args['linkToAlbum']);
			}
		} else {
			$p['url'] = $args;
		}

		return $p;
	}

	private function trueOrFalse($val){
		$isTrue = false;
		if($val == 'true' || (is_numeric($val) && $val != 0)){
			$isTrue = true;
		}
	
		return $isTrue;

	}

	public function display($args){
		$p = WP_PicasaWeb::setArguments($args);

		if($p['showRandomAlbum']){ // we need to try and load all albums, pick one at random, then try this over again...
			
			if($albums =  WP_PicasaWeb::loadFeed($p['url'])) {
				shuffle($albums);

				$p['url'] = $albums[0]['gphoto']['rsslink'];

			}
		}

		

		$list = "";
		
		if($images = WP_PicasaWeb::loadFeed($p['url'])){
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
				$imgLink= $image['link'];
				if($p['size'] == 160){
					$imgUrl .= '&crop=1';
				}
				
				if($p['linkToAlbum']){
					$imgLink = substr($imgLink, 0, strrpos($imgLink, '/'));
				}

				$list .= '<a href="'.$imgLink.'"><img src="' . $imgUrl .'" alt="'.$image['title'].'" /></a>';
			}
		}




		print $list;

	}




	private function loadFeed($url){
		$items = array();
		if($items = fetch_rss($url)){
			$items =  $items->items;
		} 

		return $items;
	}

	
}

function picasaWeb($args){
	$pw = new WP_PicasaWeb();
	$pw->display($args);
}

endif;


?>
