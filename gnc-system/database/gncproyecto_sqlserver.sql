-- BASE DE DATOS: gncproyecto

IF DB_ID('gncproyecto') IS NULL
BEGIN
    CREATE DATABASE gncproyecto;
END
GO

USE gncproyecto;
GO


/*================ Tablas =================*/
-- Tabla: tbl_auditLogs
IF OBJECT_ID('dbo.tbl_auditLogs', 'U') IS NOT NULL DROP TABLE dbo.tbl_auditLogs;
GO
CREATE TABLE dbo.tbl_auditLogs (
    id            INT IDENTITY(1,1) NOT NULL,
    action_type   VARCHAR(50)  NULL,
    product_name  VARCHAR(100) NULL,
    action_date   DATETIME     NULL DEFAULT GETDATE(),
    CONSTRAINT PK_audit_logs PRIMARY KEY (id)
);
GO

-- Tabla: tbl_categories
IF OBJECT_ID('dbo.tbl_categories', 'U') IS NOT NULL DROP TABLE dbo.tbl_categories;
GO
CREATE TABLE dbo.tbl_categories (
    id          INT IDENTITY(1,1) NOT NULL,
    name        VARCHAR(100) NOT NULL,
    description VARCHAR(MAX) NULL,
    CONSTRAINT PK_categories PRIMARY KEY (id)
);
GO

-- Tabla: tbl_paymentMethods
IF OBJECT_ID('dbo.tbl_paymentMethods', 'U') IS NOT NULL DROP TABLE dbo.tbl_paymentMethods;
GO
CREATE TABLE dbo.tbl_paymentMethods (
    id   INT IDENTITY(1,1) NOT NULL,
    name VARCHAR(50) NOT NULL,
    CONSTRAINT PK_payment_methods PRIMARY KEY (id)
);
GO

-- Tabla: tbl_suppliers
IF OBJECT_ID('dbo.tbl_suppliers', 'U') IS NOT NULL DROP TABLE dbo.tbl_suppliers;
GO
CREATE TABLE dbo.tbl_suppliers (
    id      INT IDENTITY(1,1) NOT NULL,
    name    VARCHAR(100) NOT NULL,
    phone   VARCHAR(30)  NULL,
    email   VARCHAR(100) NULL,
    address VARCHAR(255) NULL,
    CONSTRAINT PK_suppliers PRIMARY KEY (id)
);
GO

-- Tabla: tbl_users
IF OBJECT_ID('dbo.tbl_users', 'U') IS NOT NULL DROP TABLE dbo.tbl_users;
GO
CREATE TABLE dbo.tbl_users (
    id       INT IDENTITY(1,1) NOT NULL,
    name     VARCHAR(100) NOT NULL,
    email    VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    CONSTRAINT PK_users PRIMARY KEY (id),
    CONSTRAINT UQ_users_email UNIQUE (email)
);
GO

-- Tabla: tbl_products
IF OBJECT_ID('dbo.tbl_products', 'U') IS NOT NULL DROP TABLE dbo.tbl_products;
GO
CREATE TABLE dbo.tbl_products (
    id          INT IDENTITY(1,1) NOT NULL,
    category_id INT NOT NULL,
    name        VARCHAR(100) NOT NULL,
    description VARCHAR(MAX) NULL,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT NOT NULL DEFAULT 0,
    image       VARCHAR(255) NULL,
    CONSTRAINT PK_products PRIMARY KEY (id),
    CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES dbo.tbl_categories (id),
    CONSTRAINT check_price CHECK (price > 0),
    CONSTRAINT check_stock CHECK (stock >= 0)
);
GO

-- Tabla: tbl_purchases
IF OBJECT_ID('dbo.tbl_purchases', 'U') IS NOT NULL DROP TABLE dbo.tbl_purchases;
GO
CREATE TABLE dbo.tbl_purchases (
    id             INT IDENTITY(1,1) NOT NULL,
    supplier_id    INT NOT NULL,
    purchase_date  DATETIME NULL DEFAULT GETDATE(),
    total          DECIMAL(10,2) NOT NULL,
    product_name   VARCHAR(100) NULL,
    quantity_boxes INT NULL,
    status         VARCHAR(50) NULL,
    CONSTRAINT PK_purchases PRIMARY KEY (id),
    CONSTRAINT fk_purchase_supplier FOREIGN KEY (supplier_id) REFERENCES dbo.tbl_suppliers (id)
);
GO

