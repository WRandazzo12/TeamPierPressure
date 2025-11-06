<?php
// index.php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$name = $_SESSION['firstname'] ?? $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ocean Cruises - Interactive Booking</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="container">
    <header>
      <h1>🚢 Ocean Cruises</h1>
      <nav>
        <?php if ($isLoggedIn): ?>
          <span style="font-weight:700;margin-right:12px;">Welcome, <?= htmlspecialchars($name) ?></span>
          <a href="logout.php">Logout</a>
          <a href="profile.php">Profile Page</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 0): ?>
    <a href="admin_bookings.php">View Booked Rooms</a>
<?php endif; ?>

      </nav>
    </header>

    <div class="hero">
      <h2>Click on the Ship to Explore Cabins & Book Your Dream Cruise</h2>

      <div class="ship-container">
        <svg class="cruise-ship" width="700" height="380" viewBox="0 0 700 380">
          <defs>
            <linearGradient id="oceanGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:#0ea5e9;stop-opacity:0.3" />
              <stop offset="100%" style="stop-color:#0284c7;stop-opacity:0.6" />
            </linearGradient>
            <linearGradient id="hullGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:#1e40af" />
              <stop offset="100%" style="stop-color:#1e3a8a" />
            </linearGradient>
            <linearGradient id="deckGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:#60a5fa" />
              <stop offset="100%" style="stop-color:#2563eb" />
            </linearGradient>
            <filter id="glow">
              <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
              <feMerge>
                <feMergeNode in="coloredBlur"/>
                <feMergeNode in="SourceGraphic"/>
              </feMerge>
            </filter>
          </defs>

          <!-- Ocean -->
          <rect x="0" y="250" width="700" height="140" fill="url(#oceanGradient)"/>
          <path d="M0 270 Q175 260 350 270 T700 270 L700 390 L0 390 Z" fill="#0284c7" opacity="0.4">
            <animateTransform attributeName="transform" type="translate" values="0,0; -60,0; 0,0" dur="5s" repeatCount="indefinite"/>
          </path>
          <path d="M0 280 Q175 270 350 280 T700 280 L700 390 L0 390 Z" fill="#38bdf8" opacity="0.25">
            <animateTransform attributeName="transform" type="translate" values="0,0; 60,0; 0,0" dur="6s" repeatCount="indefinite"/>
          </path>

          <!-- Hull -->
          <path d="M80 260 Q350 200 620 260 L560 300 L140 300 Z" fill="url(#hullGradient)" stroke="#0f172a" stroke-width="3"/>

          <!-- Decks -->
          <rect class="clickable-deck" data-deck="interior" x="140" y="220" width="420" height="40" fill="#3b82f6" stroke="#1e40af" stroke-width="3" rx="10"/>
          <text x="350" y="245" text-anchor="middle" fill="white" font-size="16" font-weight="bold">Interior Cabins - From $89/night</text>

          <rect class="clickable-deck" data-deck="ocean" x="160" y="180" width="380" height="40" fill="#0ea5e9" stroke="#0284c7" stroke-width="3" rx="10"/>
          <text x="350" y="205" text-anchor="middle" fill="white" font-size="16" font-weight="bold">Ocean View - From $129/night</text>

          <rect class="clickable-deck" data-deck="balcony" x="180" y="140" width="340" height="40" fill="#06b6d4" stroke="#0891b2" stroke-width="3" rx="10"/>
          <text x="350" y="165" text-anchor="middle" fill="white" font-size="16" font-weight="bold">Balcony Cabins - From $189/night</text>

          <rect class="clickable-deck" data-deck="suite" x="200" y="100" width="300" height="40" fill="#0891b2" stroke="#0e7490" stroke-width="3" rx="10"/>
          <text x="350" y="125" text-anchor="middle" fill="white" font-size="16" font-weight="bold">Luxury Suites - From $299/night</text>

          <!-- Bridge -->
          <rect x="260" y="60" width="180" height="40" fill="url(#deckGradient)" stroke="#1e3a8a" stroke-width="3" rx="10"/>
          <rect x="290" y="35" width="120" height="25" fill="#1e3a8a" stroke="#1e40af" stroke-width="2" rx="6"/>

          <!-- Smokestacks -->
          <rect x="310" y="20" width="16" height="20" fill="#374151" rx="3"/>
          <rect x="340" y="20" width="16" height="20" fill="#374151" rx="3"/>
          <rect x="370" y="20" width="16" height="20" fill="#374151" rx="3"/>
          <circle cx="318" cy="15" r="4" fill="#9ca3af" opacity="0.7">
            <animate attributeName="cy" values="15;-15;-40" dur="3s" repeatCount="indefinite"/>
            <animate attributeName="opacity" values="0.7;0.4;0" dur="3s" repeatCount="indefinite"/>
          </circle>
          <circle cx="348" cy="15" r="4" fill="#9ca3af" opacity="0.7">
            <animate attributeName="cy" values="15;-15;-40" dur="3.5s" repeatCount="indefinite"/>
            <animate attributeName="opacity" values="0.7;0.4;0" dur="3.5s" repeatCount="indefinite"/>
          </circle>
          <circle cx="378" cy="15" r="4" fill="#9ca3af" opacity="0.7">
            <animate attributeName="cy" values="15;-15;-40" dur="4s" repeatCount="indefinite"/>
            <animate attributeName="opacity" values="0.7;0.4;0" dur="4s" repeatCount="indefinite"/>
          </circle>

          <!-- Portholes -->
          <g>
            <circle cx="160" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="200" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="240" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="280" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="320" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="360" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="400" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="440" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
            <circle cx="480" cy="275" r="6" fill="#facc15" filter="url(#glow)"/>
          </g>
        </svg>
      </div>

      <!-- Easter egg target -->
      <p id="bullseye" style="margin-top: 30px; color: #0369a1; font-weight: 600; font-size: 18px; cursor: pointer;">
        🎯 Click on any deck level to explore cabin options and pricing!
      </p>

      <!-- Explosion effect -->
      <div id="explosion" style="display:none; text-align:center; margin-top:20px;">
        <div id="explosion-icon" style="font-size:60px;">🔥</div>
        <div id="explosion-message" style="margin-top:10px; font-size:22px; font-weight:bold; color:darkred;">
          I guess you will stay on the land, land lubber!
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Modal -->
  <div id="bookingModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="modalTitle">Select Your Cabin</h2>
      <div id="cabinOptions"></div>
      <form id="bookingForm" class="booking-form" style="display: none;">
        <h3>Complete Your Booking</h3>
        <select id="cruiseSelect" required><option value="">Select Cruise</option></select>
        <input type="number" id="passengers" min="1" max="4" value="2" placeholder="Number of Passengers" required>
        <input type="date" id="departureDate" required>
        <input type="text" id="customerName" placeholder="Full Name" required>
        <input type="email" id="customerEmail" placeholder="Email Address" required>
        <input type="tel" id="customerPhone" placeholder="Phone Number" required>
        <div class="price-summary">
          <h4>Price Summary</h4>
          <p>Price per night: $<span id="pricePerNight">0</span></p>
          <p>Total nights: <span id="totalNights">7</span></p>
          <p>Passengers: <span id="totalPassengers">2</span></p>
          <p class="total-price">Total: $<span id="totalPrice">0</span></p>
        </div>
        <button type="submit">Book Now</button>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
