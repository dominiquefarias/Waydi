import { Ticket, Bell, MapPin, Moon, TrainFront } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import type { Note } from '../data/trip';

const ICONS: Record<string, LucideIcon> = { Ticket, Bell, MapPin, Moon, TrainFront };

const TONE_BG: Record<string, string> = {
  lila: '#ECE6FB',
  rosa: '#FBD8E8',
  lavender: '#F0EEFF',
};

interface NotesPanelProps {
  notes: Note[];
}

export default function NotesPanel({ notes }: NotesPanelProps) {
  return (
    <div className="rounded-[26px] bg-white p-5 soft">
      <div className="flex items-center justify-between mb-4">
        <h3 className="font-extrabold text-ink text-base">Notas importantes</h3>
        <span className="px-2.5 py-1 rounded-xl bg-lavender text-lila-deep text-xs font-bold">
          {notes.length}
        </span>
      </div>
      <div className="flex flex-col gap-3">
        {notes.map((note, i) => {
          const Icon = ICONS[note.icon] ?? Bell;
          const bg = TONE_BG[note.tone] ?? '#ECE6FB';
          return (
            <div
              key={i}
              className="rounded-2xl p-4 flex gap-3 items-start"
              style={{ backgroundColor: bg }}
            >
              <div
                className="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                style={{ backgroundColor: 'white', opacity: 0.8 }}
              >
                <Icon size={15} className="text-muted" />
              </div>
              <div>
                <div className="font-bold text-ink text-sm leading-tight">{note.title}</div>
                <div className="text-muted text-xs mt-0.5 leading-snug">{note.text}</div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