-- Tabla: tbl_purchaseDetails
IF OBJECT_ID('dbo.tbl_purchaseDetails', 'U') IS NOT NULL DROP TABLE dbo.tbl_purchaseDetails;
GO
CREATE TABLE dbo.tbl_purchaseDetails (
    id          INT IDENTITY(1,1) NOT NULL,
    purchase_id INT NOT NULL,
    product_id  INT NOT NULL,
    quantity    INT NOT NULL,
    subtotal    DECIMAL(10,2) NOT NULL,
    CONSTRAINT PK_purchase_details PRIMARY KEY (id),
    CONSTRAINT fk_purchase_detail_product FOREIGN KEY (product_id) REFERENCES dbo.tbl_products (id),
    CONSTRAINT fk_purchase_detail_purchase FOREIGN KEY (purchase_id) REFERENCES dbo.tbl_purchases (id)
);
GO

-- Tabla: tbl_sales
IF OBJECT_ID('dbo.tbl_sales', 'U') IS NOT NULL DROP TABLE dbo.tbl_sales;
GO
CREATE TABLE dbo.tbl_sales (
    id                 INT IDENTITY(1,1) NOT NULL,
    user_id            INT NOT NULL,
    payment_method_id  INT NOT NULL,
    total              DECIMAL(10,2) NOT NULL,
    sale_date          DATETIME NULL DEFAULT GETDATE(),
    CONSTRAINT PK_sales PRIMARY KEY (id),
    CONSTRAINT fk_sale_payment_method FOREIGN KEY (payment_method_id) REFERENCES dbo.tbl_paymentMethods (id),
    CONSTRAINT fk_sale_user FOREIGN KEY (user_id) REFERENCES dbo.tbl_users (id)
);
GO

-- Tabla: tbl_saleDetails
IF OBJECT_ID('dbo.tbl_saleDetails', 'U') IS NOT NULL DROP TABLE dbo.tbl_saleDetails;
GO
CREATE TABLE dbo.tbl_saleDetails (
    id        INT IDENTITY(1,1) NOT NULL,
    sale_id   INT NOT NULL,
    product_id INT NOT NULL,
    quantity  INT NOT NULL,
    subtotal  DECIMAL(10,2) NOT NULL,
    CONSTRAINT PK_sale_details PRIMARY KEY (id),
    CONSTRAINT fk_sale_detail_product FOREIGN KEY (product_id) REFERENCES dbo.tbl_products (id),
    CONSTRAINT fk_sale_detail_sale FOREIGN KEY (sale_id) REFERENCES dbo.tbl_sales (id)
);
GO
/*================ DATOS ================*/
-- categories
SET IDENTITY_INSERT dbo.tbl_categories ON;
INSERT INTO dbo.tbl_categories (id, name, description) VALUES
    (1, N'Proteinas', N'Suplementos proteicos'),
    (2, N'Creatinas', N'Suplementos para fuerza y rendimiento'),
    (3, N'Vitaminas', N'Vitaminas y minerales'),
    (4, N'Pre entrenos', N'Suplementos energéticos'),
    (5, N'Accesorios', N'Shakers y accesorios deportivos');
SET IDENTITY_INSERT dbo.tbl_categories OFF;
GO

-- paymentMethods
SET IDENTITY_INSERT dbo.tbl_paymentMethods ON;
INSERT INTO dbo.tbl_paymentMethods (id, name) VALUES
    (1, N'SINPE'),
    (2, N'Efectivo');
SET IDENTITY_INSERT dbo.tbl_paymentMethods OFF;
GO

-- suppliers
SET IDENTITY_INSERT dbo.tbl_suppliers ON;
INSERT INTO dbo.tbl_suppliers (id, name, phone, email, address) VALUES
    (1, N'GNC USA', N'8906 7889', N'gnc@gmail.com', N'San José');
SET IDENTITY_INSERT dbo.tbl_suppliers OFF;
GO

-- users
SET IDENTITY_INSERT dbo.tbl_users ON;
INSERT INTO dbo.tbl_users (id, name, email, password) VALUES
    (1, N'2AC Team', N'2acteam@gmail.com', N'$2y$10$UispaEgFucSHtW6Ug9R/vuykxBmKORoLIPfRGbLpouQcDSRJRQ1ou'); -- password real: 2ac
SET IDENTITY_INSERT dbo.tbl_users OFF;
GO


