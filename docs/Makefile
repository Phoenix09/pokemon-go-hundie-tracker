all: index.html

index.html: ../generate.php ../pokemon.json ../shinies.json
	php ../generate.php > index.html

clean:
	$(RM) index.html

test:
	php -S 0.0.0.0:7776

.PHONY: all clean test
