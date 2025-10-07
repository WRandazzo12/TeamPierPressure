"use client"

import { useState } from "react"
import { CruiseShip } from "@/components/cruise-ship"
import { BookingModal } from "@/components/booking-modal"

export type CabinType = "interior" | "ocean" | "balcony" | "suite"

export interface CabinData {
  title: string
  price: number
  description: string
  features: string[]
  color: string
}

export const cabinData: Record<CabinType, CabinData> = {
  interior: {
    title: "Interior Cabins",
    price: 89,
    description: "Comfortable interior staterooms with modern amenities",
    features: ["Queen bed", "Private bathroom", "TV & WiFi", "Room service"],
    color: "#3b82f6",
  },
  ocean: {
    title: "Ocean View Cabins",
    price: 129,
    description: "Enjoy stunning ocean views from your private window",
    features: ["Ocean view window", "Queen bed", "Sitting area", "Mini-fridge", "Room service"],
    color: "#0ea5e9",
  },
  balcony: {
    title: "Balcony Cabins",
    price: 189,
    description: "Private balcony with breathtaking ocean panoramas",
    features: ["Private balcony", "Queen bed", "Sitting area", "Mini-bar", "Priority boarding"],
    color: "#06b6d4",
  },
  suite: {
    title: "Luxury Suites",
    price: 299,
    description: "Spacious suites with premium amenities and concierge service",
    features: ["Separate living room", "King bed", "Large balcony", "Butler service", "Priority everything"],
    color: "#0891b2",
  },
}

export default function Home() {
  const [selectedCabin, setSelectedCabin] = useState<CabinType | null>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)

  const handleDeckClick = (cabinType: CabinType) => {
    setSelectedCabin(cabinType)
    setIsModalOpen(true)
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-sky-500 to-sky-700">
      <div className="container mx-auto px-4 py-6">
        <header className="flex items-center justify-between bg-white/95 rounded-2xl shadow-xl px-8 py-6 mb-8">
          <h1 className="text-4xl font-bold text-blue-900">🚢 Ocean Cruises</h1>
          <nav className="flex gap-4">
            <a
              href="#"
              className="px-6 py-3 text-blue-900 font-semibold rounded-lg hover:bg-blue-900 hover:text-white transition-all duration-300 hover:-translate-y-0.5"
            >
              Login
            </a>
            <a
              href="#"
              className="px-6 py-3 text-blue-900 font-semibold rounded-lg hover:bg-blue-900 hover:text-white transition-all duration-300 hover:-translate-y-0.5"
            >
              Register
            </a>
          </nav>
        </header>

        <div className="bg-white/95 rounded-3xl shadow-2xl p-10 text-center">
          <h2 className="text-3xl font-bold text-blue-900 mb-8">
            Click on the Ship Levels to Explore Cabins & Book Your Dream Cruise
          </h2>

          <CruiseShip onDeckClick={handleDeckClick} />

          <p className="mt-8 text-sky-900 font-semibold text-lg">
            🎯 Click on any deck level to explore cabin options and pricing!
          </p>
        </div>
      </div>

      <BookingModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} cabinType={selectedCabin} />
    </div>
  )
}
