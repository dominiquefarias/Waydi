import { MapPinIcon } from './Icons';
import { DayTabs } from './DayTabs';
import type { Trip, Day } from '../data/trip';

interface TripHeroProps {
  trip: Trip;
  activeDayIndex: number;
  onPickDay: (i: number) => void;
}

export function TripHero({ trip, activeDayIndex, onPickDay }: TripHeroProps) {
  const totalStops = trip.days.reduce((s: number, d: Day) => s + d.activities.length, 0);

  return (
    <div
      className="relative overflow-hidden rounded-[30px] p-6 sm:p-8 mb-6 soft"
      style={{ background: 'linear-gradient(120deg, #E9E1FF 0%, #F3E9FB 48%, #FBE6F0 100%)' }}
    >
      <div className="absolute -right-10 -top-12 h-44 w-44 rounded-full bg-white/40 blur-2xl pointer-events-none" />
      <div className="absolute right-24 top-10 h-24 w-24 rounded-full bg-[#FBD8E8]/50 blur-xl pointer-events-none" />

      <div className="relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5">
        <div>
          <div className="flex items-center gap-2 text-[12.5px] font-bold text-[#B49BF0]">
            <MapPinIcon size={15} sw={2.2} />
            {trip.city}
            <span className="text-[#A9A3BC]">·</span>
            <span className="text-[#807A95]">{trip.range}</span>
          </div>
          <h1 className="mt-2 text-[30px] sm:text-[38px] leading-none font-extrabold text-[#332E45] tracking-tight">
            {trip.title}
          </h1>
          <p className="mt-2.5 text-[14px] text-[#807A95] font-medium max-w-md">{trip.subtitle}</p>
        </div>
        <div className="flex items-center gap-2.5">
          <div className="rounded-2xl bg-white/70 backdrop-blur px-4 py-3 text-center">
            <p className="text-[22px] font-extrabold text-[#332E45] leading-none">{trip.days.length}</p>
            <p className="text-[11px] font-bold text-[#807A95] mt-1">días</p>
          </div>
          <div className="rounded-2xl bg-white/70 backdrop-blur px-4 py-3 text-center">
            <p className="text-[22px] font-extrabold text-[#332E45] leading-none">{totalStops}</p>
            <p className="text-[11px] font-bold text-[#807A95] mt-1">paradas</p>
          </div>
        </div>
      </div>

      <div className="relative mt-6">
        <DayTabs days={trip.days} active={activeDayIndex} onPick={onPickDay} />
      </div>
    </div>
  );
}
