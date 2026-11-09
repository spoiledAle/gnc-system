IF DB_ID('gncproyecto') IS NULL
BEGIN
    CREATE DATABASE gncproyecto;
END
GO

USE gncproyecto;
GO


/*================ Tablas =================*/
-- Las tablas de auditoría (tbl_audit*) se crean en la sección
-- "AUDITORÍA DE LA BASE DE DATOS", al final de este script.

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
    (1, N'2AC Team', N'2acteam@gmail.com', N'SIN_ACCESO');
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
-- Los disparadores de auditoría (dis01 a dis03) están en la sección
-- "AUDITORÍA DE LA BASE DE DATOS", al final de este script.

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


GO


/* ============================================================
   FUNCIÓN ESCALAR 1: fn_valorInventarioProducto

   Descripción:
   Calcula cuánto vale el inventario disponible de un producto,
   multiplicando su precio por las unidades que hay en stock.

   Importancia o utilidad:
   Permite conocer el valor monetario almacenado de cada producto.
   Puede utilizarse en reportes de inventario y para identificar
   productos que representan una mayor inversión.
   ============================================================ */

CREATE OR ALTER FUNCTION dbo.fn_valorInventarioProducto
(
    @productId INT
)
RETURNS DECIMAL(18,2)
AS
BEGIN
    DECLARE @valor DECIMAL(18,2);

    SELECT @valor = CAST(price * stock AS DECIMAL(18,2))
    FROM dbo.tbl_products
    WHERE id = @productId;


    RETURN @valor;
END;
GO


/* ============================================================
   FUNCIÓN ESCALAR 2: fn_unidadesVendidasProducto

   Descripción:
   Calcula el total de unidades vendidas de un producto.

   Importancia o utilidad:
   Ayuda a medir la demanda de cada producto. Esta información
   puede apoyar decisiones de compra, reposición de inventario
   y análisis de productos más vendidos.
   ============================================================ */

CREATE OR ALTER FUNCTION dbo.fn_unidadesVendidasProducto

(
    @productId INT
)
RETURNS INT
AS
BEGIN
    DECLARE @cantidad INT;

    SELECT @cantidad = SUM(quantity)
    FROM dbo.tbl_saleDetails
    WHERE product_id = @productId;

    RETURN COALESCE(@cantidad, 0);
END;
GO


/* ============================================================
   FUNCIÓN DE TABLA DE VARIAS INSTRUCCIONES 1:
   fn_productosBajoLimite

   Descripción:
   Devuelve los productos cuyo stock es menor que un límite
   indicado e informa cuántas unidades faltan para alcanzarlo.

   Importancia o utilidad:
   Ayuda a detectar productos que necesitan reposición y a
   calcular cuántas unidades habría que adquirir para llegar
   al nivel de inventario elegido.
   ============================================================ */

CREATE OR ALTER FUNCTION dbo.fn_productosBajoLimite

(
    @limite INT
)
RETURNS @resultado TABLE
(
    productId INT,
    producto VARCHAR(100),
    categoria VARCHAR(100),
    stock INT,
    faltantes INT
)
AS
BEGIN
    INSERT INTO @resultado
        (productId, producto, categoria, stock, faltantes)
    SELECT
        p.id,
        p.name,
        c.name,
        p.stock,
        @limite - p.stock
    FROM dbo.tbl_products AS p
    INNER JOIN dbo.tbl_categories AS c
        ON c.id = p.category_id
    WHERE p.stock < @limite;

    RETURN;

END;
GO


/* ============================================================
   FUNCIÓN DE TABLA DE VARIAS INSTRUCCIONES 2:
   fn_resumenVentasPeriodo

   Descripción:
   Presenta las unidades y el monto vendido de cada producto
   durante un intervalo de fechas.

   Importancia o utilidad:
   Permite analizar el desempeño de los productos en un período
   específico, por ejemplo, durante un día, mes o trimestre.
   Esto facilita la elaboración de reportes de ventas.
   ============================================================ */

CREATE OR ALTER FUNCTION dbo.fn_resumenVentasPeriodo
(
    @fechaInicio DATE,
    @fechaFin DATE
)
RETURNS @resultado TABLE

(
    productId INT,
    producto VARCHAR(100),
    unidadesVendidas INT,
    montoVendido DECIMAL(18,2)
)
AS
BEGIN
    INSERT INTO @resultado
        (productId, producto, unidadesVendidas, montoVendido)

    SELECT
        p.id,
        p.name,
        SUM(sd.quantity),
        SUM(CAST(sd.subtotal AS DECIMAL(18,2)))
    FROM dbo.tbl_saleDetails AS sd
    INNER JOIN dbo.tbl_sales AS s
        ON s.id = sd.sale_id
    INNER JOIN dbo.tbl_products AS p
        ON p.id = sd.product_id
    WHERE s.sale_date >= @fechaInicio
      AND s.sale_date < DATEADD(DAY, 1, @fechaFin)
    GROUP BY p.id, p.name;

    RETURN;
END;
GO


/* ============================================================
   FUNCIÓN DE TABLA EN LÍNEA 1: fn_productosPorCategoria

   Descripción:
   Devuelve los productos que pertenecen a una categoría
   específica, junto con su precio y stock.

   Importancia o utilidad:
   Permite reutilizar el filtro por categoría en consultas
   de inventario o en pantallas donde se muestran productos
   según la categoría seleccionada.
   ============================================================ */

CREATE OR ALTER FUNCTION dbo.fn_productosPorCategoria
(
    @categoryId INT
)
RETURNS TABLE
AS
RETURN
(
    SELECT
        p.id AS productId,
        p.name AS producto,
        c.name AS categoria,
        p.price AS precio,
        p.stock
    FROM dbo.tbl_products AS p
    INNER JOIN dbo.tbl_categories AS c
        ON c.id = p.category_id
    WHERE p.category_id = @categoryId
);

GO


/* ============================================================
   FUNCIÓN DE TABLA EN LÍNEA 2: fn_detalleVenta

   Descripción:
   Muestra los productos, cantidades, subtotales, fecha
   y método de pago de una venta determinada.

   Importancia o utilidad:
   Facilita consultar el detalle de una venta usando solamente
   su ID. Puede utilizarse para revisar transacciones, generar
   comprobantes o atender consultas sobre ventas anteriores.
   ============================================================ */

CREATE OR ALTER FUNCTION dbo.fn_detalleVenta
(
    @saleId INT
)
RETURNS TABLE
AS
RETURN
(
    SELECT
        s.id AS saleId,
        s.sale_date AS fechaVenta,
        p.name AS producto,
        sd.quantity AS cantidad,
        sd.subtotal,
        pm.name AS metodoPago
    FROM dbo.tbl_sales AS s
    INNER JOIN dbo.tbl_saleDetails AS sd
        ON sd.sale_id = s.id
    INNER JOIN dbo.tbl_products AS p
        ON p.id = sd.product_id
    INNER JOIN dbo.tbl_paymentMethods AS pm
        ON pm.id = s.payment_method_id
    WHERE s.id = @saleId
);

GO

--Escalar consulta
SELECT dbo.fn_valorInventarioProducto(5) AS valorInventario;
SELECT dbo.fn_unidadesVendidasProducto(5) AS unidadesVendidas;

--De varias instrucciones consultas
SELECT * FROM dbo.fn_productosBajoLimite(5);
SELECT * FROM dbo.fn_resumenVentasPeriodo('2026-06-01', '2026-06-30');

--En linea consultas
SELECT * FROM dbo.fn_productosPorCategoria(1);
SELECT * FROM dbo.fn_detalleVenta(5);
GO

/* ============================================================
   CUENTAS SQL, USUARIOS, ROLES Y PERMISOS DE LA APLICACIÓN
   ============================================================ */
USE master;
GO
/* Las contraseñas no se escriben en este script. Cuando un login no
   existe, se crea con una contraseña aleatoria que se muestra UNA
   sola vez en la pestaña Messages de SSMS: guárdala en un lugar
   seguro. Si el login ya existe, no se modifica.
   Para cambiarla después:
   ALTER LOGIN gnc_admin WITH PASSWORD = '<nueva contraseña>'; */
DECLARE @logins TABLE (login_name SYSNAME PRIMARY KEY);
INSERT INTO @logins VALUES (N'gnc_admin'), (N'gnc_empleado'), (N'gnc_cliente');

DECLARE @login SYSNAME, @password NVARCHAR(128);

