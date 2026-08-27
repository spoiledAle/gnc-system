-- --------------------------------------------------------
-- Script de creación de base de datos para SQL Server
-- Proyecto GNC SYSTEM
--
-- Convención de nombres del curso:
--   Tbl        -> tablas (PascalCase)
--   v          -> vistas (camelCase, empieza en minúscula)
--   Dis        -> disparadores/triggers
--   camelCase  -> columnas/atributos
--   MAYÚSCULAS -> nombre de la base de datos
--
-- Uso: abrir este archivo en SQL Server Management Studio conectado
-- a tu instancia local y ejecutarlo completo (Execute / F5).
-- --------------------------------------------------------

IF DB_ID('GNCPROYECTO') IS NULL
BEGIN
    CREATE DATABASE GNCPROYECTO;
END
GO

USE GNCPROYECTO;
GO

-- --------------------------------------------------------
-- Tablas
-- --------------------------------------------------------

CREATE TABLE TblCategory (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL
);
GO

CREATE TABLE TblPaymentMethod (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(50) NOT NULL
);
GO

CREATE TABLE TblSupplier (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    phone NVARCHAR(30) NULL,
    email NVARCHAR(100) NULL,
    address NVARCHAR(255) NULL
);
GO

CREATE TABLE TblUser (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    email NVARCHAR(100) NOT NULL UNIQUE,
    password NVARCHAR(100) NOT NULL
);
GO

CREATE TABLE TblProduct (
    id INT IDENTITY(1,1) PRIMARY KEY,
    categoryId INT NOT NULL,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image NVARCHAR(255) NULL,
    CONSTRAINT fk_TblProduct_category FOREIGN KEY (categoryId) REFERENCES TblCategory(id),
    CONSTRAINT check_price CHECK (price > 0),
    CONSTRAINT check_stock CHECK (stock >= 0)
);
GO

CREATE TABLE TblPurchase (
    id INT IDENTITY(1,1) PRIMARY KEY,
    supplierId INT NOT NULL,
    purchaseDate DATETIME NOT NULL DEFAULT GETDATE(),
    total DECIMAL(10,2) NOT NULL,
    productName NVARCHAR(100) NULL,
    quantityBoxes INT NULL,
    status NVARCHAR(50) NULL,
    CONSTRAINT fk_TblPurchase_supplier FOREIGN KEY (supplierId) REFERENCES TblSupplier(id)
);
GO

CREATE TABLE TblPurchaseDetail (
    id INT IDENTITY(1,1) PRIMARY KEY,
    purchaseId INT NOT NULL,
    productId INT NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_TblPurchaseDetail_purchase FOREIGN KEY (purchaseId) REFERENCES TblPurchase(id),
    CONSTRAINT fk_TblPurchaseDetail_product FOREIGN KEY (productId) REFERENCES TblProduct(id)
);
GO

CREATE TABLE TblSale (
    id INT IDENTITY(1,1) PRIMARY KEY,
    userId INT NOT NULL,
    paymentMethodId INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    saleDate DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT fk_TblSale_user FOREIGN KEY (userId) REFERENCES TblUser(id),
    CONSTRAINT fk_TblSale_paymentMethod FOREIGN KEY (paymentMethodId) REFERENCES TblPaymentMethod(id)
);
GO

CREATE TABLE TblSaleDetail (
    id INT IDENTITY(1,1) PRIMARY KEY,
    saleId INT NOT NULL,
    productId INT NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_TblSaleDetail_sale FOREIGN KEY (saleId) REFERENCES TblSale(id),
    CONSTRAINT fk_TblSaleDetail_product FOREIGN KEY (productId) REFERENCES TblProduct(id)
);
GO

CREATE TABLE TblAuditLog (
    id INT IDENTITY(1,1) PRIMARY KEY,
    actionType NVARCHAR(50) NULL,
    productName NVARCHAR(100) NULL,
    actionDate DATETIME NOT NULL DEFAULT GETDATE()
);
GO

-- --------------------------------------------------------
-- Vistas
-- --------------------------------------------------------

CREATE VIEW vOutOfStockProducts
AS
SELECT *
FROM TblProduct
WHERE stock = 0;
GO

CREATE VIEW vSalesHistory
AS
SELECT
    sale.id,
    usr.name              AS userName,
    prod.name             AS productName,
    pm.name               AS paymentMethod,
    saleDetail.quantity,
    sale.total,
    sale.saleDate
