<?php
require 'db.php';
require 'includes/email.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);
$booking_stmt = $conn->prepare(
    'SELECT b.*, c.full_name, c.email, c.phone, c.address, r.room_name, r.price, r.capacity ' .
    'FROM bookings b JOIN customers c ON c.id = b.customer_id JOIN rooms r ON r.id = b.room_id WHERE b.id = ?'
);
$booking_stmt->bind_param('i', $booking_id);
$booking_stmt->execute();
$booking = $booking_stmt->get_result()->fetch_assoc();

if (!$booking) {
    die('Booking not found.');
}

$settings = $conn->query('SELECT * FROM settings ORDER BY id DESC LIMIT 1')->fetch_assoc();
$tax_rate = (float)($settings['tax_rate'] ?? 0);
$subtotal = (float)$booking['total_amount'];
$tax_amount = round($subtotal * $tax_rate / 100, 2);
$grand_total = round($subtotal + $tax_amount, 2);

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = trim($_POST['payment_method'] ?? 'card');
    $payment_reference = trim($_POST['payment_reference'] ?? '');
    $allowed_methods = ['card', 'transfer', 'cash'];

    if (!in_array($payment_method, $allowed_methods, true)) {
        $error = 'Please select a valid payment method.';
    } else {
        $new_status = 'confirmed';
        $new_payment_status = $payment_method === 'cash' ? 'pending' : 'paid';
        $payment_status_value = $new_payment_status === 'paid' ? 'completed' : 'pending';

        $update_stmt = $conn->prepare('UPDATE bookings SET status = ?, payment_status = ? WHERE id = ?');
        $update_stmt->bind_param('ssi', $new_status, $new_payment_status, $booking_id);
        $update_stmt->execute();

        $payment_stmt = $conn->prepare('INSERT INTO payments (booking_id, amount, method, reference, status) VALUES (?, ?, ?, ?, ?)');
        $payment_stmt->bind_param('idsss', $booking_id, $grand_total, $payment_method, $payment_reference, $payment_status_value);
        $payment_stmt->execute();

        send_booking_confirmation($conn, $booking_id, $payment_method, $grand_total, $payment_reference);
        $success = 'Booking confirmed successfully. We will contact you shortly.';
        $booking['status'] = $new_status;
        $booking['payment_status'] = $new_payment_status;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout | Kaizer B&B</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="container" style="padding:40px 0;">
    <div class="checkout-shell">
      <div class="panel">
        <p class="eyebrow">CHECKOUT</p>
        <h2>Complete your reservation</h2>
        <p class="muted">Your stay is almost ready. Review the details below and choose a payment method.</p>

        <?php if ($success): ?>
          <div class="success-banner"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="error-banner"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="summary-box">
          <div class="summary-row"><span>Guest</span><strong><?= htmlspecialchars($booking['full_name']) ?></strong></div>
          <div class="summary-row"><span>Room</span><strong><?= htmlspecialchars($booking['room_name']) ?></strong></div>
          <div class="summary-row"><span>Dates</span><strong><?= htmlspecialchars($booking['check_in']) ?> → <?= htmlspecialchars($booking['check_out']) ?></strong></div>
          <div class="summary-row"><span>Guests</span><strong><?= (int)$booking['guests'] ?></strong></div>
          <div class="summary-row"><span>Subtotal</span><strong>R <?= number_format($subtotal, 2) ?></strong></div>
          <div class="summary-row"><span>Tax</span><strong>R <?= number_format($tax_amount, 2) ?></strong></div>
          <div class="summary-row total"><span>Grand Total</span><strong>R <?= number_format($grand_total, 2) ?></strong></div>
        </div>

        <form method="POST" class="form-grid" style="margin-top:20px;">
          <div>
            <label>Payment Method</label>
            <select name="payment_method">
              <option value="card">Card</option>
              <option value="transfer">Bank Transfer</option>
              <option value="cash">Cash on Arrival</option>
            </select>
          </div>
          <div>
            <label>Reference / Receipt No.</label>
            <input name="payment_reference" placeholder="Optional" />
          </div>
          <div class="full-width">
            <button class="btn btn-primary" type="submit">Confirm Booking</button>
            <a class="btn btn-dark" href="index.php" style="margin-left:8px;">Back to Home</a>
          </div>
        </form>
      </div>
      <div class="panel accent-panel">
        <h3>Why guests love Kaizer B&B</h3>
        <ul>
          <li>Flexible check-in support</li>
          <li>Secure payment handling</li>
          <li>Professional concierge service</li>
          <li>Instant booking confirmation</li>
        </ul>
      </div>
    </div>
  </div>
</body>
</html>