WHILE EXISTS (SELECT 1 FROM @logins)
BEGIN
    SELECT TOP 1 @login = login_name FROM @logins ORDER BY login_name;
    DELETE FROM @logins WHERE login_name = @login;

    IF SUSER_ID(@login) IS NULL
    BEGIN
        SET @password = N'Gnc!' + CONVERT(NVARCHAR(64), CRYPT_GEN_RANDOM(16), 2) + N'a';

        EXEC (N'CREATE LOGIN ' + QUOTENAME(@login)
            + N' WITH PASSWORD = N''' + @password + N''''
            + N', CHECK_POLICY = ON, DEFAULT_DATABASE = gncproyecto;');

        PRINT N'Login ' + @login + N' creado. Contraseña: ' + @password
            + N'  (guárdala, no se vuelve a mostrar)';
    END;
END;
GO
USE gncproyecto;
GO
IF USER_ID(N'gnc_admin') IS NULL
    CREATE USER gnc_admin FOR LOGIN gnc_admin;
GO
IF USER_ID(N'gnc_empleado') IS NULL
    CREATE USER gnc_empleado FOR LOGIN gnc_empleado;
GO
IF USER_ID(N'gnc_cliente') IS NULL
    CREATE USER gnc_cliente FOR LOGIN gnc_cliente;
GO
IF DATABASE_PRINCIPAL_ID(N'rolAdmin') IS NULL
    CREATE ROLE rolAdmin AUTHORIZATION dbo;
GO
IF IS_ROLEMEMBER(N'rolAdmin', N'gnc_admin') <> 1
    ALTER ROLE rolAdmin ADD MEMBER gnc_admin;
GO
GRANT CONNECT TO gnc_admin;
GO
IF DATABASE_PRINCIPAL_ID(N'rolEmpleado') IS NULL
    CREATE ROLE rolEmpleado AUTHORIZATION dbo;
GO
IF IS_ROLEMEMBER(N'rolEmpleado', N'gnc_empleado') <> 1
    ALTER ROLE rolEmpleado ADD MEMBER gnc_empleado;
GO
GRANT CONNECT TO gnc_empleado;
GO
IF DATABASE_PRINCIPAL_ID(N'rolCliente') IS NULL
    CREATE ROLE rolCliente AUTHORIZATION dbo;
GO
IF IS_ROLEMEMBER(N'rolCliente', N'gnc_cliente') <> 1
    ALTER ROLE rolCliente ADD MEMBER gnc_cliente;
GO
GRANT CONNECT TO gnc_cliente;
GO
GRANT SELECT, INSERT, UPDATE, DELETE ON SCHEMA::dbo TO rolAdmin;
GRANT EXECUTE ON SCHEMA::dbo TO rolAdmin;
GO
GRANT SELECT ON dbo.tbl_categories TO rolEmpleado;
GRANT SELECT ON dbo.tbl_products TO rolEmpleado;
GRANT SELECT ON dbo.tbl_paymentMethods TO rolEmpleado;
GRANT SELECT ON dbo.tbl_suppliers TO rolEmpleado;
GRANT SELECT ON dbo.tbl_sales TO rolEmpleado;
GRANT SELECT ON dbo.tbl_saleDetails TO rolEmpleado;
GRANT SELECT ON dbo.tbl_purchases TO rolEmpleado;
GRANT SELECT ON dbo.tbl_purchaseDetails TO rolEmpleado;
GRANT SELECT ON dbo.tbl_users (id, name) TO rolEmpleado;
GO
DENY INSERT, UPDATE, DELETE ON dbo.tbl_products TO rolEmpleado;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_categories TO rolEmpleado;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_suppliers TO rolEmpleado;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_paymentMethods TO rolEmpleado;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_users TO rolEmpleado;
GO
GRANT SELECT ON dbo.tbl_categories TO rolCliente;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_categories TO rolCliente;
GRANT SELECT ON dbo.tbl_products TO rolCliente;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_products TO rolCliente;
GRANT SELECT ON dbo.tbl_paymentMethods TO rolCliente;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_paymentMethods TO rolCliente;
DENY SELECT, INSERT, UPDATE, DELETE ON dbo.tbl_suppliers TO rolCliente;
DENY SELECT, INSERT, UPDATE, DELETE ON dbo.tbl_purchases TO rolCliente;
DENY SELECT, INSERT, UPDATE, DELETE ON dbo.tbl_purchaseDetails TO rolCliente;
GO
/* Perfil vinculado al login SQL: necesario ANTES de las vistas nuevas. */
IF COL_LENGTH('dbo.tbl_users', 'sqlLogin') IS NULL
BEGIN
    ALTER TABLE dbo.tbl_users ADD sqlLogin NVARCHAR(128) NULL;
END;
GO
IF NOT EXISTS (
    SELECT 1 FROM sys.indexes
    WHERE name = N'UX_tbl_users_sqlLogin'
      AND object_id = OBJECT_ID(N'dbo.tbl_users')
)
BEGIN
    CREATE UNIQUE INDEX UX_tbl_users_sqlLogin
    ON dbo.tbl_users(sqlLogin) WHERE sqlLogin IS NOT NULL;
END;
GO
/* La contraseña real la valida SQL Server con el login; tbl_users no
   guarda contraseñas de los usuarios con login SQL. */
IF NOT EXISTS (SELECT 1 FROM dbo.tbl_users WHERE sqlLogin = N'gnc_admin')
BEGIN
    INSERT INTO dbo.tbl_users (name, email, password, sqlLogin)
    VALUES ('Administrador', 'admin@gnc.com', 'AUTENTICACION_SQL_SERVER', N'gnc_admin');
END;
GO
IF NOT EXISTS (SELECT 1 FROM dbo.tbl_users WHERE sqlLogin = N'gnc_empleado')
BEGIN
    INSERT INTO dbo.tbl_users (name, email, password, sqlLogin)
    VALUES ('Empleado', 'empleado@gnc.com', 'AUTENTICACION_SQL_SERVER', N'gnc_empleado');
END;
GO
IF NOT EXISTS (SELECT 1 FROM dbo.tbl_users WHERE sqlLogin = N'gnc_cliente')
BEGIN
    INSERT INTO dbo.tbl_users (name, email, password, sqlLogin)
    VALUES ('Cliente', 'cliente@gnc.com', 'AUTENTICACION_SQL_SERVER', N'gnc_cliente');
END;
GO
-- Limpia contraseñas en texto plano que hayan quedado de versiones
-- anteriores. Se conservan los hashes bcrypt ($2y$...).
UPDATE dbo.tbl_users
SET password = CASE WHEN sqlLogin IS NULL THEN 'SIN_ACCESO'
                    ELSE 'AUTENTICACION_SQL_SERVER' END
WHERE password NOT LIKE '$2y$%'
  AND password NOT IN ('AUTENTICACION_SQL_SERVER', 'SIN_ACCESO');
GO
CREATE OR ALTER VIEW dbo.vw_appIdentity
AS
SELECT id, name, email, sqlLogin
FROM dbo.tbl_users
WHERE sqlLogin = ORIGINAL_LOGIN();
GO
GRANT SELECT ON dbo.vw_appIdentity TO rolAdmin;
GRANT SELECT ON dbo.vw_appIdentity TO rolEmpleado;
GRANT SELECT ON dbo.vw_appIdentity TO rolCliente;
GO

/* ============================================================
   COMPLEMENTO: USUARIOS, REGISTRO Y COMPRAS DE CLIENTES

   ============================================================ */

USE gncproyecto;
GO

/* ============================================================
   VISTAS: CADA CLIENTE CONSULTA SUS PROPIAS COMPRAS
   ============================================================ */

CREATE OR ALTER VIEW dbo.v_misCompras
AS
SELECT
    s.id,
    s.sale_date,
    s.total,
    pm.name AS payment_method
FROM dbo.tbl_sales AS s
INNER JOIN dbo.tbl_users AS u
    ON u.id = s.user_id
INNER JOIN dbo.tbl_paymentMethods AS pm
    ON pm.id = s.payment_method_id
WHERE u.sqlLogin = ORIGINAL_LOGIN();
GO

CREATE OR ALTER VIEW dbo.v_misCompraDetalles
AS
SELECT
    s.id AS sale_id,
    p.name AS product_name,
    d.quantity,
    d.subtotal
FROM dbo.tbl_sales AS s
INNER JOIN dbo.tbl_users AS u
    ON u.id = s.user_id
INNER JOIN dbo.tbl_saleDetails AS d
    ON d.sale_id = s.id
INNER JOIN dbo.tbl_products AS p
    ON p.id = d.product_id
WHERE u.sqlLogin = ORIGINAL_LOGIN();
GO

GRANT SELECT ON dbo.v_misCompras TO rolCliente;
GRANT SELECT ON dbo.v_misCompraDetalles TO rolCliente;

GRANT SELECT ON dbo.v_historialVentas TO rolEmpleado;
GRANT SELECT ON dbo.v_historialCompras TO rolEmpleado;
GO

/* ============================================================
   REGISTRAR VENTA / COMPRA
   ============================================================ */

CREATE OR ALTER PROCEDURE dbo.pa_finalizarVenta
    @items NVARCHAR(MAX),
    @paymentMethodId INT,
    @token UNIQUEIDENTIFIER
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    IF COALESCE(IS_ROLEMEMBER('rolAdmin'), 0) <> 1
       AND COALESCE(IS_ROLEMEMBER('rolEmpleado'), 0) <> 1
       AND COALESCE(IS_ROLEMEMBER('rolCliente'), 0) <> 1
        THROW 51100, 'Acceso denegado.', 1;

    DECLARE @userId INT = (
        SELECT id
        FROM dbo.tbl_users
        WHERE sqlLogin = ORIGINAL_LOGIN()
    );

    IF @userId IS NULL OR @token IS NULL
        THROW 51101, 'Cuenta o solicitud inválida.', 1;

    IF ISJSON(@items) <> 1
       OR LEFT(LTRIM(@items), 1) <> '['
        THROW 51102, 'Carrito inválido.', 1;

    IF (SELECT COUNT(*) FROM OPENJSON(@items))
       NOT BETWEEN 1 AND 100
        THROW 51102, 'Carrito inválido.', 1;

    IF EXISTS (
        SELECT 1
        FROM OPENJSON(@items)
        WITH (
            product_id INT,
            quantity INT
        )
        WHERE product_id IS NULL
           OR quantity IS NULL
           OR quantity NOT BETWEEN 1 AND 100000
    )
        THROW 51102, 'Producto o cantidad inválida.', 1;

    IF EXISTS (
        SELECT product_id
        FROM OPENJSON(@items)
        WITH (
            product_id INT,
            quantity INT
        )
        GROUP BY product_id
        HAVING COUNT(*) > 1
    )
        THROW 51102, 'Productos repetidos en el carrito.', 1;

    DECLARE @cart TABLE (
        product_id INT PRIMARY KEY,
        quantity INT,
        price DECIMAL(10,2)
    );

    INSERT INTO @cart (product_id, quantity)
    SELECT product_id, quantity
    FROM OPENJSON(@items)
    WITH (
        product_id INT,
        quantity INT
    );

    BEGIN TRY
        BEGIN TRANSACTION;

        DECLARE @lockResult INT;
        DECLARE @resource NVARCHAR(255) =
            N'gnc_checkout_' + CONVERT(NVARCHAR(36), @token);

        EXEC @lockResult = sys.sp_getapplock
            @Resource = @resource,
            @LockMode = 'Exclusive',
            @LockOwner = 'Transaction',
            @LockTimeout = 10000;

        IF @lockResult < 0
            THROW 51103, 'Solicitud ocupada. Inténtalo nuevamente.', 1;

        IF NOT EXISTS (
            SELECT 1
            FROM dbo.tbl_paymentMethods
            WHERE id = @paymentMethodId
        )
            THROW 51104, 'Método de pago inválido.', 1;

        UPDATE c
        SET c.price = p.price
        FROM @cart AS c
        INNER JOIN dbo.tbl_products AS p WITH (UPDLOCK, HOLDLOCK)
            ON p.id = c.product_id;

        IF EXISTS (
            SELECT 1 FROM @cart WHERE price IS NULL
        )
            THROW 51105, 'Un producto ya no está disponible.', 1;

        IF EXISTS (
            SELECT 1
            FROM @cart AS c
            INNER JOIN dbo.tbl_products AS p
                ON p.id = c.product_id
            WHERE p.stock < c.quantity
        )
            THROW 51106, 'Stock insuficiente.', 1;

        DECLARE @total DECIMAL(18,2) = (
            SELECT SUM(CONVERT(DECIMAL(18,2), price) * quantity)
            FROM @cart
        );

        IF @total > 99999999.99
            THROW 51107, 'El total supera el límite permitido.', 1;

        INSERT INTO dbo.tbl_sales (
            user_id,
            payment_method_id,
            total,
            sale_date
        )
        VALUES (
            @userId,
            @paymentMethodId,
            @total,
            GETDATE()
        );

        DECLARE @saleId INT = CONVERT(INT, SCOPE_IDENTITY());

        INSERT INTO dbo.tbl_saleDetails (
            sale_id,
            product_id,
            quantity,
            subtotal
        )
        SELECT
            @saleId,
            product_id,
            quantity,
            price * quantity
        FROM @cart;

        COMMIT TRANSACTION;

        SELECT @saleId AS sale_id;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;

        THROW;
    END CATCH;
END;
GO

GRANT EXECUTE ON dbo.pa_finalizarVenta
TO rolAdmin, rolEmpleado, rolCliente;
GO

/* ============================================================
   CERTIFICADO PARA LOS PROCEDIMIENTOS DE CUENTAS

   ============================================================ */

/* La clave privada del certificado se protege con la master key de
   la base, que SQL Server abre automáticamente con la clave de servicio
   de la instancia. Así se puede firmar sin escribir ninguna contraseña
   en este script. */

-- 1. Master key de la base, con una contraseña aleatoria que nunca se
--    vuelve a necesitar.
IF NOT EXISTS (
    SELECT 1 FROM sys.symmetric_keys
    WHERE name = N'##MS_DatabaseMasterKey##'
)
BEGIN
    DECLARE @masterKeyPassword NVARCHAR(128) =
        N'Gnc!' + CONVERT(NVARCHAR(64), CRYPT_GEN_RANDOM(32), 2) + N'a';

    EXEC (N'CREATE MASTER KEY ENCRYPTION BY PASSWORD = N'''
        + @masterKeyPassword + N''';');
END;
GO

-- 2. Las versiones anteriores protegían el certificado con una
--    contraseña escrita en el script. Si existe uno así, se reemplaza:
--    se quitan sus firmas, su usuario y su copia en master, y en el
--    paso 3 se crea uno nuevo. Más abajo se vuelve a firmar todo.
IF EXISTS (
    SELECT 1 FROM sys.certificates
    WHERE name = N'certGncAccounts'
      AND pvt_key_encryption_type = 'PW'
)
BEGIN
    DECLARE @dropSignatures NVARCHAR(MAX) = N'';

    SELECT @dropSignatures += N'DROP SIGNATURE FROM '
        + QUOTENAME(OBJECT_SCHEMA_NAME(cp.major_id)) + N'.'
        + QUOTENAME(OBJECT_NAME(cp.major_id))
        + N' BY CERTIFICATE certGncAccounts;'
    FROM sys.crypt_properties AS cp
    INNER JOIN sys.certificates AS c
        ON c.thumbprint = cp.thumbprint
    WHERE c.name = N'certGncAccounts'
      AND cp.class = 1;

    EXEC (@dropSignatures);

    IF USER_ID(N'userGncAccountsCertificate') IS NOT NULL
        DROP USER userGncAccountsCertificate;

    DROP CERTIFICATE certGncAccounts;

    EXEC (N'USE master;
        IF SUSER_ID(N''loginGncAccountsCertificate'') IS NOT NULL
            DROP LOGIN loginGncAccountsCertificate;
        IF CERT_ID(N''certGncAccounts'') IS NOT NULL
            DROP CERTIFICATE certGncAccounts;');
END;
GO

-- 3. Certificado nuevo, protegido por la master key.
IF CERT_ID('certGncAccounts') IS NULL
BEGIN
    CREATE CERTIFICATE certGncAccounts
    WITH SUBJECT = 'Procedimientos de cuentas GNC';
END;
GO

IF USER_ID('userGncAccountsCertificate') IS NULL
    CREATE USER userGncAccountsCertificate
    FROM CERTIFICATE certGncAccounts;
GO

GRANT ALTER ANY USER TO userGncAccountsCertificate;
GRANT VIEW DEFINITION TO userGncAccountsCertificate;

GRANT ALTER ON ROLE::rolAdmin
TO userGncAccountsCertificate;

GRANT ALTER ON ROLE::rolEmpleado
TO userGncAccountsCertificate;

GRANT ALTER ON ROLE::rolCliente
TO userGncAccountsCertificate;
GO

DECLARE @certificate VARBINARY(MAX) =
    CERTENCODED(CERT_ID('certGncAccounts'));
DECLARE @thumbprint VARBINARY(32) = (
    SELECT thumbprint FROM sys.certificates WHERE name = N'certGncAccounts'
);

-- La copia en master (solo la clave pública) debe ser la misma que la
-- de la base. Si quedó una copia de otro certificado (por ejemplo, si
-- la base se borró y se volvió a crear), se reemplaza.
DECLARE @sql NVARCHAR(MAX) =
    N'USE master;

    IF EXISTS (
        SELECT 1 FROM sys.certificates
        WHERE name = N''certGncAccounts''
          AND thumbprint <> '
        + CONVERT(NVARCHAR(MAX), @thumbprint, 1) + N'
    )
    BEGIN
        IF SUSER_ID(N''loginGncAccountsCertificate'') IS NOT NULL
            DROP LOGIN loginGncAccountsCertificate;
        DROP CERTIFICATE certGncAccounts;
    END;

    IF CERT_ID(''certGncAccounts'') IS NULL
        CREATE CERTIFICATE certGncAccounts
        FROM BINARY='
        + CONVERT(NVARCHAR(MAX), @certificate, 1) + N';

    IF SUSER_ID(''loginGncAccountsCertificate'') IS NULL
        CREATE LOGIN loginGncAccountsCertificate
        FROM CERTIFICATE certGncAccounts;

    GRANT ALTER ANY LOGIN TO loginGncAccountsCertificate;
    GRANT VIEW ANY DEFINITION TO loginGncAccountsCertificate;';

EXEC (@sql);
GO

/* ============================================================
   VALIDAR SI EL LOGIN SIGUE ACTIVO
   ============================================================ */

CREATE OR ALTER PROCEDURE dbo.pa_validarCuenta
AS
BEGIN
    SET NOCOUNT ON;

    SELECT
        CONVERT(
            INT,
            CASE WHEN is_disabled = 0 THEN 1 ELSE 0 END
        ) AS active
    FROM sys.sql_logins
    WHERE name = ORIGINAL_LOGIN();
END;
GO

ADD SIGNATURE TO OBJECT::dbo.pa_validarCuenta
BY CERTIFICATE certGncAccounts;
GO

GRANT EXECUTE ON dbo.pa_validarCuenta
TO rolAdmin, rolEmpleado, rolCliente;
GO

/* ============================================================
   ADMIN: LISTAR USUARIOS
   ============================================================ */

CREATE OR ALTER PROCEDURE dbo.pa_listarUsuarios
AS
BEGIN
    SET NOCOUNT ON;

    IF COALESCE(IS_ROLEMEMBER('rolAdmin'), 0) <> 1
        THROW 51200, 'Solo el administrador puede gestionar usuarios.', 1;

    SELECT
        u.id,
        u.name,
        u.email,
        u.sqlLogin,
        CONVERT(
            INT,
            CASE WHEN l.is_disabled = 0 THEN 1 ELSE 0 END
        ) AS active,
        CASE
            WHEN IS_ROLEMEMBER('rolAdmin', u.sqlLogin) = 1
                THEN 'ADMIN'
            WHEN IS_ROLEMEMBER('rolEmpleado', u.sqlLogin) = 1
                THEN 'EMPLEADO'
            WHEN IS_ROLEMEMBER('rolCliente', u.sqlLogin) = 1
                THEN 'CLIENTE'
            ELSE 'SIN ROL'
        END AS role
    FROM dbo.tbl_users AS u
    INNER JOIN sys.sql_logins AS l
        ON l.name = u.sqlLogin
    ORDER BY u.name, u.id;
END;
GO

/* ============================================================
   ADMIN: CAMBIAR ROL Y ACTIVAR / DESACTIVAR
   ============================================================ */

CREATE OR ALTER PROCEDURE dbo.pa_administrarUsuario
    @id INT,
    @role VARCHAR(10),
    @active BIT
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    IF COALESCE(IS_ROLEMEMBER('rolAdmin'), 0) <> 1
        THROW 51200, 'Solo el administrador puede gestionar usuarios.', 1;

    IF @role IS NULL
       OR @role NOT IN ('ADMIN', 'EMPLEADO', 'CLIENTE')
       OR @active IS NULL
        THROW 51201, 'Rol o estado inválido.', 1;

    BEGIN TRY
        BEGIN TRANSACTION;

        DECLARE @result INT;

        EXEC @result = sys.sp_getapplock
            @Resource = 'gnc_manage_accounts',
            @LockMode = 'Exclusive',
            @LockOwner = 'Transaction',
            @LockTimeout = 10000;

        IF @result < 0
            THROW 51202, 'Otro administrador está actualizando cuentas.', 1;

        DECLARE @login SYSNAME = (
            SELECT sqlLogin FROM dbo.tbl_users WHERE id = @id
        );

        IF @login IS NULL
           OR NOT EXISTS (
               SELECT 1 FROM sys.sql_logins WHERE name = @login
           )
            THROW 51203, 'Cuenta no encontrada.', 1;

        IF @login NOT IN (
            'gnc_admin', 'gnc_empleado', 'gnc_cliente'
        )
           AND LEFT(@login, 8) <> 'cliente_'
            THROW 51204, 'Esta cuenta no se administra desde la aplicación.', 1;

        IF IS_SRVROLEMEMBER('sysadmin', @login) = 1
           OR IS_SRVROLEMEMBER('securityadmin', @login) = 1
            THROW 51204, 'Cuenta con privilegios especiales.', 1;

        IF (@role <> 'ADMIN' OR @active = 0)
           AND IS_ROLEMEMBER('rolAdmin', @login) = 1
           AND EXISTS (
               SELECT 1
               FROM sys.sql_logins
               WHERE name = @login AND is_disabled = 0
           )
           AND (
               SELECT COUNT(*)
               FROM sys.database_role_members AS m
               INNER JOIN sys.database_principals AS r
                   ON r.principal_id = m.role_principal_id
               INNER JOIN sys.database_principals AS u
                   ON u.principal_id = m.member_principal_id
               INNER JOIN sys.sql_logins AS l
                   ON l.sid = u.sid
               WHERE r.name = 'rolAdmin'
                 AND l.is_disabled = 0
           ) <= 1
            THROW 51205, 'Debe quedar al menos un administrador activo.', 1;

        DECLARE @commands NVARCHAR(MAX) = N'';

        IF IS_ROLEMEMBER('rolAdmin', @login) = 1
            SET @commands +=
                N'ALTER ROLE rolAdmin DROP MEMBER '
                + QUOTENAME(@login) + N';';

        IF IS_ROLEMEMBER('rolEmpleado', @login) = 1
            SET @commands +=
                N'ALTER ROLE rolEmpleado DROP MEMBER '
                + QUOTENAME(@login) + N';';

        IF IS_ROLEMEMBER('rolCliente', @login) = 1
            SET @commands +=
                N'ALTER ROLE rolCliente DROP MEMBER '
                + QUOTENAME(@login) + N';';

        DECLARE @databaseRole SYSNAME =
            CASE @role
                WHEN 'ADMIN' THEN 'rolAdmin'
                WHEN 'EMPLEADO' THEN 'rolEmpleado'
                ELSE 'rolCliente'
            END;

        SET @commands +=
            N'ALTER ROLE ' + QUOTENAME(@databaseRole)
            + N' ADD MEMBER ' + QUOTENAME(@login) + N';';

        SET @commands +=
            N'ALTER LOGIN ' + QUOTENAME(@login)
            + CASE
                WHEN @active = 1 THEN N' ENABLE;'
                ELSE N' DISABLE;'
              END;

        EXEC (@commands);

        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;

        THROW;
    END CATCH;
END;
GO

ADD SIGNATURE TO OBJECT::dbo.pa_listarUsuarios
BY CERTIFICATE certGncAccounts;
GO

ADD SIGNATURE TO OBJECT::dbo.pa_administrarUsuario
BY CERTIFICATE certGncAccounts;
GO

GRANT EXECUTE ON dbo.pa_listarUsuarios TO rolAdmin;
GRANT EXECUTE ON dbo.pa_administrarUsuario TO rolAdmin;
GO

/* ============================================================
   REGISTRO: CUENTA TÉCNICA

   Solo se utiliza desde PHP para ejecutar el registro.
   No se utiliza para entrar al sistema.
   ============================================================ */

USE master;
GO

/* Igual que los demás logins: se crea con una contraseña aleatoria
   que se muestra una sola vez en Messages. La aplicación la lee de
   config/database.local.php ($registrationPassword) o de la variable
   de entorno DB_REGISTRATION_PASSWORD. Si el login ya existe, su
   contraseña no se cambia. */
IF SUSER_ID(N'gnc_registro') IS NULL
BEGIN
    DECLARE @registrationPassword NVARCHAR(128) =
        N'Gnc!' + CONVERT(NVARCHAR(64), CRYPT_GEN_RANDOM(16), 2) + N'a';

    EXEC (N'CREATE LOGIN gnc_registro WITH PASSWORD = N'''
        + @registrationPassword + N''''
        + N', CHECK_POLICY = ON, DEFAULT_DATABASE = gncproyecto;');

    PRINT N'Login gnc_registro creado. Contraseña: ' + @registrationPassword
        + N'  (ponla en $registrationPassword de database.local.php'
        + N' o en DB_REGISTRATION_PASSWORD; no se vuelve a mostrar)';
END
ELSE
BEGIN
    ALTER LOGIN gnc_registro ENABLE;
END;
GO
USE gncproyecto;
GO
IF USER_ID(N'gnc_registro') IS NULL
BEGIN
    CREATE USER gnc_registro FOR LOGIN gnc_registro;
END
ELSE
BEGIN
    ALTER USER gnc_registro WITH LOGIN = gnc_registro;
END;
GO
GRANT CONNECT TO gnc_registro;
GO

/* ============================================================
   REGISTRAR CUENTA SIEMPRE COMO CLIENTE
   ============================================================ */

CREATE OR ALTER PROCEDURE dbo.pa_registrarCliente
    @username NVARCHAR(64),
    @name NVARCHAR(200),
    @email NVARCHAR(200),
    @password NVARCHAR(256)
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    IF ORIGINAL_LOGIN() <> 'gnc_registro'
        THROW 51300, 'Registro no autorizado.', 1;

    SET @username = LOWER(LTRIM(RTRIM(@username)));
    SET @email = LOWER(LTRIM(RTRIM(@email)));
    SET @name = LTRIM(RTRIM(@name));

    IF @username IS NULL
       OR LEN(@username) NOT BETWEEN 4 AND 30
       OR @username COLLATE Latin1_General_100_BIN2
          LIKE '%[^a-z0-9_]%'
       OR LEFT(@username, 1) COLLATE Latin1_General_100_BIN2
          NOT LIKE '[a-z]'
       OR @name IS NULL
       OR LEN(@name) NOT BETWEEN 1 AND 100
       OR @email IS NULL
       OR LEN(@email) NOT BETWEEN 3 AND 100
       OR @email NOT LIKE '%_@_%._%'
        THROW 51301, 'Datos de registro inválidos.', 1;

    IF @password IS NULL
       OR DATALENGTH(@password) / 2 NOT BETWEEN 12 AND 128
       OR @password COLLATE Latin1_General_100_BIN2
          NOT LIKE '%[A-Z]%'
       OR @password COLLATE Latin1_General_100_BIN2
          NOT LIKE '%[a-z]%'
       OR @password NOT LIKE '%[0-9]%'
       OR @password COLLATE Latin1_General_100_BIN2
          NOT LIKE '%[^a-zA-Z0-9]%'
        THROW 51301, 'La contraseña no cumple los requisitos.', 1;

    DECLARE @login SYSNAME = N'cliente_' + @username;

    BEGIN TRY
        BEGIN TRANSACTION;

        IF EXISTS (
            SELECT 1
            FROM dbo.tbl_users WITH (UPDLOCK, HOLDLOCK)
            WHERE email = @email OR sqlLogin = @login
        )
           OR SUSER_ID(@login) IS NOT NULL
            THROW 51302, 'Usuario o correo ya registrado.', 1;

        DECLARE @commands NVARCHAR(MAX) =
            N'CREATE LOGIN ' + QUOTENAME(@login)
            + N' WITH PASSWORD=N'''
            + REPLACE(@password, '''', '''''')
            + N''', CHECK_POLICY=ON, DEFAULT_DATABASE='
            + QUOTENAME(DB_NAME()) + N';'
            + N'CREATE USER ' + QUOTENAME(@login)
            + N' FOR LOGIN ' + QUOTENAME(@login) + N';'
            + N'ALTER ROLE rolCliente ADD MEMBER '
            + QUOTENAME(@login) + N';';

        EXEC (@commands);

        INSERT INTO dbo.tbl_users (
            name, email, password, sqlLogin
        )
        VALUES (
            @name,
            @email,
            'AUTENTICACION_SQL_SERVER',
            @login
        );

        COMMIT TRANSACTION;

        SELECT @login AS sqlLogin;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;

        THROW;
    END CATCH;
END;
GO

ADD SIGNATURE TO OBJECT::dbo.pa_registrarCliente
BY CERTIFICATE certGncAccounts;
GO

GRANT EXECUTE ON dbo.pa_registrarCliente TO gnc_registro;
GO

/* Permisos para los pedidos operativos del empleado. */

GRANT INSERT, UPDATE ON dbo.tbl_purchases TO rolEmpleado;
GRANT INSERT, UPDATE ON dbo.tbl_purchaseDetails TO rolEmpleado;
GO

/* ============================================================
   COMPROBACIONES FINALES
   ============================================================ */
USE gncproyecto;
GO
SELECT r.name AS rol, u.name AS usuario
FROM sys.database_role_members AS m
INNER JOIN sys.database_principals AS r
    ON r.principal_id = m.role_principal_id
INNER JOIN sys.database_principals AS u
    ON u.principal_id = m.member_principal_id
WHERE r.name IN ('rolAdmin', 'rolEmpleado', 'rolCliente');
GO
SELECT id, name, email, sqlLogin
FROM dbo.tbl_users
WHERE sqlLogin IS NOT NULL;
GO
SELECT name, is_disabled, is_policy_checked
FROM sys.sql_logins
WHERE name IN ('gnc_admin', 'gnc_empleado', 'gnc_cliente', 'gnc_registro');
GO
SELECT name, compatibility_level
FROM sys.databases WHERE name = DB_NAME();
GO

/* ============================================================
   AUDITORÍA DE LA BASE DE DATOS

   Objetivo:
   Registrar quién, cuándo y qué cambió en la información crítica
   del negocio (inventario, ventas y acceso al sistema), sin
   auditar indiscriminadamente todas las tablas.

   Alcance:
   - tbl_products     INSERT, UPDATE, DELETE  -> tbl_auditProducts
   - tbl_sales        INSERT, UPDATE, DELETE  -> tbl_auditSales
   - tbl_saleDetails  INSERT, UPDATE, DELETE  -> tbl_auditSales
   - Inicio, cierre y expiración de sesión     -> tbl_auditSessions

   Identificación del usuario:
   Cada usuario de la aplicación se conecta a SQL Server con su
   propio login (gnc_admin, gnc_empleado, cliente_xxx), así que
   ORIGINAL_LOGIN() devuelve al usuario real y se busca su id en
   tbl_users.sqlLogin. Si el cambio se hace con un login que no
   pertenece a la aplicación (por ejemplo, desde SSMS con Windows
   Authentication), se usa el valor 'user_id' que PHP guarda con
   sp_set_session_context (config/database.php). En todos los
   casos se conserva también el nombre del login.

   Inicio y cierre de sesión:
   No modifican ninguna tabla, por lo que un trigger de datos no
   los detecta. La aplicación llama al procedimiento
   dbo.pa_registrarSesion al iniciar sesión, al cerrarla y cuando
   la sesión expira. Los intentos fallidos no llegan a abrir una
   conexión; SQL Server ya los registra en su propio log de
   errores (auditoría de inicio de sesión de la instancia).
   ============================================================ */

USE gncproyecto;
GO

-- La tabla anterior (tbl_auditLogs) solo guardaba la acción y el
-- nombre del producto; se reemplaza por tbl_auditProducts.
DROP TABLE IF EXISTS dbo.tbl_auditLogs;
GO


/*================ TABLAS DE AUDITORÍA ================*/

/* ------------------------------------------------------------
   AUD1: tbl_auditProducts
   Propósito: historial de cambios del inventario. Guarda el
   valor anterior y el nuevo de nombre, categoría, precio y stock.
   Llave primaria: id (IDENTITY).
   Recibe datos de: dis_auditProducts.
   No tiene llaves foráneas a propósito: el historial debe
   conservarse aunque el producto o el usuario se eliminen.
   ------------------------------------------------------------ */
DROP TABLE IF EXISTS dbo.tbl_auditProducts;
GO
CREATE TABLE dbo.tbl_auditProducts (
    id               INT IDENTITY(1,1) NOT NULL,
    operation        VARCHAR(6)    NOT NULL,
    product_id       INT           NOT NULL,
    old_name         VARCHAR(100)  NULL,
    new_name         VARCHAR(100)  NULL,
    old_category_id  INT           NULL,
    new_category_id  INT           NULL,
    old_price        DECIMAL(10,2) NULL,
    new_price        DECIMAL(10,2) NULL,
    old_stock        INT           NULL,
    new_stock        INT           NULL,
    user_id          INT           NULL,
    login_name       NVARCHAR(128) NOT NULL
        CONSTRAINT DF_audit_products_login DEFAULT ORIGINAL_LOGIN(),
    action_date      DATETIME2(0)  NOT NULL
        CONSTRAINT DF_audit_products_date DEFAULT SYSDATETIME(),
    CONSTRAINT PK_audit_products PRIMARY KEY (id),
    CONSTRAINT CK_audit_products_operation
        CHECK (operation IN ('INSERT', 'UPDATE', 'DELETE'))
);
GO

/* ------------------------------------------------------------
   AUD2: tbl_auditSales
   Propósito: historial de ventas y de sus detalles. Como las dos
   tablas tienen columnas distintas, los valores anteriores y
   nuevos se guardan en formato JSON.
   Llave primaria: id (IDENTITY).
   Recibe datos de: dis_auditSales y dis_auditSaleDetails.
   ------------------------------------------------------------ */
DROP TABLE IF EXISTS dbo.tbl_auditSales;
GO
CREATE TABLE dbo.tbl_auditSales (
    id           INT IDENTITY(1,1) NOT NULL,
    table_name   VARCHAR(30)    NOT NULL,
    operation    VARCHAR(6)     NOT NULL,
    record_id    INT            NOT NULL,
    sale_id      INT            NOT NULL,
    old_values   NVARCHAR(MAX)  NULL,
    new_values   NVARCHAR(MAX)  NULL,
    user_id      INT            NULL,
    login_name   NVARCHAR(128)  NOT NULL
        CONSTRAINT DF_audit_sales_login DEFAULT ORIGINAL_LOGIN(),
    action_date  DATETIME2(0)   NOT NULL
        CONSTRAINT DF_audit_sales_date DEFAULT SYSDATETIME(),
    CONSTRAINT PK_audit_sales PRIMARY KEY (id),
    CONSTRAINT CK_audit_sales_table
        CHECK (table_name IN ('tbl_sales', 'tbl_saleDetails')),
    CONSTRAINT CK_audit_sales_operation
        CHECK (operation IN ('INSERT', 'UPDATE', 'DELETE')),
    CONSTRAINT CK_audit_sales_old_json
        CHECK (old_values IS NULL OR ISJSON(old_values) = 1),
    CONSTRAINT CK_audit_sales_new_json
        CHECK (new_values IS NULL OR ISJSON(new_values) = 1)
);
GO

/* ------------------------------------------------------------
   AUD3: tbl_auditSessions
   Propósito: registrar cuándo cada usuario entra y sale del
   sistema, y desde qué IP.
   Llave primaria: id (IDENTITY).
   Recibe datos de: pa_registrarSesion, llamado desde PHP.
   ------------------------------------------------------------ */
DROP TABLE IF EXISTS dbo.tbl_auditSessions;
GO
CREATE TABLE dbo.tbl_auditSessions (
    id          INT IDENTITY(1,1) NOT NULL,
    event       VARCHAR(10)    NOT NULL,
    user_id     INT            NULL,
    login_name  NVARCHAR(128)  NOT NULL
        CONSTRAINT DF_audit_sessions_login DEFAULT ORIGINAL_LOGIN(),
    client_ip   VARCHAR(45)    NULL,
    event_date  DATETIME2(0)   NOT NULL
        CONSTRAINT DF_audit_sessions_date DEFAULT SYSDATETIME(),
    CONSTRAINT PK_audit_sessions PRIMARY KEY (id),
    CONSTRAINT CK_audit_sessions_event
        CHECK (event IN ('LOGIN', 'LOGOUT', 'EXPIRADA'))
);
GO


/*================ FUNCIÓN DE APOYO ================*/

/* ------------------------------------------------------------
   fn_auditUserId
   Devuelve el id (tbl_users) del usuario que hace el cambio.
   1. Busca el login de la conexión en tbl_users.sqlLogin.
   2. Si no lo encuentra, usa SESSION_CONTEXT('user_id').
   3. Si tampoco existe, devuelve NULL (el login queda guardado).
   ------------------------------------------------------------ */
CREATE OR ALTER FUNCTION dbo.fn_auditUserId()
RETURNS INT
AS
BEGIN
    DECLARE @userId INT;

    SELECT @userId = id
    FROM dbo.tbl_users
    WHERE sqlLogin = ORIGINAL_LOGIN();

    IF @userId IS NULL
        SET @userId = TRY_CONVERT(INT, SESSION_CONTEXT(N'user_id'));

    RETURN @userId;
END;
GO


/*================ DISPARADORES DE AUDITORÍA ================*/

/* ------------------------------------------------------------
   dis01: dis_auditProducts
   Tabla: tbl_products. Operaciones: INSERT, UPDATE, DELETE.

   Une inserted y deleted con FULL OUTER JOIN por id, así procesa
   todas las filas de la operación en una sola instrucción:
   - Solo en inserted        -> INSERT (no hay valores anteriores)
   - Solo en deleted         -> DELETE (no hay valores nuevos)
   - En las dos tablas       -> UPDATE
   En los UPDATE solo se registra la fila si cambió alguna columna
   auditada; si solo cambió la descripción o la imagen, no se
   registra nada.
   ------------------------------------------------------------ */
CREATE OR ALTER TRIGGER dbo.dis_auditProducts
ON dbo.tbl_products
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM inserted)
       AND NOT EXISTS (SELECT 1 FROM deleted)
        RETURN;

    DECLARE @userId INT = dbo.fn_auditUserId();

    INSERT INTO dbo.tbl_auditProducts (
        operation, product_id,
        old_name, new_name,
        old_category_id, new_category_id,
        old_price, new_price,
        old_stock, new_stock,
        user_id
    )
    SELECT
        CASE
            WHEN d.id IS NULL THEN 'INSERT'
            WHEN i.id IS NULL THEN 'DELETE'
            ELSE 'UPDATE'
        END,
        COALESCE(i.id, d.id),
        d.name, i.name,
        d.category_id, i.category_id,
        d.price, i.price,
        d.stock, i.stock,
        @userId
    FROM inserted AS i
    FULL OUTER JOIN deleted AS d
        ON d.id = i.id
    WHERE i.id IS NULL
       OR d.id IS NULL
       OR EXISTS (
           SELECT i.name, i.category_id, i.price, i.stock
           EXCEPT
           SELECT d.name, d.category_id, d.price, d.stock
       );
END;
GO

/* ------------------------------------------------------------
   dis02: dis_auditSales
   Tabla: tbl_sales. Operaciones: INSERT, UPDATE, DELETE.

   Misma lógica que dis01. Los valores se guardan como JSON con
   FOR JSON PATH. Incluye los UPDATE del total que hace
   dis_recalculateSaleTotal cuando cambian los detalles.
   ------------------------------------------------------------ */
CREATE OR ALTER TRIGGER dbo.dis_auditSales
ON dbo.tbl_sales
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM inserted)
       AND NOT EXISTS (SELECT 1 FROM deleted)
        RETURN;

    DECLARE @userId INT = dbo.fn_auditUserId();

    INSERT INTO dbo.tbl_auditSales (
        table_name, operation, record_id, sale_id,
        old_values, new_values, user_id
    )
    SELECT
        'tbl_sales',
        CASE
            WHEN d.id IS NULL THEN 'INSERT'
            WHEN i.id IS NULL THEN 'DELETE'
            ELSE 'UPDATE'
        END,
        COALESCE(i.id, d.id),
        COALESCE(i.id, d.id),
        CASE WHEN d.id IS NOT NULL THEN (
            SELECT d.user_id, d.payment_method_id, d.total, d.sale_date
            FOR JSON PATH, WITHOUT_ARRAY_WRAPPER, INCLUDE_NULL_VALUES
        ) END,
        CASE WHEN i.id IS NOT NULL THEN (
            SELECT i.user_id, i.payment_method_id, i.total, i.sale_date
            FOR JSON PATH, WITHOUT_ARRAY_WRAPPER, INCLUDE_NULL_VALUES
        ) END,
        @userId
    FROM inserted AS i
    FULL OUTER JOIN deleted AS d
        ON d.id = i.id
    WHERE i.id IS NULL
       OR d.id IS NULL
       OR EXISTS (
           SELECT i.user_id, i.payment_method_id, i.total, i.sale_date
           EXCEPT
           SELECT d.user_id, d.payment_method_id, d.total, d.sale_date
       );
