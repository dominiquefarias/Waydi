import { useState, type FormEvent } from 'react';
import { Link, useSearchParams, useNavigate } from 'react-router-dom';
import { api } from '../lib/api';

export function ResetPasswordPage() {
  const [searchParams]          = useSearchParams();
  const navigate                = useNavigate();
  const token                   = searchParams.get('token') ?? '';
  const [password,  setPassword]  = useState('');
  const [confirm,   setConfirm]   = useState('');
  const [error,     setError]     = useState('');
  const [loading,   setLoading]   = useState(false);
  const [done,      setDone]      = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError('');
    if (password !== confirm) { setError('Las contraseñas no coinciden'); return; }
    if (password.length < 8)  { setError('Mínimo 8 caracteres'); return; }
    setLoading(true);
    try {
      await api.auth.resetPassword(token, password);
      setDone(true);
      setTimeout(() => navigate('/login'), 3000);
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : 'Error al restablecer la contraseña');
    } finally {
      setLoading(false);
    }
  }

  if (!token) {
    return (
      <div className="min-h-screen flex items-center justify-center px-4 bg-[#F6F4FC]">
        <div className="rounded-[30px] bg-white p-8 soft text-center max-w-sm w-full">
          <p className="text-[14px] text-[#C0567E] font-semibold">Enlace inválido.</p>
          <Link to="/forgot-password" className="block mt-4 text-[13px] font-bold text-[#B49BF0]">
            Solicitar uno nuevo →
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex items-center justify-center px-4 bg-[#F6F4FC]">
      <div className="w-full max-w-sm">
        <div className="flex items-center justify-center gap-2.5 mb-8">
          <span className="grid place-items-center h-10 w-10 rounded-xl bg-[#332E45] text-[#DCD0FF] font-extrabold text-[17px]">W</span>
          <span className="text-[18px] font-extrabold text-[#332E45] tracking-tight">waydi</span>
        </div>

        <div className="rounded-[30px] bg-white p-8 soft">
          {done ? (
            <div className="text-center">
              <div className="inline-grid place-items-center h-14 w-14 rounded-2xl bg-[#DDF2E8] text-[#4C9C77] mb-4 mx-auto">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M20 6 9 17l-5-5"/>
                </svg>
              </div>
              <h2 className="text-[20px] font-extrabold text-[#332E45] mb-2">¡Contraseña actualizada!</h2>
              <p className="text-[13.5px] text-[#807A95]">Redirigiendo al inicio de sesión…</p>
            </div>
          ) : (
            <>
              <h1 className="text-[22px] font-extrabold text-[#332E45] tracking-tight mb-1">Nueva contraseña</h1>
              <p className="text-[13.5px] text-[#807A95] font-medium mb-6">Elige una contraseña segura.</p>

              <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                <div>
                  <label className="block text-[12.5px] font-bold text-[#332E45] mb-1.5">Nueva contraseña</label>
                  <input
                    type="password"
                    value={password}
                    onChange={e => setPassword(e.target.value)}
                    required
                    placeholder="Mínimo 8 caracteres"
                    className="w-full rounded-2xl border border-[#ECE6FB] bg-[#F6F4FC] px-4 py-3 text-[14px] text-[#332E45] placeholder-[#A9A3BC] outline-none focus:border-[#B49BF0] focus:ring-2 focus:ring-[#DCD0FF] transition-all"
                  />
                </div>
                <div>
                  <label className="block text-[12.5px] font-bold text-[#332E45] mb-1.5">Confirmar contraseña</label>
                  <input
                    type="password"
                    value={confirm}
                    onChange={e => setConfirm(e.target.value)}
                    required
                    placeholder="Repite la contraseña"
                    className="w-full rounded-2xl border border-[#ECE6FB] bg-[#F6F4FC] px-4 py-3 text-[14px] text-[#332E45] placeholder-[#A9A3BC] outline-none focus:border-[#B49BF0] focus:ring-2 focus:ring-[#DCD0FF] transition-all"
                  />
                </div>

                {error && (
                  <p className="text-[12.5px] font-semibold text-[#C0567E] bg-[#FDEBF2] rounded-xl px-4 py-2.5">{error}</p>
                )}

                <button
                  type="submit"
                  disabled={loading}
                  className="w-full rounded-2xl bg-[#332E45] text-white font-bold text-[14px] py-3.5 hover:bg-[#4a4360] disabled:opacity-60 transition-all duration-300 soft-sm"
                >
                  {loading ? 'Guardando…' : 'Guardar contraseña'}
                </button>
              </form>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
