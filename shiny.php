<?php
error_reporting(E_ALL);

$master = "https://api.github.com/repos/ZeChrales/PogoAssets/git/trees/master";

$data = curl_get($master);
$json = json_decode($data, true);

$k = array_search("decrypted_assets", array_column($json["tree"], "path"));
$decrypted_assets = $json["tree"][$k]["url"];

$data = curl_get($decrypted_assets);
$json = json_decode($data, true);

$shinies = array();
foreach ($json["tree"] as $e) {
	if (is_int(strpos($e["path"], "pokemon_icon_")) && is_int(strpos($e["path"], "shiny"))) {
		preg_match("/(\d+)/", $e["path"], $matches);
		$shinies[] = $matches[1];
	}
}

$shinies = array_unique($shinies);

function curl_get($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Phoenix09");
	$ret = curl_exec($ch);
	if ($ret !== false) {
		return $ret;
	}
	echo curl_error($ch) ."\n";
	return false;
}

function can_be_shiny($pid) {
	global $shinies;
	if ($pid >= 1 && $pid <= 3) {
		return true;
	}
	if (in_array($pid, $shinies)) {
		return true;
	}
	return false;
}
