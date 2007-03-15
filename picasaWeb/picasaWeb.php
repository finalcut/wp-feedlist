<?php

/*
	Plugin Name: PicasaWeb
	Plugin URI: 
	Description: Display thumbnails from one of your picasaweb feeds.
	Author: Bill Rawlinson
	Author URI: http://blog.rawlinson.us/
	Version: 1.4
*/


if(!class_exists('Zend')){
ini_set("include_path", dirname(__FILE__)."\\ZendLibrary\\");
require_once 'Zend/Feed.php';
}
	
if ( !in_array('PicasaWeb', get_declared_classes() ) ) :

class WP_PicasaWeb
{

		function setArguments($args){
			$nationality = "en_US";
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
						$p['url'] = 'http://picasaweb.google.com/data/feed/base/user/' . $p['username'] . '/albumid/';
						$p['albumid'] = $args['albumid'];
						$category='photo';
						$p['url'] .= "albumid/".$p['albumid'];
					}

					$p['url'] .= "?kind=". $category ."&access=public&hl=" . $nationality;

				}else{
					$p['url'] = $args['url'];
				}


				if (isset($args['random'])){

						$p['random'] = $this->trueOrFalse($args['random']);

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

	function trueOrFalse($val){
		$isTrue = false;
		if($val == 'true' || (is_numeric($val) && $val != 0)){
			$isTrue = true;
		}
	
		return $isTrue;

	}

	function display($args){
		$p = WP_PicasaWeb::setArguments($args);


		if($p['showRandomAlbum']){ // we need to try and load all albums, pick one at random, then try this over again...
			
			if($albums =  WP_PicasaWeb::loadFeed($p['url'])) {
				shuffle($albums);
				$p['url'] = $albums[0]['album_feed_url'];

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
				$imgUrl = $image['album_thumbnail_url'].'?imgmax='. $p['size'];
				$imgLink= $image['image_url'];
				if($p['size'] == 160){
					$imgUrl .= '&crop=1';
				}
				
				if($p['linkToAlbum']){
					$imgLink= $image['album_url'];
				}

				$list .= '<a href="'.$imgLink.'"><img src="' . $imgUrl .'" alt="'.$image['title'].'" /></a>';
			}
		}




		print $list;

	}


	function loadFeed($url){
		try {
			Zend_Feed::registerNamespace('media','http://search.yahoo.com/mrss/');
			Zend_Feed::registerNamespace('gphoto','http://schemas.google.com/photos/2007');
			$feed = Zend_Feed::import($url);
		} catch (Zend_Feed_Exception $e) {
			// feed import failed
			print "Exception caught importing feed: {$e->getMessage()}\n";
			exit;
		}
		
		$items = array();
		foreach($feed as $item){

			/*figure out the image id if we can */
			$baseID = $item->link('alternate');
			$baseID = explode('/',$baseID);
			$baseID = $baseID[count($baseID)-1];
				
			$items[] = array(
					'title' => $item->title(),
					'image_url'=> $item->link('alternate') . '/' . $baseID,
					'album_url' => $item->link('alternate'),
					'description' => $item->summary(),
					'album_feed_url' => $item->link('http://schemas.google.com/g/2005#feed'),
					'album_thumbnail_url'=>$item->group->content['url']
					
				);

		}
		return $items;
	} 

	function debug($val){

		print("<pre>");
		print_r($val);
		print("</pre>");

	}

	
}

function picasaWeb($args){
	$pw = new WP_PicasaWeb();
	$pw->display($args);
}

endif;


?>