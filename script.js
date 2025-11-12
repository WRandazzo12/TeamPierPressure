// Enhanced JavaScript for better interactivity
const $ = window.jQuery // Declare the $ variable

$(document).ready(() => {
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
  })

  // Booking form
  window.showBookingForm = (cabinType) => {
    $("#cabinOptions").slideUp(300)
    setTimeout(() => {
      $("#bookingForm").slideDown(300)
      populateCruiseOptions()
    }, 300)
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
    const pricePerNight = parseInt($("#pricePerNight").text()) || 0
    const nights = parseInt($("#totalNights").text()) || 7
    const passengers = parseInt($("#passengers").val()) || 2
    const total = pricePerNight * nights * passengers
    $("#totalPrice").text(total.toLocaleString())
    $("#totalPassengers").text(passengers)
  }

  $("#passengers").on("input", updateTotalPrice)

  $("#bookingForm").on("submit", function (e) {
    e.preventDefault()
    const submitBtn = $(this).find('button[type="submit"]')
    const originalText = submitBtn.text()
    submitBtn.text("Processing...").prop("disabled", true)
    setTimeout(() => {
      alert("🎉 Booking confirmed! You will receive a confirmation email shortly.")
      $("#bookingModal").fadeOut(300)
      submitBtn.text(originalText).prop("disabled", false)
      this.reset()
    }, 2000)
  })

  $(".close, .modal").on("click", function (e) {
    if (e.target === this) $("#bookingModal").fadeOut(300)
  })

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
