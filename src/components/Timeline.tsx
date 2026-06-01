import { ClockIcon, ChevronLeftIcon, ChevronRightIcon, PlusIcon } from './Icons';
import { ActivityCard } from './ActivityCard';
import type { Day } from '../data/trip';

interface TimelineProps {
  day: Day;
  dayIndex: number;
  totalDays: number;
  selectedActivity: number;
  onSelectActivity: (i: number) => void;
  onPrevDay: () => void;
  onNextDay: () => void;
}

export function Timeline({
  day,
  dayIndex,
  selectedActivity,
  onSelectActivity,
  onPrevDay,
  onNextDay,
}: TimelineProps) {
  return (
    <div className="rounded-[30px] bg-[#FBFAFE] p-5 sm:p-6 soft border border-white">
      <div className="flex items-center justify-between mb-5">
        <div>
          <h2 className="text-[19px] font-extrabold text-[#332E45] tracking-tight">
            {day.label} · {day.theme}
          </h2>
          <p className="text-[12.5px] text-[#807A95] font-semibold mt-1 flex items-center gap-1.5">
            <ClockIcon size={14} sw={2} />
            {day.activities.length} actividades · {day.weekday} {day.date}
          </p>
        </div>
        <div className="flex items-center gap-1.5">
          <button
            onClick={onPrevDay}
            aria-label="Día anterior"
            className="grid place-items-center h-9 w-9 rounded-full bg-white text-[#332E45] soft-sm hover:bg-[#ECE6FB] transition-colors"
          >
            <ChevronLeftIcon size={17} />
          </button>
          <button
            onClick={onNextDay}
            aria-label="Día siguiente"
            className="grid place-items-center h-9 w-9 rounded-full bg-white text-[#332E45] soft-sm hover:bg-[#ECE6FB] transition-colors"
          >
            <ChevronRightIcon size={17} />
          </button>
        </div>
      </div>

      <div className="relative" key={dayIndex}>
        <div className="absolute left-[27px] top-2 bottom-8 w-[2px] rounded-full"
          style={{ background: 'linear-gradient(to bottom, #DCD0FF, #E7DEF8, transparent)' }}
        />
        <div className="relative">
          {day.activities.map((activity, i) => (
            <ActivityCard
              key={i}
              activity={activity}
              index={i}
              selected={i === selectedActivity}
              onSelect={onSelectActivity}
            />
          ))}
        </div>

        <div className="flex gap-4">
          <div className="w-14 shrink-0" />
          <button className="flex-1 flex items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-[#D9CEF4] bg-white/50 py-3.5 text-[13.5px] font-bold text-[#B49BF0] hover:bg-[#DCD0FF] hover:text-[#332E45] hover:border-transparent transition-all duration-300">
            <PlusIcon size={17} sw={2.2} />
            Añadir actividad
          </button>
        </div>
      </div>
    </div>
  );
}
