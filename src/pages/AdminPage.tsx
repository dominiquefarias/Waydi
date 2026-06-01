import { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { api } from '../lib/api';
import type { AdminStats, AdminUser, AdminTrip } from '../lib/api';

type Tab = 'dashboard' | 'users' | 'trips';

export function AdminPage() {
  const { user, logout } = useAuth();
  const navigate         = useNavigate();
  const [tab, setTab]    = useState<Tab>('dashboard');

  if (!user?.is_admin) {
    navigate('/');
    return null;
  }

  return (
    <div className="min-h-screen bg-[#F6F4FC]" style={{ fontFamily: '"Plus Jakarta Sans", system-ui, sans-serif' }}>
      {/* Sidebar */}
      <div className="fixed top-0 left-0 h-full w-56 bg-[#332E45] flex flex-col z-20">
        <div className="flex items-center gap-2.5 px-5 py-5 border-b border-white/10">
          <span className="grid place-items-center h-8 w-8 rounded-xl bg-[#DCD0FF] text-[#332E45] font-extrabold text-[14px]">W</span>
          <div>
            <span className="block text-[14px] font-extrabold text-white tracking-tight">waydi</span>
            <span className="block text-[10px] font-semibold text-white/40 -mt-0.5">Admin Panel</span>
          </div>
        </div>

        <nav className="flex-1 px-3 py-4 flex flex-col gap-1">
          {([
            { id: 'dashboard', label: 'Dashboard',  icon: '▦' },
            { id: 'users',     label: 'Usuarios',   icon: '👤' },
            { id: 'trips',     label: 'Viajes',     icon: '✈️' },
          ] as { id: Tab; label: string; icon: string }[]).map(item => (
            <button key={item.id} onClick={() => setTab(item.id)}
              className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-all text-left
                ${tab === item.id ? 'bg-[#DCD0FF] text-[#332E45]' : 'text-white/60 hover:bg-white/10 hover:text-white'}`}>
              <span className="text-[16px]">{item.icon}</span>{item.label}
            </button>
          ))}
        </nav>

        <div className="px-3 py-4 border-t border-white/10">
          <div className="flex items-center gap-2.5 px-3 py-2 mb-2">
            <span className="h-7 w-7 rounded-full bg-[#DCD0FF] grid place-items-center text-[11px] font-bold text-[#332E45] shrink-0">
              {user.name[0].toUpperCase()}
            </span>
            <div className="min-w-0">
              <p className="text-[12px] font-bold text-white truncate">{user.name}</p>
              <p className="text-[10px] text-white/40 truncate">{user.email}</p>
            </div>
          </div>
          <button onClick={() => { logout(); navigate('/login'); }}
            className="w-full text-[12px] font-semibold text-white/50 hover:text-white transition-colors px-3 py-1.5 rounded-lg hover:bg-white/10 text-left">
            Cerrar sesión
          </button>
        </div>
      </div>

      {/* Main content */}
      <div className="ml-56 p-8">
        {tab === 'dashboard' && <DashboardTab />}
        {tab === 'users'     && <UsersTab />}
        {tab === 'trips'     && <TripsTab />}
      </div>
    </div>
  );
}

/* ── Dashboard ───────────────────────────────────────────── */
function DashboardTab() {
  const [stats, setStats] = useState<AdminStats | null>(null);
  const [error, setError] = useState('');

  useEffect(() => {
    api.admin.stats().then(setStats).catch(e => setError(e.message));
  }, []);

  if (error) return <ErrorBox msg={error} />;
  if (!stats) return <Loader />;

  const statCards = [
    { label: 'Usuarios totales',    value: stats.total_users,      sub: `+${stats.new_users_week} esta semana`,  color: 'bg-[#ECE3FF] text-[#6A48C0]' },
    { label: 'Viajes creados',      value: stats.total_trips,      sub: `+${stats.new_trips_week} esta semana`,  color: 'bg-[#FCE0EC] text-[#C0567E]' },
    { label: 'Actividades totales', value: stats.total_activities, sub: 'en todos los viajes',                   color: 'bg-[#DDF2E8] text-[#4C9C77]' },
    { label: 'Viajes compartidos',  value: stats.total_shares,     sub: 'colaboraciones activas',                color: 'bg-[#E3ECFF] text-[#5772BE]' },
  ];

  return (
    <div>
      <h1 className="text-[24px] font-extrabold text-[#332E45] tracking-tight mb-6">Dashboard</h1>

      {/* Stat cards */}
      <div className="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        {statCards.map(c => (
          <div key={c.label} className="rounded-[24px] bg-white p-5 soft">
            <span className={`inline-block text-[11px] font-bold px-2.5 py-1 rounded-full mb-3 ${c.color}`}>{c.label}</span>
            <p className="text-[32px] font-extrabold text-[#332E45] leading-none">{c.value}</p>
            <p className="text-[12px] text-[#807A95] font-medium mt-1">{c.sub}</p>
          </div>
        ))}
      </div>

      {/* Categories + registrations */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="rounded-[24px] bg-white p-6 soft">
          <h2 className="text-[15px] font-extrabold text-[#332E45] mb-4">Actividades por categoría</h2>
          <div className="flex flex-col gap-2.5">
            {stats.categories.map(cat => {
              const total = stats.categories.reduce((s, c) => s + Number(c.count), 0);
              const pct   = total ? Math.round((Number(cat.count) / total) * 100) : 0;
              return (
                <div key={cat.category}>
                  <div className="flex justify-between text-[12.5px] font-semibold text-[#332E45] mb-1">
                    <span>{cat.category}</span>
                    <span className="text-[#807A95]">{cat.count} ({pct}%)</span>
                  </div>
                  <div className="h-2 rounded-full bg-[#F6F4FC] overflow-hidden">
                    <div className="h-full rounded-full bg-[#DCD0FF] transition-all" style={{ width: `${pct}%` }} />
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        <div className="rounded-[24px] bg-white p-6 soft">
          <h2 className="text-[15px] font-extrabold text-[#332E45] mb-4">Registros últimos 7 días</h2>
          {stats.registrations_chart.length === 0 ? (
            <p className="text-[13px] text-[#A9A3BC] font-medium">Sin registros esta semana</p>
          ) : (
            <div className="flex items-end gap-2 h-32">
              {stats.registrations_chart.map(d => {
                const max = Math.max(...stats.registrations_chart.map(x => Number(x.count)));
                const h   = max ? Math.round((Number(d.count) / max) * 100) : 0;
                return (
                  <div key={d.day} className="flex-1 flex flex-col items-center gap-1">
                    <span className="text-[10px] font-bold text-[#807A95]">{d.count}</span>
                    <div className="w-full rounded-t-lg bg-[#DCD0FF] transition-all" style={{ height: `${Math.max(h, 8)}%` }} />
                    <span className="text-[9px] text-[#A9A3BC] font-medium">{d.day.slice(5)}</span>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

/* ── Usuarios ────────────────────────────────────────────── */
function UsersTab() {
  const [users,   setUsers]   = useState<AdminUser[]>([]);
  const [search,  setSearch]  = useState('');
  const [loading, setLoading] = useState(true);
  const [error,   setError]   = useState('');
  const [selected, setSelected] = useState<AdminUser | null>(null);
  const [confirm,  setConfirm]  = useState<number | null>(null);

  const load = useCallback((q?: string) => {
    setLoading(true);
    api.admin.users(q).then(setUsers).catch(e => setError(e.message)).finally(() => setLoading(false));
  }, []);

  useEffect(() => { load(); }, [load]);

  const handleSearch = (e: React.FormEvent) => { e.preventDefault(); load(search); };

  const toggleAdmin = async (u: AdminUser) => {
    await api.admin.updateUser(u.id, { is_admin: u.is_admin ? 0 : 1 });
    load(search || undefined);
  };

  const deleteUser = async (id: number) => {
    await api.admin.deleteUser(id);
    setConfirm(null);
    load(search || undefined);
  };

  if (error) return <ErrorBox msg={error} />;

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-[24px] font-extrabold text-[#332E45] tracking-tight">Usuarios</h1>
        <form onSubmit={handleSearch} className="flex gap-2">
          <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Buscar por nombre o email…"
            className="rounded-2xl border border-[#ECE6FB] bg-white px-4 py-2 text-[13px] text-[#332E45] outline-none focus:border-[#B49BF0] focus:ring-2 focus:ring-[#DCD0FF] w-64 transition-all" />
          <button type="submit" className="rounded-2xl bg-[#332E45] text-white px-4 py-2 text-[13px] font-bold hover:bg-[#4a4360] transition-all">Buscar</button>
        </form>
      </div>

      {loading ? <Loader /> : (
        <div className="rounded-[24px] bg-white soft overflow-hidden">
          <table className="w-full text-[13px]">
            <thead>
              <tr className="border-b border-[#F6F4FC]">
                <th className="text-left px-5 py-3.5 font-bold text-[#807A95]">Usuario</th>
                <th className="text-left px-5 py-3.5 font-bold text-[#807A95]">Email</th>
                <th className="text-center px-5 py-3.5 font-bold text-[#807A95]">Viajes</th>
                <th className="text-center px-5 py-3.5 font-bold text-[#807A95]">Rol</th>
                <th className="text-left px-5 py-3.5 font-bold text-[#807A95]">Registro</th>
                <th className="px-5 py-3.5" />
              </tr>
            </thead>
            <tbody>
              {users.map(u => (
                <tr key={u.id} className="border-b border-[#F6F4FC] hover:bg-[#F6F4FC] transition-colors">
                  <td className="px-5 py-3.5">
                    <div className="flex items-center gap-2.5">
                      <span className="h-8 w-8 rounded-full bg-[#DCD0FF] grid place-items-center text-[11px] font-bold text-[#332E45] shrink-0">
                        {u.name[0].toUpperCase()}
                      </span>
                      <span className="font-semibold text-[#332E45]">{u.name}</span>
                    </div>
                  </td>
                  <td className="px-5 py-3.5 text-[#807A95]">{u.email}</td>
                  <td className="px-5 py-3.5 text-center font-bold text-[#332E45]">{u.total_trips ?? 0}</td>
                  <td className="px-5 py-3.5 text-center">
                    <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold
                      ${u.is_admin ? 'bg-[#ECE3FF] text-[#6A48C0]' : 'bg-[#F6F4FC] text-[#807A95]'}`}>
                      {u.is_admin ? 'Admin' : 'Usuario'}
                    </span>
                  </td>
                  <td className="px-5 py-3.5 text-[#A9A3BC]">{u.created_at?.slice(0,10)}</td>
                  <td className="px-5 py-3.5">
                    <div className="flex items-center gap-1.5 justify-end">
                      <button onClick={() => setSelected(u)}
                        className="rounded-xl bg-[#F6F4FC] hover:bg-[#ECE6FB] text-[#807A95] hover:text-[#332E45] px-3 py-1.5 text-[12px] font-semibold transition-all">
                        Ver
                      </button>
                      <button onClick={() => toggleAdmin(u)}
                        className="rounded-xl bg-[#F6F4FC] hover:bg-[#ECE3FF] text-[#807A95] hover:text-[#6A48C0] px-3 py-1.5 text-[12px] font-semibold transition-all">
                        {u.is_admin ? 'Quitar admin' : 'Hacer admin'}
                      </button>
                      <button onClick={() => setConfirm(u.id)}
                        className="rounded-xl bg-[#FDEBF2] text-[#C0567E] hover:bg-[#FBD8E8] px-3 py-1.5 text-[12px] font-semibold transition-all">
                        Eliminar
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          {users.length === 0 && <p className="text-center text-[13px] text-[#A9A3BC] py-10">Sin resultados</p>}
        </div>
      )}

      {/* User detail modal */}
      {selected && (
        <Modal onClose={() => setSelected(null)}>
          <h2 className="text-[18px] font-extrabold text-[#332E45] mb-1">{selected.name}</h2>
          <p className="text-[13px] text-[#807A95] mb-4">{selected.email}</p>
          <p className="text-[12.5px] font-bold text-[#332E45] mb-2">Viajes ({selected.trips?.length ?? 0})</p>
          <div className="flex flex-col gap-2 max-h-60 overflow-y-auto">
            {selected.trips?.map(t => (
              <div key={t.id} className="rounded-xl bg-[#F6F4FC] px-3 py-2.5">
                <p className="text-[13px] font-bold text-[#332E45]">{t.title}</p>
                <p className="text-[11.5px] text-[#807A95]">{t.city} · {t.date_range}</p>
              </div>
            ))}
            {!selected.trips?.length && <p className="text-[12.5px] text-[#A9A3BC]">Sin viajes creados</p>}
          </div>
        </Modal>
      )}

      {/* Confirm delete */}
      {confirm !== null && (
        <Modal onClose={() => setConfirm(null)}>
          <p className="text-[16px] font-extrabold text-[#332E45] mb-2">¿Eliminar usuario?</p>
          <p className="text-[13px] text-[#807A95] mb-5">Se borrarán también todos sus viajes y datos. Esta acción no se puede deshacer.</p>
          <div className="flex gap-2 justify-end">
            <button onClick={() => setConfirm(null)} className="rounded-xl bg-[#F6F4FC] text-[#807A95] px-4 py-2 text-[13px] font-bold">Cancelar</button>
            <button onClick={() => deleteUser(confirm)} className="rounded-xl bg-[#C0567E] text-white px-4 py-2 text-[13px] font-bold hover:bg-[#a04568] transition-all">Eliminar</button>
          </div>
        </Modal>
      )}
    </div>
  );
}

/* ── Viajes ──────────────────────────────────────────────── */
function TripsTab() {
  const [trips,   setTrips]   = useState<AdminTrip[]>([]);
  const [search,  setSearch]  = useState('');
  const [loading, setLoading] = useState(true);
  const [error,   setError]   = useState('');
  const [confirm, setConfirm] = useState<number | null>(null);

  const load = useCallback((q?: string) => {
    setLoading(true);
    api.admin.trips(q).then(setTrips).catch(e => setError(e.message)).finally(() => setLoading(false));
  }, []);

  useEffect(() => { load(); }, [load]);

  const handleSearch = (e: React.FormEvent) => { e.preventDefault(); load(search); };

  const deleteTrip = async (id: number) => {
    await api.admin.deleteTrip(id);
    setConfirm(null);
    load(search || undefined);
  };

  if (error) return <ErrorBox msg={error} />;

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-[24px] font-extrabold text-[#332E45] tracking-tight">Viajes</h1>
        <form onSubmit={handleSearch} className="flex gap-2">
          <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Buscar por título, ciudad o usuario…"
            className="rounded-2xl border border-[#ECE6FB] bg-white px-4 py-2 text-[13px] text-[#332E45] outline-none focus:border-[#B49BF0] focus:ring-2 focus:ring-[#DCD0FF] w-72 transition-all" />
          <button type="submit" className="rounded-2xl bg-[#332E45] text-white px-4 py-2 text-[13px] font-bold hover:bg-[#4a4360] transition-all">Buscar</button>
        </form>
      </div>

      {loading ? <Loader /> : (
        <div className="rounded-[24px] bg-white soft overflow-hidden">
          <table className="w-full text-[13px]">
            <thead>
              <tr className="border-b border-[#F6F4FC]">
                <th className="text-left px-5 py-3.5 font-bold text-[#807A95]">Viaje</th>
                <th className="text-left px-5 py-3.5 font-bold text-[#807A95]">Propietario</th>
                <th className="text-center px-5 py-3.5 font-bold text-[#807A95]">Días</th>
                <th className="text-center px-5 py-3.5 font-bold text-[#807A95]">Actividades</th>
                <th className="text-center px-5 py-3.5 font-bold text-[#807A95]">Colaboradores</th>
                <th className="text-left px-5 py-3.5 font-bold text-[#807A95]">Creado</th>
                <th className="px-5 py-3.5" />
              </tr>
            </thead>
            <tbody>
              {trips.map(t => (
                <tr key={t.id} className="border-b border-[#F6F4FC] hover:bg-[#F6F4FC] transition-colors">
                  <td className="px-5 py-3.5">
                    <p className="font-bold text-[#332E45]">{t.title}</p>
                    <p className="text-[11.5px] text-[#807A95]">{t.city} · {t.date_range}</p>
                  </td>
                  <td className="px-5 py-3.5">
                    <p className="font-semibold text-[#332E45]">{t.owner_name}</p>
                    <p className="text-[11.5px] text-[#A9A3BC]">{t.owner_email}</p>
                  </td>
                  <td className="px-5 py-3.5 text-center font-bold text-[#332E45]">{t.total_days}</td>
                  <td className="px-5 py-3.5 text-center font-bold text-[#332E45]">{t.total_activities}</td>
                  <td className="px-5 py-3.5 text-center">
                    {Number(t.total_collaborators) > 0
                      ? <span className="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-[#E3ECFF] text-[#5772BE]">{t.total_collaborators}</span>
                      : <span className="text-[#A9A3BC]">—</span>}
                  </td>
                  <td className="px-5 py-3.5 text-[#A9A3BC]">{t.created_at?.slice(0,10)}</td>
                  <td className="px-5 py-3.5">
                    <button onClick={() => setConfirm(t.id)}
                      className="rounded-xl bg-[#FDEBF2] text-[#C0567E] hover:bg-[#FBD8E8] px-3 py-1.5 text-[12px] font-semibold transition-all">
                      Eliminar
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          {trips.length === 0 && <p className="text-center text-[13px] text-[#A9A3BC] py-10">Sin resultados</p>}
        </div>
      )}

      {confirm !== null && (
        <Modal onClose={() => setConfirm(null)}>
          <p className="text-[16px] font-extrabold text-[#332E45] mb-2">¿Eliminar viaje?</p>
          <p className="text-[13px] text-[#807A95] mb-5">Se borrarán todos sus días, actividades y notas. Esta acción no se puede deshacer.</p>
          <div className="flex gap-2 justify-end">
            <button onClick={() => setConfirm(null)} className="rounded-xl bg-[#F6F4FC] text-[#807A95] px-4 py-2 text-[13px] font-bold">Cancelar</button>
            <button onClick={() => deleteTrip(confirm)} className="rounded-xl bg-[#C0567E] text-white px-4 py-2 text-[13px] font-bold hover:bg-[#a04568] transition-all">Eliminar</button>
          </div>
        </Modal>
      )}
    </div>
  );
}

/* ── Helpers ─────────────────────────────────────────────── */
function Loader() {
  return <div className="flex items-center justify-center py-20"><span className="text-[14px] font-semibold text-[#A9A3BC]">Cargando…</span></div>;
}

function ErrorBox({ msg }: { msg: string }) {
  return <div className="rounded-2xl bg-[#FDEBF2] px-5 py-4 text-[13.5px] font-semibold text-[#C0567E]">{msg}</div>;
}

function Modal({ children, onClose }: { children: React.ReactNode; onClose: () => void }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center px-4">
      <div className="absolute inset-0 bg-[#332E45]/40 backdrop-blur-sm" onClick={onClose} />
      <div className="relative rounded-[26px] bg-white p-6 w-full max-w-md soft-lift">{children}</div>
    </div>
  );
}