END;
GO

/* ------------------------------------------------------------
   dis03: dis_auditSaleDetails
   Tabla: tbl_saleDetails. Operaciones: INSERT, UPDATE, DELETE.

   Registra qué productos y cantidades se agregan, cambian o
   eliminan de una venta. Escribe en la misma tabla que dis02
   (table_name = 'tbl_saleDetails'), así una venta completa se
   consulta filtrando por sale_id.
   ------------------------------------------------------------ */
CREATE OR ALTER TRIGGER dbo.dis_auditSaleDetails
ON dbo.tbl_saleDetails
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM inserted)
       AND NOT EXISTS (SELECT 1 FROM deleted)
        RETURN;

    DECLARE @userId INT = dbo.fn_auditUserId();

    INSERT INTO dbo.tbl_auditSales (
        table_name, operation, record_id, sale_id,
        old_values, new_values, user_id
    )
    SELECT
        'tbl_saleDetails',
        CASE
            WHEN d.id IS NULL THEN 'INSERT'
            WHEN i.id IS NULL THEN 'DELETE'
            ELSE 'UPDATE'
        END,
        COALESCE(i.id, d.id),
        COALESCE(i.sale_id, d.sale_id),
        CASE WHEN d.id IS NOT NULL THEN (
            SELECT d.sale_id, d.product_id, d.quantity, d.subtotal
            FOR JSON PATH, WITHOUT_ARRAY_WRAPPER, INCLUDE_NULL_VALUES
        ) END,
        CASE WHEN i.id IS NOT NULL THEN (
            SELECT i.sale_id, i.product_id, i.quantity, i.subtotal
            FOR JSON PATH, WITHOUT_ARRAY_WRAPPER, INCLUDE_NULL_VALUES
        ) END,
        @userId
    FROM inserted AS i
    FULL OUTER JOIN deleted AS d
        ON d.id = i.id
    WHERE i.id IS NULL
       OR d.id IS NULL
       OR EXISTS (
           SELECT i.sale_id, i.product_id, i.quantity, i.subtotal
           EXCEPT
           SELECT d.sale_id, d.product_id, d.quantity, d.subtotal
       );
