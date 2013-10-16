<?php
class csvHandler {
	var $fname;

	function csvHandler($fname) {
		$this->fname = $fname;
	}
	
	function isTime($secs) {
		$fname = $this->fname;
		$lastmod = 0;
		if (file_exists($fname))
			$lastmod = filemtime($fname);
		if ((time() - $lastmod) > $secs)
			return true;
		else
			return false;
	}
	
	function persist($photoCounts) {
/*
		$fname = $this->fname;
		$fp = fopen($fname, "w");
		foreach($photoCounts as $name=>$val) {
			$data = $name . "," . $val . "\n";
			fwrite($fp, $data);
		}
		fclose($fp);
*/
	}
	
	function getPhotoCounts() {
		$fname = $this->fname;
		$photoCounts = array();
		if (file_exists($fname)) {
			$fdata = file_get_contents($fname);
			$fdata = explode("\n", $fdata);
			foreach($fdata as $line=>$data) {
				$d = split(",", $data);
				if ($d[0] != "")
					$photoCounts[$d[0]] = $d[1];
			}
		}
		return $photoCounts;
	}
}
?>
