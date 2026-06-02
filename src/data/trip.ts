import type { CategoryName } from '../lib/categories';

export type NoteTone = 'lila' | 'rosa' | 'lavender';

export interface Activity {
  time: string;
  duration: string;
  name: string;
  place: string;
  category: CategoryName;
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

export const trip: Trip = {
  title: 'Aventura en París',
  subtitle: 'Un viaje lleno de arte, gastronomía y cultura francesa',
  range: '12 – 15 Junio 2026',
  city: 'París, Francia',
  travelers: 2,
  days: [
    {
      label: 'Día 1',
      weekday: 'Jue',
      date: '12 Jun',
      theme: 'Llegada & íconos',
      activities: [
        { time: '09:00', duration: '1 h',   name: 'Desayuno en Café de Flore', place: 'Saint-Germain-des-Prés',  category: 'Gastronomía' },
        { time: '11:00', duration: '2 h',   name: 'Torre Eiffel',              place: 'Champ de Mars',            category: 'Cultura'     },
        { time: '14:00', duration: '1.5 h', name: 'Almuerzo en Le Jules Verne',place: 'Torre Eiffel · Piso 2',   category: 'Gastronomía' },
        { time: '16:30', duration: '1.5 h', name: 'Paseo por el Sena',         place: 'Pont de l\'Alma',          category: 'Ocio'        },
        { time: '20:00', duration: '1.5 h', name: 'Crucero nocturno',           place: 'Puerto de París',         category: 'Ocio'        },
      ],
      notes: [
        { icon: 'Ticket',  tone: 'lila',    title: 'Reserva crucero',    text: 'Confirmación #WYD-2847 · Embarque 19:45' },
        { icon: 'Bell',    tone: 'rosa',    title: 'Recordatorio',       text: 'Llevar pasaporte para acceso VIP Torre Eiffel' },
        { icon: 'MapPin',  tone: 'lavender',title: 'Tip local',          text: 'Café de Flore abre a las 7:30 — llega antes de las 9:00 para evitar fila' },
      ],
      pins: [[42,38],[28,55],[32,58],[55,65],[48,72]],
    },
    {
      label: 'Día 2',
      weekday: 'Vie',
      date: '13 Jun',
      theme: 'Arte & museos',
      activities: [
        { time: '09:30', duration: '1 h',   name: 'Desayuno en Angelina',       place: 'Rue de Rivoli',                         category: 'Gastronomía' },
        { time: '11:00', duration: '3 h',   name: 'Museo del Louvre',            place: '1er Arrondissement',                    category: 'Cultura'     },
        { time: '14:30', duration: '1 h',   name: 'Jardines de las Tullerías',   place: 'Entre el Louvre y Place de la Concorde',category: 'Naturaleza'  },
        { time: '16:00', duration: '2 h',   name: 'Museo de Orsay',              place: '7e Arrondissement',                     category: 'Cultura'     },
        { time: '19:30', duration: '1.5 h', name: 'Cena en Le Marais',           place: '4e Arrondissement',                     category: 'Gastronomía' },
      ],
      notes: [
        { icon: 'Ticket',  tone: 'lila',    title: 'Entrada Louvre',     text: 'Reserva online · Evita filas · Entra por Pirámide' },
        { icon: 'Moon',    tone: 'lavender',title: 'Tip nocturno',       text: 'Le Marais tiene vida nocturna hasta tarde los viernes' },
        { icon: 'Bell',    tone: 'rosa',    title: 'Recordatorio',       text: 'Guardar mapa del Louvre — es fácil perderse' },
      ],
      pins: [[38,42],[35,48],[45,52],[40,60],[58,55]],
    },
    {
      label: 'Día 3',
      weekday: 'Sáb',
      date: '14 Jun',
      theme: 'Montmartre & bohemia',
      activities: [
        { time: '10:00', duration: '1.5 h', name: 'Sacré-Cœur',                 place: 'Montmartre, 18e',          category: 'Cultura'     },
        { time: '12:00', duration: '1 h',   name: 'Place du Tertre',             place: 'Montmartre',               category: 'Ocio'        },
        { time: '13:30', duration: '1.5 h', name: 'Almuerzo en La Maison Rose',  place: 'Rue de l\'Abreuvoir',      category: 'Gastronomía' },
        { time: '15:30', duration: '0.5 h', name: 'Moulin Rouge (foto)',          place: 'Boulevard de Clichy',      category: 'Ocio'        },
        { time: '18:00', duration: '2 h',   name: 'Galeries Lafayette',          place: 'Boulevard Haussmann',      category: 'Compras'     },
      ],
      notes: [
        { icon: 'MapPin',  tone: 'lila',    title: 'Acceso Sacré-Cœur', text: 'Sube por el funicular · Vistas panorámicas al llegar' },
        { icon: 'Bell',    tone: 'rosa',    title: 'Recordatorio',      text: 'Galeries Lafayette cierra a las 20:30 los sábados' },
        { icon: 'Ticket',  tone: 'lavender',title: 'Tip compras',       text: 'Pide el formulario de Tax Refund en caja · Mínimo €100' },
      ],
      pins: [[30,25],[32,28],[34,30],[38,22],[52,38]],
    },
    {
      label: 'Día 4',
      weekday: 'Dom',
      date: '15 Jun',
      theme: 'Versalles & despedida',
      activities: [
        { time: '08:30', duration: '0.5 h', name: 'Tren RER a Versalles',        place: 'Estación Saint-Lazare',          category: 'Transporte'  },
        { time: '10:00', duration: '3 h',   name: 'Palacio de Versalles',        place: 'Versalles, Yvelines',            category: 'Cultura'     },
        { time: '13:00', duration: '1.5 h', name: 'Picnic en los jardines',      place: 'Jardines de Versalles',          category: 'Naturaleza'  },
        { time: '16:00', duration: '0.5 h', name: 'Regreso a París',             place: 'Estación de Versalles-RD',       category: 'Transporte'  },
        { time: '19:00', duration: '2 h',   name: 'Cena de despedida',           place: 'Le Grand Véfour, Palais Royal',  category: 'Gastronomía' },
      ],
      notes: [
        { icon: 'TrainFront', tone: 'lila',    title: 'RER C',           text: 'Sale cada 15 min · Línea C · Bajarse en Versailles-RG' },
        { icon: 'Ticket',     tone: 'rosa',    title: 'Entrada Palacio', text: 'Reserva con skip-the-line · Código QR en el email' },
        { icon: 'Bell',       tone: 'lavender',title: 'Reserva cena',    text: 'Le Grand Véfour · Mesa 19:00 · A nombre de Ana' },
      ],
      pins: [[50,50],[20,70],[22,72],[50,50],[55,58]],
    },
  ],
};
