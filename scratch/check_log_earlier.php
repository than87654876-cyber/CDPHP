<?php
$lines = file('storage/logs/laravel.log');
$last200 = array_slice($lines, -200, 120);
echo implode('', $last200);
