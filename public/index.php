<?php

use App\Controllers\RarbgController;

ini_set('display_errors', 'On');

require('vendor/autoload.php');

$rarbg = new RarbgController();

print_r($rarbg->query('Crimes of the Future'));
