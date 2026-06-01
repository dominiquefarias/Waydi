import { useState } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import { TRIP } from './data/trip';
import { TopBar } from './components/TopBar';
import { TripHero } from './components/TripHero';
import { Timeline } from './components/Timeline';
import { MapPanel } from './components/MapPanel';
import { NotesPanel } from './components/NotesPanel';
import { LoginPage } from './pages/LoginPage';
import { RegisterPage } from './pages/RegisterPage';
import { ForgotPasswordPage } from './pages/ForgotPasswordPage';
import { ResetPasswordPage } from './pages/ResetPasswordPage';
import { AdminPage } from './pages/AdminPage';

function ItineraryView() {
  const { logout, user } = useAuth();
  const [dayIndex, setDayIndex] = useState(0);
  const [selectedActivity, setSelectedActivity] = useState(1);

  const day = TRIP.days[dayIndex];

  const pickDay = (i: number) => { setDayIndex(i); setSelectedActivity(0); };
  const prevDay = () => pickDay((dayIndex - 1 + TRIP.days.length) % TRIP.days.length);
  const nextDay = () => pickDay((dayIndex + 1) % TRIP.days.length);

  return (
    <div className="min-h-screen w-full px-4 sm:px-6 lg:px-10 py-6 lg:py-9 bg-[#F6F4FC]"
      style={{ fontFamily: '"Plus Jakarta Sans", system-ui, sans-serif' }}>
      <div className="mx-auto max-w-[1180px]">
        <TopBar user={user} onLogout={logout} />
        <TripHero trip={TRIP} activeDayIndex={dayIndex} onPickDay={pickDay} />

        <div className="grid grid-cols-1 lg:grid-cols-[1.55fr_1fr] gap-6 items-start">
          <Timeline
            day={day} dayIndex={dayIndex} totalDays={TRIP.days.length}
            selectedActivity={selectedActivity} onSelectActivity={setSelectedActivity}
            onPrevDay={prevDay} onNextDay={nextDay}
          />
          <div className="flex flex-col gap-6 lg:sticky lg:top-6">
            <MapPanel day={day} selected={selectedActivity} onSelect={setSelectedActivity} city={TRIP.city} />
            <NotesPanel notes={day.notes} />
          </div>
        </div>

        <p className="text-center text-[11.5px] text-[#A9A3BC] font-semibold mt-8">
          waydi · planifica suave, viaja ligero
        </p>
      </div>
    </div>
  );
}

function RequireAuth({ children }: { children: React.ReactNode }) {
  const { user, loading } = useAuth();
  if (loading) return (
    <div className="min-h-screen flex items-center justify-center bg-[#F6F4FC]">
      <span className="text-[14px] font-semibold text-[#A9A3BC]">Cargando…</span>
    </div>
  );
  return user ? <>{children}</> : <Navigate to="/login" replace />;
}

function RequireAdmin({ children }: { children: React.ReactNode }) {
  const { user, loading } = useAuth();
  if (loading) return (
    <div className="min-h-screen flex items-center justify-center bg-[#F6F4FC]">
      <span className="text-[14px] font-semibold text-[#A9A3BC]">Cargando…</span>
    </div>
  );
  if (!user) return <Navigate to="/login" replace />;
  if (!user.is_admin) return <Navigate to="/" replace />;
  return <>{children}</>;
}

export default function App() {
  return (
    <Routes>
      <Route path="/login"          element={<LoginPage />} />
      <Route path="/register"       element={<RegisterPage />} />
      <Route path="/forgot-password" element={<ForgotPasswordPage />} />
      <Route path="/reset-password" element={<ResetPasswordPage />} />
      <Route path="/" element={
        <RequireAuth><ItineraryView /></RequireAuth>
      } />
      <Route path="/admin" element={
        <RequireAdmin><AdminPage /></RequireAdmin>
      } />
    </Routes>
  );
}
