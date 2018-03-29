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
});
