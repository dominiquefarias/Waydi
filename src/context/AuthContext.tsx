import { createContext, useContext, useEffect, useState } from 'react';
import { api } from '../lib/api';

interface User { id: number; name: string; email: string; is_admin: number; }

interface AuthCtx {
  user: User | null;
  loading: boolean;
  login:  (email: string, password: string) => Promise<void>;
  register: (name: string, email: string, password: string) => Promise<void>;
  logout: () => void;
}

const AuthContext = createContext<AuthCtx | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser]       = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem('waydi_token');
    if (!token) { setLoading(false); return; }
    api.auth.me()
      .then(setUser)
      .catch(() => localStorage.removeItem('waydi_token'))
      .finally(() => setLoading(false));
  }, []);

  async function login(email: string, password: string) {
    const { token } = await api.auth.login({ email, password });
    localStorage.setItem('waydi_token', token);
    const fullUser = await api.auth.me();
    setUser(fullUser);
  }

  async function register(name: string, email: string, password: string) {
    const { token } = await api.auth.register({ name, email, password });
    localStorage.setItem('waydi_token', token);
    const fullUser = await api.auth.me();
    setUser(fullUser);
  }

  function logout() {
    localStorage.removeItem('waydi_token');
    setUser(null);
  }

  return (
    <AuthContext.Provider value={{ user, loading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth debe usarse dentro de AuthProvider');
  return ctx;
}
