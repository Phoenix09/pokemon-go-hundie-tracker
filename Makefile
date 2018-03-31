all: shinies.json
	make -C docs $@

shinies.json:
	php shiny.php

.PHONY: shinies.json
