
test: 
	@"./vendor/bin/phpunit" -c tests/phpunit.xml tests
	
test-cov-cli:
	@"./vendor/bin/phpunit" -c tests/phpunit.xml  --coverage-text tests

test-cov-html:
	@"./vendor/bin/phpunit" -c tests/phpunit.xml  --coverage-html reports tests

compatinfo:
	@phpci print -R --report full src > compatinfo.log
	@less compatinfo.log

docs:
	vendor/bin/apigen.php --destination doc/ --source src/

phar: clean
	@mkdir ./build
	@php -d "phar.readonly=0" "./bin/compile.php"

test-phar: phar
	@PHAR=1 "./vendor/bin/phpunit" -c tests/phpunit.xml tests

clean:
	@rm -Rf ./build