SET IDENTITY_INSERT dbo.tbl_products ON;
INSERT INTO dbo.tbl_products (id, category_id, name, description, price, stock, image) VALUES
    (3, 1, N'Whey Protein', N'Proteina de la más alta calidad', 19000.00, 0, N'wheyprotein.png'),
    (5, 3, N'Pre entreno BUM Strawberry', N'El preworkout BUM sabor Strawberry es un pre entreno pensado para darte energía, enfoque y mejor rendimiento en el gym sin sentirse "demasiado pesado". Tiene ingredientes para mejorar el bombeo muscular, aguante y fuerza durante el entrenamiento', 18000.00, 87, N'preentrenobumstrawberry.jpg'),
    (6, 4, N'Vitamina C Member Selection', N'La vitamina C de Member''s Selection es un suplemento utilizado para apoyar el sistema inmunológico y aportar antioxidantes. Generalmente viene en tabletas o gomitas y ayuda también en la formación de colágeno y absorción de hierro. Se suele tomar una vez al día según la dosis indicada en el envase.', 140000.00, 88, N'vitaminac.webp');
SET IDENTITY_INSERT dbo.tbl_products OFF;
GO

-- purchases
SET IDENTITY_INSERT dbo.tbl_purchases ON;
INSERT INTO dbo.tbl_purchases (id, supplier_id, purchase_date, total, product_name, quantity_boxes, status) VALUES
    (1, 1, '2026-06-09T16:17:17', 10000.00, N'Whey Protein', 2, N'Recibido');
SET IDENTITY_INSERT dbo.tbl_purchases OFF;
GO


SET IDENTITY_INSERT dbo.tbl_purchaseDetails ON;
INSERT INTO dbo.tbl_purchaseDetails (id, purchase_id, product_id, quantity, subtotal) VALUES
    (1, 1, 3, 2, 10000.00);
SET IDENTITY_INSERT dbo.tbl_purchaseDetails OFF;
GO

-- sales
SET IDENTITY_INSERT dbo.tbl_sales ON;
INSERT INTO dbo.tbl_sales (id, user_id, payment_method_id, total, sale_date) VALUES
    (1, 1, 1, 19000.00, '2026-06-09T16:16:52'),
    (2, 1, 1, 19000.00, '2026-06-09T17:00:12'),
    (3, 1, 1, 19000.00, '2026-06-09T17:00:39'),
    (4, 1, 1, 19000.00, '2026-06-09T17:00:40'),
    (5, 1, 1, 18000.00, '2026-06-09T17:00:49'),
    (6, 1, 1, 19000.00, '2026-06-09T17:00:56'),
    (7, 1, 1, 19000.00, '2026-06-09T17:35:47'),
    (8, 1, 1, 19000.00, '2026-06-09T17:42:06'),
    (9, 1, 1, 140000.00, '2026-06-09T17:42:31'),
    (10, 1, 1, 19000.00, '2026-06-09T18:39:58'),
    (11, 1, 1, 18000.00, '2026-06-09T18:40:05');
SET IDENTITY_INSERT dbo.tbl_sales OFF;
GO

-- saleDetails
SET IDENTITY_INSERT dbo.tbl_saleDetails ON;
INSERT INTO dbo.tbl_saleDetails (id, sale_id, product_id, quantity, subtotal) VALUES
    (1, 1, 3, 1, 19000.00),
    (5, 5, 5, 1, 18000.00),
    (9, 9, 6, 1, 140000.00),
    (10, 10, 3, 1, 19000.00),
    (11, 11, 5, 1, 18000.00);
SET IDENTITY_INSERT dbo.tbl_saleDetails OFF;
GO

