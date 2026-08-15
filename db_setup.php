<?php
function create_booking_tables($conn) {
    $conn->query("CREATE DATABASE IF NOT EXISTS `kaizerbnb`");
    $conn->select_db('kaizerbnb');
    $sql = [];
    $sql[] = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $sql[] = "CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_name VARCHAR(100) NOT NULL,
        type VARCHAR(50) NOT NULL,
        capacity INT NOT NULL DEFAULT 1,
        price DECIMAL(10,2) NOT NULL DEFAULT 15.00,
        status VARCHAR(20) NOT NULL DEFAULT 'available',
        description TEXT,
        image VARCHAR(255) DEFAULT 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=900&q=80'
    )";
    $sql[] = "CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(30),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $sql[] = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        room_id INT NOT NULL,
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        guests INT NOT NULL DEFAULT 1,
        total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (room_id) REFERENCES rooms(id)
    )";
    $sql[] = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        method VARCHAR(50) NOT NULL,
        reference VARCHAR(100),
        status VARCHAR(30) NOT NULL DEFAULT 'completed',
        paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id)
    )";
    $sql[] = "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        site_name VARCHAR(100) DEFAULT 'Kaizer B&B',
        currency VARCHAR(10) DEFAULT 'ZAR',
        tax_rate DECIMAL(5,2) DEFAULT 15.00,
        contact_email VARCHAR(100) DEFAULT 'info@kaizerbnb.com',
        contact_phone VARCHAR(30) DEFAULT '+27 00 000 0000'
    )";

    foreach ($sql as $query) {
        if (!$conn->query($query)) {
            die('Install failed: ' . $conn->error);
        }
    }

    $conn->query("INSERT INTO admins (name, email, password) VALUES ('Admin', 'admin@kaizerbnb.com', '\$2y\$10\$NPlzZ29ZaplOv4r5cUJ/.uIY3eqHiknyovC4UMMfWZSlqygzJTd52') ON DUPLICATE KEY UPDATE email=email");
    $conn->query("INSERT INTO settings (id, site_name, currency, tax_rate, contact_email, contact_phone) VALUES (1, 'Kaizer B&B', 'ZAR', 15.00, 'info@kaizerbnb.com', '+27 82 000 0000') ON DUPLICATE KEY UPDATE id=id");
    $conn->query("INSERT INTO rooms (room_name, type, capacity, price, status, description) VALUES
        ('Garden Deluxe', 'Deluxe', 2, 1800.00, 'available', 'Elegant room with a king-sized bed, veranda, and garden view.'),
        ('Royal Suite', 'Suite', 4, 3200.00, 'available', 'Luxury suite with lounge area, spa bath, and premium finishes.'),
        ('Executive Loft', 'Loft', 2, 2100.00, 'available', 'Modern loft with workspace, seating area, and elevated comfort.')
    ON DUPLICATE KEY UPDATE room_name=room_name");
}

function ensure_booking_database($conn) {
    $conn->query("CREATE DATABASE IF NOT EXISTS `kaizerbnb`");
    $conn->select_db('kaizerbnb');
    $result = $conn->query("SHOW TABLES LIKE 'rooms'");
    if (!$result || $result->num_rows === 0) {
        create_booking_tables($conn);
    }
}
?>