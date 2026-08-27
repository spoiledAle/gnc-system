<?php

// Copia este archivo como "database.local.php" en esta misma carpeta y ajusta
// los valores a tu propia instalación de SQL Server. Ese archivo sí es tuyo
// (está en .gitignore), así que no se sube al repositorio ni pisa el de tus
// compañeros.

// Nombre del servidor/instancia, igual que en "Server name" al conectar en SSMS.
// Ejemplos comunes: "localhost", ".", "DESKTOP-ABC123\SQLEXPRESS"
$host = "localhost";

$database = "GNCPROYECTO";

// Si usas Windows Authentication (lo normal si SSMS te deja entrar sin pedirte
// usuario/contraseña), deja estos dos en null.
$user = null;
$password = null;

// Si en cambio usas SQL Server Authentication, comenta las 2 lineas de arriba
// y descomenta estas con tus datos:
// $user = "sa";
// $password = "tu_password";
