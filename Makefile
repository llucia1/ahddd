docker-up:
	@echo "Up All Services"
	docker-compose up -d

docker-composer-install:
	@echo "Execute Composer"
	docker exec -ti gridcp-api sh -c "composer install"

docker-bootstrap:
	@echo "Create Database if not exists"
	docker exec -ti gridcp-mysql sh -c "cd /docker-entrypoint-initdb.d/ && mysql -uroot -pmysqlPass!123 < init.sql"

docker-access-bd:
	@echo "Access to container BD"
	docker exec -ti gridcp-mysql bash

docker-access-api:
	@echo "Access to container API"
	docker exec -ti gridcp-api bash

docker-down:
	@echo "Down docker-compose"
	rm -r ./app/vendor
	rm -r ./app/var
	docker-compose down

docker-logs:
	@echo "Watch log in GridCP-API"
	docker logs -f gridcp-api

docker-clear-all:
	@echo "Warning !!!! Delete ALL volumes, containers and images"
	docker volume prune
	docker system prune -a

docker-create-dkim-email:
	@echo "Create Dkim key"
	docker exec   -ti gridcp-api sh -c "openssl genrsa -out /var/www/config/dkim/privateKey.pem 4096"
	@echo "Create  privateKey"
	docker exec   -ti gridcp-api sh -c "openssl req -new -x509 -nodes -days 3600 -key /var/www/config/dkim/privateKey.pem -out /var/www/config/dkim/caKey.pem"
symfony-lint:
	@echo "Lint PHP"
	docker exec gridcp-api ./vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php --allow-risky=yes --dry-run

symfony-execute-migrations:
	@echo "Execute Migrations"
	docker exec  -ti gridcp-api  sh -c "php bin/console doctrine:migrations:migrate"


symfony-execute-fixtures:
	@echo "Execute Fixtures"
	docker exec -ti gridcp-api sh -c "php bin/console doctrine:fixtures:load --append"

symfony-create-keys:
	@echo "Create Keys"
	docker exec -ti gridcp-api sh -c "php bin/console lexik:jwt:generate-keypair"

symfony-test:
	@echo "Execute Testing"
	docker exec -ti gridcp-api sh -c "APP_ENV=test php bin/phpunit --verbose --configuration phpunit.dist.xml"

symfony-coverage:
	@echo "Execute Coverage Testing"
	docker exec -ti gridcp-api sh -c "APP_ENV=test XDEBUG_MODE=coverage php bin/phpunit --coverage-text --configuration phpunit.dist.xml"

symfony-router:
	@echo "View Routes"
	docker exec -ti gridcp-api sh -c "php bin/console debug:router"

symfony-logs:
	@echo "Symfony Logs"
	docker exec -ti gridcp-api sh -c "tail -f var/log/dev.log"

symfony-doctrine-mapping:
	@echo "Symfony Doctrine Mapping"
	docker exec -ti gridcp-api sh -c "php bin/console doctrine:mapping:info"