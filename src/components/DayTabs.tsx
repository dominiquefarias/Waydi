import type { Day } from '../data/trip';

interface DayTabsProps {
  days: Day[];
  activeIndex: number;
  onChange: (i: number) => void;
}

export default function DayTabs({ days, activeIndex, onChange }: DayTabsProps) {
  return (
    <div className="flex gap-2.5 overflow-x-auto scrollbar-hide mt-6 pb-1">
      {days.map((day, i) => {
        const active = i === activeIndex;
        return (
          <button
            key={i}
            onClick={() => onChange(i)}
            className={`flex-shrink-0 flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 ${
              active
                ? 'bg-ink text-white'
                : 'bg-white soft-sm text-ink hover:bg-lavender'
            }`}
          >
            <div
              className={`w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0 ${
                active ? 'bg-lila text-[#6A48C0]' : 'bg-lavender text-lila-deep'
              }`}
            >
              {i + 1}
            </div>
            <div className="text-left">
              <div className="font-bold text-sm leading-tight">{day.label}</div>
              <div className={`text-xs leading-tight mt-0.5 ${active ? 'text-white/70' : 'text-muted'}`}>
                {day.weekday} · {day.date}
              </div>
            </div>
          </button>
        );
      })}
    </div>
  );
}
