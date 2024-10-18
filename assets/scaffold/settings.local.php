<?php

$databases['default']['default'] = [
  'driver' => getenv('DB_DRIVER'),
  'database' => getenv('DB_NAME'),
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASSWORD'),
  'host' => getenv('DB_HOST'),
  'port' => getenv('DB_PORT'),
];

$settings['hash_salt'] = 'localhost';
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['config_exclude_modules'] = ['devel', 'stage_file_proxy'];
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

$config['system.logging']['error_level'] = 'verbose';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
