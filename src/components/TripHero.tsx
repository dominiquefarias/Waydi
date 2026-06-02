import { MapPin } from 'lucide-react';
import type { Trip } from '../data/trip';
import DayTabs from './DayTabs';

interface TripHeroProps {
  trip: Trip;
  dayIndex: number;
  onDayChange: (i: number) => void;
}

export default function TripHero({ trip, dayIndex, onDayChange }: TripHeroProps) {
  const totalActivities = trip.days.reduce((sum, d) => sum + d.activities.length, 0);

  return (
    <div
      className="rounded-[30px] p-8 mb-6 soft relative overflow-hidden"
      style={{ background: 'linear-gradient(120deg, #E9E1FF 0%, #F3E9FB 48%, #FBE6F0 100%)' }}
    >
      {/* decorative circles */}
      <div
        className="absolute -top-10 -right-10 w-48 h-48 rounded-full opacity-40 blur-2xl pointer-events-none"
        style={{ background: '#FBD8E8' }}
      />
      <div
        className="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-30 blur-2xl pointer-events-none"
        style={{ background: '#DCD0FF' }}
      />

      <div className="relative">
        <div className="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
          <div className="flex-1">
            <div className="flex items-center gap-2 text-sm font-semibold text-lila-deep mb-3">
              <MapPin size={14} />
              <span>{trip.city}</span>
              <span className="text-muted">·</span>
              <span className="text-muted font-medium">{trip.range}</span>
            </div>
            <h1 className="text-[38px] font-extrabold tracking-tight text-ink leading-tight">
              {trip.title}
            </h1>
            <p className="text-muted mt-2 text-base">{trip.subtitle}</p>
          </div>

          <div className="flex gap-3 lg:flex-shrink-0">
            <div className="bg-white/70 backdrop-blur rounded-2xl px-5 py-3 text-center">
              <div className="text-2xl font-extrabold text-ink">{trip.days.length}</div>
              <div className="text-xs text-muted font-medium mt-0.5">días</div>
            </div>
            <div className="bg-white/70 backdrop-blur rounded-2xl px-5 py-3 text-center">
              <div className="text-2xl font-extrabold text-ink">{totalActivities}</div>
              <div className="text-xs text-muted font-medium mt-0.5">paradas</div>
            </div>
          </div>
        </div>

        <DayTabs days={trip.days} activeIndex={dayIndex} onChange={onDayChange} />
      </div>
    </div>
  );
}
