import { useState } from 'react';
import './index.css';
import { trip } from './data/trip';
import TopBar from './components/TopBar';
import TripHero from './components/TripHero';
import Timeline from './components/Timeline';
import MapPanel from './components/MapPanel';
import NotesPanel from './components/NotesPanel';

export default function App() {
  const [dayIndex, setDayIndex] = useState(0);
  const [selectedActivity, setSelectedActivity] = useState(0);

  const day = trip.days[dayIndex];

  function handleDayChange(i: number) {
    setDayIndex(i);
    setSelectedActivity(0);
  }

  function handlePrevDay() {
    if (dayIndex > 0) handleDayChange(dayIndex - 1);
  }

  function handleNextDay() {
    if (dayIndex < trip.days.length - 1) handleDayChange(dayIndex + 1);
  }

  return (
    <div className="min-h-screen bg-canvas">
      <div className="max-w-[1180px] mx-auto px-4 sm:px-6 lg:px-10 py-6 lg:py-9">
        <TopBar />

        <TripHero
          trip={trip}
          dayIndex={dayIndex}
          onDayChange={handleDayChange}
        />

        <div className="grid lg:grid-cols-[1.55fr_1fr] gap-6 items-start">
          {/* left: timeline */}
          <Timeline
            day={day}
            dayIndex={dayIndex}
            totalDays={trip.days.length}
            selectedActivity={selectedActivity}
            onSelectActivity={setSelectedActivity}
            onPrevDay={handlePrevDay}
            onNextDay={handleNextDay}
          />

          {/* right: map + notes (sticky on desktop) */}
          <div className="flex flex-col gap-5 lg:sticky lg:top-6">
            <MapPanel
              day={day}
              selectedActivity={selectedActivity}
              onSelectActivity={setSelectedActivity}
            />
            <NotesPanel notes={day.notes} />
          </div>
        </div>

        <footer className="text-center text-faint text-xs mt-10 pb-4">
          Prototipo · waydi · planifica suave, viaja ligero
        </footer>
      </div>
    </div>
  );
}
