jjj:
	@ echo "There is no default task"

build-dev:
	composer install
	npm ci
	npx webpack --mode=development --watch --progress

build-production:
	npm ci
	npx webpack --mode=production

release:
	make build-production

	mkdir -p ./dist
	rm -rf ./dist/*

	mkdir -p ./dist/entity-viewer

	cp -r ./assets ./dist/entity-viewer/

	cp ./package.json      ./dist/entity-viewer/
	cp ./package-lock.json ./dist/entity-viewer/
	cp ./webpack.config.js ./dist/entity-viewer/

	cp ./readme.txt ./dist/entity-viewer/
	cp ./LICENSE    ./dist/entity-viewer/

	cp ./entity-viewer.php ./dist/entity-viewer/
	cp -r ./src ./dist/entity-viewer/

	cd ./dist && zip -r entity-viewer.zip entity-viewer

## Tests
test-e2e:
	cd ./custom/dev-env && \
	docker-compose exec -w "/project" test_php vendor/bin/codecept build && \
	docker-compose exec -w "/project" test_php vendor/bin/codecept run acceptance

vnc:
	# sudo apt-get -y install tigervnc-common
	# vncpasswd ./tests/e2e/.vnc-passwd
	# password is "secret" (default for Selenium docker-images)
	vncviewer -passwd ./tests/e2e/.vnc-passwd localhost::5900 &

dev-env--shell-test:
	cd ./custom/dev-env && docker-compose exec test_php bash

## Development environment

### Setup
dev-env--up:
	make wp-core-download
	make dev-env--download
	cd ./custom/dev-env && make up
	@ echo "\nWaiting for mysql..."
	sleep 5
	make dev-env--install

wp-core-download:
	rm -rf ./custom/wp-core
	git clone --depth=1 --branch=5.9 git@github.com:WordPress/WordPress.git ./custom/wp-core
	rm -rf ./custom/wp-core/.git

dev-env--download:
	rm -fr ./custom/dev-env && \
	mkdir -p ./custom/dev-env && \
	cd ./custom/dev-env && \
	git clone -b 5.4.42 --depth=1 -- git@github.com:wodby/docker4wordpress.git . && \
	rm ./docker-compose.override.yml && \
	cp ../../tools/dev-env/docker-compose.yml . && \
	cp ../../tools/dev-env/.env . && \
	cp ../../tools/dev-env/wp-config.php ../wp-core/

dev-env--install:
	cd ./custom/dev-env && \
	make wp 'core install --url="http://ev.docker.local:8000/" --title="Dev site" --admin_user="admin" --admin_password="admin" --admin_email="admin@docker.local" --skip-email' && \
	make wp 'plugin activate entity-viewer' && \
	make wp 'ev test-data remove' && make wp 'ev test-data generate' && \
	\
	docker-compose exec mariadb mysql -uroot -ppassword -e "create database wordpress_test;" && \
	docker-compose exec mariadb mysql -uroot -ppassword -e "GRANT ALL PRIVILEGES ON wordpress_test.* TO 'wordpress'@'%';" && \
	docker-compose exec test_php wp core install --url="http://test.ev.docker.local:8000/" --title="Testing site" --admin_user="admin" --admin_password="admin" --admin_email="admin@docker.local" --skip-email && \
	docker-compose exec test_php wp plugin activate entity-viewer && \
	docker-compose exec test_php wp ev test-data remove && \
	docker-compose exec test_php wp ev test-data generate

### Regular commands
dev-env--start:
	cd ./custom/dev-env && make start

dev-env--stop:
	cd ./custom/dev-env && make stop

dev-env--prune:
	cd ./custom/dev-env && make prune

dev-env--restart:
	cd ./custom/dev-env && make stop
	cd ./custom/dev-env && make start

dev-env--recreate:
	make dev-env--prune && make dev-env--up

dev-env--shell:
	cd ./custom/dev-env && make shell
