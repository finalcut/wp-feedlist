<?
/*
	Plugin Name: Feed List
	Plugin URI: http://rawlinson.us/blog/?p=212
	Description: Displays any ATOM or RSS feed in your blog.
	Author: Bill Rawlinson
	Author URI: http://blog.rawlinson.us/
	Version: 2.2b
*/

	// include files
		$relroot = dirname(__FILE__).'/../../';



		// get the magpie libary
			if (file_exists($relroot . 'wp-includes/rss.php')) {
				require_once($relroot . 'wp-includes/rss.php');
			} else if(file_exists($relroot . 'wp-includes/rss-functions.php')){
				require_once($relroot . 'wp-includes/rss-functions.php');
			} else {
				function FeedListInitError(){
				?>
			
				<div id="message" style="margin-top: 15px; padding: 10px;" class="updated fade">There was a problem initializing the feedlist plugin.  Make sure the file feedlist.php is directly under your <strong>wp-content/plugins</strong> directory and not a subdirectory.</div>
			<?
				}
			}
		// end
	// end

	
	
	class FeedList {
		
		/* core methods */
			// called automagically if you use a inline filter (inside a post/page).
			function FeedListFilter($args){
				return FeedList::BuildFeedOutput($args);
			}


			// call this if you want to process one feed
			function FeedListFeed($args){
				echo FeedList::BuildFeedOutput($args);
			}



			// call this if you want to process a feed file
			function FeedListFile($args){
				$args = FeedList::GetArgumentArray($args);
				$output = '';
				// Seed the random number generator:
				srand((double)microtime()*1000000);

				$feed = Array();

				$feedInfo = FeedList::LoadFile($args['file']);
				if(count($feedInfo)){ // we have some feeds
					// Randomize the array:
					shuffle($feedInfo);
					// Make sure we are set to show something:
					($args['feedsToShow'] < 1) ? 1 : $args['feedsToShow'];
					($args['feedsToShow'] > sizeof($feedInfo)) ? sizeof($feedInfo) : $args['feedsToShow'];

					// we will fetch each feed, then coallate items
					for($i=0;$i<$args['feedsToShow'];$i++){
						$thisFeed = $feedInfo[$i];

						$urlAndTitle =  preg_split("/~/", $thisFeed);
						$feedUrl = trim($urlAndTitle[0]);
						$feedTitle = trim($urlAndTitle[1]);
						
						$rs = FeedList::GetFeed($feedUrl);

						if($rs){
							$items = $rs->items;

							if($args['random']){
								shuffle($items);
							}
							// Slice off the number of items that we want:
							if ($args['num_items'] > 0)
							{
								$items = array_slice($items, 0, $args['num_items']);
							}

							if(!$args['mergeFeeds']){
								$output.= '<div class="feedTitle">'.$feedTitle.'</div>';
								if($args['show_date']){
									$output .= '<div class="feedDate">updated: '.fl_tz_convert($rs->last_modified,0,Date('I')).'</div>';
								}

								$output.=FeedList::Draw($items,$args);
							} else {
								$feed = array_merge($feed,$items);
							}

						}

					}
				$output .= '<ul class="randomFeed">';

				if($args['mergeFeeds']){
					$output.=FeedList::Draw($feed,$args);
				} 
			
				$output .= '</ul>';


				} else {
					$output = $args['before'] . 'No Items Were Found In the Provided Feeds. Perhaps there is a communication problem.' . $args['after'];
				}

				// coallate feed items
				echo $output;

			}
		/* end core methods */



		/* basic settings - you can edit these */
			function GetSettings(){
							/*
					CONFIGURATION SETTINGS
					----------------------

					cacheTimeout		how long should your cache file live in seconds?  By default it is 21600 or 6 hours.
								most sites prefer you use caching so please make sure you do!

					connectionTimeout	how long should I try to connect the feed provider before I give up, default is 15 seconds


					showRssLinkListJS	TRUE by default and will include a small block of JS in your header.  If it is false the JS will not be
								included. If you want the $new_window = 'true' option to use the JS then this must also be true.
								Otherwise both true and simple will hardcode the target="_blank" into the new window links
				*/

				// DEFINE THE SETTINGS -- EDIT AS YOU NEED:
				$feedListDebug = false; // To debug this script during programming (true/false).

				$cacheTimeout = 21600;		// 21600 sec is 6 hours.
				$connectionTimeout = 15;	// 15 seconds is default
				$showRSSLinkListJS = true;
				
				$Language = 'en_US'; // Choose your language (from the available languages below,in the translations):
				
				
				$Translations = array(); // Please send in your suggestions/translations:

					// English:
					$Translations['en_US'] = array();
					$Translations['en_US']['ReadMore']		= 'Read more...';

					// Dutch:
					$Translations['nl_NL'] = array();
					$Translations['nl_NL']['ReadMore']		= '[lees verder]';

				
				$feedListFile = '/feeds.txt'; // IF you are going to use the random feedlist generator make sure this holds the correct name for your feed file:

				// Build an array out of the settings and send them back:
				$settings = array (	'feedListDebug' => $feedListDebug,
							'cacheTimeout' => $cacheTimeout,
							'connectionTimeout' => $connectionTimeout,
							'showRSSLinkListJS' => $showRSSLinkListJS,
							'language' => $Language,
							'translations' => $Translations,
							'feedListFile' => $feedListFile
				);

				return $settings;

			}

			function GetDefaults(){
				$settings = FeedList::GetSettings();
				return array(	'rss_feed_url' => 'http://del.icio.us/rss',
							'num_items' => 15,
							'show_description' => true,
							'random' => false,
							'before' => '<li>',
							'after' => '</li>',
							'description_separator' => ' - ',
							'encoding' => false,
							'sort' => 'none',
							'new_window' => false,
							'ignore_cache' => false,
							'suppress_link' => false,
							'show_date' => false,
							'additional_fields' => '',
							'max_characters' => 0,
							'max_char_wordbreak' => true,
							'file'=>$settings['file'],
							'feedsToShow'=>0,
							'mergeFeeds'=>false
						);
			
			}
		/* end basic settings */
			function BuildFeedOutput($args){
				$args = FeedList::GetArgumentArray($args);

				$rs = FeedList::GetFeed($args['rss_feed_url']);
				$output = '';
				if($rs){
					$items = $rs->items;
					if($args['random']){
						shuffle($items);
					}
					// Slice off the number of items that we want:
					if ($args['num_items'] > 0)
					{
						$items = array_slice($items, 0, $args['num_items']);
					}
					$output = FeedList::Draw($items,$args);
				}

				return $output;
			}


			function Draw($items,$args){
				$settings = FeedList::GetSettings();
				$items = FeedList::NormalizeDate($items);

				$items = FeedList::SortItems($items,$args['sort']);

				// Explicitly set this because $new_window could be "simple":
				$target = '';
				if($new_window == true && $settings["showRSSLinkListJS"])
				{
					$target=' rel="external" ';
				}
				elseif ($new_window == true || $new_window == 'simple')
				{
					$target=' target="_blank" ';
				}

				$output ='';

				foreach($items as $item){
					$thisLink = '';
					$linkTitle = '';
					$thisDescription = '';
					$thisTitle = $item['title'];

					if ($args['encoding']){ // very poor and limited internationalization effort
						$thisTitle = htmlentities(utf8_decode($thisTitle));
					}

					if (isset($item['content']['encoded']) || isset($item['description'])){
						if (isset($item['description'])){
							$thisDescription = $item['description'];
						}
						else{
							$thisDescription = $item['content']['encoded'];
						}
						
						// Handle max_characters and max_char_wordbreak before the htmlentities makes it more complicated:
						if (!empty($args['max_characters']) && is_numeric($args['max_characters']))
						{
							$thisDescription = substr($thisDescription, 0, $args['max_characters']);

							// If true, we cut on the last space:
							if (!empty($args['max_char_wordbreak']))
							{
								$max_char_pos = strrpos($thisDescription, ' ');
								if ($max_char_pos > 0)
								{
									$thisDescription = substr($thisDescription, 0, $max_char_pos);
								}
							} 

						} else if ($encoding) { 
							//further really weak attempt at internationalization
							$thisDescription = htmlentities(utf8_decode($thisDescription));
						}

						$linkTitle = $thisDescription;
						$linkTitle = strip_tags($linkTitle);
						$linkTitle = str_replace(array("\n", "\t", '"'), array('', '', "'"), $linkTitle);
						$linkTitle = substr($linkTitle, 0, 300);
	
						if (strlen(trim($thisDescription)))
						{
							$thisDescription = $args['description_seperator'].$thisDescription;
						}
					}

					// Only build the hyperlink if a link is provided..and we are not told to suppress the link:
					if (!$args['suppress_link'] && strlen(trim($item['link'])) && strlen(trim($thisTitle))){
						$thisLink = '<span class="rssLinkListItemTitle"><a href="'.htmlentities(utf8_decode($item['link'])).'"' . $target .' title="'.$linkTitle.'">'.$thisTitle.'</a></span>';
					}
					elseif (strlen(trim($item['link'])) && $args['show_description'])
					{
						// If we don't have a title but we do have a description we want to show.. link the description
						$thisLink = '<span class="rssLinkListItemTitle"><a href="'.htmlentities(utf8_decode($item['link'])).'"' . $target .'><span class="rssLinkListItemDesc">'.$thisDescription.'</span></a></span>';
						$thisDescription = '';
					}
					else
					{
						$thisLink = '<span class="rssLinkListItemTitle">' . $thisTitle . '</span>';
					}

					// Determine if any extra data should be shown:
					$extraData = '';
					if (strlen($args['additional_fields'])){
						// Magpie converts all key names to lowercase so we do too:
						$args['additional_fields'] = strtolower($args['additional_fields']);

						// Get each additional field:
						$addFields = explode('~', $args['additional_fields']);

						foreach ($addFields as $addField)
						{
							// Determine if the field was a nested field:
							$fieldDef = explode('.', $addField);
							$thisNode = $item;
							foreach($fieldDef as $fieldName)
							{
								// Check to see if the fieldName has a COLON in it, if so then we are referencing an array:
								$thisField = explode(':', $fieldName);
								$fieldName = $thisField[0];

								$thisNode = $thisNode[$fieldName];
								if (count($thisField) == 2)
								{
									$fieldName = $thisField[1];
									$thisNode = $thisNode[$fieldName];
								}
							}
							if (is_string($thisNode) && isset($thisNode))
							{
								$extraData .= '<div class="feedExtra'.str_replace(".","",$addField).'">' . $thisNode . '</div>';
							}
						}
					}

					if ($args['show_description']){
						$output .= $args['before'].$thisLink.$thisDescription.$extraData;
					}else{
						$output .= $args['before'].$thisLink.$extraData;
					}
					if (is_numeric($args['max_characters']) && $args['max_characters'] > 0) {
						$output .= '<div class="ReadMoreLink"><a href="'.htmlentities(utf8_decode($item['link'])).'">'.$settings["translations"][$settings["language"]]['ReadMore'].'</a> &nbsp; </div>';
					}

					$output .= $args['after'];



				}


				return $output;
			}

			function ArrayPush(&$arr) {
			   $args = func_get_args();
			   foreach ($args as $arg) {
				   if (is_array($arg)) {
					   foreach ($arg as $key => $value) {
						   $arr[$key] = $value;
						   $ret++;
					   }
				   }else{
					   $arr[$arg] = "";
				   }
			   }
			   return $ret;
			}
		/* utility functions */

			function NormalizeDate($items){
				$newItems = array();

				foreach($items as $item){
					if(array_key_exists('pubdate',$item)) {
						$d = $item['pubdate'];
						$d = explode(' ',$d);

						$d = $d[3]   . FeedList::GetMonthNum($d[2]) .   $d[1] . $d[4] . '0000';
						$d = FeedList::MakeNumericOnly($d);
						FeedList::ArrayPush($item,array("feeddate"=>$d));

					} else if (array_key_exists('published',$item)) {
						$d = $item['published'];
						$d = FeedList::MakeNumericOnly($d);
						FeedList::ArrayPush($item,array("feeddate"=>$d));
					} else if (array_key_exists('dc',$item) && array_key_exists('date',$item['dc'])) {
						$d = $item['dc'];
						$d = $d['date'];
						$d = FeedList::MakeNumericOnly($d);
						FeedList::ArrayPush($item,array("feeddate"=>$d));
					} else {
						$d = date("YmdHmsO");
						$d = FeedList::MakeNumericOnly($d);
						FeedList::ArrayPush($item,array("feeddate"=>$d));

					}
					array_push($newItems,$item);
				}

				return $newItems;
			}

			function MakeNumericOnly($val){
				return ereg_replace( '[^0-9]+', '', $val);
			}


			function GetMonthNum($month){
				$months = array('jan'=>'01','feb'=>'02','mar'=>'03','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12');
				$month = strtolower($month);
				return $months[$month];
			}

			function SortItems($items,$args){
				$sort = strtolower($args);
				$sort = explode(" ",$sort);


				if(count($sort) ==1 || $sort[1] == 'asc'){
					$sort[1] = SORT_ASC;
				} elseif ($sort[1] == 'desc') {
					$sort[1] = SORT_DESC;
				} else {
					$sort[1] = '';
				}

				if($sort[0] == 'feeddate'){
					$sort[2] = SORT_NUMERIC;
				} else {
					$sort[2] = SORT_STRING;
				}
				if (($sort[1]!='') && count($items))
				{
					// Order  by sortCol:
					foreach($items as $item)
					{
						$sortBy[] = $item[$sort[0]];
					}

					// Make titles lowercase (otherwise capitals will come before lowercase):
					$sortByLower = array_map('strtolower', $sortBy);

					array_multisort($sortByLower, $sort[1], $sort[2], $items);
				}
				
				return $items;
			}

			function LoadFile($file){
				/*	
					load the $feedListFile  contents into an array, using the --NEXT-- text as
					a delimeter between feeds and a tilde (~) between URL and TITLE
				*/
				$x = file($file);
				return preg_split("/--NEXT--/", join('', file($file)));
			}

			function GetArgumentArray($args){
				$args = FeedList::AssignDefaults($args);
				$a = array();
				foreach($args as $d=>$v){
					if($args[$d] === 'true') { 
						$a[$d] = 1;
					}else if($args[$d] === 'false'){
						$a[$d] = 0;
					}else{
						$a[$d] = $v;
					}

					$a[$d] =  html_entity_decode($a[$d]);


				}
				return $a;
			}


			function AssignDefaults($args){
				$defaults = FeedList::GetDefaults();
				$a = array();
				$i=0;
				foreach ($defaults as $d => $v)
				{
					$a[$d] = isset($args[$d]) ? $args[$d] : $v;
					$a[$d] = isset($args[$i]) ? $args[$i] : $a[$d];
					$i++;
				}
				return $a;
			}

			function GetFeed($feedUrl){
				$feed = false;
				if(function_exists('fetch_rss')){
					$feed =  fetch_rss($feedUrl);
				} else {
					return $feed;
				}
			}

			function InitializeReader($ignore_cache){
				$settings = FeedList::GetSettings();

				if ($ignore_cache)
				{
					if (is_numeric($ignore_cache))
					{
						define('MAGPIE_CACHE_AGE', $ignore_cache);
					}
					else
					{
						define('MAGPIE_CACHE_ON', false);
					}
				}
				else
				{
					define('MAGPIE_CACHE_AGE', $settings["cacheTimeout"]);
				}
				define('MAGPIE_DEBUG', false);
				define('MAGPIE_FETCH_TIME_OUT', $settings["connectionTimeout"]);
			}

			function Debug($val,$name=''){
				if(strlen($name)){
					print('<h1>'.$name.'</h1>');
				}
				print('<pre>');
				print_r($val);
				print('</pre>');
			}

		/* end utility functions */

	}

		function rssLinkListFilter($text)
		{
			return preg_replace_callback("/<!--rss:(.*)-->/", "feedListFilter", $text);
		}


	/* Templates can call any of these functions */
		function rssLinkList($args){
			return feedList($args);
		}
		function feedList($args){
			$feed = new FeedList();

			if(!is_array($args)){
				$args = func_get_args();
			}
			return $feed->FeedListFeed($args);
		}

		function randomFeedList($args){
			if(!is_array($args)){
				$args = parse_str($args,$a);
				$args = $a;
			}
			$feed = new FeedList();
			return $feed->FeedListFile($args);
		}
		
		function feedListFilter($args){
			$args = explode(",",$args[1]);
			$a = array();
			foreach($args as $arg){
				$arg = explode(":=",$arg);
				$a[$arg[0]] = $arg[1];
			}
			$args = $a;
			$feed = new FeedList();
			return $feed->FeedListFilter($args);
		}

	/* end template functions */

		if (function_exists('add_filter'))
		{
			add_filter('the_content', 'rssLinkListFilter');
		}

		if(function_exists('FeedListInitError')){
			add_action('admin_head','FeedListInitError');
		}

?>