END;
GO


/*================ PROCEDIMIENTO DE SESIONES ================*/

/* ------------------------------------------------------------
   pa_registrarSesion
   Lo llama PHP con la conexión del propio usuario:
   - controllers/Logincontroller.php -> UserModel   'LOGIN'
   - views/logout.php                                'LOGOUT'
   - config/auth_check.php (inactividad)             'EXPIRADA'
   El usuario se obtiene de la conexión, no de un parámetro, así
   nadie puede registrar una sesión a nombre de otra persona.
   ------------------------------------------------------------ */
CREATE OR ALTER PROCEDURE dbo.pa_registrarSesion
    @event VARCHAR(10),
    @clientIp VARCHAR(45) = NULL
AS
BEGIN
    SET NOCOUNT ON;

    IF @event IS NULL OR @event NOT IN ('LOGIN', 'LOGOUT', 'EXPIRADA')
        THROW 51400, 'Evento de sesión inválido.', 1;

    INSERT INTO dbo.tbl_auditSessions (event, user_id, client_ip)
    VALUES (@event, dbo.fn_auditUserId(), @clientIp);
END;
GO


/*================ PERMISOS SOBRE LA AUDITORÍA ================*/

/* Nadie puede escribir, modificar ni borrar la auditoría
   directamente, ni siquiera el administrador. Los triggers y
   pa_registrarSesion sí pueden insertar porque pertenecen al
   mismo dueño (dbo) que las tablas (encadenamiento de propiedad).
   Solo el administrador puede consultarla. */
