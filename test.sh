docker network create nordcode_test
cp docker/docker-compose-test.yml.example docker-compose-test.yml
cp docker/var.env.example docker/var_test.env
cp .env.test .env.test.local

alias dd_test='docker-compose -f docker-compose-test.yml'
dd_test up -d

FILE=bin/phpunit
if test -f "$FILE"; then
    echo "$FILE exists. No further actions."
else
    echo "$FILE does not exist. Adding a dependency"
    dd_test exec fpm_test composer require --dev symfony/phpunit-bridge
fi


dd_test exec fpm_test bin/phpunit --bootstrap=tests/bootstrap.php
dd_test down
