__Yii2 Api Skeleton__

This application is to use with `php-dev-stack` repo, or standalone.

__Contents__

Yii2 2.0.21 + DevMustafa package to work with `RabbitMQ`. Other components
are configred in `php-dev-stack` package via `Dockerfile` and `docker-compose.yml` files.

__Using with php-dev-stack__

1. Clone `php-dev-stack` repo into some directory
2. Clone `yii2-api-skeleton` into `app` directory of `php-dev-stack` directory root.
3. Run `composer install` in `app` directory and then after succeed
4. Perform some settings in `docker-compose.yml` file.
5. Run `docker-compose up -d`
6. Profit!
