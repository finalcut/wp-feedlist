installation:

put picasaWeb (or this directory) in your wordpress plugins folder and activate it with the wordpress controlpanel.


usage:

whever you want the thumbnails to appear on yourwebsite add the following code:

<?
picasaWeb('url of picasaweb feed');
?>


how to get your picasa web feed

go to picasaweb.google.com and find your album (or albums).  On the page with your album is a link that says "rss" click on it.  The page that loads is the feed.  Copy that pages
url and paste it in the call to the picasaWeb function explained in the usage section above.


how to make your thumbnails look differently:

use CSS.  This readme.txt won't even begin to pretend it can teach you css.  Check out http://www.w3schools.com/css/default.asp  for a primer on CSS.