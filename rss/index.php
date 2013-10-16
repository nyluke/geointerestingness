<? header('Content-type: text/xml'); ?>
<?php

require_once("../phpFlickr/phpFlickr.php");

function getRSS($max) {
	$f = new phpFlickr("?");
	$f->enableCache("fs", "../phpFlickrCache", 43200);
	$photos_interesting = $f->interestingness_getList(NULL, NULL, 200);
	$count = 0;
	
	foreach ((array)$photos_interesting['photo'] as $photo) {
		$g = $f->photos_geo_getLocation($photo[id]);
		$inf = $f->photos_getInfo($photo[id], NULL);
		$country = $g['location']['country']['_content'];
		$title = $inf['title'];
		$pic = "<img border=0 src=" . $f->buildPhotoURL($photo, "Square") . " />";
		
		if ($country != NULL) {
			
			echo "<item><title>" . $country . ": ";
			if ($title != NULL) { 
				echo $title;
			}
			else {
				echo "Untitled";
			}
			echo "</title>";
			echo "<description><![CDATA[<p><img border=0 src=" . $f->buildPhotoURL($photo, "medium") . " />";
			echo "</p>]]></description>";
			echo "<link>http://www.flickr.com/photos/$photo[owner]/$photo[id]</link></item>";
			$count++;
		}
		
		if ($count == $max) {
			break;
		}
	}
}

?>
<?php echo('<?xml version="1.0" encoding="utf-8"?>'); ?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">

<channel>
<title>Flickr GeoInterestingness</title>
<description>Where do the most interesting photos of the day come from?</description>
<link>http://www.nyluke.com/geointerestingness</link>

<?php getRSS(20) ?>

</channel>
</rss>