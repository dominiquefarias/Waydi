interface IconProps {
  size?: number;
  sw?: number;
  className?: string;
}

function I({ d, size = 20, sw = 1.7, className, children }: IconProps & { d?: string; children?: React.ReactNode }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none"
      stroke="currentColor" strokeWidth={sw} strokeLinecap="round" strokeLinejoin="round"
      className={className}>
      {d ? <path d={d} /> : children}
    </svg>
  );
}

export const CoffeeIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M17 8h1a3 3 0 0 1 0 6h-1"/>
    <path d="M3 8h14v6a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/>
    <path d="M6 2v2M10 2v2M14 2v2"/>
  </I>
);

export const LandmarkIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M3 21h18"/>
    <path d="M5 21V10M9 21V10M15 21V10M19 21V10"/>
    <path d="m12 3 8 5H4l8-5Z"/>
  </I>
);

export const UtensilsIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M4 3v7a2 2 0 0 0 2 2v9M7 3v6M5 3v6"/>
    <path d="M18 3c-1.5 0-3 1.5-3 4s1.5 4 3 4v9"/>
  </I>
);

export const BoatIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M3 14h18l-2 5H5l-2-5Z"/>
    <path d="M12 14V5M12 5 7 9M12 5l5 4"/>
  </I>
);

export const SparklesIcon = (p: IconProps) => (
  <I {...p}>
    <path d="m12 4 1.6 4.4L18 10l-4.4 1.6L12 16l-1.6-4.4L6 10l4.4-1.6Z"/>
    <path d="M19 15l.7 1.8L21.5 17l-1.8.7L19 19.5l-.7-1.8L16.5 17l1.8-.5Z"/>
  </I>
);

export const TreeIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M12 22v-7"/>
    <path d="M9 15a4 4 0 0 1-1-7.5A4 4 0 0 1 15.5 6 4 4 0 0 1 15 15Z"/>
  </I>
);

export const CameraIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M3 8h3l2-2h8l2 2h3v11H3Z"/>
    <circle cx="12" cy="13" r="3.2"/>
  </I>
);

export const TrainIcon = (p: IconProps) => (
  <I {...p}>
    <rect x="5" y="3" width="14" height="13" rx="3"/>
    <path d="M5 11h14M9 16l-2 4M15 16l2 4"/>
    <circle cx="9" cy="8" r="0.6" fill="currentColor"/>
    <circle cx="15" cy="8" r="0.6" fill="currentColor"/>
  </I>
);

export const BagIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M6 8h12l-1 12H7L6 8Z"/>
    <path d="M9 8a3 3 0 0 1 6 0"/>
  </I>
);

export const PaletteIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.5-.9 1.5-1.7 0-1.3-1.1-1.6-1.1-2.8 0-.8.7-1.5 1.6-1.5H16a5 5 0 0 0 5-5c0-3.9-4-7-9-7Z"/>
    <circle cx="7.5" cy="11" r="0.9" fill="currentColor"/>
    <circle cx="11" cy="7.5" r="0.9" fill="currentColor"/>
    <circle cx="15" cy="8.5" r="0.9" fill="currentColor"/>
  </I>
);

export const SunIcon = (p: IconProps) => (
  <I {...p}>
    <circle cx="12" cy="12" r="4"/>
    <path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5 19 19M19 5l-1.5 1.5M6.5 17.5 5 19"/>
  </I>
);

export const MapPinIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"/>
    <circle cx="12" cy="10" r="2.5"/>
  </I>
);

export const ClockIcon = (p: IconProps) => (
  <I {...p}>
    <circle cx="12" cy="12" r="9"/>
    <path d="M12 7v5l3 2"/>
  </I>
);

export const PlusIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M12 5v14M5 12h14"/>
  </I>
);

export const ChevronLeftIcon = (p: IconProps) => (
  <I {...p}><path d="m15 18-6-6 6-6"/></I>
);

export const ChevronRightIcon = (p: IconProps) => (
  <I {...p}><path d="m9 6 6 6-6 6"/></I>
);

export const ShareIcon = (p: IconProps) => (
  <I {...p}>
    <circle cx="18" cy="5" r="3"/>
    <circle cx="6" cy="12" r="3"/>
    <circle cx="18" cy="19" r="3"/>
    <path d="m8.6 13.5 6.8 4M15.4 6.5 8.6 10.5"/>
  </I>
);

export const ExpandIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
  </I>
);

export const BellIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
  </I>
);

export const TicketIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2 2 2 0 0 0 0 6 2 2 0 0 1-2 2H5a2 2 0 0 1-2-2 2 2 0 0 0 0-6Z"/>
    <path d="M13 7v10" strokeDasharray="2 2"/>
  </I>
);

export const MoonIcon = (p: IconProps) => (
  <I {...p}>
    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>
  </I>
);

const ICON_MAP: Record<string, (p: IconProps) => React.ReactElement> = {
  coffee: CoffeeIcon,
  landmark: LandmarkIcon,
  utensils: UtensilsIcon,
  boat: BoatIcon,
  sparkles: SparklesIcon,
  tree: TreeIcon,
  camera: CameraIcon,
  train: TrainIcon,
  bag: BagIcon,
  palette: PaletteIcon,
  sun: SunIcon,
  mappin: MapPinIcon,
  clock: ClockIcon,
  plus: PlusIcon,
  chevL: ChevronLeftIcon,
  chevR: ChevronRightIcon,
  share: ShareIcon,
  expand: ExpandIcon,
  bell: BellIcon,
  ticket: TicketIcon,
  moon: MoonIcon,
};

export function getIcon(name: string): (p: IconProps) => React.ReactElement {
  return ICON_MAP[name] ?? MapPinIcon;
}
