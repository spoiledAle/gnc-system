# Cómo levantar el proyecto (SQL Server)

El proyecto se conecta a SQL Server usando PDO + el driver `sqlsrv` de Microsoft.
Como la base de datos y los drivers viven en la máquina de cada quien (no se
suben al repo), cada persona del equipo tiene que hacer esta configuración
**una sola vez** en su propia compu.

## 1. Requisitos

- XAMPP con PHP 8.2 (verifica con `php -v`)
- SQL Server instalado localmente (sirve Express) + SQL Server Management
  Studio (SSMS)
- Microsoft ODBC Driver 17 o 18 for SQL Server — normalmente ya viene con
  SSMS. Si no, se descarga desde el sitio de Microsoft.

## 2. Instalar los drivers de PHP para SQL Server

PHP no trae soporte para SQL Server por defecto; hay que agregar dos
extensiones: `pdo_sqlsrv` y `sqlsrv`.

1. Revisa tu versión y arquitectura de PHP:
   ```
   php -v
   php -i | findstr "Thread Safety"
   php -i | findstr "Architecture"
   ```
   Si usas XAMPP en Windows con PHP 8.2, normalmente es x64 y
   "Thread Safety => enabled".

2. Descarga el paquete de Microsoft para tu versión de PHP desde:
   https://github.com/microsoft/msphpsql/releases
   Busca el release que trae un `Windows-8.2.zip` (para PHP 8.2) y adentro
   toma los archivos `x64/php_pdo_sqlsrv_82_ts.dll` y
   `x64/php_sqlsrv_82_ts.dll` (usa `_nts` en vez de `_ts` solo si tu
   "Thread Safety" sale disabled).

3. Copia esos dos `.dll` a la carpeta `ext` de tu PHP (en XAMPP:
   `C:\xampp\php\ext`).

4. Abre `C:\xampp\php\php.ini` y agrega al final del bloque de extensiones:
   ```
   extension=php_pdo_sqlsrv_82_ts.dll
   extension=php_sqlsrv_82_ts.dll
   ```

5. Reinicia Apache (o si vas a probar por consola, corre
   `php -m | findstr sqlsrv` y deberías ver `pdo_sqlsrv` y `sqlsrv` en la
   lista).

## 3. Crear la base de datos en tu SQL Server

1. Abre SSMS y conéctate a tu instancia local (usualmente con "Windows
   Authentication", sin usuario/contraseña).
2. Abre el archivo [`database/gncproyecto_sqlserver.sql`](database/gncproyecto_sqlserver.sql)
   de este repositorio.
3. Ejecútalo completo (F5). Esto crea la base `GNCPROYECTO`, todas las
   tablas (`Tbl*`), las vistas, el procedimiento almacenado y los
   disparadores (`Dis*`), y carga los datos de ejemplo (incluye 3 usuarios
   de prueba, ver abajo).

## 4. Configurar la conexión

1. Copia [`config/database.local.example.php`](config/database.local.example.php)
   como `config/database.local.php` (en la misma carpeta).
2. Ábrelo y pon el nombre de tu servidor en `$host`, tal como aparece en el
   cuadro "Server name" cuando te conectas por SSMS (por ejemplo `localhost`,
   `.`, o `TU-PC\SQLEXPRESS`).
3. Este archivo no se sube al repositorio (está en `.gitignore`), así que
   cada quien mantiene su propia configuración sin pisar la de los demás.

## 5. Servir el proyecto con Apache

Como el proyecto no vive dentro de `C:\xampp\htdocs`, hay que decirle a
Apache dónde encontrarlo. La forma más simple es crear un enlace (junction)
que apunte a tu copia del repo, sin mover ni duplicar archivos. Desde
PowerShell:

```powershell
New-Item -ItemType Junction -Path "C:\xampp\htdocs\gnc-system" -Target "RUTA\A\TU\REPO\gnc-system"
```

(Reemplaza `RUTA\A\TU\REPO\gnc-system` por la ruta real donde clonaste el
proyecto en tu máquina — la carpeta que contiene `config`, `models`,
`views`, etc.)

Alternativa: simplemente clona/copia el proyecto directo dentro de
`C:\xampp\htdocs\gnc-system`.

## 6. Probarlo

1. Inicia **Apache** desde el Panel de Control de XAMPP (MySQL ya no hace
   falta para este proyecto).
2. Asegúrate de que el servicio de SQL Server esté corriendo.
3. Abre `http://localhost/gnc-system/views/login.php`.

Usuarios de prueba que ya están cargados (solo para desarrollo local):

| Email | Contraseña |
|---|---|
| 2acteam@gmail.com | 2ac |
| alegrodriguez78@gmail.com | Alessandro1234 |

## Problemas comunes

- **"could not find driver" / la extensión no carga**: revisa que el
  `.dll` coincida exactamente con tu versión de PHP, arquitectura (x64) y
  "Thread Safety" (ts vs nts). Un `.dll` que no combina con tu PHP no
  carga, y a veces no da ningún error visible.
- **Error de conexión / login failed**: confirma el `$host` en tu
  `database.local.php` contra lo que usas en SSMS, y que iniciaste sesión
  en Windows con el mismo usuario que tiene permisos en SQL Server.
- **La página se ve pero no carga datos**: revisa que ejecutaste el script
  completo del paso 3 (tablas, vistas, procedimiento y triggers) y que no
  quedó a medias por algún error en SSMS.
