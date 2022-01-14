default:
	echo "There is no default task"

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
full-install-dev-env:
	make wp-core-download
	make download-dev-env
	cd ./custom/dev-env && make up
	@ echo "\nWaiting for mysql..."
	sleep 5
	make install-dev-env

wp-core-download:
	rm -rf ./custom/wp-core
	git clone --depth=1 --branch=5.7.2 git@github.com:WordPress/WordPress.git ./custom/wp-core
	rm -rf ./custom/wp-core/.git

download-dev-env:
	rm -fr ./custom/dev-env && \
	mkdir -p ./custom/dev-env && \
	cd ./custom/dev-env && \
	git clone -b 5.4.42 --depth=1 -- git@github.com:wodby/docker4wordpress.git . && \
	rm ./docker-compose.override.yml && \
	cp ../../tools/dev-env/docker-compose.yml . && \
	cp ../../tools/dev-env/.env . && \
	cp ../../tools/dev-env/wp-config.php ../wp-core/

install-dev-env:
	cd ./custom/dev-env && \
	make wp 'core install --url="http://ev.docker.localhost:8000/" --title="Test site" --admin_user="admin" --admin_password="admin" --admin_email="admin@example.org" --skip-email' && \
	make wp 'plugin activate entity-viewer'