-- auditLogs
SET IDENTITY_INSERT dbo.tbl_auditLogs ON;
INSERT INTO dbo.tbl_auditLogs (id, action_type, product_name, action_date) VALUES
    (1, N'INSERT', N'Whey Protein', '2026-06-09T16:15:10'),
    (2, N'UPDATE', N'Whey Protein', '2026-06-09T16:16:52'),
    (3, N'UPDATE', N'Whey Protein', '2026-06-09T16:16:52'),
    (4, N'UPDATE', N'Whey Protein', '2026-06-09T16:17:17'),
    (5, N'UPDATE', N'Whey Protein', '2026-06-09T16:18:02'),
    (6, N'INSERT', N'Creatina Monohidratada', '2026-06-09T16:37:57'),
    (7, N'DELETE', N'Creatina Monohidratada', '2026-06-09T16:38:23'),
    (8, N'INSERT', N'Pre entreno BUM Strawberry', '2026-06-09T16:46:40'),
    (9, N'INSERT', N'Vitamina C Member Selection', '2026-06-09T16:49:10'),
    (10, N'UPDATE', N'Whey Protein', '2026-06-09T16:51:55'),
    (11, N'INSERT', N'Creatina Monohidratada', '2026-06-09T16:52:41'),
    (12, N'DELETE', N'Creatina Monohidratada', '2026-06-09T16:52:46'),
    (13, N'UPDATE', N'Whey Protein', '2026-06-09T16:59:40'),
    (14, N'UPDATE', N'Whey Protein', '2026-06-09T17:00:04'),
    (18, N'UPDATE', N'Pre entreno BUM Strawberry', '2026-06-09T17:00:49'),
    (19, N'UPDATE', N'Pre entreno BUM Strawberry', '2026-06-09T17:00:49'),
    (21, N'UPDATE', N'Whey Protein', '2026-06-09T17:01:15'),
    (22, N'UPDATE', N'Whey Protein', '2026-06-09T17:01:27'),
    (25, N'UPDATE', N'Vitamina C Member Selection', '2026-06-09T17:42:31'),
    (26, N'UPDATE', N'Vitamina C Member Selection', '2026-06-09T17:42:31'),
    (27, N'UPDATE', N'Whey Protein', '2026-06-09T18:39:58'),
    (28, N'UPDATE', N'Pre entreno BUM Strawberry', '2026-06-09T18:40:05');
SET IDENTITY_INSERT dbo.tbl_auditLogs OFF;
GO

/*================ PROCEDIMIENTOS ALMACENADOS ================*/
--P1
CREATE OR ALTER PROCEDURE dbo.pa_insertarProducto
    @category_id INT,
    @name VARCHAR(100),
    @description VARCHAR(MAX) = NULL,
    @price DECIMAL(10,2),
    @stock INT = 0,
    @image VARCHAR(255) = NULL
AS
BEGIN
    SET NOCOUNT ON;

    -- Validar que la categoría exista
    IF NOT EXISTS (
        SELECT 1
        FROM dbo.tbl_categories
        WHERE id = @category_id
    )
    BEGIN
        ;THROW 51001,
            'La categoria indicada no existe.',
            1;
    END;


    -- Validar nombre
    IF NULLIF(LTRIM(RTRIM(@name)), '') IS NULL
    BEGIN
        ;THROW 51002,
            'El nombre del producto es obligatorio.',
            1;
    END;


    -- Validar precio
    IF @price <= 0
    BEGIN
        ;THROW 51003,
            'El precio debe ser mayor que cero.',
            1;
    END;


    -- Validar stock
    IF @stock < 0
    BEGIN
        ;THROW 51004,
            'El stock no puede ser negativo.',
            1;
    END;


    -- Insertar producto
    INSERT INTO dbo.tbl_products
    (
        category_id,
        name,
        description,
        price,
        stock,
        image
    )
    VALUES
    (
        @category_id,
        @name,
        @description,
        @price,
        @stock,
        @image
    );


    -- Mostrar el producto creado
    DECLARE @nuevo_id INT = SCOPE_IDENTITY();

    SELECT *
    FROM dbo.tbl_products
    WHERE id = @nuevo_id;

END;
GO

/*================ PROCEDIMIENTO ALMACENADO ================*/
--P2
CREATE OR ALTER PROCEDURE dbo.pa_actualizarProducto
    @id INT,
    @category_id INT,
    @name VARCHAR(100),
    @description VARCHAR(MAX) = NULL,
    @price DECIMAL(10,2),
    @stock INT,
    @image VARCHAR(255) = NULL
AS
BEGIN
    SET NOCOUNT ON;


    -- Validar que el producto exista
    IF NOT EXISTS (
        SELECT 1
        FROM dbo.tbl_products
        WHERE id = @id
    )
    BEGIN
        ;THROW 51005,
            'El producto indicado no existe.',
            1;
    END;


    -- Validar categoría
    IF NOT EXISTS (
        SELECT 1
        FROM dbo.tbl_categories
        WHERE id = @category_id
    )
    BEGIN
        ;THROW 51006,
            'La categoria indicada no existe.',
            1;
    END;


    -- Validar nombre
    IF NULLIF(LTRIM(RTRIM(@name)), '') IS NULL
    BEGIN
        ;THROW 51007,
            'El nombre del producto es obligatorio.',
            1;
    END;


    -- Validar precio
    IF @price <= 0
    BEGIN
        ;THROW 51008,
            'El precio debe ser mayor que cero.',
            1;
    END;


    -- Validar stock
    IF @stock < 0
    BEGIN
        ;THROW 51009,
            'El stock no puede ser negativo.',
            1;
    END;


    -- Actualizar producto
    UPDATE dbo.tbl_products
    SET
        category_id = @category_id,
        name = @name,
        description = @description,
        price = @price,
        stock = @stock,
        image = @image
    WHERE id = @id;


    -- Mostrar producto actualizado
    SELECT *
    FROM dbo.tbl_products
    WHERE id = @id;

