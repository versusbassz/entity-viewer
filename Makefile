default:
	echo "There is no default task"

build-dev:
	npm ci
	npx webpack --mode=development --watch --progress

build-production:
	npm ci
	npx webpack --mode=production
