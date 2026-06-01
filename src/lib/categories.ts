import type { Category, NoteTone } from '../data/trip';

export interface CategoryStyle {
  chip: string;
  soft: string;
  dot: string;
  ic: string;
}

export const CAT: Record<Category, CategoryStyle> = {
  'Cultura':     { chip: 'bg-[#ECE3FF] text-[#6A48C0]', soft: 'bg-[#F3EDFF]', dot: '#A98BF0', ic: 'text-[#6A48C0]' },
  'Gastronomía': { chip: 'bg-[#FCE0EC] text-[#C0567E]', soft: 'bg-[#FDEBF2]', dot: '#F0A0C2', ic: 'text-[#C0567E]' },
  'Ocio':        { chip: 'bg-[#E3ECFF] text-[#5772BE]', soft: 'bg-[#EDF2FF]', dot: '#9DB6F0', ic: 'text-[#5772BE]' },
  'Naturaleza':  { chip: 'bg-[#DDF2E8] text-[#4C9C77]', soft: 'bg-[#EAF7F0]', dot: '#8FD9B6', ic: 'text-[#4C9C77]' },
  'Transporte':  { chip: 'bg-[#E9E5F5] text-[#766AA4]', soft: 'bg-[#F1EEF9]', dot: '#B3A6D8', ic: 'text-[#766AA4]' },
  'Compras':     { chip: 'bg-[#FBEAD7] text-[#B8854A]', soft: 'bg-[#FCF1E4]', dot: '#E9C79A', ic: 'text-[#B8854A]' },
};

export interface ToneStyle {
  wrap: string;
  ic: string;
}

export const TONE: Record<NoteTone, ToneStyle> = {
  lila:     { wrap: 'bg-[#F1EBFF]', ic: 'bg-[#DCD0FF] text-[#6A48C0]' },
  rosa:     { wrap: 'bg-[#FDEBF2]', ic: 'bg-[#FBD8E8] text-[#C0567E]' },
  lavender: { wrap: 'bg-[#EFEBFB]', ic: 'bg-[#E2DAF7] text-[#766AA4]' },
};
