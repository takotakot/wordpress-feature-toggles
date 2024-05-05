# WordPress Feature Toggles

## Features

This plugin allows you to enable or disable features in WordPress. It is useful for testing new features in a live environment or for enabling features for specific users.

## License

This software is provided under the GPL v2 or later or MIT-X.

## Development

```
composer install
```

```
cp -n ./docker_configs/.env.example ./bedrock/.env
cp -n ./docker_configs/.env.local.example ./bedrock/.env.local
```
\* Modify `docker_configs/.env.compose.develop` if needed.

Recover deleted files after `composer install`.
```
git checkout -- bedrock/config/environments/testing.php
```

### Unit testing

```
git --git-dir= apply application_php.patch
```

If there are some update with autoload's run:

```
composer dump-autoload
```

Start MariaDB container before running tests.

```
docker compose -f docker-compose_test.yml --env-file=docker_config/.env.compose.test up -d
```

Run tests

```
# ./vendor/bin/phpunit
composer test
```
