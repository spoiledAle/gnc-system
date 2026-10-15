<?php

require_once __DIR__ . '/../config/session.php';

clearGncSession();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

header('Location: login.php');
exit;