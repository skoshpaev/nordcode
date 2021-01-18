cp docker/docker-compose-test.yml.example docker-compose-test.yml
cp docker/docker-compose.yml.example docker-compose.yml
cp docker/var.env.example docker/var.env
cp docker/var.env.example docker/var_test.env
cp .env .env.local

docker network create nordcode
docker network create nordcode_test
alias dd='docker-compose'
alias console='docker-compose exec fpm bin/console'
dd up -d

dd exec fpm composer install
console doctrine:migrations:migrate -n
console doctrine:fixtures:load -n

sh test.sh