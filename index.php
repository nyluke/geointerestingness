<?php

require_once("phpFlickr/phpFlickr.php");
require_once("csvHandler.php");

$listSize = 200;

$f = new phpFlickr("?");
$f->enableCache("fs", "phpFlickrCache", 43200);
$photos_interesting = $f->interestingness_getList(NULL, NULL, $listSize);
$countryList = array();
$max = 10;
$count = 0;
$result = "";

foreach ((array)$photos_interesting['photo'] as $photo) {
	$g = $f->photos_geo_getLocation($photo['id']);
	$inf = $f->photos_getInfo($photo['id'], NULL);
	$country = $g['location']['country']['_content'];
	$title = $inf['title'];
		// strip quotes
		$title = str_replace("'", "", "$title");
		$title = str_replace("\"", "", "$title");
		// truncate
		$chars = 40;
        $title = $title." "; 
        $title = substr($title,0,$chars); 
        $title = substr($title,0,strrpos($title,' ')); 
	$pic = "<img border=0 src=" . $f->buildPhotoURL($photo, "Square") . ">";
	
	if ($country != NULL) {
		$titleLink = "http://www.flickr.com/photos/$photo[owner]/$photo[id]";
		$action = "\"javascript:onClick=changePic('titleImage','" . $f->buildPhotoURL($photo, "Medium") . "','" . $country . "','" . $title . "','" . $titleLink . "');\">";

		$result = $result . "<tr><td><a href=";
		$result = $result . $action . $pic . "</td>";
		$result = $result . "<td align=top valign=center style=font-size:20pt>";
		$result = $result . "<a href=" . $action . $country . "<br>";
		$result = $result . "<span style=font-size:10pt>";
		if ($title != NULL)  {
			$result = $result . $title;
		}
		else { 
			$result = $result . "Untitled";
		}
		$result = $result . "</span></a></td></tr>";
		$countryList[] = $country;
		$count++;
	}
	
	if ($count == $max) {
		break;
	}
}


function getList($max) {
	global $result;
	echo $result;
}

function getTitle() {
	global $f, $photos_interesting, $listSize;
	srand(time());

	for ($p=0; $p <= $listSize; $p++) {
		$random = (rand()%$listSize);
		$photo = $photos_interesting['photo'][$random];
		$g = $f->photos_geo_getLocation($photo['id']);
		$inf = $f->photos_getInfo($photo['id'], NULL);
		$country = $g['location']['country']['_content'];
		$title = $inf['title'];

		if ($country != NULL) {
			echo "<a id=titlePicLink target=_blank href=http://www.flickr.com/photos/$photo[owner]/$photo[id]>";
			echo "<img border=0 name=titleImage src=" . $f->buildPhotoURL($photo, "Medium") . ">";
			echo "<br><br><div id=country style=font-size:20pt;color:black>" . $country . "</div>";
			echo "<div id=title style=font-size:10pt;color:black>";
			if ($title != NULL)  {
				echo $title;
			}
			else { 
				echo "Untitled";
			}
			echo "</div></a><br><br>";
			echo "<div class=author>" . getStats() . "<a href=rss><img border=0 src=rss.gif></a><br>(images now included)<br><i><a href=mailto:nyluke@gmail.com>nyluke@gmail.com</a></i><br><br><br></div>";
			break;
		}
	}
}

function getStats() {
	global $countryList;
	$csvh = new csvHandler("stats.csv");
	$pc = $csvh->getPhotoCounts();
	if ($csvh->isTime(86400)) {
		foreach($countryList as $c) {
			if (array_key_exists($c, $pc))
				$pc[$c]++;
			else
				$pc[$c] = 1;
		}
		$csvh->persist($pc);
	}

	$result = "";
	arsort($pc);
	foreach($pc as $name=>$count) {
		$result = $result . "<table border=0 width=500><tr><td width=100></td><td width=150>" . $name . "</td><td width=" . $count . " bgcolor=blue></td><td></td></tr></table>";
	}
	
	return $result;
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<link href="geointerestingness.css" rel="stylesheet" type="text/css" />
<script language="JavaScript">
function changePic(img_name,img_src,country,title,titleLink) {
	document[img_name].src=img_src;
	document.getElementById("country").innerHTML=country;
	document.getElementById("title").innerHTML=title;
	document.getElementById("titlePicLink").href=titleLink;
}
</script>
<title>GeoInterestingness</title>
</head>
<body>
<div class="pagetitle">
	<div id="name">Flickr GeoInterestingness</div>
	<div id="subname">Where do the most interesting photos of the day come from?</div>
	<br><br>
	<div id="titleImage"><?php getTitle() ?></div>
</div>

<div id=list>
	<table border=0 cellspacing=10><?php getList(20) ?></table>
	<br>
		<script type="text/javascript"><!--
		google_ad_client = "pub-7154129033849688";
		/* 250x250, created 5/16/08 */
		google_ad_slot = "0874801492";
		google_ad_width = 250;
		google_ad_height = 250;
		//-->
		</script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
</div>

<br><br>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("?");
pageTracker._initData();
pageTracker._trackPageview();
</script>
</body>
</html>
