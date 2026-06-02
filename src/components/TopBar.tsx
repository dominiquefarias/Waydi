import { Share2 } from 'lucide-react';

export default function TopBar() {
  return (
    <div className="flex items-center justify-between mb-6">
      <div className="flex items-center gap-3">
        <div className="w-9 h-9 rounded-xl bg-ink flex items-center justify-center">
          <span className="text-lila font-extrabold text-lg leading-none">W</span>
        </div>
        <span className="text-ink font-extrabold text-xl tracking-tight">waydi</span>
      </div>
      <div className="flex items-center gap-3">
        <button
          className="w-9 h-9 rounded-full bg-white soft-sm flex items-center justify-center text-muted hover:bg-lavender transition-colors duration-200"
          aria-label="Compartir"
        >
          <Share2 size={16} />
        </button>
        <div className="flex -space-x-2">
          <div className="w-8 h-8 rounded-full bg-lila flex items-center justify-center text-[#6A48C0] font-bold text-sm ring-2 ring-white">
            A
          </div>
          <div className="w-8 h-8 rounded-full bg-rosa flex items-center justify-center text-[#C0567E] font-bold text-sm ring-2 ring-white">
            M
          </div>
        </div>
      </div>
    </div>
  );
}
