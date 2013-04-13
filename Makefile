
test: 
	@cd tests; ../vendor/bin/phpunit .
	
test-cov-cli:
	@cd tests; ../vendor/bin/phpunit --coverage-text .

test-cov-html:
	cd tests; ../vendor/bin/phpunit --coverage-html ../reports .
		
docs:
	vendor/bin/apigen.php --destination doc/ --source src/
