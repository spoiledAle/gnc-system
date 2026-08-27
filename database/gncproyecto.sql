-- Volcando estructura de base de datos para gncproyecto
CREATE DATABASE IF NOT EXISTS `gncproyecto` USE `gncproyecto`;
-- Volcando estructura para tabla gncproyecto.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 6 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.categories: ~5 rows (aproximadamente)
INSERT INTO `categories` (`id`, `name`, `description`)
VALUES (1, 'Proteinas', 'Suplementos proteicos'),
  (
    2,
    'Creatinas',
    'Suplementos para fuerza y rendimiento'
  ),
  (3, 'Vitaminas', 'Vitaminas y minerales'),
  (4, 'Pre entrenos', 'Suplementos energéticos'),
  (
    5,
    'Accesorios',
    'Shakers y accesorios deportivos'
  );
-- Volcando estructura para tabla gncproyecto.payment_methods
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.payment_methods: ~3 rows (aproximadamente)
INSERT INTO `payment_methods` (`id`, `name`)
VALUES (1, 'Efectivo'),
  (2, 'Tarjeta'),
  (3, 'SINPE');
-- Volcando estructura para tabla gncproyecto.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10, 2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_product_category` (`category_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `check_price` CHECK (`price` > 0),
  CONSTRAINT `check_stock` CHECK (`stock` >= 0)
) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.products: ~3 rows (aproximadamente)
-- Volcando estructura para tabla gncproyecto.purchase_details
CREATE TABLE IF NOT EXISTS `purchase_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_purchase_detail_purchase` (`purchase_id`),
  KEY `fk_purchase_detail_product` (`product_id`),
  CONSTRAINT `fk_purchase_detail_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_purchase_detail_purchase` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.purchase_details: ~0 rows (aproximadamente)
-- Volcando estructura para tabla gncproyecto.purchases
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `purchase_date` datetime DEFAULT current_timestamp(),
  `total` decimal(10, 2) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity_boxes` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_purchase_supplier` (`supplier_id`),
  CONSTRAINT `fk_purchase_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.purchases: ~4 rows (aproximadamente)
-- Volcando estructura para tabla gncproyecto.sale_details
CREATE TABLE IF NOT EXISTS `sale_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sale_detail_sale` (`sale_id`),
  KEY `fk_sale_detail_product` (`product_id`),
  CONSTRAINT `fk_sale_detail_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_sale_detail_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.sale_details: ~2 rows (aproximadamente)
-- Volcando estructura para tabla gncproyecto.sales
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `total` decimal(10, 2) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sale_user` (`user_id`),
  KEY `fk_sale_payment_method` (`payment_method_id`),
  CONSTRAINT `fk_sale_payment_method` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `fk_sale_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.sales: ~3 rows (aproximadamente)
-- Volcando estructura para tabla gncproyecto.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.suppliers: ~1 rows (aproximadamente)
-- Volcando estructura para tabla gncproyecto.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci;
-- Volcando datos para la tabla gncproyecto.users: ~1 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `password`)
VALUES (1, 'Admin2AC', 'admin2AC@gmail.com', 'Admin1234');
-- Volcando estructura para disparador gncproyecto.decrease_stock_after_sale
SET @OLDTMP_SQL_MODE = @@SQL_MODE,
  SQL_MODE = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER // CREATE TRIGGER decrease_stock_after_sale
AFTER
INSERT ON sale_details FOR EACH ROW BEGIN
UPDATE products
SET stock = stock - NEW.quantity
WHERE id = NEW.product_id;
END // DELIMITER;
SET SQL_MODE = @OLDTMP_SQL_MODE;
-- Volcando estructura para disparador gncproyecto.increase_stock_after_purchase
SET @OLDTMP_SQL_MODE = @@SQL_MODE,
  SQL_MODE = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER // CREATE TRIGGER increase_stock_after_purchase
AFTER
INSERT ON purchase_details FOR EACH ROW BEGIN
UPDATE products
SET stock = stock + NEW.quantity
WHERE id = NEW.product_id;
END // DELIMITER;
SET SQL_MODE = @OLDTMP_SQL_MODE;
/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */
;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */
;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */
;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */
;