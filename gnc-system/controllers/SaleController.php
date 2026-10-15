<?php

require_once __DIR__ . '/../config/auth_check.php';
requireRole(['ADMIN', 'EMPLEADO', 'CLIENTE']);

require_once __DIR__ . '/../config/forms.php';
require_once __DIR__ . '/../models/SaleModel.php';

gncValidatePost();

$model = new SaleModel();

$_SESSION['cart'] ??= [];
$action = $_POST['action'] ?? '';

try {
    if ($action === 'add') {
        $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$id || !$quantity || $quantity < 1 || $quantity > 100000) {
            throw new RuntimeException('Producto o cantidad inválida.');
        }

        $product = $model->getProductStock($id);

        if (!$product) {
            throw new RuntimeException('Producto no encontrado.');
        }

        $index = null;

        foreach ($_SESSION['cart'] as $i => $item) {
            if ((int) $item['product_id'] === $id) {
                $index = $i;
                break;
            }
        }

        $quantity += $index !== null
            ? (int) $_SESSION['cart'][$index]['quantity']
            : 0;

        if ($quantity > (int) $product['stock']) {
            throw new RuntimeException('Stock insuficiente.');
        }

        $item = [
            'product_id' => $id,
            'product_name' => $product['name'],
            'quantity' => $quantity,
            'subtotal' => $quantity * (float) $product['price'],
        ];

        if ($index === null) {
            $_SESSION['cart'][] = $item;
        } else {
            $_SESSION['cart'][$index] = $item;
        }
    } elseif ($action === 'remove') {
        $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);

        if (
            $index === false
            || $index === null
            || !isset($_SESSION['cart'][$index])
        ) {
            throw new RuntimeException('Producto del carrito inválido.');
        }

        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    } elseif ($action === 'empty') {
        $_SESSION['cart'] = [];
        unset($_SESSION['checkoutToken']);
    } elseif ($action === 'finish') {
        $token = $_POST['checkoutToken'] ?? '';

        if (
            !is_string($token)
            || !isset($_SESSION['checkoutToken'])
            || !hash_equals($_SESSION['checkoutToken'], $token)
        ) {
            throw new RuntimeException('Solicitud vencida. Recarga el carrito.');
        }

        if (!$_SESSION['cart']) {
            throw new RuntimeException('El carrito está vacío.');
        }

        $payment = filter_input(
            INPUT_POST,
            'payment_method_id',
            FILTER_VALIDATE_INT
        );

        if (!$payment) {
            throw new RuntimeException('Selecciona un método de pago.');
        }

        $items = array_map(
            static fn ($item) => [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ],
            $_SESSION['cart']
        );

        $statement = $conn->prepare("
            EXEC dbo.pa_finalizarVenta
                @items = ?,
                @paymentMethodId = ?,
                @token = ?
        ");

        $statement->execute([
            json_encode($items, JSON_THROW_ON_ERROR),
            $payment,
            $token,
        ]);

        $sale = $statement->fetch(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        if (!$sale) {
            throw new RuntimeException('No se pudo confirmar la operación.');
        }

        $_SESSION['cart'] = [];
        unset($_SESSION['checkoutToken'], $_SESSION['edit_index']);

        $_SESSION['success'] =
            'Transacción #' . $sale['sale_id'] . ' registrada correctamente.';

        $destination = $_SESSION['user']['role'] === 'CLIENTE'
            ? 'my-purchases.php'
            : 'sales-history.php';

        header('Location: ../views/sales/' . $destination);
        exit;
    } else {
        throw new RuntimeException('Acción inválida.');
    }
} catch (PDOException $exception) {
    $code = (int) ($exception->errorInfo[1] ?? 0);

    error_log('GNC: registrar transacción. Código: ' . $code);

    $_SESSION['error'] = in_array(
        $code,
        [51102, 51103, 51104, 51105, 51106, 51107, 50002],
        true
    )
        ? 'Revisa productos, cantidades, stock y método de pago.'
        : 'No se pudo guardar la operación. Revisa la consola PHP.';
} catch (RuntimeException $exception) {
    $_SESSION['error'] = $exception->getMessage();
}

header('Location: ../views/sales/sales.php');
exit;