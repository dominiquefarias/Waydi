import { MapPin, Maximize2 } from 'lucide-react';
import type { Day } from '../data/trip';
import { categories } from '../lib/categories';

interface MapPanelProps {
  day: Day;
  selectedActivity: number;
  onSelectActivity: (i: number) => void;
}

export default function MapPanel({ day, selectedActivity, onSelectActivity }: MapPanelProps) {
  return (
    <div className="rounded-[26px] overflow-hidden soft relative" style={{ height: '244px' }}>
      {/* SVG map */}
      <svg
        width="100%"
        height="100%"
        viewBox="0 0 400 244"
        preserveAspectRatio="xMidYMid slice"
        xmlns="http://www.w3.org/2000/svg"
      >
        {/* background */}
        <rect width="400" height="244" fill="#EDE9F8" />

        {/* park blobs */}
        <ellipse cx="80" cy="60" rx="55" ry="38" fill="#DCEFE2" opacity="0.9" />
        <ellipse cx="310" cy="180" rx="48" ry="32" fill="#DCEFE2" opacity="0.85" />
        <ellipse cx="200" cy="120" rx="30" ry="22" fill="#DCEFE2" opacity="0.6" />

        {/* lavender circle feature */}
        <circle cx="160" cy="85" r="28" fill="#DCD0FF" opacity="0.45" />

        {/* river */}
        <path
          d="M 0 155 C 80 140, 140 165, 220 148 C 290 132, 350 158, 400 145"
          fill="none"
          stroke="#CFE0F5"
          strokeWidth="18"
          strokeLinecap="round"
          opacity="0.9"
        />

        {/* major streets */}
        <line x1="0" y1="80" x2="400" y2="80" stroke="white" strokeWidth="3" opacity="0.8" />
        <line x1="0" y1="130" x2="400" y2="130" stroke="white" strokeWidth="2.5" opacity="0.7" />
        <line x1="0" y1="200" x2="400" y2="200" stroke="white" strokeWidth="2" opacity="0.6" />
        <line x1="100" y1="0" x2="100" y2="244" stroke="white" strokeWidth="2.5" opacity="0.7" />
        <line x1="220" y1="0" x2="220" y2="244" stroke="white" strokeWidth="3" opacity="0.8" />
        <line x1="340" y1="0" x2="340" y2="244" stroke="white" strokeWidth="2" opacity="0.6" />
        {/* diagonal */}
        <line x1="0" y1="244" x2="250" y2="0" stroke="white" strokeWidth="2" opacity="0.5" />

        {/* secondary streets */}
        <line x1="0" y1="50" x2="400" y2="50" stroke="white" strokeWidth="1" opacity="0.35" />
        <line x1="0" y1="105" x2="400" y2="105" stroke="white" strokeWidth="1" opacity="0.3" />
        <line x1="160" y1="0" x2="160" y2="244" stroke="white" strokeWidth="1" opacity="0.35" />
        <line x1="280" y1="0" x2="280" y2="244" stroke="white" strokeWidth="1" opacity="0.3" />
      </svg>

      {/* pins */}
      {day.pins.map(([x, y], i) => {
        const cat = categories[day.activities[i]?.category];
        if (!cat) return null;
        const isSelected = i === selectedActivity;

        return (
          <button
            key={`${i}-${day.label}`}
            onClick={() => onSelectActivity(i)}
            className="pin-pop absolute flex flex-col items-center transition-transform duration-200 hover:scale-110"
            style={{
              left: `${x}%`,
              top: `${y}%`,
              transform: 'translateX(-50%) translateY(-100%)',
              animationDelay: `${i * 60}ms`,
              zIndex: isSelected ? 30 : 10,
            }}
            aria-label={day.activities[i]?.name}
          >
            <div
              className="flex items-center justify-center rounded-full text-white font-bold transition-all duration-200"
              style={{
                backgroundColor: cat.pinColor,
                width: isSelected ? '36px' : '28px',
                height: isSelected ? '36px' : '28px',
                fontSize: isSelected ? '13px' : '11px',
                boxShadow: isSelected
                  ? `0 0 0 4px white, 0 4px 12px rgba(0,0,0,0.15)`
                  : `0 2px 6px rgba(0,0,0,0.12)`,
              }}
            >
              {i + 1}
            </div>
            {/* pin triangle */}
            <div
              className="w-0 h-0"
              style={{
                borderLeft: '4px solid transparent',
                borderRight: '4px solid transparent',
                borderTop: `6px solid ${cat.pinColor}`,
                marginTop: '-1px',
              }}
            />
          </button>
        );
      })}

      {/* top-left label */}
      <div className="absolute top-3 left-3 flex items-center gap-1.5 bg-white/90 backdrop-blur rounded-full px-3 py-1.5 text-xs font-semibold text-ink shadow-sm">
        <MapPin size={11} className="text-lila-deep" />
        París
      </div>

      {/* bottom-right button */}
      <button className="absolute bottom-3 right-3 bg-white/90 backdrop-blur rounded-xl px-3 py-1.5 text-xs font-semibold text-ink flex items-center gap-1.5 shadow-sm hover:bg-white transition-colors duration-200">
        <Maximize2 size={11} />
        Ver mapa
      </button>
    </div>
  );
}
