<?php
error_reporting(E_ALL);
$pokemons = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "pokemon.json"), true);
$shinies = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "shinies.json"), true);


function get_form_image($i, $form=NULL) {
	return get_img(get_image_url($i, $form), $form);
}

function get_image_url($i, $form=NULL) {
	$f = 0;
	if ($i == 201) {
		if ($form == NULL) {
			$f = 16;
		} else {
			switch ($form) {
				case "!":
					$f = 37;
					break;
				case "?":
					$f = 38;
					break;
				default:
					$f = ord(strtoupper($form)) - ord('A') + 11;
			}
		}
	}

	if ($i == 351) {
		switch ($form) {
			case "Sunny":
				$f = 12;
				break;
			case "Rainy":
				$f = 13;
				break;
			case "Snowy":
				$f = 14;
				break;
			case "Normal":
			default:
				$f = 11;
		}
	}
	return sprintf("https://cdn.rawgit.com/RealAwkwardPig/PogoAssets/0de98fbe/decrypted_assets/pokemon_icon_%03d_%02d.png", $i, $f);
}

function get_gender_image($gender) {
	switch ($gender) {
		case "Male":
			$url = "https://use.fontawesome.com/releases/v5.0.8/svgs/solid/mars.svg";
			break;
		case "Female":
			$url = "https://use.fontawesome.com/releases/v5.0.8/svgs/solid/venus.svg";
			break;
		case "Genderless":
		default:
			$url = "https://use.fontawesome.com/releases/v5.0.8/svgs/solid/genderless.svg";
	}
	return get_img($url, $gender);
}

function get_img($url, $alt, $class_name=NULL) {
	$class = "";
	if (!empty($class_name)) {
		$class = sprintf('class=%s', $class_name);
	}
	return sprintf('<img %s src="%s" alt="%3$s" title="%3$s"/>', $class, $url, $alt);
}

function flexbox($inner) {
	return "<div class=\"flex-container\">". implode("", $inner) ."</div>";
}

function flexitem($item, $attrs=array()) {
	$new = array();
	$shiny = "";
	foreach ($attrs as $k => $v) {
		if ($k == "data-shiny") {
			$shiny = '<div class="shiny"></div>';
		}
		$new[] = sprintf('%s="%s"', $k, $v);
	}
	return sprintf('<div class="flex-item" %s><div class="owned"></div>%s%s</div>', implode(" ", $new), $item, $shiny);
}

function can_be_shiny($pid) {
	global $shinies;

	if (in_array($pid, $shinies)) {
		return true;
	}
	return false;
}

echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
<title>Pokémon GO Hundie Tracker</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="script.js"></script>
</head>
<body>
<table id="stats">
<tr><th colspan="5">Statistics</th></tr>
<tr><th>&nbsp;</th><th>Owned</th><th>&nbsp;</th><th>Total</th></tr>
<tr title="This count does not include genders/forms"><td class="stats-label">Species</td><td class="stats-species-owned"></td><td class="stats-divider">/</td><td class="stats-total-species"></td></tr>
<tr title="This count includes genders/forms"><td class="stats-label">All</td><td class="stats-owned"></td><td class="stats-divider">/</td><td class="stats-total-all"></td></tr>
</table>
<br>
<table>
<tr><th>&nbsp;</th><th>Pokémon</th><th>&nbsp;</th></tr>

EOT;

foreach ($pokemons as $id => $pokemon) {
	$forms = array();
	$genders = array();
	$content = "";
	$shiny = can_be_shiny($id);

	if (count($pokemon["forms"])) {
		for ($i = 0; $i < count($pokemon["forms"]); $i++) {
			$fid = $i + 1;
			$form_name = $pokemon["forms"][$i];
			if ($id == 201) {
				$forms[] = flexitem(get_form_image($id, $form_name) ."<br>". $form_name, array("data-form-name" => $form_name));
				if ($shiny) $forms[] = flexitem(get_form_image($id, $form_name) ."<br>". $form_name, array("data-form-name" => $form_name, "data-shiny" => ""));
			} else {
				if ($pokemon["genders"][0] != "Genderless") {
					foreach ($pokemon["genders"] as $gender) {
						$forms[] = flexitem(get_form_image($id, $form_name) ."<br>". get_gender_image($gender), array("data-form-name" => $form_name, "data-gender" => $gender));
						if ($shiny) $forms[] = flexitem(get_form_image($id, $form_name) ."<br>". get_gender_image($gender), array("data-form-name" => $form_name, "data-gender" => $gender, "data-shiny" => ""));
					}
				} else {
					$forms[] = flexitem($form_name, array("data-form-name" => $form_name));
					if ($shiny) $forms[] = flexitem($form_name, array("data-form-name" => $form_name, "data-shiny" => ""));
				}
			}
		}
		$content = flexbox($forms);
	} else {
		foreach ($pokemon["genders"] as $gender) {
			$genders[] = flexitem(get_gender_image($gender), array("data-gender" => $gender));
			if ($shiny) $genders[] = flexitem(get_gender_image($gender), array("data-gender" => $gender, "data-shiny" => ""));
		}
		$content = flexbox($genders);
	}
	printf('<tr data-pid="%d"><td class="pokemon-img">%s</td><td>%s</td><td>%s</td></tr>', $id, get_img(get_image_url($id), $pokemon["name"], "pokemon-img"), $pokemon["name"], $content);
	echo "\n";
}

echo <<<EOT
</table>
</body>
</html>
EOT;
