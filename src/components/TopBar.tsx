import { ShareIcon } from './Icons';

export function TopBar() {
  return (
    <div className="flex items-center justify-between mb-6">
      <div className="flex items-center gap-2.5">
        <span className="grid place-items-center h-9 w-9 rounded-xl bg-[#332E45] text-[#DCD0FF] font-extrabold text-[15px]">W</span>
        <span className="text-[15px] font-extrabold text-[#332E45] tracking-tight">waydi</span>
      </div>
      <div className="flex items-center gap-2">
        <button
          className="grid place-items-center h-10 w-10 rounded-full bg-white text-[#332E45] soft-sm hover:bg-[#ECE6FB] transition-colors"
          aria-label="Compartir"
        >
          <ShareIcon size={17} />
        </button>
        <div className="flex items-center -space-x-2">
          <span className="h-9 w-9 rounded-full bg-[#DCD0FF] ring-2 ring-[#F6F4FC] grid place-items-center text-[12px] font-bold text-[#332E45]">A</span>
          <span className="h-9 w-9 rounded-full bg-[#FBD8E8] ring-2 ring-[#F6F4FC] grid place-items-center text-[12px] font-bold text-[#C0567E]">M</span>
        </div>
      </div>
    </div>
  );
}