END;
GO

/*================ PROCEDIMIENTO ALMACENADO ================*/
--P3
CREATE OR ALTER PROCEDURE dbo.pa_eliminarProducto
    @id INT
AS
BEGIN
    SET NOCOUNT ON;


    -- Validar que el producto exista
    IF NOT EXISTS (
        SELECT 1
        FROM dbo.tbl_products
        WHERE id = @id
    )
    BEGIN
        ;THROW 51010,
            'El producto indicado no existe.',
            1;
    END;


    -- No permitir eliminar productos que tengan ventas
    IF EXISTS (
        SELECT 1
        FROM dbo.tbl_saleDetails
        WHERE product_id = @id
    )
    BEGIN
        ;THROW 51011,
            'No se puede eliminar el producto porque tiene ventas registradas.',
            1;
    END;


    -- No permitir eliminar productos que tengan compras
    IF EXISTS (
        SELECT 1
        FROM dbo.tbl_purchaseDetails
        WHERE product_id = @id
    )
    BEGIN
        ;THROW 51012,
            'No se puede eliminar el producto porque tiene compras registradas.',
            1;
    END;


    DELETE FROM dbo.tbl_products
    WHERE id = @id;


    SELECT
        'Producto eliminado correctamente.' AS mensaje;

END;
GO

/*================ PROCEDIMIENTO ALMACENADO ================*/
--P4
CREATE OR ALTER PROCEDURE dbo.pa_buscarProducto
    @id INT
AS
BEGIN
    SET NOCOUNT ON;


    -- Validar existencia
    IF NOT EXISTS (
        SELECT 1
        FROM dbo.tbl_products
        WHERE id = @id
    )
    BEGIN
        ;THROW 51013,
            'No se encontro un producto con ese ID.',
            1;
    END;


    SELECT
        p.id,
        p.name,
        p.description,
        p.price,
        p.stock,
        p.image,
        p.category_id,
        c.name AS category_name
    FROM dbo.tbl_products p

    INNER JOIN dbo.tbl_categories c
        ON p.category_id = c.id

    WHERE p.id = @id;

END;
GO

/*================ PROCEDIMIENTO ALMACENADO ================*/
--P5
CREATE OR ALTER PROCEDURE dbo.pa_filtrarProductos
    @name VARCHAR(100) = NULL,
    @category_id INT = NULL,
    @precio_minimo DECIMAL(10,2) = NULL,
    @precio_maximo DECIMAL(10,2) = NULL,
    @stock_maximo INT = NULL
AS
BEGIN
    SET NOCOUNT ON;


    -- Validar rango de precios
    IF @precio_minimo IS NOT NULL
       AND @precio_maximo IS NOT NULL
       AND @precio_minimo > @precio_maximo
    BEGIN
        ;THROW 51014,
            'El precio minimo no puede ser mayor que el precio maximo.',
            1;
    END;


    SELECT
        p.id,
        p.name,
        p.description,
        p.price,
        p.stock,
        p.image,
        p.category_id,
        c.name AS category_name
    FROM dbo.tbl_products p

    INNER JOIN dbo.tbl_categories c
        ON p.category_id = c.id

    WHERE
        (
            @name IS NULL
            OR p.name LIKE '%' + @name + '%'
        )

        AND
        (
            @category_id IS NULL
            OR p.category_id = @category_id
        )

        AND
        (
            @precio_minimo IS NULL
            OR p.price >= @precio_minimo
        )

        AND
        (
            @precio_maximo IS NULL
            OR p.price <= @precio_maximo
        )

        AND
        (
            @stock_maximo IS NULL
            OR p.stock <= @stock_maximo
        )

    ORDER BY p.name ASC;

END;
GO

/*================ PROCEDIMIENTO ALMACENADO ================*/
--P6
CREATE OR ALTER PROCEDURE dbo.pa_productosStockBajo
    @limite INT = 5
AS
BEGIN
    SET NOCOUNT ON;


    IF @limite < 0
    BEGIN
        ;THROW 51015,
            'El limite de stock no puede ser negativo.',
            1;
    END;


    SELECT
        p.id,
        p.name,
        p.description,
        p.price,
        p.stock,
        c.name AS category_name
    FROM dbo.tbl_products p

    INNER JOIN dbo.tbl_categories c
        ON p.category_id = c.id

    WHERE p.stock < @limite

    ORDER BY p.stock ASC;

