<?php
require 'db.php';
$sql = "SELECT * FROM rooms WHERE status = 'available' ORDER BY price ASC";
$result = $conn->query($sql);
$rooms = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$settings = $conn->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($settings['site_name'] ?? 'Kaizer B&B') ?></title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <header class="hero">
    <nav class="topbar container">
      <div class="brand">
        <div class="brand-mark">K</div>
        <div>
          <h1>Kaizer B&B</h1>
          <p>Luxury stay, warm hospitality</p>
        </div>
      </div>
      <div class="nav-actions">
        <a href="#rooms">Rooms</a>
        <a href="#about">About</a>
        <a href="admin/login.php">Admin Login</a>
      </div>
    </nav>

    <div class="container hero-grid">
      <div>
        <p class="eyebrow">BOOK YOUR PERFECT STAY</p>
        <h2>Elegant rooms, professional service, and unforgettable comfort.</h2>
        <p>Enjoy premium accommodation, tailored hospitality, and fast booking support at Kaizer B&B.</p>
        <a class="btn btn-primary" href="#rooms">View Rooms</a>
      </div>
      <div class="hero-card">
        <h3>Quick Booking Search</h3>
        <form action="search.php" method="GET">
          <label>Check-in</label>
          <input type="date" name="check_in" required />
          <label>Check-out</label>
          <input type="date" name="check_out" required />
          <label>Guests</label>
          <select name="guests">
            <option value="1">1 Guest</option>
            <option value="2">2 Guests</option>
            <option value="3">3 Guests</option>
            <option value="4">4 Guests</option>
          </select>
          <button class="btn btn-dark" type="submit">Search Availability</button>
        </form>
      </div>
    </div>
  </header>

  <main class="container">
    <section id="rooms" class="section">
      <div class="section-title">
        <h3>Featured Rooms</h3>
        <p>Comfortable, stylish, and ready for your next stay.</p>
      </div>
      <div class="cards">
        <?php foreach ($rooms as $room): ?>
          <article class="card">
            <img src="<?= htmlspecialchars($room['image']) ?>" alt="<?= htmlspecialchars($room['room_name']) ?>" />
            <div class="card-body">
              <h4><?= htmlspecialchars($room['room_name']) ?></h4>
              <p><?= htmlspecialchars($room['description']) ?></p>
              <div class="meta">
                <span><?= htmlspecialchars($room['type']) ?></span>
                <span>Capacity: <?= (int)$room['capacity'] ?></span>
              </div>
              <div class="price-row">
                <strong>R <?= number_format($room['price'], 2) ?></strong>
                <a class="btn btn-small" href="booking.php?room_id=<?= (int)$room['id'] ?>">Book Now</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="about" class="section about-grid">
      <div>
        <p class="eyebrow">WHY CHOOSE US</p>
        <h3>Professional hospitality with a personal touch.</h3>
        <p>Kaizer B&B offers polished accommodation with warm service, modern comfort, and smooth reservation handling.</p>
      </div>
      <div class="info-box">
        <h4>What we offer</h4>
        <ul>
          <li>Fast online booking</li>
          <li>Flexible room selection</li>
          <li>Secure payments</li>
          <li>Responsive admin management</li>
        </ul>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container footer-row">
      <p>© <?= date('Y') ?> Kaizer B&B</p>
      <p><?= htmlspecialchars($settings['contact_email'] ?? 'info@kaizerbnb.com') ?></p>
    </div>
  </footer>
</body>
</html>
