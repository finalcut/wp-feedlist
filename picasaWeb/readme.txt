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

<? 
	picasaWeb(array("username"=>"bill.rawlinson","albumid"=>"4995878000989831185","random"=>false,"num"=>3, "size"=>144)); 
?>




how to make your thumbnails look differently:

use CSS.  This readme.txt won't even begin to pretend it can teach you css.  Check out http://www.w3schools.com/css/default.asp  for a primer on CSS.