DENY INSERT, UPDATE, DELETE ON dbo.tbl_auditProducts TO rolAdmin, rolEmpleado, rolCliente;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_auditSales    TO rolAdmin, rolEmpleado, rolCliente;
DENY INSERT, UPDATE, DELETE ON dbo.tbl_auditSessions TO rolAdmin, rolEmpleado, rolCliente;

DENY SELECT ON dbo.tbl_auditProducts TO rolEmpleado, rolCliente;
DENY SELECT ON dbo.tbl_auditSales    TO rolEmpleado, rolCliente;
DENY SELECT ON dbo.tbl_auditSessions TO rolEmpleado, rolCliente;

GRANT EXECUTE ON dbo.pa_registrarSesion TO rolAdmin, rolEmpleado, rolCliente;
GO


/* ============================================================
   COMPROBACIÓN DE LA AUDITORÍA

   Pruebas de INSERT, UPDATE y DELETE con varias filas a la vez.
   Al terminar, los datos de prueba se eliminan y solo quedan los
   registros de auditoría como evidencia.

   Desde SSMS la conexión usa Windows Authentication, que no es un
   usuario de la aplicación; por eso se simula al usuario 1
   (2AC Team) con SESSION_CONTEXT, igual que hace la aplicación.
   ============================================================ */

