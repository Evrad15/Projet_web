<?php

// Utiliser /tmp pour le storage
$_ENV['APP_STORAGE'] = '/tmp';
putenv('APP_STORAGE=/tmp');

// Créer les dossiers nécessaires dans /tmp
@mkdir('/tmp/storage/framework/views', 0777, true);
@mkdir('/tmp/storage/framework/cache/data', 0777, true);
@mkdir('/tmp/storage/framework/sessions', 0777, true);
@mkdir('/tmp/storage/logs', 0777, true);

$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';

require __DIR__ . '/../public/index.php';
