const BASE = import.meta.env.VITE_API_URL ?? 'http://localhost:4000';

function getToken() {
  return localStorage.getItem('waydi_token');
}

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const token = getToken();
  const res = await fetch(`${BASE}${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options.headers,
    },
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.error ?? 'Error de servidor');
  return data as T;
}

export const api = {
  auth: {
    register: (body: { name: string; email: string; password: string }) =>
      request<{ token: string; user: { id: number; name: string; email: string } }>(
        '/api/auth/register', { method: 'POST', body: JSON.stringify(body) }
      ),
    login: (body: { email: string; password: string }) =>
      request<{ token: string; user: { id: number; name: string; email: string } }>(
        '/api/auth/login', { method: 'POST', body: JSON.stringify(body) }
      ),
    me: () =>
      request<{ id: number; name: string; email: string }>('/api/auth/me'),
    forgotPassword: (email: string) =>
      request<{ message: string }>(
        '/api/auth/forgot-password', { method: 'POST', body: JSON.stringify({ email }) }
      ),
    resetPassword: (token: string, password: string) =>
      request<{ message: string }>(
        '/api/auth/reset-password', { method: 'POST', body: JSON.stringify({ token, password }) }
      ),
  },
  trips: {
    list: () => request<Trip[]>('/api/trips'),
    get:  (id: number) => request<Trip>(`/api/trips/${id}`),
    create: (body: Partial<Trip>) =>
      request<Trip>('/api/trips', { method: 'POST', body: JSON.stringify(body) }),
    update: (id: number, body: Partial<Trip>) =>
      request<{ message: string }>(`/api/trips/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
    delete: (id: number) =>
      request<{ message: string }>(`/api/trips/${id}`, { method: 'DELETE' }),
  },
};

export interface Trip {
  id: number;
  user_id: number;
  title: string;
  subtitle: string;
  date_range: string;
  city: string;
  travelers: number;
  created_at: string;
  days?: DayDB[];
}

export interface DayDB {
  id: number;
  trip_id: number;
  position: number;
  label: string;
  weekday: string;
  date: string;
  theme: string;
  activities: ActivityDB[];
  notes: NoteDB[];
}

export interface ActivityDB {
  id: number;
  day_id: number;
  position: number;
  time: string;
  duration: string;
  name: string;
  place: string;
  category: string;
  icon: string;
  pin_x: number;
  pin_y: number;
}

export interface NoteDB {
  id: number;
  day_id: number;
  position: number;
  icon: string;
  tone: 'lila' | 'rosa' | 'lavender';
  title: string;
  text: string;
}