EXEC sys.sp_set_session_context @key = N'user_id', @value = 1;
GO

-- Productos: INSERT de 2 filas, UPDATE de 2 filas, DELETE de 2 filas.
INSERT INTO dbo.tbl_products (category_id, name, description, price, stock)
VALUES
    (2, 'Prueba auditoría A', NULL, 10000.00, 20),
    (2, 'Prueba auditoría B', NULL, 12000.00, 30);

UPDATE dbo.tbl_products
SET price = price + 1000.00
WHERE name LIKE 'Prueba auditoría%';
GO

-- Ventas: una venta con 2 detalles, se modifica y se elimina.
DECLARE @productA INT = (SELECT id FROM dbo.tbl_products WHERE name = 'Prueba auditoría A');
DECLARE @productB INT = (SELECT id FROM dbo.tbl_products WHERE name = 'Prueba auditoría B');
DECLARE @saleId INT;

INSERT INTO dbo.tbl_sales (user_id, payment_method_id, total)
VALUES (1, 1, 0);

SET @saleId = SCOPE_IDENTITY();

INSERT INTO dbo.tbl_saleDetails (sale_id, product_id, quantity, subtotal)
VALUES
    (@saleId, @productA, 2, 22000.00),
    (@saleId, @productB, 1, 13000.00);

