CREATE TABLE products (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    description VARCHAR(256) NULL,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

INSERT INTO products (id, name, description, quantity, created_at, updated_at, deleted_at) VALUES
(1, 'Mouse Wireless Ergonomis', 'Mouse nirkabel 2.4GHz dengan sensor optik presisi tinggi', 25, NOW(), NOW(), NULL),
(2, 'Keyboard Mekanikal RGB', 'Keyboard mekanikal 87 tombol dengan switch tactile dan lampu RGB', 15, NOW(), NOW(), NULL),
(3, 'Kabel Data USB Type-C', 'Kabel pengisian daya cepat 60W dengan bahan nylon braided tahan lama', 50, NOW(), NOW(), NULL),
(4, 'Stop Kontak 4 Lubang', 'Stop kontak dengan kabel sepanjang 1.5 meter dan saklar individual', 30, NOW(), NOW(), NULL),
(5, 'Lampu LED 12 Watt', 'Lampu bohlam LED hemat energi dengan cahaya putih terang (Cool Daylight)', 100, NOW(), NOW(), NULL),
(6, 'Buku Tulis A5 Grid', 'Buku catatan isi 80 lembar dengan kertas kotak-kotak halus 80gsm', 40, NOW(), NOW(), NULL),
(7, 'Pulpen Gel Hitam 0.5mm', 'Pulpen tinta gel hitam dengan ujung pena presisi anti macet', 75, NOW(), NOW(), NULL),
(8, 'Botol Minum Stainless 500ml', 'Tumbler termos tahan panas dan dingin hingga 12 jam', 20, NOW(), NOW(), NULL),
(9, 'Handuk Mandi Microfiber', 'Handuk ukuran 70x140cm dengan daya serap air tinggi dan cepat kering', 0, NOW(), NOW(), NOW()),
(10, 'Sapu Lantai Nylon', 'Sapu lantai dengan bulu nylon halus yang awet dan mudah dibersihkan', 12, NOW(), NOW(), NULL);





CREATE TABLE order_status (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(64) NOT NULL,
    is_midtrans_status BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

INSERT INTO order_status (id, name, is_midtrans_status, created_at, updated_at, deleted_at) VALUES
( 1, 'INITIATED', FALSE, NOW(), NOW(), NULL ),
( 2, 'PENDING', TRUE, NOW(), NOW(), NULL ),
( 3, 'CAPTURE', TRUE, NOW(), NOW(), NULL ),
( 4, 'SETTLEMENT', TRUE, NOW(), NOW(), NULL ),
( 5, 'DENY', TRUE, NOW(), NOW(), NULL ),
( 6, 'CANCEL', TRUE, NOW(), NOW(), NULL ),
( 7, 'EXPIRE', TRUE, NOW(), NOW(), NULL ),
( 8, 'FAILURE', TRUE, NOW(), NOW(), NULL ),
( 9, 'REFUND', TRUE, NOW(), NOW(), NULL ),
( 10, 'CHARGEBACK', TRUE, NOW(), NOW(), NULL ),
( 11, 'PARTIAL_REFUND', TRUE, NOW(), NOW(), NULL ),
( 12, 'PARTIAL_CHARGEBACK', TRUE, NOW(), NOW(), NULL ),
( 13, 'AUTHORIZE', TRUE, NOW(), NOW(), NULL );





CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_order_status INT NOT NULL,
    customer_name VARCHAR(256) NOT NULL,
    customer_email VARCHAR(256) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    description VARCHAR(256) NOT NULL,
    total_price INT NOT NULL,
    payment_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_orders_order_status FOREIGN KEY (id_order_status) REFERENCES order_status(id) ON UPDATE RESTRICT ON DELETE RESTRICT
);

ALTER TABLE `orders` AUTO_INCREMENT = 52;




CREATE TABLE order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_orders INT NOT NULL,
    id_products INT NOT NULL,
    name INT NOT NULL,
    quantity INT NOT NULL,
    price INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_order_details_orders FOREIGN KEY (id_orders) REFERENCES orders(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_order_details_products FOREIGN KEY (id_products) REFERENCES products(id) ON UPDATE RESTRICT ON DELETE RESTRICT
);


CREATE TABLE midtrans_snaps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_orders INT NOT NULL,
    snap_token VARCHAR(256) NULL,
    snap_redirect_url VARCHAR(256) NULL,
    expired_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_midtrans_snaps_orders FOREIGN KEY (id_orders) REFERENCES orders(id) ON UPDATE RESTRICT ON DELETE RESTRICT
);
