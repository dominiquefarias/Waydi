import type { Day } from '../data/trip';

interface DayTabsProps {
  days: Day[];
  active: number;
  onPick: (i: number) => void;
}

export function DayTabs({ days, active, onPick }: DayTabsProps) {
  const handleKeyDown = (e: React.KeyboardEvent, i: number) => {
    if (e.key === 'ArrowRight') onPick((i + 1) % days.length);
    if (e.key === 'ArrowLeft')  onPick((i - 1 + days.length) % days.length);
  };

  return (
    <div className="flex gap-2.5 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1" role="tablist">
      {days.map((d, i) => {
        const on = i === active;
        return (
          <button
            key={i}
            role="tab"
            aria-selected={on}
            onClick={() => onPick(i)}
            onKeyDown={(e) => handleKeyDown(e, i)}
            className={`group shrink-0 flex items-center gap-3 rounded-2xl pl-3 pr-4 py-2.5 transition-all duration-300 min-h-[44px]
              ${on ? 'bg-[#332E45] text-white soft-sm' : 'bg-white text-[#332E45] hover:bg-[#ECE6FB] soft-sm'}`}
          >
            <span className={`grid place-items-center h-9 w-9 rounded-xl text-[13px] font-bold transition-colors
              ${on ? 'bg-[#DCD0FF] text-[#332E45]' : 'bg-[#ECE6FB] text-[#B49BF0] group-hover:bg-white'}`}>
              {String(i + 1)}
            </span>
            <span className="text-left leading-none">
              <span className="block text-[13px] font-bold tracking-tight">{d.label}</span>
              <span className={`block text-[11px] mt-1 font-medium ${on ? 'text-white/60' : 'text-[#A9A3BC]'}`}>
                {d.weekday} · {d.date}
              </span>
            </span>
          </button>
        );
      })}
    </div>
  );
}
