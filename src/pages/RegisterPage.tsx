import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export function RegisterPage() {
  const { register } = useAuth();
  const navigate     = useNavigate();
  const [name,     setName]     = useState('');
  const [email,    setEmail]    = useState('');
  const [password, setPassword] = useState('');
  const [confirm,  setConfirm]  = useState('');
  const [error,    setError]    = useState('');
  const [loading,  setLoading]  = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError('');
    if (password !== confirm) { setError('Las contraseñas no coinciden'); return; }
    if (password.length < 8)  { setError('La contraseña debe tener al menos 8 caracteres'); return; }
    setLoading(true);
    try {
      await register(name, email, password);
      navigate('/');
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : 'Error al crear la cuenta');
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
          <h1 className="text-[22px] font-extrabold text-[#332E45] tracking-tight mb-1">Crea tu cuenta</h1>
          <p className="text-[13.5px] text-[#807A95] font-medium mb-6">Empieza a planificar tus viajes</p>

          <form onSubmit={handleSubmit} className="flex flex-col gap-4">
            <div>
              <label className="block text-[12.5px] font-bold text-[#332E45] mb-1.5">Nombre</label>
              <input
                type="text"
                value={name}
                onChange={e => setName(e.target.value)}
                required
                placeholder="Tu nombre"
                className="w-full rounded-2xl border border-[#ECE6FB] bg-[#F6F4FC] px-4 py-3 text-[14px] text-[#332E45] placeholder-[#A9A3BC] outline-none focus:border-[#B49BF0] focus:ring-2 focus:ring-[#DCD0FF] transition-all"
              />
            </div>
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
            <div>
              <label className="block text-[12.5px] font-bold text-[#332E45] mb-1.5">Contraseña</label>
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
              {loading ? 'Creando cuenta…' : 'Crear cuenta'}
            </button>
          </form>
        </div>

        <p className="text-center text-[13px] text-[#807A95] font-medium mt-5">
          ¿Ya tienes cuenta?{' '}
          <Link to="/login" className="font-bold text-[#B49BF0] hover:text-[#332E45] transition-colors">
            Inicia sesión
          </Link>
        </p>
      </div>
    </div>
  );
}
