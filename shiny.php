<?php
error_reporting(E_ALL);

$shiniesfile = __DIR__."/shinies.json";

$master = "https://api.github.com/repos/ZeChrales/PogoAssets/git/trees/master";

$timestamp = (int) shell_exec("git log -1 --format=%at shinies.json");
$arr = curl_get($master, $timestamp);
if ($arr["info"]["http_code"] === 304) {
	echo "Shiny data up-to-date\n";
	exit;
}

$json = json_decode($arr["data"], true);

$k = array_search("decrypted_assets", array_column($json["tree"], "path"));
$decrypted_assets = $json["tree"][$k]["url"];

$arr = curl_get($decrypted_assets, $timestamp);
if ($arr["info"]["http_code"] === 304) {
	echo "Shiny data up-to-date\n";
	exit;
}
$timestamp = $arr["info"]["filetime"];

$json = json_decode($arr["data"], true);

$shinies = array();
foreach ($json["tree"] as $e) {
	if (is_int(strpos($e["path"], "pokemon_icon_")) && is_int(strpos($e["path"], "shiny"))) {
		preg_match("/(\d+)/", $e["path"], $matches);
		$shinies[] = $matches[1];
	}
}

$shinies = array_values(array_unique($shinies));

$fp = fopen($shiniesfile, "w");
fwrite($fp, json_encode($shinies, JSON_PRETTY_PRINT));
fclose($fp);
touch($shiniesfile, $timestamp);

echo "Shiny data updated\n";


// Functions
function curl_get($url, $timestamp=0) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Phoenix09");
	curl_setopt($ch, CURLOPT_TIMEVALUE, $timestamp);
	curl_setopt($ch, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
	curl_setopt($ch, CURLOPT_FILETIME, true);
	$ret = curl_exec($ch);
	if ($ret !== false) {
		return array("data" => $ret, "info" => curl_getinfo($ch));
	}
	echo curl_error($ch) ."\n";
	return false;
}
