default:
	echo "There is no default task"

build-dev:
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
	cd ./dist && zip -r entity-viewer.zip entity-viewer

wp-core-download:
	rm -rf ./custom/wp-core
	git clone --depth=1 --branch=5.7.2 git@github.com:WordPress/WordPress.git ./custom/wp-core
	rm -rf ./custom/wp-core/.git
