
test: 
	@"./vendor/bin/phpunit" -c tests/phpunit.xml tests
	
test-cov-cli:
	@"./vendor/bin/phpunit" -c tests/phpunit.xml  --coverage-text tests

test-cov-html:
	@"./vendor/bin/phpunit" -c tests/phpunit.xml  --coverage-html reports tests

docs:
	vendor/bin/apigen.php --destination doc/ --source src/
