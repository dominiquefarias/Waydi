import { MapPinIcon, ExpandIcon } from './Icons';
import { CAT } from '../lib/categories';
import type { Day } from '../data/trip';

interface MapPanelProps {
  day: Day;
  selected: number;
  onSelect: (i: number) => void;
  city: string;
}

export function MapPanel({ day, selected, onSelect, city }: MapPanelProps) {
  return (
    <div className="relative rounded-[26px] overflow-hidden soft border border-white">
      <div className="absolute inset-0 bg-[#EDE9F8]" />

      <svg
        viewBox="0 0 100 78"
        className="relative w-full block"
        preserveAspectRatio="xMidYMid slice"
        style={{ height: 244 }}
        aria-hidden="true"
      >
        <rect x="6" y="8" width="22" height="16" rx="5" fill="#DCEFE2" />
        <rect x="70" y="48" width="26" height="22" rx="6" fill="#DCEFE2" />
        <circle cx="80" cy="16" r="9" fill="#E7DEF8" />
        <path d="M-4 52 C 20 40, 40 64, 62 50 S 96 40, 110 52 L110 78 L-4 78 Z" fill="#CFE0F5" opacity="0.85" />
        <path d="M-4 52 C 20 40, 40 64, 62 50 S 96 40, 110 52" fill="none" stroke="#BCD2EF" strokeWidth="0.8" />
        <g stroke="#FFFFFF" strokeWidth="2.4" strokeLinecap="round" opacity="0.9">
          <path d="M0 28 H100" /><path d="M0 38 H100" />
          <path d="M30 0 V78" /><path d="M58 0 V78" /><path d="M82 0 V78" />
          <path d="M8 4 L92 70" />
        </g>
        <g stroke="#EBE6F6" strokeWidth="0.7">
          <path d="M0 33 H100" /><path d="M44 0 V78" /><path d="M70 0 V78" />
        </g>
      </svg>

      <div className="absolute inset-0">
        {day.activities.map((activity, i) => {
          const pin = day.pins[i] ?? [50, 50];
          const c = CAT[activity.category];
          const on = i === selected;
          return (
            <button
              key={i}
              onClick={() => onSelect(i)}
              aria-label={`${activity.name} — parada ${i + 1}`}
              className="absolute transition-all duration-300"
              style={{
                left: `${pin[0]}%`,
                top: `${pin[1]}%`,
                transform: 'translateX(-50%) translateY(-100%)',
                zIndex: on ? 30 : 10,
                animation: `pinPop .4s both`,
                animationDelay: `${i * 60}ms`,
              }}
            >
              <span className="flex flex-col items-center">
                <span
                  className={`grid place-items-center rounded-full font-extrabold text-white shadow-md transition-all duration-300
                    ${on ? 'h-9 w-9 text-[14px] ring-4 ring-white' : 'h-7 w-7 text-[12px] ring-2 ring-white/80 hover:scale-110'}`}
                  style={{ background: c.dot }}
                >
                  {i + 1}
                </span>
                <span
                  className="block h-2 w-2 rotate-45 -mt-1 rounded-[1px]"
                  style={{ background: c.dot }}
                />
              </span>
            </button>
          );
        })}
      </div>

      <div className="absolute top-3 left-3 flex items-center gap-1.5 rounded-full bg-white/90 backdrop-blur px-3 py-1.5 soft-sm">
        <span className="text-[#B49BF0]"><MapPinIcon size={14} sw={2.2} /></span>
        <span className="text-[12px] font-bold text-[#332E45]">{city.split(',')[0]}</span>
      </div>

      <button className="absolute bottom-3 right-3 flex items-center gap-1.5 rounded-full bg-white px-3.5 py-2 text-[12px] font-bold text-[#332E45] soft-sm hover:bg-[#ECE6FB] transition-colors">
        <ExpandIcon size={14} sw={2.2} />
        Ver mapa
      </button>
    </div>
  );
}