UPDATE dbo.tbl_saleDetails
SET quantity = 3, subtotal = 33000.00
WHERE sale_id = @saleId AND product_id = @productA;

UPDATE dbo.tbl_sales
SET payment_method_id = 2
WHERE id = @saleId;

DELETE FROM dbo.tbl_saleDetails WHERE sale_id = @saleId;
DELETE FROM dbo.tbl_sales WHERE id = @saleId;
GO

DELETE FROM dbo.tbl_products
WHERE name LIKE 'Prueba auditoría%';
GO

-- Sesiones: inicio y cierre de sesión.
EXEC dbo.pa_registrarSesion @event = 'LOGIN', @clientIp = '127.0.0.1';
EXEC dbo.pa_registrarSesion @event = 'LOGOUT', @clientIp = '127.0.0.1';
GO

EXEC sys.sp_set_session_context @key = N'user_id', @value = NULL;
GO

-- Evidencia: lo que registró cada tabla de auditoría.
SELECT a.id, a.operation, a.product_id,
       a.old_name, a.new_name,
       a.old_price, a.new_price,
       a.old_stock, a.new_stock,
       u.name AS usuario, a.login_name, a.action_date
FROM dbo.tbl_auditProducts AS a
LEFT JOIN dbo.tbl_users AS u ON u.id = a.user_id
ORDER BY a.id;

SELECT a.id, a.table_name, a.operation, a.record_id, a.sale_id,
       a.old_values, a.new_values,
       u.name AS usuario, a.login_name, a.action_date
FROM dbo.tbl_auditSales AS a
LEFT JOIN dbo.tbl_users AS u ON u.id = a.user_id
ORDER BY a.id;

SELECT a.id, a.event, u.name AS usuario,
       a.login_name, a.client_ip, a.event_date
FROM dbo.tbl_auditSessions AS a
LEFT JOIN dbo.tbl_users AS u ON u.id = a.user_id
ORDER BY a.id;
GO


/* ============================================================
   RESPALDO Y RESTAURACIÓN

   Objetivo:
   Permitir que el administrador haga respaldos completos de
   gncproyecto, compruebe que el archivo no esté dañado y pruebe
   la recuperación restaurándolo en una base aparte
   (gncproyecto_prueba), sin tocar nunca la base real.

   Procedimientos:
   - pa_respaldoCompleto   BACKUP DATABASE con CHECKSUM
   - pa_verificarRespaldo  RESTORE VERIFYONLY
   - pa_restaurarRespaldo  RESTORE en gncproyecto_prueba
   - pa_listarRespaldos    historial para la pantalla

   Archivos:
   Se guardan en la carpeta de respaldos de la instancia
   (SERVERPROPERTY('InstanceDefaultBackupPath')). Los
   procedimientos solo reciben el nombre del archivo, nunca una
   ruta, y solo aceptan archivos creados por pa_respaldoCompleto.

   Permisos:
   Los usuarios de la aplicación no pueden respaldar ni restaurar.
   Los procedimientos se firman con certGncAccounts (el mismo
   certificado de la gestión de cuentas):
   - userGncAccountsCertificate (en gncproyecto) es miembro de
     db_backupoperator, para poder hacer BACKUP DATABASE.
   - loginGncAccountsCertificate (en master) es miembro de
     dbcreator, para poder verificar y restaurar.
   Solo rolAdmin puede ejecutarlos, y la restauración siempre va a
   gncproyecto_prueba porque ese nombre está fijo en el código.
   ============================================================ */

USE gncproyecto;
GO


/*================ TABLA DE HISTORIAL ================*/

/* ------------------------------------------------------------
   tbl_backupHistory
   Registra cada respaldo, verificación y restauración, con su
   resultado. Solo se crea si no existe, para no perder el
   historial al volver a ejecutar esta sección.
   ------------------------------------------------------------ */
