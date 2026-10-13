<?php
$lines = file('storage/logs/laravel.log');
$last80 = array_slice($lines, -80);
echo implode('', $last80);
