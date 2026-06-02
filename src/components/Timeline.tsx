import { Clock, ChevronLeft, ChevronRight, Plus } from 'lucide-react';
import type { Day } from '../data/trip';
import ActivityCard from './ActivityCard';

interface TimelineProps {
  day: Day;
  dayIndex: number;
  totalDays: number;
  selectedActivity: number;
  onSelectActivity: (i: number) => void;
  onPrevDay: () => void;
  onNextDay: () => void;
}

export default function Timeline({
  day,
  dayIndex,
  totalDays,
  selectedActivity,
  onSelectActivity,
  onPrevDay,
  onNextDay,
}: TimelineProps) {
  return (
    <div className="rounded-[30px] bg-[#FBFAFE] p-6 soft">
      {/* header */}
      <div className="flex items-start justify-between mb-5">
        <div>
          <h2 className="text-[19px] font-extrabold text-ink leading-tight">
            {day.label} · {day.theme}
          </h2>
          <div className="flex items-center gap-1.5 text-sm text-muted mt-1">
            <Clock size={13} className="text-faint" />
            <span>{day.activities.length} actividades · {day.weekday} {day.date}</span>
          </div>
        </div>
        <div className="flex gap-1.5 mt-0.5">
          <button
            onClick={onPrevDay}
            disabled={dayIndex === 0}
            className="w-8 h-8 rounded-full bg-white soft-sm flex items-center justify-center text-muted hover:bg-lavender transition-colors duration-200 disabled:opacity-30"
            aria-label="Día anterior"
          >
            <ChevronLeft size={16} />
          </button>
          <button
            onClick={onNextDay}
            disabled={dayIndex === totalDays - 1}
            className="w-8 h-8 rounded-full bg-white soft-sm flex items-center justify-center text-muted hover:bg-lavender transition-colors duration-200 disabled:opacity-30"
            aria-label="Día siguiente"
          >
            <ChevronRight size={16} />
          </button>
        </div>
      </div>

      {/* timeline */}
      <div className="relative">
        {/* vertical line */}
        <div
          className="absolute top-0 bottom-0 w-0.5 rounded-full"
          style={{
            left: '27px',
            background: 'linear-gradient(to bottom, #DCD0FF, #E7DEF8, transparent)',
          }}
        />

        <div key={dayIndex}>
          {day.activities.map((activity, i) => (
            <ActivityCard
              key={i}
              activity={activity}
              index={i}
              order={i + 1}
              selected={selectedActivity === i}
              onSelect={() => onSelectActivity(i)}
            />
          ))}
        </div>

        {/* add activity button */}
        <div className="flex gap-4">
          <div className="w-14 flex-shrink-0" />
          <button className="flex-1 flex items-center justify-center gap-2 rounded-3xl border-2 border-dashed border-lila text-lila-deep font-semibold text-sm py-3 hover:bg-lila hover:text-ink hover:border-transparent transition-all duration-200">
            <Plus size={16} />
            Añadir actividad
          </button>
        </div>
      </div>
    </div>
  );
}
