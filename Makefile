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
	mkdir -p ./dist/entity-viewer/assets

	cp ./README.md       ./dist/entity-viewer/
	cp ./entity-viewer.php ./dist/entity-viewer/

	cp -r ./assets/build ./dist/entity-viewer/assets

	cp -r ./inc ./dist/entity-viewer/
	cp -r ./src ./dist/entity-viewer/
	cd ./dist && zip -r entity-viewer.zip entity-viewer

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
	git clone --depth=1 --branch=5.7.2 git@github.com:WordPress/WordPress.git ./custom/wp-core
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
	make wp 'core install --url="http://ev.docker.localhost:8000/" --title="Test site" --admin_user="admin" --admin_password="admin" --admin_email="admin@example.org" --skip-email' && \
	make wp 'plugin activate entity-viewer' && \
	make wp 'ev test-data remove' && make wp 'ev test-data generate'

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

dev-env--shell:
	cd ./custom/dev-env && make shell
