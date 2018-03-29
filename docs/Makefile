all: index.html

index.html: ../generate.php ../pokemon.json
	php ../generate.php > index.html

clean:
	$(RM) index.html

test:
	php -S 0.0.0.0:7777

.PHONY: all clean test
