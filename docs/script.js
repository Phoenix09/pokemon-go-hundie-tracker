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

	return "pokemon" + pid + form + gender;
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
			var state = localStorage.getItem(getPokemonKey(this));
			if (state == "1") {
				$(this).find("div.owned").attr("data-owned", "");
			}
		});
	});

	updateStats();
});

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
