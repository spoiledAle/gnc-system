-- --------------------------------------------------------
-- Host:                         localhost
-- Versión del servidor:         12.2.2-MariaDB - MariaDB Server
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.14.0.7165
-- --------------------------------------------------------
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET NAMES utf8 */
;
/*!50503 SET NAMES utf8mb4 */
;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */
;
/*!40103 SET TIME_ZONE='+00:00' */
;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */
;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */
;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */
;
-- Volcando estructura de base de datos para gncproyecto
CREATE DATABASE IF NOT EXISTS `gncproyecto`
/*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */
;
USE `gncproyecto`;
-- Volcando estructura para tabla gncproyecto.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 6 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
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
) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
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
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `check_price` CHECK (`price` > 0),
  CONSTRAINT `check_stock` CHECK (`stock` >= 0)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Volcando datos para la tabla gncproyecto.products: ~2 rows (aproximadamente)
INSERT INTO `products` (
    `id`,
    `category_id`,
    `name`,
    `description`,
    `price`,
    `stock`,
    `image`
  )
VALUES (
    1,
    1,
    'Whey Protein',
    'Proteina sabor vainilla',
    35000.00,
    19,
    'whey.jpg'
  ),
  (
    2,
    2,
    'Creatina Monohidratada',
    'Creatina para fuerza',
    18000.00,
    15,
    'creatina.jpg'
  );
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
  CONSTRAINT `fk_purchase_detail_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_purchase_detail_purchase` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
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
  CONSTRAINT `fk_purchase_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Volcando datos para la tabla gncproyecto.purchases: ~3 rows (aproximadamente)
INSERT INTO `purchases` (
    `id`,
    `supplier_id`,
    `purchase_date`,
    `total`,
    `product_name`,
    `quantity_boxes`,
    `status`
  )
VALUES (
    1,
    1,
    '2026-05-30 19:25:04',
    5.00,
    NULL,
    NULL,
    NULL
  ),
  (
    2,
    1,
    '2026-05-30 19:25:36',
    18.00,
    NULL,
    NULL,
    NULL
  ),
  (
    3,
    1,
    '2026-05-30 19:33:01',
    1800000.00,
    'proteina',
    4,
    'Pendiente'
  );
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
  CONSTRAINT `fk_sale_detail_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sale_detail_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Volcando datos para la tabla gncproyecto.sale_details: ~1 rows (aproximadamente)
INSERT INTO `sale_details` (
    `id`,
    `sale_id`,
    `product_id`,
    `quantity`,
    `subtotal`
  )
VALUES (1, 1, 1, 1, 35000.00);
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
  CONSTRAINT `fk_sale_payment_method` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sale_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Volcando datos para la tabla gncproyecto.sales: ~1 rows (aproximadamente)
INSERT INTO `sales` (
    `id`,
    `user_id`,
    `payment_method_id`,
    `total`,
    `sale_date`
  )
VALUES (1, 1, 3, 35000.00, '2026-05-30 19:47:40');
-- Volcando estructura para tabla gncproyecto.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Volcando datos para la tabla gncproyecto.suppliers: ~1 rows (aproximadamente)
INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`)
VALUES (
    1,
    'GNC Costa Rica',
    '8888-8888',
    'gnc@gmail.com',
    'San Jose'
  );
-- Volcando estructura para tabla gncproyecto.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Volcando datos para la tabla gncproyecto.users: ~1 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `password`)
VALUES (
    1,
    'Admin2AC',
    'admin2AC@gmail.com',
    '$2y$10$SLwUHrbj8chJabj9iq4hFeNsoaHnVbulX7PST3yUvIzxY9bsd/BKq'
  );
-- Volcando estructura para disparador gncproyecto.decrease_stock_after_sale
SET @OLDTMP_SQL_MODE = @@SQL_MODE,
  SQL_MODE = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
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
  SQL_MODE = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
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