<?php

// Créer les dossiers dans /tmp
@mkdir('/tmp/storage/framework/views', 0777, true);
@mkdir('/tmp/storage/framework/cache/data', 0777, true);
@mkdir('/tmp/storage/framework/sessions', 0777, true);
@mkdir('/tmp/storage/framework/sessions', 0777, true);
@mkdir('/tmp/storage/logs', 0777, true);

// Définir les variables d'environnement
putenv('APP_STORAGE=/tmp/storage');
$_ENV['APP_STORAGE'] = '/tmp/storage';

// Redéfinir les constantes de Laravel
define('LARAVEL_STORAGE_PATH', '/tmp/storage');

$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';

require __DIR__ . '/../public/index.php';
