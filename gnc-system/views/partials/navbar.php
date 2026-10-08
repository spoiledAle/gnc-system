<?php
/**
 * Navbar reutilizable — GNC System
 * 
 * Incluir en cada vista con:
 *   <?php $currentPage = 'products'; include __DIR__ . '/../partials/navbar.php'; ?>
 * 
 * Ajustar la ruta relativa según la profundidad de la vista.
 * La variable $currentPage determina cuál link se marca como activo.
 * La variable $basePath define la ruta base relativa hacia la carpeta views/.
 */

// Detectar la ruta base si no fue definida
if (!isset($basePath)) {
    $basePath = '../';
}

// Obtener nombre del usuario logueado
$loggedUserName = $_SESSION['user']['name'] ?? $_SESSION['user'] ?? '';
?>

<nav class="navbar">
    <a href="<?php echo $basePath; ?>home.php" class="logo">
        <img src="<?php echo $basePath; ?>../assets/images/GNC_Logo.svg.png" alt="GNC Logo">
    </a>

    <!-- Hamburger toggle (mobile) -->
    <button class="nav-toggle" id="navToggle" aria-label="Abrir menú">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Links de navegación -->
    <div class="nav-links" id="navLinks">
        <a href="<?php echo $basePath; ?>home.php"
           class="<?php echo ($currentPage ?? '') === 'home' ? 'active' : ''; ?>">
            Inicio
        </a>
        <a href="<?php echo $basePath; ?>products/products.php"
           class="<?php echo ($currentPage ?? '') === 'products' ? 'active' : ''; ?>">
            Productos
        </a>
        <a href="<?php echo $basePath; ?>sales/sales.php"
           class="<?php echo ($currentPage ?? '') === 'sales' ? 'active' : ''; ?>">
            Ventas
        </a>
        <a href="<?php echo $basePath; ?>sales/sales-history.php"
           class="<?php echo ($currentPage ?? '') === 'history' ? 'active' : ''; ?>">
            Historial
        </a>
        <a href="<?php echo $basePath; ?>suppliers/suppliers.php"
           class="<?php echo ($currentPage ?? '') === 'suppliers' ? 'active' : ''; ?>">
            Proveedores
        </a>
        <a href="<?php echo $basePath; ?>payment_methods/payment-methods.php"
           class="<?php echo ($currentPage ?? '') === 'payment_methods' ? 'active' : ''; ?>">
            Pagos
        </a>
        <a href="<?php echo $basePath; ?>purchases/purchases.php"
           class="<?php echo ($currentPage ?? '') === 'purchases' ? 'active' : ''; ?>">
            Pedidos
        </a>
        <a href="<?php echo $basePath; ?>audits/audits.php"
           class="<?php echo ($currentPage ?? '') === 'audits' ? 'active' : ''; ?>">
            Auditorías
        </a>
    </div>

    <div class="nav-actions">
        <?php if ($loggedUserName): ?>
            <span class="nav-user"><?php echo htmlspecialchars($loggedUserName); ?></span>
        <?php endif; ?>
        <a href="<?php echo $basePath; ?>logout.php" class="btn">Cerrar Sesión</a>
    </div>
</nav>

<script>
(function() {
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');
    if (toggle && links) {
        toggle.addEventListener('click', function() {
            toggle.classList.toggle('active');
            links.classList.toggle('open');
        });
        // Cerrar menú al hacer click en un link
        links.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                toggle.classList.remove('active');
                links.classList.remove('open');
            });
        });
    }
})();
</script>