END;
GO

/*================ VISTAS ================*/
--V1
CREATE OR ALTER VIEW dbo.v_productosSinStock
AS
SELECT
    p.id,
    p.name AS producto,
    p.description,
    p.price,
    p.stock,
    c.name AS categoria
FROM dbo.tbl_products p
INNER JOIN dbo.tbl_categories c
    ON p.category_id = c.id
WHERE p.stock = 0;
GO

/*================ VISTAS ================*/
--V2
CREATE OR ALTER VIEW dbo.v_historialVentas
AS
SELECT
    s.id AS venta_id,
    u.name AS usuario,
    p.name AS producto,
    pm.name AS metodo_pago,
    sd.quantity AS cantidad,
    sd.subtotal,
    s.total,
    s.sale_date AS fecha_venta
FROM dbo.tbl_saleDetails sd
INNER JOIN dbo.tbl_sales s
    ON sd.sale_id = s.id
INNER JOIN dbo.tbl_products p
    ON sd.product_id = p.id
INNER JOIN dbo.tbl_users u
    ON s.user_id = u.id
INNER JOIN dbo.tbl_paymentMethods pm
    ON s.payment_method_id = pm.id;
GO

/*================ VISTAS ================*/
--V3
CREATE OR ALTER VIEW dbo.v_inventarioProductos
AS
SELECT
    p.id,
    p.name AS producto,
    c.name AS categoria,
    p.price AS precio,
    p.stock,
    p.image
FROM dbo.tbl_products p
INNER JOIN dbo.tbl_categories c
    ON p.category_id = c.id;
GO

/*================ VISTAS ================*/
--V4
CREATE OR ALTER VIEW dbo.v_productosStockBajo
AS
SELECT
    p.id,
    p.name AS producto,
    c.name AS categoria,
    p.price AS precio,
    p.stock
FROM dbo.tbl_products p
INNER JOIN dbo.tbl_categories c
    ON p.category_id = c.id
WHERE p.stock < 5;
GO

/*================ VISTAS ================*/
--V5
CREATE OR ALTER VIEW dbo.v_historialCompras
AS
SELECT
    pu.id AS compra_id,
    s.name AS proveedor,
    p.name AS producto,
    pd.quantity AS cantidad,
    pd.subtotal,
    pu.total,
    pu.status AS estado,
    pu.purchase_date AS fecha_compra
FROM dbo.tbl_purchaseDetails pd
INNER JOIN dbo.tbl_purchases pu
    ON pd.purchase_id = pu.id
INNER JOIN dbo.tbl_products p
    ON pd.product_id = p.id
INNER JOIN dbo.tbl_suppliers s
    ON pu.supplier_id = s.id;
GO

/*================ VISTAS ================*/
--v6
CREATE OR ALTER VIEW dbo.v_resumenProductosVendidos
AS
SELECT
    p.id AS producto_id,
    p.name AS producto,
    SUM(sd.quantity) AS cantidad_vendida,
    SUM(sd.subtotal) AS total_vendido
FROM dbo.tbl_saleDetails sd
INNER JOIN dbo.tbl_products p
    ON sd.product_id = p.id
GROUP BY
    p.id,
    p.name;
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis01
CREATE OR ALTER TRIGGER dbo.dis_auditInsertProduct
ON dbo.tbl_products
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    INSERT INTO dbo.tbl_auditLogs (action_type, product_name)
    SELECT 'INSERT', name
    FROM inserted;
END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis02
CREATE OR ALTER TRIGGER dbo.dis_auditUpdateProduct
ON dbo.tbl_products
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    INSERT INTO dbo.tbl_auditLogs (action_type, product_name)
    SELECT 'UPDATE', name
    FROM inserted;
END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis03
CREATE OR ALTER TRIGGER dbo.dis_auditDeleteProduct
ON dbo.tbl_products
AFTER DELETE
AS
BEGIN
    SET NOCOUNT ON;

    INSERT INTO dbo.tbl_auditLogs (action_type, product_name)
    SELECT 'DELETE', name
    FROM deleted;
