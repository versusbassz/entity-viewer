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

	mkdir -p ./dist/wp-meta-viewer
	mkdir -p ./dist/wp-meta-viewer/assets

	cp ./README.md       ./dist/wp-meta-viewer/
	cp ./meta-viewer.php ./dist/wp-meta-viewer/

	cp -r ./assets/build ./dist/wp-meta-viewer/assets

	cp -r ./inc ./dist/wp-meta-viewer/
	cd ./dist && zip -r wp-meta-viewer.zip wp-meta-viewer

wp-core-download:
	rm -rf ./custom/wp-core
	git clone --depth=1 --branch=5.7.2 git@github.com:WordPress/WordPress.git ./custom/wp-core
	rm -rf ./custom/wp-core/.git
