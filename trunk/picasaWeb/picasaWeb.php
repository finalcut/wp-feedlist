<?php

/*
	Plugin Name: PicasaWeb
	Plugin URI: http://rawlinson.us/blog/articles/picasaweb-wordpress-plugin/
	Description: Display thumbnails from one of your picasaweb feeds.
	Author: Bill Rawlinson
	Author URI: http://blog.rawlinson.us/
	Version: 1.7
*/




if(!class_exists('lastRSS')){
	require_once(dirname(__FILE__).'/lastRSS.php');
//	require_once 'lastRSS.php';
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
						$p['url'] = "http://picasaweb.google.com/data/feed/base/user/" . $p['username'] ."";
					}

					if(isset($args['albumid'])){
						$p['url'] = 'http://picasaweb.google.com/data/feed/base/user/' . $p['username'] . '/albumid/';
						$p['albumid'] = $args['albumid'];
						$category='photo';
						$p['url'] .= $p['albumid'];
					}

					$p['url'] .= "?kind=". $category ."&access=public&alt=rss&hl=" . $nationality;

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
				$imgLink= $image['image_link'];
				
				if($p['linkToAlbum']){
					$imgLink = $img['album_link'];
				}
				
				if($p['size'] == 160){
					$imgUrl .= '&crop=1';
				}
				

				$list .= '<a href="'.$imgLink.'"><img src="' . $imgUrl .'" alt="'.$image['title'].'" /></a>';
			}
		}




		print $list;

	}


	function loadFeed($url){
			$rss = new lastRSS;
			$feed = $rss->get($url);

			if(!$feed) {
			// feed import failed
			print "Exception caught importing feed - Feed file not found";
			exit;
			}

		
		$items = array();


		foreach($feed['items'] as $item){

		//picasaweb uses a pretty complex atom feed format - so we hare kind of hacking to get the properties from
		// the rss format found by lastRSS - this makes the whole plugin a bit more usable to more people.

		// this regular expression matching is courtesy of Kiroro at http://kiroro.prophp.org/blog/
			if(preg_match('<img [^/]* src="([^"]*)" [^/]*/>', $item['description'],$imgUrlMatches)) {
				$imgurl = $imgUrlMatches[1];
			}


			$fileurl = explode('/',$imgurl);
			$fileurl = $fileurl[count($fileurl)-1];
			$imgurl = explode('s288',$imgurl);

			$imgurl=$imgurl[0];	

			$albumLink = $item['link'];

			$i = strstr($albumLink, "photo#");
			if(strlen($i) > 0){
				$albumLink = substr($albumLink, 0,  strlen($albumLink) - strlen($i)-1);
			}

			$str=explode('<',$item['title']);
			$items[] = array(
						'title' =>  urldecode($fileurl),
						'image_url'=> $imgurl,
						'image_link' => $item['link'],
						'album_link'=>$albumLink,
						'description' => strip_tags($str[0]),
						'album_feed_url' =>$item['guid'] ,
						'album_thumbnail_url'=>$imgurl
						
					);
		}
//		WP_PicasaWeb::debug($items);
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