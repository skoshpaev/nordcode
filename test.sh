docker network create nordcode_test
cp docker/docker-compose-test.yml.example docker/docker-compose-test.yml
cp docker/var.env.example docker/var_test.env
cp .env.test .env.test.local

alias dd_test='docker-compose -f docker-compose-test.yml'
dd_test up -d
dd_test exec fpm_test bin/phpunit --bootstrap=tests/bootstrap.php
dd_test down
