
all: 

doc: force
	phpwiki.phar doc doc/html

phpci:
	phpci print -R --reference=PHP5 src

phpunit:
	phpunit --coverage-html build tests

force: ;
