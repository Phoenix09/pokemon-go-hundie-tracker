function getPokemonKey (e) {
	var pid = $(e).closest("tr").attr("data-pid");

	var gender = $(e).attr("data-gender");
	if (gender != "Genderless" && gender != undefined) {
		gender = "_" + gender;
	} else {
		gender = "";
	}
	var form = $(e).attr("data-form-name");
	if (form !== undefined && form !== null) {
		form = "_" + form;
	} else {
		form = "";
	}

	var shiny = $(e).attr("data-shiny");
	if (shiny !== undefined && shiny !== null) {
		shiny = "_shiny";
	} else {
		shiny = "";
	}

	return "pokemon" + pid + form + gender + shiny;
}

function updateStats() {
	var total_species = $("tr[data-pid]").length;
	var total_all = $(".flex-item").length;
	var owned = $(".flex-item > div[data-owned]").length;
	var species_owned = $("tr[data-pid]:has(div[data-owned])").length;

	var stats = $("#stats");
	stats.find("td.stats-owned").text(owned);
	stats.find("td.stats-species-owned").text(species_owned);
	stats.find("td.stats-total-species").text(total_species);
	stats.find("td.stats-total-all").text(total_all);
}

$(function() {
	$("div.flex-item").click(function () {
		var div = $(this).find("div.owned");
		var attr = div.attr("data-owned");
		if (typeof attr !== typeof undefined && attr !== false) {
			div.removeAttr("data-owned");
			localStorage.setItem(getPokemonKey(this), 0);
		} else {
			div.attr("data-owned", "");
			localStorage.setItem(getPokemonKey(this), 1);
		}
		updateStats();
	});

	$("img.pokemon-img").on("error", function() {
		var pid = $(this).closest("tr").attr("data-pid");
		$(this).attr("src", "https://assets.pokemon.com/assets/cms2/img/pokedex/full/" + pid + ".png");
	});

	// Load saved data
	$("tr[data-pid]").each(function() {
		$(this).find(".flex-item").each(function() {
			var key = getPokemonKey(this);
			var state = localStorage.getItem(key);
			// This is to deal with Alolan forms being added
			// So we update the normal form with the value of the old one
			if (key.includes("_Normal")) {
				var state2 = localStorage.getItem(key.replace("_Normal", ""));
				if (state2 == "1") {
					localStorage.setItem(key, 1);
					state = "1";
				}
			}
			if (state == "1") {
				$(this).find("div.owned").attr("data-owned", "");
			}
		});
	});

	updateStats();
});
