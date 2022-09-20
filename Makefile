default: composer cs psalm

composer:
	composer install --no-interaction --no-progress --optimize-autoloader --quiet

update:
	composer update --no-interaction --no-progress --optimize-autoloader

cs:
	vendor/bin/php-cs-fixer fix --verbose --using-cache=no

psalm:
	vendor/bin/psalm --no-progress --no-cache

generate:
	vendor/bin/doctrine-migrations migrations:generate

migrate:
	vendor/bin/doctrine-migrations migrations:migrate --no-interaction --allow-no-migration