FROM TblSaleDetail AS saleDetail
INNER JOIN TblSale           AS sale ON saleDetail.saleId = sale.id
INNER JOIN TblProduct        AS prod ON saleDetail.productId = prod.id
INNER JOIN TblUser           AS usr  ON sale.userId = usr.id
INNER JOIN TblPaymentMethod  AS pm   ON sale.paymentMethodId = pm.id;
GO

-- --------------------------------------------------------
-- Procedimiento almacenado
-- --------------------------------------------------------

CREATE PROCEDURE spGetLowStockProducts
AS
BEGIN
    SET NOCOUNT ON;
    SELECT *
    FROM TblProduct
    WHERE stock < 5
    ORDER BY stock ASC;
END
GO

-- --------------------------------------------------------
-- Disparadores (triggers)
-- --------------------------------------------------------

CREATE TRIGGER DisAuditInsertProduct
ON TblProduct
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO TblAuditLog (actionType, productName)
    SELECT 'INSERT', name FROM inserted;
END
GO

CREATE TRIGGER DisAuditUpdateProduct
ON TblProduct
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO TblAuditLog (actionType, productName)
    SELECT 'UPDATE', name FROM inserted;
END
GO

CREATE TRIGGER DisAuditDeleteProduct
ON TblProduct
AFTER DELETE
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO TblAuditLog (actionType, productName)
    SELECT 'DELETE', name FROM deleted;
END
GO

CREATE TRIGGER DisIncreaseStockAfterPurchase
ON TblPurchaseDetail
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE p
    SET p.stock = p.stock + i.quantity
    FROM TblProduct p
    INNER JOIN inserted i ON p.id = i.productId;
END
GO

CREATE TRIGGER DisDecreaseStockAfterSale
ON TblSaleDetail
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE p
    SET p.stock = p.stock - i.quantity
    FROM TblProduct p
    INNER JOIN inserted i ON p.id = i.productId;
END
GO

-- --------------------------------------------------------
-- Datos (desactivamos temporalmente los triggers de auditoría/stock
-- para que la carga inicial no genere entradas de auditoría falsas
-- ni descuente/aumente el stock dos veces)
-- --------------------------------------------------------

DISABLE TRIGGER DisAuditInsertProduct ON TblProduct;
DISABLE TRIGGER DisAuditUpdateProduct ON TblProduct;
DISABLE TRIGGER DisAuditDeleteProduct ON TblProduct;
DISABLE TRIGGER DisIncreaseStockAfterPurchase ON TblPurchaseDetail;
DISABLE TRIGGER DisDecreaseStockAfterSale ON TblSaleDetail;
GO

SET IDENTITY_INSERT TblCategory ON;
INSERT INTO TblCategory (id, name, description) VALUES (1, N'Proteinas', N'Suplementos proteicos');
INSERT INTO TblCategory (id, name, description) VALUES (2, N'Creatinas', N'Suplementos para fuerza y rendimiento');
INSERT INTO TblCategory (id, name, description) VALUES (3, N'Vitaminas', N'Vitaminas y minerales');
INSERT INTO TblCategory (id, name, description) VALUES (4, N'Pre entrenos', N'Suplementos energéticos');
INSERT INTO TblCategory (id, name, description) VALUES (5, N'Accesorios', N'Shakers y accesorios deportivos');
SET IDENTITY_INSERT TblCategory OFF;
GO

SET IDENTITY_INSERT TblPaymentMethod ON;
INSERT INTO TblPaymentMethod (id, name) VALUES (1, N'SINPE');
INSERT INTO TblPaymentMethod (id, name) VALUES (2, N'Efectivo');
SET IDENTITY_INSERT TblPaymentMethod OFF;
GO

SET IDENTITY_INSERT TblSupplier ON;
INSERT INTO TblSupplier (id, name, phone, email, address) VALUES (1, N'GNC USA', N'8906 7889', N'gnc@gmail.com', N'San José');
SET IDENTITY_INSERT TblSupplier OFF;
GO

