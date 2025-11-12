<?php
// rooms.php
session_start();
require_once 'connection.php';
$isLoggedIn = isset($_SESSION['user_id']);
$name = $_SESSION['firstname'] ?? $_SESSION['email'] ?? '';

// Fetch rooms from database, organized by floor
$roomsByFloor = [];
try {
    $stmt = $conn->prepare("SELECT * FROM rooms ORDER BY floor, room_number");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($room = $result->fetch_assoc()) {
        $floor = $room['floor'];
        if (!isset($roomsByFloor[$floor])) {
            $roomsByFloor[$floor] = [];
        }
        $roomsByFloor[$floor][] = $room;
    }
    $stmt->close();
} catch (Exception $e) {
    // If rooms table doesn't exist, show empty array
    $roomsByFloor = [];
}

// Get room type icons and colors
function getRoomTypeInfo($type) {
    $info = [
        'interior' => ['icon' => '🛏️', 'color' => '#3b82f6', 'name' => 'Interior'],
        'ocean' => ['icon' => '🌊', 'color' => '#0ea5e9', 'name' => 'Ocean View'],
        'balcony' => ['icon' => '🏖️', 'color' => '#06b6d4', 'name' => 'Balcony'],
        'suite' => ['icon' => '👑', 'color' => '#0891b2', 'name' => 'Luxury Suite']
    ];
    return $info[$type] ?? $info['interior'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Rooms - Ocean Cruises</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .rooms-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      margin: 30px 0;
    }
    
    .room-card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }
    
    .room-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }
    
    .room-header {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 3px solid #e2e8f0;
    }
    
    .room-title {
      font-size: 1.8rem;
      color: #1e40af;
      margin-bottom: 10px;
    }
    
    .room-price {
      font-size: 2rem;
      font-weight: bold;
      color: #059669;
      margin: 10px 0;
    }
    
    .room-price span {
      font-size: 1rem;
      color: #64748b;
      font-weight: normal;
    }
    
    .room-description {
      color: #64748b;
      font-size: 1rem;
      line-height: 1.6;
      margin-bottom: 20px;
      flex-grow: 1;
    }
    
    .room-features {
      margin-top: 20px;
    }
    
    .room-features h4 {
      color: #1e40af;
      margin-bottom: 15px;
      font-size: 1.1rem;
    }
    
    .room-features ul {
      list-style: none;
      padding: 0;
    }
    
    .room-features li {
      padding: 8px 0;
      color: #475569;
      display: flex;
      align-items: center;
    }
    
    .room-features li::before {
      content: "✓";
      color: #10b981;
      font-weight: bold;
      margin-right: 10px;
      font-size: 1.2rem;
    }
    
    .book-button {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 20px;
      width: 100%;
    }
    
    .book-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    }
    
    .room-image {
      width: 100%;
      height: 200px;
      background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
      border-radius: 10px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: #0284c7;
    }
    
    .page-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .page-header h2 {
      font-size: 2.5rem;
      color: #1e40af;
      margin-bottom: 15px;
    }
    
    .page-header p {
      font-size: 1.2rem;
      color: #64748b;
    }

    /* Floor-based room selection styles */
    .room-selection-section {
      margin-top: 60px;
      padding-top: 40px;
      border-top: 3px solid #e2e8f0;
    }

    .floor-section {
      margin-bottom: 50px;
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .floor-header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 2px solid #e2e8f0;
    }

    .floor-header h3 {
      font-size: 2rem;
      color: #1e40af;
      margin: 0;
    }

    .floor-rooms {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .room-select-card {
      background: #f8fafc;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 20px;
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
    }

    .room-select-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      border-color: #3b82f6;
    }

    .room-select-card.selected {
      border-color: #10b981;
      background: #ecfdf5;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .room-select-card.booked {
      opacity: 0.6;
      cursor: not-allowed;
      background: #f1f5f9;
    }

    .room-select-card.booked:hover {
      transform: none;
      box-shadow: none;
    }

    .room-number {
      font-size: 1.5rem;
      font-weight: bold;
      color: #1e40af;
      margin-bottom: 10px;
    }

    .room-type-badge {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .room-price-small {
      font-size: 1.3rem;
      font-weight: bold;
      color: #059669;
      margin: 10px 0;
    }

    .room-status {
      position: absolute;
      top: 10px;
      right: 10px;
      padding: 5px 10px;
      border-radius: 15px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .room-status.available {
      background: #10b981;
      color: white;
    }

    .room-status.booked {
      background: #ef4444;
      color: white;
    }

    .room-status.maintenance {
      background: #f59e0b;
      color: white;
    }

    .select-room-btn {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
      transition: all 0.3s ease;
    }

    .select-room-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }

    .select-room-btn:disabled {
      background: #94a3b8;
      cursor: not-allowed;
      transform: none;
    }

    .booking-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .booking-modal-content {
      background-color: white;
      margin: 5% auto;
      padding: 30px;
      border-radius: 15px;
      width: 90%;
      max-width: 600px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    .close-modal {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close-modal:hover {
      color: #000;
    }

    .booking-form input,
    .booking-form select {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 1rem;
    }

    .booking-form button[type="submit"] {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      width: 100%;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1>🚢 Ocean Cruises</h1>
      <nav>
        <a href="index.php">Home</a>
        <a href="rooms.php">Rooms</a>
        <?php if ($isLoggedIn): ?>
          <span style="font-weight:700;margin-right:12px;">Welcome, <?= htmlspecialchars($name) ?></span>
          <a href="logout.php">Logout</a>
          <a href="profile.php">Profile Page</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
      </nav>
    </header>

    <div class="hero">
      <div class="page-header">
        <h2>Our Staterooms & Suites</h2>
        <p>Discover the perfect accommodation for your cruise adventure</p>
      </div>

      <div class="rooms-container">
        <!-- Interior Cabin -->
        <div class="room-card">
          <div class="room-image">🛏️</div>
          <div class="room-header">
            <h3 class="room-title">Interior Cabins</h3>
            <div class="room-price">$89<span>/night</span></div>
          </div>
          <p class="room-description">
            Comfortable interior staterooms with modern amenities. Perfect for travelers who plan to spend most of their time exploring the ship and destinations.
          </p>
          <div class="room-features">
            <h4>✨ Features & Amenities:</h4>
            <ul>
              <li>Queen bed</li>
              <li>Private bathroom</li>
              <li>TV & WiFi</li>
              <li>Room service</li>
            </ul>
          </div>
          <?php if ($isLoggedIn): ?>
            <button class="book-button" onclick="window.location.href='index.php'">Book Now</button>
          <?php else: ?>
            <button class="book-button" onclick="alert('🔒 Please log in to book a room.\\n\\nYou will be redirected to the login page.'); window.location.href='login.php';" style="background: linear-gradient(135deg, #64748b, #475569);">Login to Book</button>
          <?php endif; ?>
        </div>

        <!-- Ocean View Cabin -->
        <div class="room-card">
          <div class="room-image">🌊</div>
          <div class="room-header">
            <h3 class="room-title">Ocean View Cabins</h3>
            <div class="room-price">$129<span>/night</span></div>
          </div>
          <p class="room-description">
            Enjoy stunning ocean views from your private window. Wake up to the beauty of the sea and natural light flooding your room.
          </p>
          <div class="room-features">
            <h4>✨ Features & Amenities:</h4>
            <ul>
              <li>Ocean view window</li>
              <li>Queen bed</li>
              <li>Sitting area</li>
              <li>Mini-fridge</li>
              <li>Room service</li>
            </ul>
          </div>
          <?php if ($isLoggedIn): ?>
            <button class="book-button" onclick="window.location.href='index.php'">Book Now</button>
          <?php else: ?>
            <button class="book-button" onclick="alert('🔒 Please log in to book a room.\\n\\nYou will be redirected to the login page.'); window.location.href='login.php';" style="background: linear-gradient(135deg, #64748b, #475569);">Login to Book</button>
          <?php endif; ?>
        </div>

        <!-- Balcony Cabin -->
        <div class="room-card">
          <div class="room-image">🏖️</div>
          <div class="room-header">
            <h3 class="room-title">Balcony Cabins</h3>
            <div class="room-price">$189<span>/night</span></div>
          </div>
          <p class="room-description">
            Private balcony with breathtaking ocean panoramas. Step outside and feel the sea breeze while enjoying your morning coffee or evening sunset.
          </p>
          <div class="room-features">
            <h4>✨ Features & Amenities:</h4>
            <ul>
              <li>Private balcony</li>
              <li>Queen bed</li>
              <li>Sitting area</li>
              <li>Mini-bar</li>
              <li>Priority boarding</li>
            </ul>
          </div>
          <?php if ($isLoggedIn): ?>
            <button class="book-button" onclick="window.location.href='index.php'">Book Now</button>
          <?php else: ?>
            <button class="book-button" onclick="alert('🔒 Please log in to book a room.\\n\\nYou will be redirected to the login page.'); window.location.href='login.php';" style="background: linear-gradient(135deg, #64748b, #475569);">Login to Book</button>
          <?php endif; ?>
        </div>

        <!-- Luxury Suite -->
        <div class="room-card">
          <div class="room-image">👑</div>
          <div class="room-header">
            <h3 class="room-title">Luxury Suites</h3>
            <div class="room-price">$299<span>/night</span></div>
          </div>
          <p class="room-description">
            Spacious suites with premium amenities and concierge service. Experience the ultimate in cruise luxury with unparalleled comfort and service.
          </p>
          <div class="room-features">
            <h4>✨ Features & Amenities:</h4>
            <ul>
              <li>Separate living room</li>
              <li>King bed</li>
              <li>Large balcony</li>
              <li>Butler service</li>
              <li>Priority everything</li>
            </ul>
          </div>
          <?php if ($isLoggedIn): ?>
            <button class="book-button" onclick="window.location.href='index.php'">Book Now</button>
          <?php else: ?>
            <button class="book-button" onclick="alert('🔒 Please log in to book a room.\\n\\nYou will be redirected to the login page.'); window.location.href='login.php';" style="background: linear-gradient(135deg, #64748b, #475569);">Login to Book</button>
          <?php endif; ?>
        </div>
      </div>

      <!-- Room Selection by Floor Section -->
      <?php if (!empty($roomsByFloor)): ?>
      <div class="room-selection-section">
        <div class="page-header">
          <h2>Select Your Room</h2>
          <p>Choose from our available staterooms organized by floor</p>
        </div>

        <?php 
        // Sort floors in descending order (floor 4 first)
        krsort($roomsByFloor);
        foreach ($roomsByFloor as $floor => $rooms): 
          $floorName = "Floor " . $floor;
        ?>
        <div class="floor-section">
          <div class="floor-header">
            <h3>📍 <?php echo $floorName; ?></h3>
          </div>
          <div class="floor-rooms">
            <?php foreach ($rooms as $room): 
              $typeInfo = getRoomTypeInfo($room['room_type']);
              $isAvailable = $room['status'] === 'available';
              $isBooked = $room['status'] === 'booked';
            ?>
            <div class="room-select-card <?php echo $isBooked ? 'booked' : ''; ?>" 
                 data-room-id="<?php echo $room['id']; ?>"
                 data-room-number="<?php echo htmlspecialchars($room['room_number']); ?>"
                 data-room-type="<?php echo htmlspecialchars($room['room_type']); ?>"
                 data-price="<?php echo $room['price_per_night']; ?>">
              <div class="room-status <?php echo $room['status']; ?>">
                <?php echo ucfirst($room['status']); ?>
              </div>
              <div class="room-number">Room <?php echo htmlspecialchars($room['room_number']); ?></div>
              <div class="room-type-badge" style="background: <?php echo $typeInfo['color']; ?>20; color: <?php echo $typeInfo['color']; ?>;">
                <?php echo $typeInfo['icon']; ?> <?php echo $typeInfo['name']; ?>
              </div>
              <div class="room-price-small">$<?php echo number_format($room['price_per_night'], 2); ?><span style="font-size: 0.9rem; color: #64748b;">/night</span></div>
              <p style="color: #64748b; font-size: 0.9rem; margin: 10px 0;">
                <?php echo htmlspecialchars($room['description']); ?>
              </p>
              <?php if ($isLoggedIn): ?>
                <?php if ($isAvailable): ?>
                  <button class="select-room-btn" onclick="selectRoom(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['room_number']); ?>', '<?php echo htmlspecialchars($room['room_type']); ?>', <?php echo $room['price_per_night']; ?>)">
                    Select Room
                  </button>
                <?php else: ?>
                  <button class="select-room-btn" disabled>Not Available</button>
                <?php endif; ?>
              <?php else: ?>
                <button class="select-room-btn" onclick="alert('🔒 Please log in to select a room.\\n\\nYou will be redirected to the login page.'); window.location.href='login.php';" style="background: linear-gradient(135deg, #64748b, #475569);">
                  Login to Select
                </button>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="room-selection-section">
        <div class="page-header">
          <h2>Room Selection</h2>
          <p style="color: #ef4444;">⚠️ Rooms database not set up yet. Please run rooms_setup.sql to create the rooms table.</p>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Booking Modal -->
  <div id="bookingModal" class="booking-modal">
    <div class="booking-modal-content">
      <span class="close-modal" onclick="closeBookingModal()">&times;</span>
      <h2>Complete Your Booking</h2>
      <form id="bookingForm" class="booking-form" onsubmit="submitBooking(event)">
        <input type="hidden" id="selectedRoomId" name="room_id">
        <input type="hidden" id="selectedRoomNumber" name="room_number">
        <input type="hidden" id="selectedRoomType" name="room_type">
        <input type="hidden" id="selectedPrice" name="price_per_night">
        
        <label>Room Number:</label>
        <input type="text" id="displayRoomNumber" readonly style="background: #f1f5f9;">
        
        <label>Room Type:</label>
        <input type="text" id="displayRoomType" readonly style="background: #f1f5f9;">
        
        <label>Price per Night:</label>
        <input type="text" id="displayPrice" readonly style="background: #f1f5f9;">
        
        <label for="cruise_name">Select Cruise:</label>
        <select id="cruise_name" name="cruise_name" required>
          <option value="">Select Cruise</option>
          <option value="7-Day Caribbean Paradise">7-Day Caribbean Paradise</option>
          <option value="10-Day Mediterranean Explorer">10-Day Mediterranean Explorer</option>
          <option value="14-Day Transatlantic Luxury">14-Day Transatlantic Luxury</option>
          <option value="5-Day Bahamas Getaway">5-Day Bahamas Getaway</option>
        </select>
        
        <label for="departure_date">Departure Date:</label>
        <input type="date" id="departure_date" name="departure_date" required>
        
        <label for="passengers">Number of Passengers:</label>
        <input type="number" id="passengers" name="passengers" min="1" max="4" value="2" required onchange="calculateTotal()" oninput="calculateTotal()">
        
        <label for="nights">Number of Nights:</label>
        <input type="number" id="nights" name="nights" min="1" value="7" required onchange="calculateTotal()" oninput="calculateTotal()">
        
        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
          <h4>Price Summary</h4>
          <p>Price per night: $<span id="pricePerNight">0</span></p>
          <p>Total nights: <span id="totalNights">7</span></p>
          <p>Passengers: <span id="totalPassengers">2</span></p>
          <p style="font-size: 1.2rem; font-weight: bold; color: #059669; margin-top: 10px;">
            Total: $<span id="totalPrice">0</span>
          </p>
        </div>
        
        <button type="submit">Confirm Booking</button>
      </form>
    </div>
  </div>

  <script>
    const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    let selectedRoom = null;

    function selectRoom(roomId, roomNumber, roomType, price) {
      if (!isLoggedIn) {
        alert('🔒 Please log in to select a room.');
        window.location.href = 'login.php';
        return;
      }

      selectedRoom = { roomId, roomNumber, roomType, price };
      
      // Update modal fields
      document.getElementById('selectedRoomId').value = roomId;
      document.getElementById('selectedRoomNumber').value = roomNumber;
      document.getElementById('selectedRoomType').value = roomType;
      document.getElementById('selectedPrice').value = price;
      
      document.getElementById('displayRoomNumber').value = roomNumber;
      document.getElementById('displayRoomType').value = roomType.charAt(0).toUpperCase() + roomType.slice(1).replace('_', ' ');
      document.getElementById('displayPrice').value = '$' + price.toFixed(2);
      document.getElementById('pricePerNight').textContent = price.toFixed(2);
      
      // Set minimum date to today
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('departure_date').setAttribute('min', today);
      
      calculateTotal();
      
      // Show modal
      document.getElementById('bookingModal').style.display = 'block';
    }

    function closeBookingModal() {
      document.getElementById('bookingModal').style.display = 'none';
      selectedRoom = null;
    }

    function calculateTotal() {
      const price = parseFloat(document.getElementById('selectedPrice').value) || 0;
      const nights = parseInt(document.getElementById('nights').value) || 7;
      const passengers = parseInt(document.getElementById('passengers').value) || 2;
      const total = price * nights * passengers;
      
      document.getElementById('totalPrice').textContent = total.toFixed(2);
      document.getElementById('totalNights').textContent = nights;
      document.getElementById('totalPassengers').textContent = passengers;
    }

    function submitBooking(event) {
      event.preventDefault();
      
      if (!selectedRoom) {
        alert('Please select a room first.');
        return;
      }

      const formData = new FormData(event.target);
      const submitBtn = event.target.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      
      submitBtn.textContent = 'Processing...';
      submitBtn.disabled = true;

      // Send booking data to server
      fetch('book_room.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('🎉 Booking confirmed! You will receive a confirmation email shortly.');
          closeBookingModal();
          location.reload(); // Reload to update room availability
        } else {
          alert('❌ Error: ' + (data.message || 'Booking failed. Please try again.'));
          submitBtn.textContent = originalText;
          submitBtn.disabled = false;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('❌ An error occurred. Please try again.');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('bookingModal');
      if (event.target == modal) {
        closeBookingModal();
      }
    }

    // Update total when passengers or nights change
    document.addEventListener('DOMContentLoaded', function() {
      const passengersInput = document.getElementById('passengers');
      const nightsInput = document.getElementById('nights');
      
      if (passengersInput) {
        passengersInput.addEventListener('change', calculateTotal);
      }
      if (nightsInput) {
        nightsInput.addEventListener('change', calculateTotal);
      }
    });
  </script>
</body>
</html>

