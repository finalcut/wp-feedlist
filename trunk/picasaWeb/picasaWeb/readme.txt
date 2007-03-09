author:		 William Rawlinson
version:	 1.3
date:		 18 Aug 2006
plugin URL:	 http://rawlinson.us/blog/articles/picasaweb-wordpress-plugin/

installation:

1. put picasaWeb (or this directory) in your wordpress plugins folder and activate it with the wordpress controlpanel.

2. REALLY REALLY IMPORTANT!!!
==============================================================================================================================================================================================
EITHER: 
	1. rename your existing rss.php file to rss.php.bak and put the included rss.php file in your wp-includes directory (make sure you save the origional one as rss.php.bak!!)
	OR
	2. insert the following three lines of PHP near line 116:

		if(count($attrs)){
			$this->current_item[$element]['attrs'] = $attrs;
		}

	These should end up in the function named: feed_start_element and should come after the line that says 		$attrs = array_change_key_case($attrs, CASE_LOWER);


	If after installing picasaWeb another rss plugin breaks (such as feedList) then remove picasaWeb and revert to the old rss.php.  I had to make a customization to rss.php because
	the default installation of magpie was deleting part of the atom feed (attributes on empty elements).
==============================================================================================================================================================================================


usage:

whever you want the thumbnails to appear on yourwebsite add the following code:

<?
	picasaWeb('url of picasaweb feed');
?>


OR if you want to show a subset of your thumbnails do this:

<?
	picasaWeb(array("url"=>'url of picasaweb feed',"num"=>2,"random"=>false));
?>

that will show the two most recent thumbnails at your picasaweb album.  Changing random to true will show a random set of thumbnails of those available.

finally you can specify different thumbnail sizes of either 144, 160, 288, 576, or 720 - no other sizes are supported by Picasaweb.
<?
	picasaWeb(array("url"=>'url of picasaweb feed',"num"=>2,"random"=>false,"size"=>144));
?>

how to get your picasa web feed

You CAN NOT use the feed that is advertised on the picasaweb pages anymore - but you can use the following as a template:

http://picasaweb.google.com/data/feed/api/user/USERNAME/albumid/ALBUMID?category=photo&alt=rss

replace USERNAME with your picasaweb username - for instance mine is bill.rawlinson
replace ALBUMID with your picasaweb album id value - something like 4995878000989831185

If you prefer to not type the entire URL everytime you can also just pass in the username and albumid like so:
THIS IS THE PREFERRED MECHANISM

<? 
	picasaWeb(array("username"=>"bill.rawlinson","albumid"=>"4995878000989831185","random"=>false,"num"=>3, "size"=>144)); 
?>


UPDATE - NEW FEATURE - SHOW A RANDOM ALBUM/Images from a Random Album

07 FEB 2007 - I have added a new feature that lets you display an image(s) from a random album within your public collection of albums to use it do this:

<? 
	picasaWeb(array("username"=>"bill.rawlinson","showRandomAlbum"=>true,"num"=>3, "size"=>144)); 
?>

For this feature to work you CAN NOT send in an albumid OR a url.  Just your username and showRandomAlbum=true.  You can still specify if you want random images in that album ("random"=>true),
how many images to show from that album ("num"=>3) and what size images you want to display ("size"=>144)

UPDATE - NEW FEATURE - MAKE THUMBNAIL LINK TO ALBUM INSTEAD OF IMAGE
16 FEB 2007 - A friendly user, Travis, submitted an update that incorporates a new argument "linkToAlbum" - if you pass in true then it will link the thumbnails to their
source album - if the thumbnails are those that represent each album then it will link to your picasaweb home directory.  I also refactored some of the code to make it cleaner.

how to make your thumbnails look differently:

use CSS.  This readme.txt won't even begin to pretend it can teach you css.  Check out http://www.w3schools.com/css/default.asp  for a primer on CSS.