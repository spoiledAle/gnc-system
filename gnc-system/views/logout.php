<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/sql_connection.php';

registerGncSessionEventFromSession('LOGOUT');

clearGncSession();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

header('Location: login.php');
exit;