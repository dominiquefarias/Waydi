/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        ink: '#332E45',
        muted: '#807A95',
        faint: '#A9A3BC',
        lila: '#DCD0FF',
        'lila-deep': '#B49BF0',
        lavender: '#ECE6FB',
        rosa: '#FBD8E8',
        'rosa-deep': '#F2A6C8',
        canvas: '#F6F4FC',
      },
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        '3xl': '1.5rem',
        '4xl': '2rem',
      },
    },
  },
  plugins: [],
};
