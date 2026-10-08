<?php

$basePath = $basePath ?? '../';
$currentPage = $currentPage ?? '';

$loggedUser = $_SESSION['user'] ?? [];

$loggedUserName = is_array($loggedUser)
    ? (string) ($loggedUser['name'] ?? '')
    : '';

$role = is_array($loggedUser)
    ? (string) ($loggedUser['role'] ?? '')
    : '';

$isAdmin = $role === 'ADMIN';
$isEmployee = $role === 'EMPLEADO';
$isCustomer = $role === 'CLIENTE';

$navigation = [];

if ($isAdmin || $isEmployee || $isCustomer) {
    $navigation[] = [
        'page' => 'home',
        'path' => 'home.php',
        'label' => 'Inicio',
    ];

    if ($isAdmin || $isEmployee) {
        $navigation[] = [
            'page' => 'products',
            'path' => 'products/products.php',
            'label' => 'Productos',
        ];
    }

    $navigation[] = [
        'page' => 'sales',
        'path' => 'sales/sales.php',
        'label' => $isCustomer ? 'Comprar productos' : 'Ventas',
    ];

    $navigation[] = [
        'page' => $isCustomer ? 'my-purchases' : 'history',
        'path' => $isCustomer
            ? 'sales/my-purchases.php'
            : 'sales/sales-history.php',
        'label' => $isCustomer ? 'Mis compras' : 'Historial',
    ];

    if ($isAdmin || $isEmployee) {
        $navigation[] = [
            'page' => 'suppliers',
            'path' => 'suppliers/suppliers.php',
            'label' => 'Proveedores',
        ];

        $navigation[] = [
            'page' => 'purchases',
            'path' => 'purchases/purchases.php',
            'label' => 'Pedidos',
        ];
    }

    if ($isAdmin) {
        $navigation[] = [
            'page' => 'payment_methods',
            'path' => 'payment_methods/payment-methods.php',
            'label' => 'Pagos',
        ];

        $navigation[] = [
            'page' => 'audits',
            'path' => 'audits/audits.php',
            'label' => 'Auditorías',
        ];

        $navigation[] = [
            'page' => 'users',
            'path' => 'users/users.php',
            'label' => 'Usuarios',
        ];
    }
}

$escapeNavbar = static function (string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};

?>

<nav class="navbar">
    <a
        href="<?= $escapeNavbar($basePath . 'home.php') ?>"
        class="logo"
    >
        <img
            src="<?= $escapeNavbar(
                $basePath . '../assets/images/GNC_Logo.svg.png'
            ) ?>"
            alt="GNC Logo"
        >
    </a>

    <button
        type="button"
        class="nav-toggle"
        id="navToggle"
        aria-label="Abrir menú"
        aria-controls="navLinks"
        aria-expanded="false"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="nav-links" id="navLinks">
        <?php foreach ($navigation as $item): ?>
            <?php
            $isActive = $currentPage === $item['page'];

            if (
                $isCustomer
                && $item['page'] === 'my-purchases'
                && $currentPage === 'history'
            ) {
                $isActive = true;
            }
            ?>

            <a
                href="<?= $escapeNavbar($basePath . $item['path']) ?>"
                class="<?= $isActive ? 'active' : '' ?>"
                <?php if ($isActive): ?>
                    aria-current="page"
                <?php endif; ?>
            >
                <?= $escapeNavbar($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="nav-actions">
        <?php if ($loggedUserName !== ''): ?>
            <span class="nav-user">
                <?= $escapeNavbar($loggedUserName) ?>
                · <?= $escapeNavbar($role) ?>
            </span>
        <?php endif; ?>

        <a
            href="<?= $escapeNavbar($basePath . 'logout.php') ?>"
            class="btn"
        >
            Cerrar sesión
        </a>
    </div>
</nav>

<script>
(function () {
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');

    if (!toggle || !links) {
        return;
    }

    function closeMenu() {
        toggle.classList.remove('active');
        links.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir menú');
    }

    toggle.addEventListener('click', function () {
        const isOpen = links.classList.toggle('open');

        toggle.classList.toggle('active', isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute(
            'aria-label',
            isOpen ? 'Cerrar menú' : 'Abrir menú'
        );
    });

    links.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
})();
</script>