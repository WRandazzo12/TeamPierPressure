// Enhanced JavaScript for better interactivity
const $ = window.jQuery // Declare the $ variable

$(document).ready(() => {
  // Store current cabin type for room loading (accessible in all functions)
  let currentCabinType = null

  // Cabin data
  const cabinData = {
    interior: {
      title: "Interior Cabins",
      price: 89,
      description: "Comfortable interior staterooms with modern amenities",
      features: ["Queen bed", "Private bathroom", "TV & WiFi", "Room service"],
      image: "/cruise-ship-cabin-interior.png",
    },
    ocean: {
      title: "Ocean View Cabins",
      price: 129,
      description: "Enjoy stunning ocean views from your private window",
      features: ["Ocean view window", "Queen bed", "Sitting area", "Mini-fridge", "Room service"],
      image: "/cruise-ship-ocean-view-cabin-with-window.jpg",
    },
    balcony: {
      title: "Balcony Cabins",
      price: 189,
      description: "Private balcony with breathtaking ocean panoramas",
      features: ["Private balcony", "Queen bed", "Sitting area", "Mini-bar", "Priority boarding"],
      image: "/cruise-ship-balcony-cabin-with-ocean-view.jpg",
    },
    suite: {
      title: "Luxury Suites",
      price: 299,
      description: "Spacious suites with premium amenities and concierge service",
      features: ["Separate living room", "King bed", "Large balcony", "Butler service", "Priority everything"],
      image: "/luxury-cruise-ship-suite-with-balcony.jpg",
    },
  }

  // Deck click handler
  $(".clickable-deck").on("click", function () {
    // Check if user is logged in
    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
      alert("🔒 Please log in to book a room.\n\nYou'll be redirected to the login page.")
      window.location.href = "login.php"
      return
    }

    const deckType = $(this).data("deck")
    const cabin = cabinData[deckType]

    $(this).addClass("clicked")
    setTimeout(() => $(this).removeClass("clicked"), 200)

    $("#modalTitle").text(cabin.title)

    const cabinHTML = `
      <div class="cabin-showcase">
        <img src="${cabin.image}" alt="${cabin.title}" style="width: 100%; border-radius: 10px; margin-bottom: 20px;">
        <h3 style="color: #1e40af; margin-bottom: 15px;">From $${cabin.price}/night</h3>
        <p style="margin-bottom: 20px; color: #64748b; font-size: 16px;">${cabin.description}</p>
        <div class="features-list" style="margin-bottom: 25px;">
          <h4 style="color: #1e40af; margin-bottom: 10px;">✨ Features & Amenities:</h4>
          <ul style="list-style: none; padding: 0;">
            ${cabin.features.map((f) => `<li style="padding: 5px 0; color: #475569;"><span style="color: #10b981; margin-right: 8px;">✓</span>${f}</li>`).join("")}
          </ul>
        </div>
        <div id="initialAvailability" style="background: #f0f9ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0284c7;">
          <h4 style="margin: 0 0 10px 0; color: #0284c7;">Availability</h4>
          <p style="margin: 5px 0; color: #0c4a6e;"><strong>Total Floors:</strong> <span id="initialTotalFloors">Loading...</span></p>
          <p style="margin: 5px 0; color: #0c4a6e;"><strong>Available Rooms:</strong> <span id="initialAvailableRooms">Loading...</span></p>
        </div>
        <button onclick="showBookingForm('${deckType}')" 
          style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
          Book This Cabin Type
        </button>
      </div>
    `
    $("#cabinOptions").html(cabinHTML)
    $("#bookingForm").hide()
    $("#bookingModal").fadeIn(300)
    $("#pricePerNight").text(cabin.price)
    updateTotalPrice()
    // Load availability data for initial view (always fresh from database)
    loadInitialAvailability()
    // Store cabin type for potential booking
    currentCabinType = deckType
  })

  // Booking form
  window.showBookingForm = (cabinType) => {
    // Reset form first to ensure clean state
    $("#bookingForm")[0].reset()
    $("#roomSelect").html('<option value="">Loading rooms...</option>')
    $("#selectedRoomId").val("")
    $("#selectedRoomNumber").val("")
    $("#selectedRoomType").val("")
    $("#selectedPricePerNight").val("")
    $("#pricePerNight").text("0")
    $("#passengers").val(2)
    $("#nights").val(7)
    updateTotalPrice()
    
    currentCabinType = cabinType
    $("#cabinOptions").slideUp(300)
    setTimeout(() => {
      $("#bookingForm").slideDown(300)
      populateCruiseOptions()
      loadAvailabilityData(cabinType)
      loadRoomsByType(cabinType)
    }, 300)
  }

  // Load rooms by type for dropdown
  function loadRoomsByType(roomType) {
    const roomSelect = $("#roomSelect")
    roomSelect.html('<option value="">Loading rooms...</option>')
    
    fetch(`get_rooms_by_type.php?room_type=${encodeURIComponent(roomType)}`)
      .then(response => response.json())
      .then(data => {
        if (data.success && data.rooms.length > 0) {
          roomSelect.empty().append('<option value="">Select a Room</option>')
          data.rooms.forEach(room => {
            const optionText = `Room ${room.room_number} ($${room.price_per_night}/night)`
            roomSelect.append(`<option value="${room.id}" 
              data-room-id="${room.id}"
              data-room-number="${room.room_number}"
              data-room-type="${room.room_type}"
              data-price="${room.price_per_night}">${optionText}</option>`)
          })
        } else {
          roomSelect.html('<option value="">No rooms available</option>')
        }
      })
      .catch(error => {
        console.error('Error loading rooms:', error)
        roomSelect.html('<option value="">Error loading rooms</option>')
      })
  }

  // Handle room selection change (use off() first to prevent duplicates)
  $("#roomSelect").off("change").on("change", function() {
    const selectedOption = $(this).find(":selected")
    if (selectedOption.val()) {
      const roomId = selectedOption.data("room-id")
      const roomNumber = selectedOption.data("room-number")
      const roomType = selectedOption.data("room-type")
      const price = parseFloat(selectedOption.data("price"))
      
      // Update hidden fields
      $("#selectedRoomId").val(roomId)
      $("#selectedRoomNumber").val(roomNumber)
      $("#selectedRoomType").val(roomType)
      $("#selectedPricePerNight").val(price)
      
      // Update price display
      $("#pricePerNight").text(price.toFixed(2))
      updateTotalPrice()
    } else {
      // Clear hidden fields
      $("#selectedRoomId").val("")
      $("#selectedRoomNumber").val("")
      $("#selectedRoomType").val("")
      $("#selectedPricePerNight").val("")
    }
  })

  // Load availability data for initial modal view
  function loadInitialAvailability() {
    fetch('get_room_availability.php')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          $("#initialTotalFloors").text(data.total_floors)
          $("#initialAvailableRooms").text(data.available_rooms)
        } else {
          $("#initialTotalFloors").text('0')
          $("#initialAvailableRooms").text('0')
        }
      })
      .catch(error => {
        console.error('Error loading availability:', error)
        $("#initialTotalFloors").text('-')
        $("#initialAvailableRooms").text('-')
      })
  }

  // Load availability data from server
  function loadAvailabilityData(cabinType) {
    fetch('get_room_availability.php')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update total floors
          $("#totalFloors").text(data.total_floors)
          
          // Update available rooms
          $("#availableRooms").text(data.available_rooms)
          
          // Update rooms by type info
          const typeMap = {
            'interior': 'Interior',
            'ocean': 'Ocean View',
            'balcony': 'Balcony',
            'suite': 'Luxury Suite'
          }
          
          let typeInfo = []
          if (data.rooms_by_type && cabinType && data.rooms_by_type[cabinType]) {
            const typeData = data.rooms_by_type[cabinType]
            typeInfo.push(`${typeMap[cabinType] || cabinType}: ${typeData.available} available`)
          }
          
          // Add all types info
          if (data.rooms_by_type) {
            const allTypes = Object.keys(data.rooms_by_type).map(type => {
              const typeData = data.rooms_by_type[type]
              return `${typeMap[type] || type}: ${typeData.available}/${typeData.total} available`
            })
            $("#roomsByTypeInfo").html(`<strong>By Type:</strong> ${allTypes.join(' | ')}`)
          } else {
            $("#roomsByTypeInfo").text('')
          }
        } else {
          $("#totalFloors").text('0')
          $("#availableRooms").text('0')
          $("#roomsByTypeInfo").text('Data not available')
        }
      })
      .catch(error => {
        console.error('Error loading availability:', error)
        $("#totalFloors").text('-')
        $("#availableRooms").text('-')
        $("#roomsByTypeInfo").text('Error loading data')
      })
  }

  function populateCruiseOptions() {
    const cruises = [
      "7-Day Caribbean Paradise",
      "10-Day Mediterranean Explorer",
      "14-Day Transatlantic Luxury",
      "5-Day Bahamas Getaway",
    ]
    const select = $("#cruiseSelect")
    select.empty().append('<option value="">Select Cruise</option>')
    cruises.forEach((cruise) => select.append(`<option value="${cruise}">${cruise}</option>`))
  }

  function updateTotalPrice() {
    const pricePerNight = parseFloat($("#pricePerNight").text()) || 0
    const nights = parseInt($("#nights").val()) || 7
    const passengers = parseInt($("#passengers").val()) || 2
    const total = pricePerNight * nights * passengers
    $("#totalPrice").text(total.toLocaleString())
    $("#totalNights").text(nights)
    $("#totalPassengers").text(passengers)
  }

  // Update total price handlers (use off() first to prevent duplicates)
  $("#passengers, #nights").off("input change").on("input change", updateTotalPrice)

  // Form submission handler (use off() first to prevent duplicates)
  $("#bookingForm").off("submit").on("submit", function (e) {
    e.preventDefault()
    
    // Validate room selection
    const roomId = $("#selectedRoomId").val()
    const roomNumber = $("#selectedRoomNumber").val()
    if (!roomId || !roomNumber) {
      alert("❌ Please select a room to continue.")
      return
    }
    
    const submitBtn = $(this).find('button[type="submit"]')
    const originalText = submitBtn.text()
    submitBtn.text("Processing...").prop("disabled", true)
    
    // Prepare form data
    const formData = new FormData()
    formData.append('room_id', $("#selectedRoomId").val())
    formData.append('room_number', $("#selectedRoomNumber").val())
    formData.append('room_type', $("#selectedRoomType").val())
    formData.append('price_per_night', $("#selectedPricePerNight").val())
    formData.append('cruise_name', $("#cruiseSelect").val())
    formData.append('departure_date', $("#departureDate").val())
    formData.append('passengers', $("#passengers").val())
    formData.append('nights', $("#nights").val() || 7)
    
    // Send booking to server
    fetch('book_room.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("🎉 Booking confirmed! You will receive a confirmation email shortly.")
        
        // Reload availability data immediately
        if (currentCabinType) {
          // Reload rooms dropdown to remove the booked room
          loadRoomsByType(currentCabinType)
          // Reload availability data
          loadAvailabilityData(currentCabinType)
        }
        // Also reload initial availability
        loadInitialAvailability()
        
        // Clear the form and reset
        this.reset()
        $("#roomSelect").html('<option value="">Select a Room</option>')
        $("#selectedRoomId").val("")
        $("#selectedRoomNumber").val("")
        $("#selectedRoomType").val("")
        $("#selectedPricePerNight").val("")
        $("#pricePerNight").text("0")
        updateTotalPrice()
        
        // Close modal after a short delay to show the update
        setTimeout(() => {
          closeBookingModal()
        }, 500)
      } else {
        alert("❌ Error: " + (data.message || 'Booking failed. Please try again.'))
      }
      submitBtn.text(originalText).prop("disabled", false)
    })
    .catch(error => {
      console.error('Error:', error)
      alert("❌ An error occurred. Please try again.")
      submitBtn.text(originalText).prop("disabled", false)
    })
  })

  // Close modal handler
  $(".close").on("click", function() {
    closeBookingModal()
  })
  
  $(".modal").on("click", function (e) {
    if (e.target === this) {
      closeBookingModal()
    }
  })

  // Function to properly close and reset the booking modal
  function closeBookingModal() {
    $("#bookingModal").fadeOut(300)
    // Reset form completely
    setTimeout(() => {
      $("#bookingForm")[0].reset()
      $("#bookingForm").hide()
      $("#cabinOptions").show()
      $("#roomSelect").html('<option value="">Select a Room</option>')
      $("#selectedRoomId").val("")
      $("#selectedRoomNumber").val("")
      $("#selectedRoomType").val("")
      $("#selectedPricePerNight").val("")
      $("#pricePerNight").text("0")
      $("#totalPrice").text("0")
      $("#totalNights").text("7")
      $("#totalPassengers").text("2")
      $("#passengers").val(2)
      $("#nights").val(7)
      currentCabinType = null
    }, 300)
  }

  // Click animation CSS
  $("<style>").text(`
    .clicked { animation: clickPulse 0.2s ease; }
    @keyframes clickPulse {
      0% { transform: scale(1); }
      50% { transform: scale(0.95); }
      100% { transform: scale(1.05); }
    }
  `).appendTo("head")

  // ===== Easter Egg: Blow up ship =====
  $("#bullseye").on("click", () => {
    const ship = document.querySelector(".cruise-ship")
    const message = document.getElementById("explosion-message")

    ship.style.transition = "all 0.8s ease"
    ship.style.transform = "scale(1.5) rotate(20deg)"
    ship.style.opacity = "0"

    setTimeout(() => {
      ship.style.display = "none"
      message.style.display = "block"
    }, 800)
  })
})
