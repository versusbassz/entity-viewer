default:
	echo "There is no default task"

build-dev:
	npm ci
	npx webpack --mode=development --watch --progress

build-production:
	npm ci
	npx webpack --mode=production

wp-core-download:
	rm -rf ./custom/wp-core
	git clone --depth=1 --branch=5.7.2 git@github.com:WordPress/WordPress.git ./custom/wp-core
	rm -rf ./custom/wp-core/.git
