"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { X, Check } from "lucide-react"
import { type CabinType, cabinData } from "@/app/page"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

interface BookingModalProps {
  isOpen: boolean
  onClose: () => void
  cabinType: CabinType | null
}

const cruiseOptions = [
  "7-Day Caribbean Paradise",
  "10-Day Mediterranean Explorer",
  "14-Day Transatlantic Luxury",
  "5-Day Bahamas Getaway",
]

export function BookingModal({ isOpen, onClose, cabinType }: BookingModalProps) {
  const [showBookingForm, setShowBookingForm] = useState(false)
  const [passengers, setPassengers] = useState(2)
  const [nights, setNights] = useState(7)
  const [formData, setFormData] = useState({
    cruise: "",
    departureDate: "",
    name: "",
    email: "",
    phone: "",
  })

  const cabin = cabinType ? cabinData[cabinType] : null
  const totalPrice = cabin ? cabin.price * nights * passengers : 0

  useEffect(() => {
    if (!isOpen) {
      setShowBookingForm(false)
      setFormData({ cruise: "", departureDate: "", name: "", email: "", phone: "" })
    }
  }, [isOpen])

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    alert("🎉 Booking confirmed! You will receive a confirmation email shortly.")
    onClose()
  }

  if (!isOpen || !cabin) return null

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" onClick={onClose}>
      <div
        className="bg-white rounded-3xl shadow-2xl w-[90%] max-w-2xl max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in duration-300"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="sticky top-0 bg-white border-b border-slate-200 px-8 py-6 flex items-center justify-between rounded-t-3xl">
          <h2 className="text-3xl font-bold text-blue-900">{cabin.title}</h2>
          <button onClick={onClose} className="text-slate-400 hover:text-blue-900 transition-colors">
            <X className="w-7 h-7" />
          </button>
        </div>

        <div className="p-8">
          {!showBookingForm ? (
            <div className="space-y-6">
              <div className="relative w-full h-64 bg-gradient-to-br from-sky-100 to-blue-200 rounded-2xl overflow-hidden">
                <img
                  src={`/.jpg?height=400&width=600&query=${encodeURIComponent(cabin.title + " cruise ship cabin")}`}
                  alt={cabin.title}
                  className="w-full h-full object-cover"
                />
              </div>

              <div className="bg-blue-50 rounded-2xl p-6 border-l-4 border-blue-600">
                <h3 className="text-2xl font-bold text-blue-900 mb-2">From ${cabin.price}/night per person</h3>
                <p className="text-slate-600 text-lg">{cabin.description}</p>
              </div>

              <div>
                <h4 className="text-xl font-bold text-blue-900 mb-4 flex items-center gap-2">
                  ✨ Features & Amenities
                </h4>
                <ul className="space-y-3">
                  {cabin.features.map((feature, index) => (
                    <li key={index} className="flex items-center gap-3 text-slate-700">
                      <Check className="w-5 h-5 text-emerald-500 flex-shrink-0" />
                      <span>{feature}</span>
                    </li>
                  ))}
                </ul>
              </div>

              <Button
                onClick={() => setShowBookingForm(true)}
                className="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white py-6 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300"
              >
                Book This Cabin Type
              </Button>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-6">
              <h3 className="text-2xl font-bold text-blue-900 mb-6">Complete Your Booking</h3>

              <div className="space-y-2">
                <Label htmlFor="cruise" className="text-base font-semibold">
                  Select Cruise *
                </Label>
                <select
                  id="cruise"
                  required
                  value={formData.cruise}
                  onChange={(e) => setFormData({ ...formData, cruise: e.target.value })}
                  className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all outline-none"
                >
                  <option value="">Select a cruise</option>
                  {cruiseOptions.map((cruise) => (
                    <option key={cruise} value={cruise}>
                      {cruise}
                    </option>
                  ))}
                </select>
              </div>

              <div className="space-y-2">
                <Label htmlFor="departureDate" className="text-base font-semibold">
                  Departure Date *
                </Label>
                <Input
                  id="departureDate"
                  type="date"
                  required
                  value={formData.departureDate}
                  onChange={(e) => setFormData({ ...formData, departureDate: e.target.value })}
                  min={new Date().toISOString().split("T")[0]}
                  className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="passengers" className="text-base font-semibold">
                  Number of Passengers *
                </Label>
                <Input
                  id="passengers"
                  type="number"
                  min="1"
                  max="4"
                  required
                  value={passengers}
                  onChange={(e) => setPassengers(Number.parseInt(e.target.value) || 1)}
                  className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="name" className="text-base font-semibold">
                  Full Name *
                </Label>
                <Input
                  id="name"
                  type="text"
                  required
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  placeholder="John Doe"
                  className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="email" className="text-base font-semibold">
                  Email Address *
                </Label>
                <Input
                  id="email"
                  type="email"
                  required
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  placeholder="john@example.com"
                  className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="phone" className="text-base font-semibold">
                  Phone Number *
                </Label>
                <Input
                  id="phone"
                  type="tel"
                  required
                  value={formData.phone}
                  onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                  placeholder="+1 (555) 123-4567"
                  className="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all"
                />
              </div>

              <div className="bg-slate-50 rounded-2xl p-6 border-l-4 border-blue-600 space-y-3">
                <h4 className="text-lg font-bold text-blue-900 mb-4">Price Summary</h4>
                <div className="flex justify-between text-slate-700">
                  <span>Price per night:</span>
                  <span className="font-semibold">${cabin.price}</span>
                </div>
                <div className="flex justify-between text-slate-700">
                  <span>Total nights:</span>
                  <span className="font-semibold">{nights}</span>
                </div>
                <div className="flex justify-between text-slate-700">
                  <span>Passengers:</span>
                  <span className="font-semibold">{passengers}</span>
                </div>
                <div className="flex justify-between text-xl font-bold text-blue-900 pt-3 border-t-2 border-slate-200">
                  <span>Total:</span>
                  <span>${totalPrice.toLocaleString()}</span>
                </div>
              </div>

              <Button
                type="submit"
                className="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-6 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300"
              >
                Book Now
              </Button>
            </form>
          )}
        </div>
      </div>
    </div>
  )
}
