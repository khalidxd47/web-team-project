-- =====================================================================
-- Store - T-shirt merch store database
-- Import in phpMyAdmin or run: mysql -u root < schema.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS store_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE store_db;

DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------------------
-- Users: passwords are stored as bcrypt hashes from password_hash()
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_email (email)
) ENGINE=InnoDB;

-- admin@store.com / admin123   and   customer@store.com / customer123
INSERT INTO users (name, email, password, role) VALUES
('Store Admin',   'admin@store.com',    '$2y$10$.DG5JMD0aJLMF1umt3UUN.BUhlmOLZcpUp8DChdjmrlvv/QY7EA9.', 'admin'),
('Sara Customer', 'customer@store.com', '$2y$10$Sd/ZIbCy89J1CXLXebaoq.wrBLjSYepsKg0UAt.pE1rWNZSkcFupm', 'customer');

-- ---------------------------------------------------------------------
-- Products: "size" holds the available sizes as a comma list (S,M,L)
-- so it can be filtered with FIND_IN_SET()
-- ---------------------------------------------------------------------
CREATE TABLE products (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title       VARCHAR(150)  NOT NULL,
    description TEXT          NOT NULL,
    category    VARCHAR(50)   NOT NULL,
    size        VARCHAR(50)   NOT NULL,
    price       DECIMAL(8, 2) NOT NULL,
    stock       INT UNSIGNED  NOT NULL DEFAULT 0,
    image_url   VARCHAR(255)  NOT NULL,
    PRIMARY KEY (id),
    KEY idx_category (category)
) ENGINE=InnoDB;

INSERT INTO products (title, description, category, size, price, stock, image_url) VALUES
('Overprint Logo Tee', 'Our signature mark printed twice, slightly off-register, in blue and fluoro pink water-based ink. 220 gsm combed cotton with a boxy fit.', 'Graphic', 'S,M,L,XL', 34.00, 42, 'assets/images/tee-white.svg'),
('Night Shift Tee', 'A heavyweight black tee with a tonal back print that only shows in direct light. Ribbed collar that keeps its shape after washing.', 'Graphic', 'M,L,XL,XXL', 36.00, 18, 'assets/images/tee-black.svg'),
('Daily Crew - Black', 'The everyday crew neck. Mid-weight 180 gsm cotton, regular fit, no branding on the front. Buy two.', 'Essentials', 'XS,S,M,L,XL,XXL', 22.00, 120, 'assets/images/tee-black.svg'),
('Daily Crew - White', 'The same everyday crew in optic white. Tight knit, so it is not see-through, and pre-shrunk before cutting.', 'Essentials', 'XS,S,M,L,XL,XXL', 22.00, 96, 'assets/images/tee-white.svg'),
('Daily Crew - Heather Grey', 'Soft heather grey blend of 90% cotton and 10% viscose. Drapes well and hides wrinkles after a long day.', 'Essentials', 'S,M,L,XL', 24.00, 7, 'assets/images/tee-grey.svg'),
('Boxy Navy Oversized', 'Dropped shoulders, a cropped body and wide sleeves. 260 gsm heavyweight cotton in deep navy.', 'Oversized', 'S,M,L,XL', 39.00, 25, 'assets/images/tee-navy.svg'),
('Olive Utility Oversized', 'Oversized cut with a hidden chest pocket and a garment-dyed olive finish. Every piece fades a little differently.', 'Oversized', 'M,L,XL', 42.00, 4, 'assets/images/tee-olive.svg'),
('Sand Dune Oversized', 'Warm sand colour, double-stitched hems and a thick 2.5 cm neck rib. Pairs with everything in the drop.', 'Oversized', 'S,M,L,XL,XXL', 39.00, 31, 'assets/images/tee-sand.svg'),
('Acid Wash Rust', 'Hand acid-washed in small batches, so no two are the same. Relaxed fit with a softened, broken-in feel.', 'Vintage Wash', 'S,M,L,XL', 45.00, 12, 'assets/images/tee-rust.svg'),
('Faded Cobalt Tee', 'Stone-washed cobalt blue with a cracked print on the chest. Looks ten years old on day one.', 'Vintage Wash', 'S,M,L', 44.00, 0, 'assets/images/tee-blue.svg'),
('Sun-Bleached Lilac', 'Pigment-dyed lilac, faded at the seams on purpose. Slightly cropped, regular sleeves.', 'Vintage Wash', 'XS,S,M,L', 44.00, 15, 'assets/images/tee-lilac.svg'),
('Fluoro Pink Print Tee', 'Fluorescent pink with a blue screen-printed graphic on the back. Loud on purpose.', 'Graphic', 'S,M,L,XL', 35.00, 22, 'assets/images/tee-pink.svg'),
('Street Map Graphic', 'Front print of an imaginary city grid in three overprinted inks. Printed by hand on our own press.', 'Graphic', 'M,L,XL', 38.00, 9, 'assets/images/tee-sand.svg'),
('Long Sleeve Layer - White', 'A long-sleeve base layer with ribbed cuffs and a small sleeve print. Wear it alone or under an oversized tee.', 'Long Sleeve', 'S,M,L,XL', 40.00, 28, 'assets/images/tee-white.svg'),
('Long Sleeve Layer - Black', 'Black long sleeve, 200 gsm cotton, thumb-friendly cuffs. The cold-evening favourite.', 'Long Sleeve', 'S,M,L,XL,XXL', 40.00, 3, 'assets/images/tee-black.svg'),
('Cobalt Pocket Tee', 'Regular fit with a sewn chest pocket in contrast blue. Simple, sturdy and easy to wear.', 'Essentials', 'S,M,L,XL', 26.00, 54, 'assets/images/tee-blue.svg'),
('Grey Marl Long Sleeve', 'Heavy marl long sleeve with a relaxed body and a printed hem tag. Built to be worn in.', 'Long Sleeve', 'M,L,XL', 42.00, 11, 'assets/images/tee-grey.svg');
