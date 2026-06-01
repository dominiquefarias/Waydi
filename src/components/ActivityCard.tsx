import { MapPinIcon, getIcon } from './Icons';
import { CAT } from '../lib/categories';
import type { Activity } from '../data/trip';

interface ActivityCardProps {
  activity: Activity;
  index: number;
  selected: boolean;
  onSelect: (i: number) => void;
}

export function ActivityCard({ activity, index, selected, onSelect }: ActivityCardProps) {
  const c = CAT[activity.category];
  const Glyph = getIcon(activity.icon);

  return (
    <div className="flex gap-4 fade-up" style={{ animationDelay: `${index * 55}ms` }}>
      <div className="flex flex-col items-center w-14 shrink-0 pt-1">
        <span className="text-[13px] font-bold text-[#332E45] tabular-nums">{activity.time}</span>
        <span className="text-[10px] font-semibold text-[#A9A3BC] mt-0.5">{activity.duration}</span>
        <span className="mt-2 h-2.5 w-2.5 rounded-full ring-4 ring-white" style={{ background: c.dot }} />
      </div>

      <button
        onClick={() => onSelect(index)}
        aria-pressed={selected}
        className={`group relative flex-1 text-left rounded-3xl bg-white p-3.5 pr-4 mb-3.5 transition-all duration-300 border
          ${selected
            ? 'soft-lift border-[#DCD0FF] -translate-y-0.5'
            : 'soft-sm border-transparent hover:-translate-y-0.5 hover:soft'
          }`}
      >
        <div className="flex items-center gap-3.5">
          <span className={`grid place-items-center h-12 w-12 rounded-2xl shrink-0 ${c.soft} ${c.ic}`}>
            <Glyph size={22} />
          </span>
          <div className="min-w-0 flex-1">
            <div className="flex items-center gap-2 flex-wrap">
              <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold ${c.chip}`}>
                {activity.category}
              </span>
              {selected && (
                <span className="inline-flex items-center gap-1 text-[11px] font-semibold text-[#B49BF0]">
                  <span className="h-1.5 w-1.5 rounded-full bg-[#B49BF0]" />
                  En el mapa
                </span>
              )}
            </div>
            <h4 className="mt-1.5 text-[15px] font-bold text-[#332E45] tracking-tight truncate">{activity.name}</h4>
            <p className="text-[12.5px] text-[#807A95] font-medium flex items-center gap-1 truncate">
              <span className="text-[#A9A3BC] shrink-0"><MapPinIcon size={13} sw={2} /></span>
              {activity.place}
            </p>
          </div>
          <span className={`grid place-items-center h-7 w-7 rounded-full shrink-0 text-[#A9A3BC] transition-all
            ${selected ? 'bg-[#DCD0FF] text-[#332E45]' : 'bg-[#F6F4FC] group-hover:bg-[#ECE6FB]'}`}>
            <span className="text-[11px] font-extrabold">{index + 1}</span>
          </span>
        </div>
      </button>
    </div>
  );
}
