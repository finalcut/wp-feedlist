author:		 William Rawlinson
version:	 0.2
date:		 18 Aug 2006
plugin URL:	 http://rawlinson.us/blog/articles/picasaweb-wordpress-plugin/

installation:

put picasaWeb (or this directory) in your wordpress plugins folder and activate it with the wordpress controlpanel.


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


how to get your picasa web feed

go to picasaweb.google.com and find your album (or albums).  On the page with your album is a link that says "rss" click on it.  The page that loads is the feed.  Copy that pages
url and paste it in the call to the picasaWeb function explained in the usage section above.


how to make your thumbnails look differently:

use CSS.  This readme.txt won't even begin to pretend it can teach you css.  Check out http://www.w3schools.com/css/default.asp  for a primer on CSS.