SET IDENTITY_INSERT TblUser ON;
INSERT INTO TblUser (id, name, email, password) VALUES (1, N'2AC Team', N'2acteam@gmail.com', N'$2y$10$xOC1g8MAeaQiKlMJ0CLCAO39wEd7TR0xvgkfQ6WkoNf/RI7BPQqFu');
INSERT INTO TblUser (id, name, email, password) VALUES (2, N'Alessandro', N'alegrodriguez78@gmail.com', N'$2y$10$vZYi.P7vMAaUbNa3LhIsTOyxh55ygNdnuUP.9gmSzBhdiWsFHtaei');
INSERT INTO TblUser (id, name, email, password) VALUES (3, N'Alessandro', N'alegrodriguez@gmail.com', N'$2y$10$Y23XRzAWvQLS4YoGU.kim.KW4yuFlOSMxr92cb/Vt6b57Y2CVqV/i');
SET IDENTITY_INSERT TblUser OFF;
GO

SET IDENTITY_INSERT TblProduct ON;
INSERT INTO TblProduct (id, categoryId, name, description, price, stock, image) VALUES (3, 1, N'Whey Protein', N'Proteina de la más alta calidad', 19000.00, 2, N'wheyprotein.png');
INSERT INTO TblProduct (id, categoryId, name, description, price, stock, image) VALUES (5, 3, N'Pre entreno BUM Strawberry', N'El preworkout BUM sabor Strawberry es un pre entreno pensado para darte energía, enfoque y mejor rendimiento en el gym sin sentirse "demasiado pesado". Tiene ingredientes para mejorar el bombeo muscular, aguante y fuerza durante el entrenamiento', 18000.00, 87, N'preentrenobumstrawberry.jpg');
INSERT INTO TblProduct (id, categoryId, name, description, price, stock, image) VALUES (6, 4, N'Vitamina C Member Selection', N'La vitamina C de Member''s Selection es un suplemento utilizado para apoyar el sistema inmunológico y aportar antioxidantes. Generalmente viene en tabletas o gomitas y ayuda también en la formación de colágeno y absorción de hierro. Se suele tomar una vez al día según la dosis indicada en el envase.', 140000.00, 88, N'vitaminac.webp');
INSERT INTO TblProduct (id, categoryId, name, description, price, stock, image) VALUES (9, 5, N'Muñequeras', N'Banda o correa que se pone alrededor de la muñeca para sujetarla o protegerla, o como adorno.', 13000.00, 15, N'Muñequeras.jpeg');
SET IDENTITY_INSERT TblProduct OFF;
GO

SET IDENTITY_INSERT TblPurchase ON;
INSERT INTO TblPurchase (id, supplierId, purchaseDate, total, productName, quantityBoxes, status) VALUES (1, 1, '2026-06-09 16:17:17', 10000.00, N'Whey Protein', 2, N'Recibido');
SET IDENTITY_INSERT TblPurchase OFF;
GO

SET IDENTITY_INSERT TblPurchaseDetail ON;
INSERT INTO TblPurchaseDetail (id, purchaseId, productId, quantity, subtotal) VALUES (1, 1, 3, 2, 10000.00);
SET IDENTITY_INSERT TblPurchaseDetail OFF;
GO

SET IDENTITY_INSERT TblSale ON;
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (1, 1, 1, 19000.00, '2026-06-09 16:16:52');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (2, 1, 1, 19000.00, '2026-06-09 17:00:12');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (3, 1, 1, 19000.00, '2026-06-09 17:00:39');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (4, 1, 1, 19000.00, '2026-06-09 17:00:40');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (5, 1, 1, 18000.00, '2026-06-09 17:00:49');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (6, 1, 1, 19000.00, '2026-06-09 17:00:56');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (7, 1, 1, 19000.00, '2026-06-09 17:35:47');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (8, 1, 1, 19000.00, '2026-06-09 17:42:06');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (9, 1, 1, 140000.00, '2026-06-09 17:42:31');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (10, 1, 1, 19000.00, '2026-06-09 18:39:58');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (11, 1, 1, 18000.00, '2026-06-09 18:40:05');
INSERT INTO TblSale (id, userId, paymentMethodId, total, saleDate) VALUES (12, 2, 2, 19000.00, '2026-07-30 10:32:57');
SET IDENTITY_INSERT TblSale OFF;
GO

SET IDENTITY_INSERT TblSaleDetail ON;
INSERT INTO TblSaleDetail (id, saleId, productId, quantity, subtotal) VALUES (1, 1, 3, 1, 19000.00);
INSERT INTO TblSaleDetail (id, saleId, productId, quantity, subtotal) VALUES (5, 5, 5, 1, 18000.00);
INSERT INTO TblSaleDetail (id, saleId, productId, quantity, subtotal) VALUES (9, 9, 6, 1, 140000.00);
INSERT INTO TblSaleDetail (id, saleId, productId, quantity, subtotal) VALUES (10, 10, 3, 1, 19000.00);
INSERT INTO TblSaleDetail (id, saleId, productId, quantity, subtotal) VALUES (11, 11, 5, 1, 18000.00);
INSERT INTO TblSaleDetail (id, saleId, productId, quantity, subtotal) VALUES (12, 12, 3, 1, 19000.00);
SET IDENTITY_INSERT TblSaleDetail OFF;
GO