END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis04
CREATE OR ALTER TRIGGER dbo.dis_decreaseStockAfterSale
ON dbo.tbl_saleDetails
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Validar que la cantidad sea mayor que cero
    IF EXISTS (
        SELECT 1
        FROM inserted
        WHERE quantity <= 0
    )
    BEGIN
        ;THROW 50001,
        'La cantidad vendida debe ser mayor que cero.',
        1;
    END;


    -- Validar que exista suficiente stock
    IF EXISTS (
        SELECT 1
        FROM dbo.tbl_products p
        INNER JOIN (
            SELECT product_id, SUM(quantity) AS quantity
            FROM inserted
            GROUP BY product_id
        ) i
            ON p.id = i.product_id
        WHERE p.stock < i.quantity
    )
    BEGIN
        ;THROW 50002,
        'No existe suficiente stock para realizar la venta.',
        1;
    END;


    -- Descontar del inventario
    ;WITH cantidades AS
    (
        SELECT
            product_id,
            SUM(quantity) AS total_quantity
        FROM inserted
        GROUP BY product_id
    )
    UPDATE p
    SET p.stock = p.stock - c.total_quantity
    FROM dbo.tbl_products p
    INNER JOIN cantidades c
        ON p.id = c.product_id;

END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis05
CREATE OR ALTER TRIGGER dbo.dis_restoreStockAfterSaleDelete
ON dbo.tbl_saleDetails
AFTER DELETE
AS
BEGIN
    SET NOCOUNT ON;

    ;WITH cantidades AS
    (
        SELECT
            product_id,
            SUM(quantity) AS total_quantity
        FROM deleted
        GROUP BY product_id
    )
    UPDATE p
    SET p.stock = p.stock + c.total_quantity
    FROM dbo.tbl_products p
    INNER JOIN cantidades c
        ON p.id = c.product_id;

END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis06
CREATE OR ALTER TRIGGER dbo.dis_adjustStockAfterSaleUpdate
ON dbo.tbl_saleDetails
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- No permitir cantidades inválidas
    IF EXISTS (
        SELECT 1
        FROM inserted
        WHERE quantity <= 0
    )
    BEGIN
        ;THROW 50003,
        'La cantidad vendida debe ser mayor que cero.',
        1;
    END;


    -- Solo modificar inventario si cambió
    -- el producto o la cantidad
    IF UPDATE(quantity) OR UPDATE(product_id)
    BEGIN

        -- Validar que el cambio no deje stock negativo
        IF EXISTS (
            SELECT 1
            FROM dbo.tbl_products p
            INNER JOIN
            (
                SELECT
                    product_id,
                    SUM(cambio) AS cambio
                FROM
                (
                    -- Devolvemos temporalmente lo que tenía antes
                    SELECT
                        product_id,
                        quantity AS cambio
                    FROM deleted

                    UNION ALL

                    -- Quitamos la nueva cantidad
                    SELECT
                        product_id,
                        -quantity AS cambio
                    FROM inserted
                ) movimientos
                GROUP BY product_id
            ) c
                ON p.id = c.product_id
            WHERE p.stock + c.cambio < 0
        )
        BEGIN
            ;THROW 50004,
            'No existe suficiente stock para modificar esta venta.',
            1;
        END;


        -- Aplicar el cambio al inventario
        ;WITH cambios AS
        (
            SELECT
                product_id,
                SUM(cambio) AS cambio
            FROM
            (
                SELECT
                    product_id,
                    quantity AS cambio
                FROM deleted

                UNION ALL

                SELECT
                    product_id,
                    -quantity AS cambio
                FROM inserted
            ) movimientos
            GROUP BY product_id
        )
        UPDATE p
        SET p.stock = p.stock + c.cambio
        FROM dbo.tbl_products p
        INNER JOIN cambios c
            ON p.id = c.product_id
        WHERE c.cambio <> 0;

    END

END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis07
CREATE OR ALTER TRIGGER dbo.dis_recalculateSaleTotal
ON dbo.tbl_saleDetails
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;

    ;WITH ventas_afectadas AS
    (
        SELECT sale_id
        FROM inserted

        UNION

        SELECT sale_id
        FROM deleted
    )
    UPDATE s
    SET s.total =
        COALESCE(
            (
                SELECT SUM(sd.subtotal)
                FROM dbo.tbl_saleDetails sd
                WHERE sd.sale_id = s.id
            ),
            0
        )
    FROM dbo.tbl_sales s
    INNER JOIN ventas_afectadas va
        ON s.id = va.sale_id;

