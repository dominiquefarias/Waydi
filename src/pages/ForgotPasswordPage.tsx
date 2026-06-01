import { useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../lib/api';

export function ForgotPasswordPage() {
  const [email,   setEmail]   = useState('');
  const [sent,    setSent]    = useState(false);
  const [error,   setError]   = useState('');
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      await api.auth.forgotPassword(email);
      setSent(true);
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : 'Error al enviar el email');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center px-4 bg-[#F6F4FC]">
      <div className="w-full max-w-sm">
        {/* Logo */}
        <div className="flex items-center justify-center gap-2.5 mb-8">
          <span className="grid place-items-center h-10 w-10 rounded-xl bg-[#332E45] text-[#DCD0FF] font-extrabold text-[17px]">W</span>
          <span className="text-[18px] font-extrabold text-[#332E45] tracking-tight">waydi</span>
        </div>

        <div className="rounded-[30px] bg-white p-8 soft">
          {sent ? (
            <div className="text-center">
              <div className="inline-grid place-items-center h-14 w-14 rounded-2xl bg-[#DDF2E8] text-[#4C9C77] mb-4 mx-auto">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M20 6 9 17l-5-5"/>
                </svg>
              </div>
              <h2 className="text-[20px] font-extrabold text-[#332E45] tracking-tight mb-2">Revisa tu email</h2>
              <p className="text-[13.5px] text-[#807A95] font-medium leading-relaxed">
                Si <strong>{email}</strong> está registrado, recibirás un enlace para restablecer tu contraseña en breve.
              </p>
              <p className="text-[12px] text-[#A9A3BC] font-medium mt-3">El enlace expira en 1 hora.</p>
            </div>
          ) : (
            <>
              <h1 className="text-[22px] font-extrabold text-[#332E45] tracking-tight mb-1">¿Olvidaste tu contraseña?</h1>
              <p className="text-[13.5px] text-[#807A95] font-medium mb-6">
                Escribe tu email y te enviaremos un enlace para recuperarla.
              </p>

              <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                <div>
                  <label className="block text-[12.5px] font-bold text-[#332E45] mb-1.5">Email</label>
                  <input
                    type="email"
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    required
                    placeholder="tu@email.com"
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
                  {loading ? 'Enviando…' : 'Enviar enlace'}
                </button>
              </form>
            </>
          )}
        </div>

        <p className="text-center text-[13px] text-[#807A95] font-medium mt-5">
          <Link to="/login" className="font-bold text-[#B49BF0] hover:text-[#332E45] transition-colors">
            ← Volver al inicio de sesión
          </Link>
        </p>
      </div>
    </div>
  );
}
