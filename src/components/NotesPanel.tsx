import { getIcon } from './Icons';
import { TONE } from '../lib/categories';
import type { Note } from '../data/trip';

interface NotesPanelProps {
  notes: Note[];
}

export function NotesPanel({ notes }: NotesPanelProps) {
  return (
    <div className="rounded-[26px] bg-white p-5 soft border border-white">
      <div className="flex items-center justify-between mb-3.5">
        <h3 className="text-[15px] font-extrabold text-[#332E45] tracking-tight">Notas importantes</h3>
        <span className="text-[11px] font-bold text-[#B49BF0] bg-[#ECE6FB] rounded-full px-2.5 py-1">
          {notes.length}
        </span>
      </div>
      <div className="flex flex-col gap-2.5">
        {notes.map((note, i) => {
          const t = TONE[note.tone];
          const Glyph = getIcon(note.icon);
          return (
            <div key={i} className={`flex items-start gap-3 rounded-2xl p-3 ${t.wrap}`}>
              <span className={`grid place-items-center h-9 w-9 rounded-xl shrink-0 ${t.ic}`}>
                <Glyph size={17} sw={1.9} />
              </span>
              <div className="min-w-0">
                <p className="text-[12.5px] font-bold text-[#332E45] leading-tight">{note.title}</p>
                <p className="text-[12.5px] text-[#807A95] font-medium leading-snug mt-0.5">{note.text}</p>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