END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis08
CREATE OR ALTER TRIGGER dbo.dis_increaseStockAfterPurchase
ON dbo.tbl_purchaseDetails
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Validación
    IF EXISTS (
        SELECT 1
        FROM inserted
        WHERE quantity <= 0
    )
    BEGIN
        ;THROW 50005,
        'La cantidad comprada debe ser mayor que cero.',
        1;
    END;


    -- Sumar productos al inventario
    ;WITH cantidades AS
    (
        SELECT
            product_id,
            SUM(quantity) AS total_quantity
        FROM inserted
        GROUP BY product_id
    )
    UPDATE p
    SET p.stock = p.stock + c.total_quantity
    FROM dbo.tbl_products p
    INNER JOIN cantidades c
        ON p.id = c.product_id;

END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis09
CREATE OR ALTER TRIGGER dbo.dis_adjustStockAfterPurchaseUpdate
ON dbo.tbl_purchaseDetails
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Validar cantidad
    IF EXISTS (
        SELECT 1
        FROM inserted
        WHERE quantity <= 0
    )
    BEGIN
        ;THROW 50006,
        'La cantidad comprada debe ser mayor que cero.',
        1;
    END;


    IF UPDATE(quantity) OR UPDATE(product_id)
    BEGIN

        -- Verificar que el ajuste no deje
        -- inventario negativo
        IF EXISTS (
            SELECT 1
            FROM dbo.tbl_products p
            INNER JOIN
            (
                SELECT
                    product_id,
                    SUM(cambio) AS cambio
                FROM
                (
                    -- Quitar la compra anterior
                    SELECT
                        product_id,
                        -quantity AS cambio
                    FROM deleted

                    UNION ALL

                    -- Aplicar la nueva compra
                    SELECT
                        product_id,
                        quantity AS cambio
                    FROM inserted
                ) movimientos
                GROUP BY product_id
            ) c
                ON p.id = c.product_id
            WHERE p.stock + c.cambio < 0
        )
        BEGIN
            ;THROW 50007,
            'No se puede modificar la compra porque el stock quedaría negativo.',
            1;
        END;


        -- Actualizar inventario
        ;WITH cambios AS
        (
            SELECT
                product_id,
                SUM(cambio) AS cambio
            FROM
            (
                SELECT
                    product_id,
                    -quantity AS cambio
                FROM deleted

                UNION ALL

                SELECT
                    product_id,
                    quantity AS cambio
                FROM inserted
            ) movimientos
            GROUP BY product_id
        )
        UPDATE p
        SET p.stock = p.stock + c.cambio
        FROM dbo.tbl_products p
        INNER JOIN cambios c
            ON p.id = c.product_id
        WHERE c.cambio <> 0;

    END

END
GO

/*================ DISPARADORES -->TRIGGERS ================*/
--dis10
CREATE OR ALTER TRIGGER dbo.dis_removeStockAfterPurchaseDelete
ON dbo.tbl_purchaseDetails
AFTER DELETE
AS
BEGIN
    SET NOCOUNT ON;

    -- Verificar si todavía existe suficiente
    -- inventario para eliminar la compra
    IF EXISTS (
        SELECT 1
        FROM dbo.tbl_products p
        INNER JOIN
        (
            SELECT
                product_id,
                SUM(quantity) AS total_quantity
            FROM deleted
            GROUP BY product_id
        ) d
            ON p.id = d.product_id
        WHERE p.stock < d.total_quantity
    )
    BEGIN
        ;THROW 50008,
        'No se puede eliminar la compra porque parte de ese inventario ya fue utilizado o vendido.',
        1;
    END;


    -- Quitar del inventario lo que había agregado la compra
    ;WITH cantidades AS
    (
        SELECT
            product_id,
            SUM(quantity) AS total_quantity
        FROM deleted
        GROUP BY product_id
    )
    UPDATE p
    SET p.stock = p.stock - c.total_quantity
    FROM dbo.tbl_products p
    INNER JOIN cantidades c
        ON p.id = c.product_id;

END
GO
/*================ DISPARADORES -->TRIGGERS ================*/
--dis11
CREATE OR ALTER TRIGGER dbo.dis_recalculatePurchaseTotal
ON dbo.tbl_purchaseDetails
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;

    ;WITH compras_afectadas AS
    (
        SELECT purchase_id
        FROM inserted

        UNION

        SELECT purchase_id
        FROM deleted
    )
    UPDATE p
    SET p.total =
        COALESCE(
            (
                SELECT SUM(pd.subtotal)
                FROM dbo.tbl_purchaseDetails pd
                WHERE pd.purchase_id = p.id
            ),
            0
        )
    FROM dbo.tbl_purchases p
    INNER JOIN compras_afectadas ca
        ON p.id = ca.purchase_id;

END
GO
