export type Category = 'Cultura' | 'Gastronomía' | 'Ocio' | 'Naturaleza' | 'Transporte' | 'Compras';
export type NoteTone = 'lila' | 'rosa' | 'lavender';

export interface Activity {
  time: string;
  duration: string;
  name: string;
  place: string;
  category: Category;
  icon: string;
}

export interface Note {
  icon: string;
  tone: NoteTone;
  title: string;
  text: string;
}

export interface Day {
  label: string;
  weekday: string;
  date: string;
  theme: string;
  activities: Activity[];
  notes: Note[];
  pins: [number, number][];
}

export interface Trip {
  title: string;
  subtitle: string;
  range: string;
  city: string;
  travelers: number;
  days: Day[];
}

export const TRIP: Trip = {
  title: 'Aventura en París',
  subtitle: 'Ruta cultural y gastronómica por la Ciudad de la Luz',
  range: '12 – 15 Junio 2026',
  city: 'París, Francia',
  travelers: 2,
  days: [
    {
      label: 'Día 1', weekday: 'Jue', date: '12 Jun', theme: 'Llegada & íconos',
      activities: [
        { time: '09:00', duration: '1 h',   name: 'Desayuno en Café de Flore',   place: 'Saint-Germain-des-Prés', category: 'Gastronomía', icon: 'coffee' },
        { time: '11:00', duration: '2 h',   name: 'Torre Eiffel',                place: 'Champ de Mars',          category: 'Cultura',     icon: 'landmark' },
        { time: '14:00', duration: '1.5 h', name: 'Almuerzo en Le Jules Verne',  place: 'Torre Eiffel · Piso 2',  category: 'Gastronomía', icon: 'utensils' },
        { time: '16:30', duration: '1 h',   name: 'Paseo por el Sena',           place: 'Quai de Branly',         category: 'Ocio',        icon: 'sun' },
        { time: '20:00', duration: '1.5 h', name: 'Crucero nocturno',            place: 'Port de la Bourdonnais', category: 'Ocio',        icon: 'boat' },
      ],
      notes: [
        { icon: 'ticket', tone: 'lila',     title: 'Reserva confirmada', text: 'Le Jules Verne · 14:00 · Mesa para 2' },
        { icon: 'bell',   tone: 'rosa',     title: 'Recordatorio',       text: 'Llega 15 min antes al embarcadero del crucero' },
        { icon: 'moon',   tone: 'lavender', title: 'Tip de la noche',    text: 'Lleva chaqueta ligera, baja la temperatura junto al río' },
      ],
      pins: [[22,30],[48,20],[55,42],[34,58],[68,68]],
    },
    {
      label: 'Día 2', weekday: 'Vie', date: '13 Jun', theme: 'Arte & museos',
      activities: [
        { time: '09:30', duration: '1 h',   name: 'Desayuno en Angelina',       place: 'Rue de Rivoli',         category: 'Gastronomía', icon: 'coffee' },
        { time: '11:00', duration: '3 h',   name: 'Museo del Louvre',           place: 'Rue de Rivoli',         category: 'Cultura',     icon: 'palette' },
        { time: '14:30', duration: '1 h',   name: 'Jardines de las Tullerías',  place: 'Place de la Concorde',  category: 'Naturaleza',  icon: 'tree' },
        { time: '16:00', duration: '2 h',   name: 'Museo de Orsay',             place: "Quai d'Orsay",          category: 'Cultura',     icon: 'landmark' },
        { time: '19:30', duration: '2 h',   name: 'Cena en Le Marais',          place: 'Rue des Rosiers',       category: 'Gastronomía', icon: 'utensils' },
      ],
      notes: [
        { icon: 'ticket', tone: 'lila',     title: 'Entrada online',    text: 'Louvre · acceso prioritario 11:00 (pirámide)' },
        { icon: 'bell',   tone: 'rosa',     title: 'Recordatorio',      text: 'Orsay cierra a las 18:00 — no te demores en Tullerías' },
        { icon: 'mappin', tone: 'lavender', title: 'Transporte',        text: 'Metro L1 → Tuileries, 4 paradas desde el hotel' },
      ],
      pins: [[30,24],[44,38],[60,30],[52,56],[72,62]],
    },
    {
      label: 'Día 3', weekday: 'Sáb', date: '14 Jun', theme: 'Montmartre & bohemia',
      activities: [
        { time: '10:00', duration: '1.5 h', name: 'Basílica Sacré-Cœur',       place: 'Montmartre',             category: 'Cultura',  icon: 'landmark' },
        { time: '12:00', duration: '1 h',   name: 'Place du Tertre',            place: 'Montmartre',             category: 'Ocio',     icon: 'palette' },
        { time: '13:30', duration: '1.5 h', name: 'Almuerzo en La Maison Rose', place: "Rue de l'Abreuvoir",     category: 'Gastronomía', icon: 'utensils' },
        { time: '15:30', duration: '45 min',name: 'Moulin Rouge (foto)',         place: 'Blvd de Clichy',         category: 'Ocio',     icon: 'camera' },
        { time: '18:00', duration: '2 h',   name: 'Galeries Lafayette',         place: 'Blvd Haussmann',         category: 'Compras',  icon: 'bag' },
      ],
      notes: [
        { icon: 'bell',   tone: 'rosa',     title: 'Recordatorio', text: 'Sube en el funicular de Montmartre — evita las escaleras' },
        { icon: 'ticket', tone: 'lila',     title: 'Reserva',      text: 'La Maison Rose · 13:30 · terraza' },
        { icon: 'mappin', tone: 'lavender', title: 'Tip',          text: 'Sube a la azotea de Lafayette: vista 360° gratis' },
      ],
      pins: [[40,22],[46,30],[34,36],[58,28],[66,58]],
    },
    {
      label: 'Día 4', weekday: 'Dom', date: '15 Jun', theme: 'Versalles & despedida',
      activities: [
        { time: '08:30', duration: '40 min', name: 'Tren RER a Versalles',  place: 'Estación Invalides',     category: 'Transporte',  icon: 'train' },
        { time: '10:00', duration: '3 h',    name: 'Palacio de Versalles',  place: 'Versalles',              category: 'Cultura',     icon: 'landmark' },
        { time: '13:00', duration: '1.5 h',  name: 'Picnic en los jardines',place: 'Jardines de Versalles',  category: 'Gastronomía', icon: 'tree' },
        { time: '16:00', duration: '40 min', name: 'Regreso a París',       place: 'RER C',                  category: 'Transporte',  icon: 'train' },
        { time: '19:00', duration: '2 h',    name: 'Cena de despedida',     place: 'Le Comptoir · Odéon',    category: 'Gastronomía', icon: 'utensils' },
      ],
      notes: [
        { icon: 'ticket', tone: 'lila',     title: 'Pase Passport',  text: 'Versalles · Palacio + jardines · 10:00' },
        { icon: 'bell',   tone: 'rosa',     title: 'Recordatorio',   text: 'Valida el billete RER antes de subir al andén' },
        { icon: 'mappin', tone: 'lavender', title: 'Despedida',      text: 'Reserva Le Comptoir confirmada · 19:00 · 2 pax' },
      ],
      pins: [[24,40],[40,30],[52,46],[64,36],[74,64]],
    },
  ],
};
