# GNC System

Sistema web de gestión para una tienda de suplementos GNC: inventario de
productos, ventas, compras a proveedores, métodos de pago y auditoría de
cambios. Desarrollado en PHP (patrón MVC) sobre una base de datos
**SQL Server**.

## Funcionalidades

- **Inicio de sesión y registro** de usuarios.
- **Productos**: agregar, modificar, eliminar, buscar y filtrar; alerta de
  productos con stock bajo.
- **Ventas**: registrar ventas y consultar el historial. El stock se
  descuenta automáticamente.
- **Compras**: registrar compras a proveedores. El stock se suma
  automáticamente.
- **Proveedores** y **métodos de pago**: mantenimiento de catálogos.
- **Auditorías**: bitácora de inserciones, modificaciones y eliminaciones de
  productos, con el usuario que realizó cada acción.

## Tecnologías

- PHP 8.2 con PDO y el driver `pdo_sqlsrv` de Microsoft
- SQL Server (Express sirve) + SQL Server Management Studio
- Apache (XAMPP)
- HTML y CSS

## Estructura del repositorio

```
gnc-system/
├── assets/        CSS e imágenes de los productos
├── config/        Conexión a la base de datos y control de sesión
├── controllers/   Lógica de cada módulo
├── database/      gncproyecto_sqlserver.sql (script único de la base)
├── models/        Acceso a datos
├── tests/         Panel de pruebas internas
├── views/         Pantallas del sistema
└── SETUP.md       Guía de instalación paso a paso
```

## Base de datos

Toda la base de datos está en un solo script:
[`gnc-system/database/gncproyecto_sqlserver.sql`](gnc-system/database/gncproyecto_sqlserver.sql).
Al ejecutarlo completo en SSMS se crea la base `gncproyecto` con:

| Prefijo | Contenido                  | Ejemplos                                                |
| ------- | -------------------------- | ------------------------------------------------------- |
| `tbl_`  | Tablas                     | `tbl_products`, `tbl_sales`, `tbl_auditProducts`        |
| `pa_`   | Procedimientos almacenados | `pa_insertarProducto`, `pa_filtrarProductos`            |
| `v_`    | Vistas                     | `v_historialVentas`, `v_inventarioProductos`            |
| `dis_`  | Disparadores (triggers)    | `dis_decreaseStockAfterSale`, `dis_auditUpdateProduct`  |
| `fn_`   | Funciones                  | `fn_valorInventarioProducto`, `fn_resumenVentasPeriodo` |

También carga datos de ejemplo para probar el sistema.

## Instalación rápida

1. Instala los drivers `pdo_sqlsrv` y `sqlsrv` en el PHP de XAMPP.
2. Ejecuta `gnc-system/database/gncproyecto_sqlserver.sql` completo en SSMS.
3. Copia `gnc-system/config/database.local.example.php` como
   `database.local.php` y pon el nombre de tu servidor en `$host`.
4. Coloca el proyecto en `C:\xampp\htdocs\gnc-system` (o crea un enlace).
5. Inicia Apache y abre `http://localhost/gnc-system/views/login.php`.

Los detalles de cada paso y la solución a errores comunes están en
**[SETUP.md](gnc-system/SETUP.md)**.

## Autores

Proyecto del curso de Administración de Bases de Datos — Universidad
Nacional (UNA), Costa Rica.
