<?php

require_once __DIR__ . '/../config/auth_check.php';

$role = $_SESSION['user']['role'] ?? '';

if (!in_array($role, ['ADMIN', 'EMPLEADO', 'CLIENTE'], true)) {
    http_response_code(403);
    exit('La sesión no tiene un rol válido. Inicia sesión nuevamente.');
}

$isAdmin = $role === 'ADMIN';
$isEmployee = $role === 'EMPLEADO';
$isCustomer = $role === 'CLIENTE';

$loggedUserName = is_array($_SESSION['user'] ?? null)
    ? (string) ($_SESSION['user']['name'] ?? '')
    : '';

$historyUrl = $isCustomer
    ? 'sales/my-purchases.php'
    : 'sales/sales-history.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GNC System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php
    $currentPage = 'home';
    $basePath = '';
    include __DIR__ . '/partials/navbar.php';
    ?>

    <main class="container">
        <div class="hero-header">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                <span><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($loggedUserName !== ''): ?>
                    <span class="hero-badge-divider">·</span>
                    <span class="hero-badge-user"><?= htmlspecialchars($loggedUserName, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <h1 class="title">GNC <span class="title-accent">SYSTEM</span></h1>

            <p class="subtitle">
                <?php if ($isCustomer): ?>
                    Explora nuestros suplementos y realiza tus compras en línea.
                <?php else: ?>
                    Administración de inventario, ventas y control de suplementos.
                <?php endif; ?>
            </p>
        </div>

        <div class="cards">
            <?php if ($isAdmin || $isEmployee): ?>
                <a href="products/products.php" class="card">
                    <div class="card-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2>Productos</h2>
                        <p>
                            <?= $isAdmin ? 'Gestiona productos e inventario.' : 'Consulta productos y stock disponible.' ?>
                        </p>
                    </div>
                    <div class="card-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
            <?php endif; ?>

            <a href="sales/sales.php" class="card">
                <div class="card-icon-wrap" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <div class="card-body">
                    <h2><?= $isCustomer ? 'Comprar productos' : 'Ventas' ?></h2>
                    <p>
                        <?= $isCustomer 
                            ? 'Consulta productos y precios, añade al carrito y confirma tu compra.' 
                            : 'Registra ventas con control de stock.' ?>
                    </p>
                </div>
                <div class="card-arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </a>

            <a href="<?= htmlspecialchars($historyUrl, ENT_QUOTES, 'UTF-8') ?>" class="card">
                <div class="card-icon-wrap" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div class="card-body">
                    <h2><?= $isCustomer ? 'Mis compras' : 'Historial de ventas' ?></h2>
                    <p>
                        <?= $isCustomer 
                            ? 'Consulta tus compras y el detalle de tus productos.' 
                            : 'Consulta las ventas realizadas y sus detalles.' ?>
                    </p>
                </div>
                <div class="card-arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </a>

            <?php if ($isAdmin || $isEmployee): ?>
                <a href="suppliers/suppliers.php" class="card">
                    <div class="card-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                            <line x1="9" y1="22" x2="9" y2="2"></line>
                            <line x1="8" y1="6" x2="10" y2="6"></line>
                            <line x1="14" y1="6" x2="16" y2="6"></line>
                            <line x1="8" y1="10" x2="10" y2="10"></line>
                            <line x1="14" y1="10" x2="16" y2="10"></line>
                            <line x1="8" y1="14" x2="10" y2="14"></line>
                            <line x1="14" y1="14" x2="16" y2="14"></line>
                            <line x1="8" y1="18" x2="10" y2="18"></line>
                            <line x1="14" y1="18" x2="16" y2="18"></line>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2>Proveedores</h2>
                        <p>
                            <?= $isAdmin ? 'Gestiona los proveedores del sistema.' : 'Consulta los proveedores disponibles.' ?>
                        </p>
                    </div>
                    <div class="card-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>

                <a href="purchases/purchases.php" class="card">
                    <div class="card-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2>Pedidos a proveedores</h2>
                        <p>Registra pedidos y actualiza su estado de recepción.</p>
                    </div>
                    <div class="card-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <a href="payment_methods/payment-methods.php" class="card">
                    <div class="card-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2>Métodos de pago</h2>
                        <p>Administra los métodos de pago disponibles.</p>
                    </div>
                    <div class="card-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>

                <a href="audits/audits.php" class="card">
                    <div class="card-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <polyline points="9 12 11 14 15 10"></polyline>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2>Auditorías</h2>
                        <p>Consulta las acciones registradas en el sistema.</p>
                    </div>
                    <div class="card-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>

                <a href="users/users.php" class="card">
                    <div class="card-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="card-body">
                        <h2>Administración de usuarios</h2>
                        <p>Consulta cuentas, cambia roles y activa o desactiva usuarios.</p>
                    </div>
                    <div class="card-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
