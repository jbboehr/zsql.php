
PHPCS_OPTS := -n -p --standard=vendor/jbboehr/coding-standard/JbboehrStandard/ruleset.xml \
	--report=full --tab-width=4 --encoding=utf-8 src tests

cbf: vendor
	./vendor/bin/phpcbf $(PHPCS_OPTS)

clean:
	rm -rf docs

coverage: vendor
	php -d zend_extension=xdebug.so ./vendor/bin/phpunit --coverage-text --coverage-html=reports

cs: vendor
	./vendor/bin/phpcs $(PHPCS_OPTS)

docs:
	./vendor/bin/apigen.php --config apigen.neon

phpunit: vendor
	./vendor/bin/phpunit

test: cs phpunit

vendor:
	composer install --optimize-autoloader

.PHONY: cbf clean coverage cs phpunit test

