<?php

// Import core settings, like 'entity_update_batch_size'. This file must be
// presented and scaffolded from core without any changes. It allows to have all
// the changes, additions and removals from core up to date on project.
include __DIR__ . '/default.settings.php';

$settings['config_sync_directory'] = '../config/sync';
// This directory is a symbolic link to the '../var/files/public' directory.
// See composer.json post-install-cmd if you want to change that.
$settings['file_public_path'] = 'files';
$settings['file_private_path'] = '../var/files/private';
$settings['file_temp_path'] = '../var/files/temporary';
$settings['skip_permissions_hardening'] = TRUE;
$settings['database_cache_max_rows']['default'] = 100_000;

$config['locale.settings']['translation']['path'] = '../var/files/translation';

include __DIR__ . '/../../../.local/settings.php';