IF OBJECT_ID('dbo.tbl_backupHistory', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.tbl_backupHistory (
        id               INT IDENTITY(1,1) NOT NULL,
        operation        VARCHAR(10)     NOT NULL,
        file_name        NVARCHAR(200)   NOT NULL,
        target_database  SYSNAME         NOT NULL,
        status           VARCHAR(8)      NOT NULL,
        message          NVARCHAR(4000)  NULL,
        user_id          INT             NULL,
        login_name       NVARCHAR(128)   NOT NULL
            CONSTRAINT DF_backup_history_login DEFAULT ORIGINAL_LOGIN(),
        action_date      DATETIME2(0)    NOT NULL
            CONSTRAINT DF_backup_history_date DEFAULT SYSDATETIME(),
        CONSTRAINT PK_backup_history PRIMARY KEY (id),
        CONSTRAINT CK_backup_history_operation
            CHECK (operation IN ('RESPALDO', 'VERIFICAR', 'RESTAURAR')),
        CONSTRAINT CK_backup_history_status
            CHECK (status IN ('EXITOSO', 'FALLIDO'))
    );
END;
GO


/*================ FUNCIÓN DE APOYO ================*/

/* ------------------------------------------------------------
   fn_rutaRespaldo
   Devuelve la ruta completa de un archivo dentro de la carpeta de
   respaldos de la instancia.
   ------------------------------------------------------------ */
CREATE OR ALTER FUNCTION dbo.fn_rutaRespaldo (@fileName NVARCHAR(200))
RETURNS NVARCHAR(1000)
AS
BEGIN
    DECLARE @folder NVARCHAR(800) =
        CONVERT(NVARCHAR(800), SERVERPROPERTY('InstanceDefaultBackupPath'));

    IF RIGHT(@folder, 1) <> N'\'
        SET @folder += N'\';

    RETURN @folder + @fileName;
END;
GO


/*================ PROCEDIMIENTOS ================*/

/* ------------------------------------------------------------
   pa_respaldoCompleto
   Hace un respaldo completo de gncproyecto en un archivo nuevo,
   llamado gncproyecto_AAAAMMDD_HHMMSS.bak.
   - CHECKSUM: SQL Server valida cada página al respaldar.
   - COPY_ONLY: no altera la cadena de respaldos de la instancia,
     por si el servidor tiene su propio plan de respaldos.
   Devuelve el nombre del archivo creado.
   ------------------------------------------------------------ */
CREATE OR ALTER PROCEDURE dbo.pa_respaldoCompleto
AS
BEGIN
    SET NOCOUNT ON;

    IF COALESCE(IS_ROLEMEMBER('rolAdmin'), 0) <> 1
       AND IS_SRVROLEMEMBER('sysadmin') <> 1
        THROW 51500, 'Solo el administrador puede hacer respaldos.', 1;

    DECLARE @fileName NVARCHAR(200) =
        N'gncproyecto_' + FORMAT(SYSDATETIME(), 'yyyyMMdd_HHmmss') + N'.bak';
    DECLARE @path NVARCHAR(1000) = dbo.fn_rutaRespaldo(@fileName);

    BEGIN TRY
        BACKUP DATABASE gncproyecto
        TO DISK = @path
        WITH COPY_ONLY, INIT, CHECKSUM,
             NAME = N'gncproyecto - respaldo completo';

        INSERT INTO dbo.tbl_backupHistory
            (operation, file_name, target_database, status, message, user_id)
        VALUES
            ('RESPALDO', @fileName, N'gncproyecto', 'EXITOSO',
             N'Respaldo completo creado.', dbo.fn_auditUserId());

        SELECT @fileName AS file_name;
    END TRY
    BEGIN CATCH
        INSERT INTO dbo.tbl_backupHistory
            (operation, file_name, target_database, status, message, user_id)
        VALUES
            ('RESPALDO', @fileName, N'gncproyecto', 'FALLIDO',
             ERROR_MESSAGE(), dbo.fn_auditUserId());

        THROW;
    END CATCH;
END;
GO

/* ------------------------------------------------------------
   pa_verificarRespaldo
   Comprueba que un respaldo se pueda leer completo y que sus
   CHECKSUM sean correctos, sin restaurarlo.
   Solo acepta archivos que pa_respaldoCompleto creó con éxito.
   ------------------------------------------------------------ */
CREATE OR ALTER PROCEDURE dbo.pa_verificarRespaldo
    @fileName NVARCHAR(200)
AS
BEGIN
    SET NOCOUNT ON;

    IF COALESCE(IS_ROLEMEMBER('rolAdmin'), 0) <> 1
       AND IS_SRVROLEMEMBER('sysadmin') <> 1
        THROW 51500, 'Solo el administrador puede verificar respaldos.', 1;

    IF NOT EXISTS (
        SELECT 1
        FROM dbo.tbl_backupHistory
        WHERE operation = 'RESPALDO'
          AND status = 'EXITOSO'
          AND file_name = @fileName
    )
        THROW 51501, 'El respaldo indicado no existe.', 1;

    DECLARE @path NVARCHAR(1000) = dbo.fn_rutaRespaldo(@fileName);

    BEGIN TRY
        RESTORE VERIFYONLY
        FROM DISK = @path
        WITH CHECKSUM;

        INSERT INTO dbo.tbl_backupHistory
            (operation, file_name, target_database, status, message, user_id)
        VALUES
            ('VERIFICAR', @fileName, N'gncproyecto', 'EXITOSO',
             N'El respaldo es válido.', dbo.fn_auditUserId());

        SELECT N'El respaldo es válido.' AS message;
    END TRY
    BEGIN CATCH
        INSERT INTO dbo.tbl_backupHistory
            (operation, file_name, target_database, status, message, user_id)
        VALUES
            ('VERIFICAR', @fileName, N'gncproyecto', 'FALLIDO',
             ERROR_MESSAGE(), dbo.fn_auditUserId());

        THROW;
    END CATCH;
END;
GO

/* ------------------------------------------------------------
   pa_restaurarRespaldo
   Restaura un respaldo en gncproyecto_prueba para comprobar que
   la recuperación funciona. La base real nunca se modifica: el
   nombre de la base destino está fijo en el código.
   - Si gncproyecto_prueba ya existe, se desconecta a quien la
     esté usando y se reemplaza.
   - Los archivos se crean en las carpetas de datos y de log de la
     instancia (MOVE), para no chocar con los de gncproyecto.
   ------------------------------------------------------------ */
CREATE OR ALTER PROCEDURE dbo.pa_restaurarRespaldo
    @fileName NVARCHAR(200)
AS
BEGIN
    SET NOCOUNT ON;

    IF COALESCE(IS_ROLEMEMBER('rolAdmin'), 0) <> 1
       AND IS_SRVROLEMEMBER('sysadmin') <> 1
        THROW 51500, 'Solo el administrador puede restaurar respaldos.', 1;

    IF NOT EXISTS (
        SELECT 1
        FROM dbo.tbl_backupHistory
        WHERE operation = 'RESPALDO'
          AND status = 'EXITOSO'
          AND file_name = @fileName
    )
        THROW 51501, 'El respaldo indicado no existe.', 1;

    DECLARE @path NVARCHAR(1000) = dbo.fn_rutaRespaldo(@fileName);

    -- Nombres lógicos de los archivos de gncproyecto (son los mismos
    -- que tiene el respaldo).
    DECLARE @dataName SYSNAME = (
        SELECT TOP 1 name FROM sys.database_files WHERE type_desc = 'ROWS'
    );
    DECLARE @logName SYSNAME = (
        SELECT TOP 1 name FROM sys.database_files WHERE type_desc = 'LOG'
    );

    DECLARE @dataFolder NVARCHAR(800) =
        CONVERT(NVARCHAR(800), SERVERPROPERTY('InstanceDefaultDataPath'));
    DECLARE @logFolder NVARCHAR(800) =
        CONVERT(NVARCHAR(800), SERVERPROPERTY('InstanceDefaultLogPath'));

    IF RIGHT(@dataFolder, 1) <> N'\' SET @dataFolder += N'\';
    IF RIGHT(@logFolder, 1) <> N'\' SET @logFolder += N'\';

    DECLARE @dataFile NVARCHAR(1000) = @dataFolder + N'gncproyecto_prueba.mdf';
    DECLARE @logFile NVARCHAR(1000) = @logFolder + N'gncproyecto_prueba_log.ldf';

    BEGIN TRY
        IF DB_ID(N'gncproyecto_prueba') IS NOT NULL
            ALTER DATABASE gncproyecto_prueba
            SET SINGLE_USER WITH ROLLBACK IMMEDIATE;

        RESTORE DATABASE gncproyecto_prueba
        FROM DISK = @path
        WITH MOVE @dataName TO @dataFile,
             MOVE @logName TO @logFile,
             REPLACE, CHECKSUM, RECOVERY;

        IF DATABASEPROPERTYEX(N'gncproyecto_prueba', 'Status') <> 'ONLINE'
            THROW 51502, 'La base de prueba no quedó en línea.', 1;

        INSERT INTO dbo.tbl_backupHistory
            (operation, file_name, target_database, status, message, user_id)
        VALUES
            ('RESTAURAR', @fileName, N'gncproyecto_prueba', 'EXITOSO',
             N'Respaldo restaurado en gncproyecto_prueba.', dbo.fn_auditUserId());

        SELECT N'Respaldo restaurado en gncproyecto_prueba.' AS message;
    END TRY
    BEGIN CATCH
        -- Si la restauración falló, la base de prueba no debe quedar
        -- bloqueada en modo de un solo usuario.
        IF DB_ID(N'gncproyecto_prueba') IS NOT NULL
           AND DATABASEPROPERTYEX(N'gncproyecto_prueba', 'Status') = 'ONLINE'
            ALTER DATABASE gncproyecto_prueba SET MULTI_USER;

        INSERT INTO dbo.tbl_backupHistory
            (operation, file_name, target_database, status, message, user_id)
        VALUES
            ('RESTAURAR', @fileName, N'gncproyecto_prueba', 'FALLIDO',
             ERROR_MESSAGE(), dbo.fn_auditUserId());

        THROW;
    END CATCH;
END;
GO

/* ------------------------------------------------------------
   pa_listarRespaldos
   Historial de respaldos, verificaciones y restauraciones, del
   más reciente al más antiguo.
   ------------------------------------------------------------ */
CREATE OR ALTER PROCEDURE dbo.pa_listarRespaldos
AS
BEGIN
    SET NOCOUNT ON;

    IF COALESCE(IS_ROLEMEMBER('rolAdmin'), 0) <> 1
       AND IS_SRVROLEMEMBER('sysadmin') <> 1
        THROW 51500, 'Solo el administrador puede ver los respaldos.', 1;

    SELECT TOP 100
        h.id,
        h.operation,
        h.file_name,
        h.target_database,
        h.status,
        h.message,
        COALESCE(u.name, h.login_name) AS user_name,
        h.action_date
    FROM dbo.tbl_backupHistory AS h
    LEFT JOIN dbo.tbl_users AS u
        ON u.id = h.user_id
    ORDER BY h.id DESC;
END;
GO


/*================ PERMISOS ================*/

-- El usuario del certificado puede respaldar gncproyecto.
IF IS_ROLEMEMBER(N'db_backupoperator', N'userGncAccountsCertificate') <> 1
    ALTER ROLE db_backupoperator ADD MEMBER userGncAccountsCertificate;
GO

-- El login del certificado puede verificar y restaurar
-- (RESTORE necesita permisos de servidor).
USE master;
GO
IF IS_SRVROLEMEMBER(N'dbcreator', N'loginGncAccountsCertificate') <> 1
    ALTER SERVER ROLE dbcreator ADD MEMBER loginGncAccountsCertificate;
GO
USE gncproyecto;
GO

-- CREATE OR ALTER borra la firma, así que se firma después de crear.
ADD SIGNATURE TO OBJECT::dbo.pa_respaldoCompleto
BY CERTIFICATE certGncAccounts;
GO

ADD SIGNATURE TO OBJECT::dbo.pa_verificarRespaldo
BY CERTIFICATE certGncAccounts;
GO

ADD SIGNATURE TO OBJECT::dbo.pa_restaurarRespaldo
BY CERTIFICATE certGncAccounts;
GO

-- Nadie modifica el historial directamente; solo los procedimientos
-- escriben en él.
DENY INSERT, UPDATE, DELETE ON dbo.tbl_backupHistory TO rolAdmin, rolEmpleado, rolCliente;
DENY SELECT ON dbo.tbl_backupHistory TO rolEmpleado, rolCliente;

GRANT EXECUTE ON dbo.pa_respaldoCompleto  TO rolAdmin;
GRANT EXECUTE ON dbo.pa_verificarRespaldo TO rolAdmin;
GRANT EXECUTE ON dbo.pa_restaurarRespaldo TO rolAdmin;
GRANT EXECUTE ON dbo.pa_listarRespaldos   TO rolAdmin;
GO


/* ============================================================
   COMPROBACIÓN DEL RESPALDO Y LA RESTAURACIÓN

   1. Se crea un respaldo completo.
   2. Se verifica el archivo.
   3. Se restaura en gncproyecto_prueba.
   4. Se compara la cantidad de filas de las tablas principales
      entre la base real y la de prueba: deben ser iguales.
   ============================================================ */

-- No se usa INSERT ... EXEC para leer el nombre del archivo porque
-- abre una transacción, y BACKUP no se permite dentro de una.
EXEC dbo.pa_respaldoCompleto;
GO

DECLARE @fileName NVARCHAR(200) = (
    SELECT TOP 1 file_name
    FROM dbo.tbl_backupHistory
    WHERE operation = 'RESPALDO' AND status = 'EXITOSO'
    ORDER BY id DESC
);

EXEC dbo.pa_verificarRespaldo @fileName = @fileName;
EXEC dbo.pa_restaurarRespaldo @fileName = @fileName;
GO

-- Comparación entre la base real y la restaurada.
SELECT t.tabla, t.filas_original, t.filas_prueba,
       CASE WHEN t.filas_original = t.filas_prueba
            THEN 'IGUAL' ELSE 'DIFERENTE' END AS resultado
FROM (
    SELECT 'tbl_products', (SELECT COUNT(*) FROM gncproyecto.dbo.tbl_products),
           (SELECT COUNT(*) FROM gncproyecto_prueba.dbo.tbl_products)
    UNION ALL
    SELECT 'tbl_sales', (SELECT COUNT(*) FROM gncproyecto.dbo.tbl_sales),
           (SELECT COUNT(*) FROM gncproyecto_prueba.dbo.tbl_sales)
    UNION ALL
    SELECT 'tbl_saleDetails', (SELECT COUNT(*) FROM gncproyecto.dbo.tbl_saleDetails),
           (SELECT COUNT(*) FROM gncproyecto_prueba.dbo.tbl_saleDetails)
    UNION ALL
    SELECT 'tbl_users', (SELECT COUNT(*) FROM gncproyecto.dbo.tbl_users),
           (SELECT COUNT(*) FROM gncproyecto_prueba.dbo.tbl_users)
) AS t (tabla, filas_original, filas_prueba);

EXEC dbo.pa_listarRespaldos;
GO