SET IDENTITY_INSERT TblAuditLog ON;
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (1, N'INSERT', N'Whey Protein', '2026-06-09 16:15:10');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (2, N'UPDATE', N'Whey Protein', '2026-06-09 16:16:52');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (3, N'UPDATE', N'Whey Protein', '2026-06-09 16:16:52');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (4, N'UPDATE', N'Whey Protein', '2026-06-09 16:17:17');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (5, N'UPDATE', N'Whey Protein', '2026-06-09 16:18:02');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (6, N'INSERT', N'Creatina Monohidratada', '2026-06-09 16:37:57');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (7, N'DELETE', N'Creatina Monohidratada', '2026-06-09 16:38:23');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (8, N'INSERT', N'Pre entreno BUM Strawberry', '2026-06-09 16:46:40');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (9, N'INSERT', N'Vitamina C Member Selection', '2026-06-09 16:49:10');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (10, N'UPDATE', N'Whey Protein', '2026-06-09 16:51:55');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (11, N'INSERT', N'Creatina Monohidratada', '2026-06-09 16:52:41');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (12, N'DELETE', N'Creatina Monohidratada', '2026-06-09 16:52:46');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (13, N'UPDATE', N'Whey Protein', '2026-06-09 16:59:40');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (14, N'UPDATE', N'Whey Protein', '2026-06-09 17:00:04');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (18, N'UPDATE', N'Pre entreno BUM Strawberry', '2026-06-09 17:00:49');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (19, N'UPDATE', N'Pre entreno BUM Strawberry', '2026-06-09 17:00:49');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (21, N'UPDATE', N'Whey Protein', '2026-06-09 17:01:15');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (22, N'UPDATE', N'Whey Protein', '2026-06-09 17:01:27');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (25, N'UPDATE', N'Vitamina C Member Selection', '2026-06-09 17:42:31');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (26, N'UPDATE', N'Vitamina C Member Selection', '2026-06-09 17:42:31');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (27, N'UPDATE', N'Whey Protein', '2026-06-09 18:39:58');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (28, N'UPDATE', N'Pre entreno BUM Strawberry', '2026-06-09 18:40:05');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (29, N'UPDATE', N'Whey Protein', '2026-07-30 10:32:24');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (30, N'UPDATE', N'Whey Protein', '2026-07-30 10:32:57');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (31, N'INSERT', N'Muñequeras', '2026-08-27 10:23:13');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (32, N'DELETE', N'Muñequeras', '2026-08-27 12:53:09');
INSERT INTO TblAuditLog (id, actionType, productName, actionDate) VALUES (33, N'INSERT', N'Muñequeras', '2026-08-27 12:53:39');
SET IDENTITY_INSERT TblAuditLog OFF;
GO

ENABLE TRIGGER DisAuditInsertProduct ON TblProduct;
ENABLE TRIGGER DisAuditUpdateProduct ON TblProduct;
ENABLE TRIGGER DisAuditDeleteProduct ON TblProduct;
ENABLE TRIGGER DisIncreaseStockAfterPurchase ON TblPurchaseDetail;
ENABLE TRIGGER DisDecreaseStockAfterSale ON TblSaleDetail;
GO

-- Reiniciar el contador IDENTITY al último valor real usado en cada tabla
DBCC CHECKIDENT ('TblCategory', RESEED, 5);
DBCC CHECKIDENT ('TblPaymentMethod', RESEED, 2);
DBCC CHECKIDENT ('TblSupplier', RESEED, 1);
DBCC CHECKIDENT ('TblUser', RESEED, 3);
DBCC CHECKIDENT ('TblProduct', RESEED, 9);
DBCC CHECKIDENT ('TblPurchase', RESEED, 1);
DBCC CHECKIDENT ('TblPurchaseDetail', RESEED, 1);
DBCC CHECKIDENT ('TblSale', RESEED, 12);
DBCC CHECKIDENT ('TblSaleDetail', RESEED, 12);
DBCC CHECKIDENT ('TblAuditLog', RESEED, 33);
GO
