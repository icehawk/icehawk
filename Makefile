
.PHONY: help

# Based on https://gist.github.com/prwhite/8168133#comment-1313022

## This help screen
help:
	printf "Available commands\n\n"
	awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")-1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "\033[33m%-40s\033[0m %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Run all static code analysers and tests
tests: phplint phpstan phpunit
.PHONY: tests

## Run PHP linting
phplint:
	docker-compose run --rm --no-deps php sh -c "sh /app/.tools/phplint.sh -p8 -f'*.php' /app/src /app/tests"
.PHONY: phplint

## Run PHPStan
phpstan:
	docker-compose run --rm --no-deps php php /app/.tools/phpstan.phar analyze --memory-limit=-1
.PHONY: phpstan

## Run Psalm
psalm:
	docker-compose run --rm --no-deps php php /app/.tools/psalm.phar
.PHONY: psalm

PHPUNIT_CLI_OPTIONS =

## Run PHPUnit
phpunit:
	docker-compose run --rm --no-deps php sh -c \
		"php \
		-derror_reporting=-1 \
		-dmemory_limit=-1 \
		/app/.tools/phpunit.phar \
		-c build/phpunit.xml \
		$(PHPUNIT_CLI_OPTIONS)"
.PHONY: phpunit

phpUnitKey 	= 4AA394086372C20A
phpStanKey 	= CF1A108D0E7AE720
phpPsalmKey = 12CE0F1D262429A5
trustedKeys = "$(phpUnitKey),$(phpStanKey),$(phpBuKey),$(phpPsalmKey)"

## Run install & update of tools via Phive
update-tools:
	docker-compose run --rm --no-deps phive sh -c "php -dmemory_limit=-1 /usr/local/bin/phive install --trust-gpg-keys \"$(trustedKeys)\" && php -dmemory_limit=-1 /usr/local/bin/phive update"
	curl -L -o "./.tools/phplint.sh" "https://gist.githubusercontent.com/hollodotme/9c1b805e9a2f946433512563edc4b702/raw/60532cb51f1b7a1550216088943bacbd3d4c9351/phplint.sh"
	chmod +x "./.tools/phplint.sh"
.PHONY: update-tools

## Run install of tools via Phive
install-tools:
	docker-compose run --rm --no-deps phive sh -c "php -dmemory_limit=-1 /usr/local/bin/phive install --trust-gpg-keys \"$(trustedKeys)\""
	curl -L -o "./.tools/phplint.sh" "https://gist.githubusercontent.com/hollodotme/9c1b805e9a2f946433512563edc4b702/raw/60532cb51f1b7a1550216088943bacbd3d4c9351/phplint.sh"
	chmod +x "./.tools/phplint.sh"
.PHONY: install-tools

## Update & start complete docker-compose setup
update: dccomposer update-tools
.PHONY: update

## Run composer
dccomposer: dcpull
	docker-compose run --rm composer
.PHONY: dccomposer

## Pull all containers
dcpull: dcbuild
	docker-compose pull
.PHONY: dcpull

## Build all containers
dcbuild:
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose build --pull --parallel
.PHONY: dcbuild