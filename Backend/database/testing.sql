CREATE TABLE order_status (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

INSERT INTO order_status (id, nama, created_at, updated_at, deleted_at) VALUES
( 1, 'INITIATED', NOW(), NOW(), NULL ),
( 2, 'PENDING', NOW(), NOW(), NULL ),
( 3, 'CAPTURE', NOW(), NOW(), NULL ),
( 4, 'SETTLEMENT', NOW(), NOW(), NULL ),
( 5, 'DENY', NOW(), NOW(), NULL ),
( 6, 'CANCEL', NOW(), NOW(), NULL ),
( 7, 'EXPIRE', NOW(), NOW(), NULL ),
( 8, 'FAILURE', NOW(), NOW(), NULL ),
( 9, 'REFUND', NOW(), NOW(), NULL ),
( 10, 'CHARGEBACK', NOW(), NOW(), NULL ),
( 11, 'PARTIAL_REFUND', NOW(), NOW(), NULL ),
( 12, 'PARTIAL_CHARGEBACK', NOW(), NOW(), NULL ),
( 13, 'AUTHORIZE', NOW(), NOW(), NULL );





CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_order_status INT NOT NULL,
    customer_name VARCHAR(256) NOT NULL,
    customer_email VARCHAR(256) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    keterangan VARCHAR(256) NOT NULL,
    gross_ammount INTEGER NOT NULL,
    snap_created_at TIMESTAMP NULL,
    snap_token VARCHAR(256) NULL,
    snap_redirect_url VARCHAR(256) NULL,
    expired_at TIMESTAMP NULL,
    payment_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_orders_order_status FOREIGN KEY (id_order_status) REFERENCES order_status(id) ON UPDATE RESTRICT ON DELETE RESTRICT
);

ALTER TABLE `orders` AUTO_INCREMENT = 52;




CREATE TABLE order_details (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_orders INT NOT NULL,
    nama INT NOT NULL,
    quantity INT NOT NULL,
    harga_total INT NOT NULL COMMENT 'Harga setelah dikalikan quantity',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_order_details_orders FOREIGN KEY (id_orders) REFERENCES orders(id) ON UPDATE RESTRICT ON DELETE RESTRICT
);
