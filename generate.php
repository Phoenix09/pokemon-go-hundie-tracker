<?php
error_reporting(E_ALL);
$pokemons = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "pokemon.json"), true);


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
					$f = 27;
					break;
				case "?":
					$f = 28;
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
	return sprintf("https://cdn.rawgit.com/RealAwkwardPig/PogoAssets/b8d55031/decrypted_assets/pokemon_icon_%03d_%02d.png", $i, $f);
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
	foreach ($attrs as $k => $v) {
		$new[] = sprintf('%s="%s"', $k, $v);
	}
	return sprintf('<div class="flex-item" %s><div class="owned"></div>%s</div>', implode(" ", $new), $item);
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
<table>
<tr><th>&nbsp;</th><th>Pokémon</th><th>&nbsp;</th></tr>

EOT;

foreach ($pokemons as $id => $pokemon) {
	$forms = array();
	$genders = array();
	$content = "";
	if (count($pokemon["forms"])) {
		for ($i = 0; $i < count($pokemon["forms"]); $i++) {
			$fid = $i + 1;
			$form_name = $pokemon["forms"][$i];
			if ($id == 201) {
				$forms[] = flexitem(get_form_image($id, $form_name) ."<br>". $form_name, array("data-form-name" => $form_name));
			} else {
				if ($pokemon["genders"][0] != "Genderless") {
					foreach ($pokemon["genders"] as $gender) {
						$forms[] = flexitem(get_form_image($id, $form_name) ."<br>". get_gender_image($gender), array("data-form-name" => $form_name, "data-gender" => $gender));
					}
				} else {
					$forms[] = flexitem($form_name, array("data-form-name" => $form_name));
				}
			}
		}
		$content = flexbox($forms);
	} else {
		foreach ($pokemon["genders"] as $gender) {
			$genders[] = flexitem(get_gender_image($gender), array("data-gender" => $gender));
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
