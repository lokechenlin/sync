<?php

$config = [];
$masterDrive = 'H';
$slaveDrive = 'G';

$config[] = [
    'master' => $masterDrive . ':\MyMovie',
    'slave' => $slaveDrive . ':\MyMovie',
    'sync_mtime' => false,
];

$config[] = [
    'master' => $masterDrive . ':\MyMusic',
    'slave' => $slaveDrive . ':\MyMusic',
    'sync_mtime' => false,
];

$config[] = [
    'master' => $masterDrive . ':\MyVM',
    'slave' => $slaveDrive . ':\MyVM',
    'sync_mtime' => false,
];

$config[] = [
    'master' => $masterDrive . ':\MySoftware',
    'slave' => $slaveDrive . ':\MySoftware',
    'sync_mtime' => false,
];

$config[] = [
    'master' => $masterDrive . ':\MyDevelopment',
    'slave' => $slaveDrive . ':\MyDevelopment',
    'sync_mtime' => false,
];





