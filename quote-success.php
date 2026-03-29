<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$tracking_number = sanitize_input($_GET['tracking'] ?? '');
$email = sanitize_input($_GET['email'] ?? '');

if (empty($tracking_number)) {
 header("Location: quote.php");
 exit;
}

// Get order details
$db = getDB();
$order = null;

try {
 $stmt = $db->prepare("SELECT * FROM orders WHERE tracking_number = ?");
 $stmt->execute([$tracking_number]);
 $order = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
 // Handle error
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Quote Confirmed - Lugomax Logistics</title>
 <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
 <?php include 'includes/header.php'; ?>

 <section class="success-section">
  <div class="container">
   <div class="success-card">
    <div class="success-icon">
     <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
      <circle cx="40" cy="40" r="40" fill="#d1fae5" />
      <path d="M25 40L35 50L55 30" stroke="#065f46" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
     </svg>
    </div>

    <h1>Quote Request Confirmed!</h1>
    <p class="success-message">Thank you for choosing Lugomax Logistics. Your delivery has been scheduled and we've generated your tracking number.</p>

    <div class="tracking-box">
     <div class="tracking-label">Your Tracking Number</div>
     <div class="tracking-number" id="trackingNumber"><?= htmlspecialchars($tracking_number) ?></div>
     <button class="btn-copy" onclick="copyTracking()">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
       <path d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H6zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1H2z" />
      </svg>
      Copy
     </button>
    </div>

    <?php if ($order): ?>
     <div class="order-summary">
      <h3>Order Summary</h3>
      <div class="summary-grid">
       <div class="summary-item">
        <div class="summary-label">Customer</div>
        <div class="summary-value"><?= htmlspecialchars($order['customer_name']) ?></div>
       </div>
       <div class="summary-item">
        <div class="summary-label">Service Type</div>
        <div class="summary-value"><?= htmlspecialchars($order['service_type']) ?></div>
       </div>
       <!-- <div class="summary-item">
        <div class="summary-label">Estimated Price</div>
        <div class="summary-value">£<?= number_format($order['price'], 2) ?></div>
       </div> -->
       <!-- <div class="summary-item">
        <div class="summary-label">Estimated Delivery</div>
        <div class="summary-value"><?= date('l, M d, Y', strtotime($order['estimated_delivery'])) ?></div>
       </div> -->
      </div>

      <div class="addresses">
       <div class="address-box">
        <h4>📍 Pickup</h4>
        <p><?= nl2br(htmlspecialchars($order['pickup_address'])) ?></p>
       </div>
       <div class="arrow">→</div>
       <div class="address-box">
        <h4>📍 Delivery</h4>
        <p><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></p>
       </div>
      </div>
     </div>
    <?php endif; ?>

    <div class="next-steps">
     <h3>What Happens Next?</h3>
     <div class="steps-grid">
      <div class="step">
       <div class="step-number">1</div>
       <h4>Save Your Tracking Number</h4>
       <p>Keep this tracking number safe. You'll need it to track your delivery.</p>
      </div>
      <div class="step">
       <div class="step-number">2</div>
       <h4>Confirmation Email</h4>
       <p>We've sent a confirmation email to <strong><?= htmlspecialchars($email) ?></strong></p>
      </div>
      <div class="step">
       <div class="step-number">3</div>
       <h4>Track Your Delivery</h4>
       <p>Use your tracking number to monitor your delivery in real-time.</p>
      </div>

      <div class="step">
       <div class="step-number">4</div>
       <h4>Pickup & Delivery</h4>
       <p>We'll collect your package and deliver it on schedule.</p>
      </div>
     </div>
    </div>

    <div class="action-buttons">
     <a href="track?track=<?= htmlspecialchars($tracking_number) ?>" class="btn-primary">Track Your Order</a>
     <a href="./" class="btn-outline">Back to Home</a>
    </div>

    <div class="contact-info">
     <p>Need help? Contact us at <strong><?= escape(get_setting('site_email', 'info@lugomax.co.uk')) ?></strong> or call <strong><?= escape(get_setting('site_phone', '+44 (0) XXX XXX XXXX')) ?></strong></p>
    </div>
   </div>
  </div>
 </section>

 <?php include 'includes/footer.php'; ?>

 <style>
  .success-section {
   padding: 80px 0;
   background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
   min-height: 80vh;
  }

  .success-card {
   background: white;
   padding: 60px 50px;
   border-radius: 16px;
   box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
   max-width: 900px;
   margin: 0 auto;
   text-align: center;
  }

  .success-icon {
   margin: 0 auto 30px;
   animation: scaleIn 0.5s ease-out;
  }

  @keyframes scaleIn {
   from {
    transform: scale(0);
   }

   to {
    transform: scale(1);
   }
  }

  .success-card h1 {
   color: #0A1F44;
   font-size: 2.5rem;
   margin-bottom: 15px;
  }

  .success-message {
   color: #64748B;
   font-size: 1.1rem;
   margin-bottom: 40px;
   line-height: 1.6;
  }

  .tracking-box {
   background: linear-gradient(135deg, #0A1F44 0%, #1a3a6b 100%);
   padding: 30px;
   border-radius: 12px;
   margin-bottom: 40px;
  }

  .tracking-label {
   color: rgba(255, 255, 255, 0.8);
   font-size: 0.9rem;
   margin-bottom: 10px;
   text-transform: uppercase;
   letter-spacing: 1px;
  }

  .tracking-number {
   color: white;
   font-size: 2.5rem;
   font-weight: 700;
   font-family: 'Courier New', monospace;
   letter-spacing: 2px;
   margin-bottom: 15px;
  }

  .btn-copy {
   background: rgba(255, 255, 255, 0.2);
   color: white;
   border: 1px solid rgba(255, 255, 255, 0.3);
   padding: 10px 20px;
   border-radius: 8px;
   cursor: pointer;
   display: inline-flex;
   align-items: center;
   gap: 8px;
   font-weight: 600;
   transition: all 0.3s;
  }

  .btn-copy:hover {
   background: rgba(255, 255, 255, 0.3);
  }

  .order-summary {
   text-align: left;
   margin-bottom: 40px;
   padding: 30px;
   background: #f8f9fa;
   border-radius: 12px;
  }

  .order-summary h3 {
   color: #0A1F44;
   margin-bottom: 20px;
   text-align: center;
  }

  .summary-grid {
   display: grid;
   grid-template-columns: repeat(2, 1fr);
   gap: 20px;
   margin-bottom: 30px;
  }

  .summary-item {
   padding: 15px;
   background: white;
   border-radius: 8px;
  }

  .summary-label {
   color: #64748B;
   font-size: 0.85rem;
   margin-bottom: 5px;
  }

  .summary-value {
   color: #0A1F44;
   font-weight: 600;
   font-size: 1.1rem;
  }

  .addresses {
   display: grid;
   grid-template-columns: 1fr auto 1fr;
   gap: 20px;
   align-items: center;
  }

  .address-box {
   background: white;
   padding: 20px;
   border-radius: 8px;
   text-align: left;
  }

  .address-box h4 {
   color: #0A1F44;
   margin-bottom: 10px;
   font-size: 1rem;
  }

  .address-box p {
   color: #64748B;
   font-size: 0.95rem;
   line-height: 1.6;
  }

  .arrow {
   font-size: 2rem;
   color: #FF6B2C;
   font-weight: bold;
  }

  .next-steps {
   text-align: left;
   margin-bottom: 40px;
  }

  .next-steps h3 {
   color: #0A1F44;
   margin-bottom: 25px;
   text-align: center;
  }

  .steps-grid {
   display: grid;
   grid-template-columns: repeat(2, 1fr);
   gap: 20px;
  }

  .step {
   background: #f8f9fa;
   padding: 25px;
   border-radius: 12px;
   text-align: left;
  }

  .step-number {
   width: 40px;
   height: 40px;
   background: #FF6B2C;
   color: white;
   border-radius: 50%;
   display: flex;
   align-items: center;
   justify-content: center;
   font-weight: 700;
   font-size: 1.2rem;
   margin-bottom: 15px;
  }

  .step h4 {
   color: #0A1F44;
   margin-bottom: 10px;
   font-size: 1.1rem;
  }

  .step p {
   color: #64748B;
   line-height: 1.6;
  }

  .action-buttons {
   display: flex;
   gap: 15px;
   justify-content: center;
   margin-bottom: 30px;
  }

  .btn-outline {
   background: transparent;
   border: 2px solid #0A1F44;
   color: #0A1F44;
   padding: 14px 32px;
   border-radius: 8px;
   text-decoration: none;
   font-weight: 600;
   transition: all 0.3s;
  }

  .btn-outline:hover {
   background: #0A1F44;
   color: white;
  }

  .contact-info {
   padding-top: 30px;
   border-top: 2px solid #f0f0f0;
   color: #64748B;
  }

  .contact-info strong {
   color: #FF6B2C;
  }

  @media (max-width: 768px) {
   .success-card {
    padding: 40px 30px;
   }

   .success-card h1 {
    font-size: 2rem;
   }

   .tracking-number {
    font-size: 1.8rem;
   }

   .summary-grid,
   .steps-grid {
    grid-template-columns: 1fr;
   }

   .addresses {
    grid-template-columns: 1fr;
   }

   .arrow {
    transform: rotate(90deg);
    text-align: center;
   }

   .action-buttons {
    flex-direction: column;
   }
  }
 </style>

 <script>
  function copyTracking() {
   const trackingNumber = document.getElementById('trackingNumber').textContent;
   navigator.clipboard.writeText(trackingNumber).then(() => {
    const btn = document.querySelector('.btn-copy');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg> Copied!';
    setTimeout(() => {
     btn.innerHTML = originalText;
    }, 2000);
   });
  }
 </script>
</body>

</html>