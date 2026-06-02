import { Landmark, Utensils, Sparkles, Trees, TrainFront, ShoppingBag } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

export type CategoryName = 'Cultura' | 'Gastronomía' | 'Ocio' | 'Naturaleza' | 'Transporte' | 'Compras';

export interface CategoryConfig {
  chipBg: string;
  chipText: string;
  iconBg: string;
  pinColor: string;
  Icon: LucideIcon;
}

export const categories: Record<CategoryName, CategoryConfig> = {
  Cultura: {
    chipBg: '#ECE3FF',
    chipText: '#6A48C0',
    iconBg: '#F3EDFF',
    pinColor: '#A98BF0',
    Icon: Landmark,
  },
  Gastronomía: {
    chipBg: '#FCE0EC',
    chipText: '#C0567E',
    iconBg: '#FDEBF2',
    pinColor: '#F0A0C2',
    Icon: Utensils,
  },
  Ocio: {
    chipBg: '#E3ECFF',
    chipText: '#5772BE',
    iconBg: '#EDF2FF',
    pinColor: '#9DB6F0',
    Icon: Sparkles,
  },
  Naturaleza: {
    chipBg: '#DDF2E8',
    chipText: '#4C9C77',
    iconBg: '#EAF7F0',
    pinColor: '#8FD9B6',
    Icon: Trees,
  },
  Transporte: {
    chipBg: '#E9E5F5',
    chipText: '#766AA4',
    iconBg: '#F1EEF9',
    pinColor: '#B3A6D8',
    Icon: TrainFront,
  },
  Compras: {
    chipBg: '#FBEAD7',
    chipText: '#B8854A',
    iconBg: '#FCF1E4',
    pinColor: '#E9C79A',
    Icon: ShoppingBag,
  },
};
