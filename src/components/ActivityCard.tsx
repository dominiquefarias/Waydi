import { MapPin } from 'lucide-react';
import type { Activity } from '../data/trip';
import { categories } from '../lib/categories';

interface ActivityCardProps {
  activity: Activity;
  index: number;
  order: number;
  selected: boolean;
  onSelect: () => void;
}

export default function ActivityCard({ activity, index, order, selected, onSelect }: ActivityCardProps) {
  const cat = categories[activity.category];
  const CatIcon = cat.Icon;

  return (
    <div
      className="fade-up flex gap-4"
      style={{ animationDelay: `${index * 55}ms` }}
    >
      {/* rail */}
      <div className="w-14 flex-shrink-0 flex flex-col items-center pt-3">
        <span className="text-sm font-bold text-ink tabular-nums leading-tight">{activity.time}</span>
        <span className="text-xs text-faint mt-0.5">{activity.duration}</span>
        <div className="mt-2 relative flex items-center justify-center">
          <div
            className="w-2.5 h-2.5 rounded-full ring-[3px] ring-white z-10"
            style={{ backgroundColor: cat.pinColor }}
          />
        </div>
      </div>

      {/* card */}
      <button
        onClick={onSelect}
        className={`flex-1 flex items-center gap-3 rounded-3xl bg-white p-3.5 text-left transition-all duration-200 mb-3 ${
          selected
            ? 'border border-lila soft-lift -translate-y-0.5'
            : 'soft-sm hover:-translate-y-0.5 hover:soft-lift border border-transparent'
        }`}
      >
        {/* icon chip */}
        <div
          className="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
          style={{ backgroundColor: cat.iconBg }}
        >
          <CatIcon size={20} style={{ color: cat.chipText }} />
        </div>

        {/* content */}
        <div className="flex-1 min-w-0">
          <span
            className="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full mb-1"
            style={{ backgroundColor: cat.chipBg, color: cat.chipText }}
          >
            {activity.category}
          </span>
          <div className="font-bold text-ink text-sm leading-tight truncate">{activity.name}</div>
          <div className="flex items-center gap-1 text-xs text-muted mt-0.5">
            <MapPin size={10} className="text-faint flex-shrink-0" />
            <span className="truncate">{activity.place}</span>
          </div>
          {selected && (
            <div className="flex items-center gap-1 text-xs font-semibold mt-1" style={{ color: '#A98BF0' }}>
              <span className="w-1.5 h-1.5 rounded-full bg-lila-deep inline-block" />
              En el mapa
            </div>
          )}
        </div>

        {/* order badge */}
        <div
          className="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 transition-colors duration-200"
          style={
            selected
              ? { backgroundColor: '#B49BF0', color: 'white' }
              : { backgroundColor: '#ECE6FB', color: '#B49BF0' }
          }
        >
          {order}
        </div>
      </button>
    </div>
  );
}
