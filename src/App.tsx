import { useState } from 'react';
import { TRIP } from './data/trip';
import { TopBar } from './components/TopBar';
import { TripHero } from './components/TripHero';
import { Timeline } from './components/Timeline';
import { MapPanel } from './components/MapPanel';
import { NotesPanel } from './components/NotesPanel';
import './index.css';

function App() {
  const [dayIndex, setDayIndex] = useState(0);
  const [selectedActivity, setSelectedActivity] = useState(1);

  const day = TRIP.days[dayIndex];

  const pickDay = (i: number) => {
    setDayIndex(i);
    setSelectedActivity(0);
  };

  const prevDay = () => pickDay((dayIndex - 1 + TRIP.days.length) % TRIP.days.length);
  const nextDay = () => pickDay((dayIndex + 1) % TRIP.days.length);

  return (
    <div className="min-h-screen w-full px-4 sm:px-6 lg:px-10 py-6 lg:py-9 bg-[#F6F4FC]" style={{ fontFamily: '"Plus Jakarta Sans", system-ui, sans-serif' }}>
      <div className="mx-auto max-w-[1180px]">
        <TopBar />
        <TripHero trip={TRIP} activeDayIndex={dayIndex} onPickDay={pickDay} />

        <div className="grid grid-cols-1 lg:grid-cols-[1.55fr_1fr] gap-6 items-start">
          <Timeline
            day={day}
            dayIndex={dayIndex}
            totalDays={TRIP.days.length}
            selectedActivity={selectedActivity}
            onSelectActivity={setSelectedActivity}
            onPrevDay={prevDay}
            onNextDay={nextDay}
          />

          <div className="flex flex-col gap-6 lg:sticky lg:top-6">
            <MapPanel
              day={day}
              selected={selectedActivity}
              onSelect={setSelectedActivity}
              city={TRIP.city}
            />
            <NotesPanel notes={day.notes} />
          </div>
        </div>

        <p className="text-center text-[11.5px] text-[#A9A3BC] font-semibold mt-8">
          Prototipo · waydi · planifica suave, viaja ligero
        </p>
      </div>
    </div>
  );
}

export default App;
