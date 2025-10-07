"use client"

import type { CabinType } from "@/app/page"

interface CruiseShipProps {
  onDeckClick: (cabinType: CabinType) => void
}

export function CruiseShip({ onDeckClick }: CruiseShipProps) {
  return (
    <div className="flex justify-center my-8 p-6 bg-gradient-to-b from-sky-300 to-sky-600 rounded-2xl shadow-inner">
      <svg className="max-w-full h-auto drop-shadow-2xl" width="700" height="380" viewBox="0 0 700 380">
        <defs>
          <linearGradient id="oceanGradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" style={{ stopColor: "#0ea5e9", stopOpacity: 0.3 }} />
            <stop offset="100%" style={{ stopColor: "#0284c7", stopOpacity: 0.6 }} />
          </linearGradient>
          <linearGradient id="hullGradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" style={{ stopColor: "#1e40af" }} />
            <stop offset="100%" style={{ stopColor: "#1e3a8a" }} />
          </linearGradient>
          <linearGradient id="deckGradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" style={{ stopColor: "#60a5fa" }} />
            <stop offset="100%" style={{ stopColor: "#2563eb" }} />
          </linearGradient>
          <filter id="glow">
            <feGaussianBlur stdDeviation="3" result="coloredBlur" />
            <feMerge>
              <feMergeNode in="coloredBlur" />
              <feMergeNode in="SourceGraphic" />
            </feMerge>
          </filter>
        </defs>

        {/* Ocean */}
        <rect x="0" y="250" width="700" height="140" fill="url(#oceanGradient)" />
        <path d="M0 270 Q175 260 350 270 T700 270 L700 390 L0 390 Z" fill="#0284c7" opacity="0.4">
          <animateTransform
            attributeName="transform"
            type="translate"
            values="0,0; -60,0; 0,0"
            dur="5s"
            repeatCount="indefinite"
          />
        </path>
        <path d="M0 280 Q175 270 350 280 T700 280 L700 390 L0 390 Z" fill="#38bdf8" opacity="0.25">
          <animateTransform
            attributeName="transform"
            type="translate"
            values="0,0; 60,0; 0,0"
            dur="6s"
            repeatCount="indefinite"
          />
        </path>

        {/* Hull */}
        <path
          d="M80 260 Q350 200 620 260 L560 300 L140 300 Z"
          fill="url(#hullGradient)"
          stroke="#0f172a"
          strokeWidth="3"
        />

        {/* Clickable Decks */}
        <g
          className="cursor-pointer transition-all duration-300 hover:scale-105 hover:brightness-110"
          onClick={() => onDeckClick("interior")}
        >
          <rect x="140" y="220" width="420" height="40" fill="#3b82f6" stroke="#1e40af" strokeWidth="3" rx="10" />
          <text x="350" y="245" textAnchor="middle" fill="white" fontSize="16" fontWeight="bold">
            Interior Cabins - From $89/night
          </text>
        </g>

        <g
          className="cursor-pointer transition-all duration-300 hover:scale-105 hover:brightness-110"
          onClick={() => onDeckClick("ocean")}
        >
          <rect x="160" y="180" width="380" height="40" fill="#0ea5e9" stroke="#0284c7" strokeWidth="3" rx="10" />
          <text x="350" y="205" textAnchor="middle" fill="white" fontSize="16" fontWeight="bold">
            Ocean View - From $129/night
          </text>
        </g>

        <g
          className="cursor-pointer transition-all duration-300 hover:scale-105 hover:brightness-110"
          onClick={() => onDeckClick("balcony")}
        >
          <rect x="180" y="140" width="340" height="40" fill="#06b6d4" stroke="#0891b2" strokeWidth="3" rx="10" />
          <text x="350" y="165" textAnchor="middle" fill="white" fontSize="16" fontWeight="bold">
            Balcony Cabins - From $189/night
          </text>
        </g>

        <g
          className="cursor-pointer transition-all duration-300 hover:scale-105 hover:brightness-110"
          onClick={() => onDeckClick("suite")}
        >
          <rect x="200" y="100" width="300" height="40" fill="#0891b2" stroke="#0e7490" strokeWidth="3" rx="10" />
          <text x="350" y="125" textAnchor="middle" fill="white" fontSize="16" fontWeight="bold">
            Luxury Suites - From $299/night
          </text>
        </g>

        {/* Bridge */}
        <rect
          x="260"
          y="60"
          width="180"
          height="40"
          fill="url(#deckGradient)"
          stroke="#1e3a8a"
          strokeWidth="3"
          rx="10"
        />
        <rect x="290" y="35" width="120" height="25" fill="#1e3a8a" stroke="#1e40af" strokeWidth="2" rx="6" />

        {/* Smokestacks */}
        <rect x="310" y="20" width="16" height="20" fill="#374151" rx="3" />
        <rect x="340" y="20" width="16" height="20" fill="#374151" rx="3" />
        <rect x="370" y="20" width="16" height="20" fill="#374151" rx="3" />
        <circle cx="318" cy="15" r="4" fill="#9ca3af" opacity="0.7">
          <animate attributeName="cy" values="15;-15;-40" dur="3s" repeatCount="indefinite" />
          <animate attributeName="opacity" values="0.7;0.4;0" dur="3s" repeatCount="indefinite" />
        </circle>
        <circle cx="348" cy="15" r="4" fill="#9ca3af" opacity="0.7">
          <animate attributeName="cy" values="15;-15;-40" dur="3.5s" repeatCount="indefinite" />
          <animate attributeName="opacity" values="0.7;0.4;0" dur="3.5s" repeatCount="indefinite" />
        </circle>
        <circle cx="378" cy="15" r="4" fill="#9ca3af" opacity="0.7">
          <animate attributeName="cy" values="15;-15;-40" dur="4s" repeatCount="indefinite" />
          <animate attributeName="opacity" values="0.7;0.4;0" dur="4s" repeatCount="indefinite" />
        </circle>

        {/* Portholes */}
        <g>
          {[160, 200, 240, 280, 320, 360, 400, 440, 480].map((x) => (
            <circle key={x} cx={x} cy="275" r="6" fill="#facc15" filter="url(#glow)" />
          ))}
        </g>
      </svg>
    </div>
  )
}
