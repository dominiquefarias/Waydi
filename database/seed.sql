-- ============================================================
--  waydi · Datos de ejemplo (viaje "Aventura en París")
--  Ejecutar después de schema.sql
-- ============================================================
USE waydi;

-- Usuario de ejemplo (contraseña: waydi1234)
INSERT INTO users (name, email, password_hash) VALUES
('Demo', 'demo@waydi.app', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Viaje
INSERT INTO trips (user_id, title, subtitle, date_range, city, travelers)
VALUES (1, 'Aventura en París', 'Ruta cultural y gastronómica por la Ciudad de la Luz',
        '12 – 15 Junio 2026', 'París, Francia', 2);

-- ── Día 1 ────────────────────────────────────────────────────
INSERT INTO days (trip_id, position, label, weekday, date, theme)
VALUES (1, 1, 'Día 1', 'Jue', '12 Jun', 'Llegada & íconos');

INSERT INTO activities (day_id, position, time, duration, name, place, category, icon, pin_x, pin_y) VALUES
(1, 1, '09:00', '1 h',   'Desayuno en Café de Flore',  'Saint-Germain-des-Prés', 'Gastronomía', 'coffee',   22, 30),
(1, 2, '11:00', '2 h',   'Torre Eiffel',               'Champ de Mars',          'Cultura',     'landmark', 48, 20),
(1, 3, '14:00', '1.5 h', 'Almuerzo en Le Jules Verne', 'Torre Eiffel · Piso 2',  'Gastronomía', 'utensils', 55, 42),
(1, 4, '16:30', '1 h',   'Paseo por el Sena',          'Quai de Branly',         'Ocio',        'sun',      34, 58),
(1, 5, '20:00', '1.5 h', 'Crucero nocturno',           'Port de la Bourdonnais', 'Ocio',        'boat',     68, 68);

INSERT INTO notes (day_id, position, icon, tone, title, text) VALUES
(1, 1, 'ticket', 'lila',     'Reserva confirmada', 'Le Jules Verne · 14:00 · Mesa para 2'),
(1, 2, 'bell',   'rosa',     'Recordatorio',       'Llega 15 min antes al embarcadero del crucero'),
(1, 3, 'moon',   'lavender', 'Tip de la noche',    'Lleva chaqueta ligera, baja la temperatura junto al río');

-- ── Día 2 ────────────────────────────────────────────────────
INSERT INTO days (trip_id, position, label, weekday, date, theme)
VALUES (1, 2, 'Día 2', 'Vie', '13 Jun', 'Arte & museos');

INSERT INTO activities (day_id, position, time, duration, name, place, category, icon, pin_x, pin_y) VALUES
(2, 1, '09:30', '1 h',   'Desayuno en Angelina',      'Rue de Rivoli',        'Gastronomía', 'coffee',   30, 24),
(2, 2, '11:00', '3 h',   'Museo del Louvre',          'Rue de Rivoli',        'Cultura',     'palette',  44, 38),
(2, 3, '14:30', '1 h',   'Jardines de las Tullerías', 'Place de la Concorde', 'Naturaleza',  'tree',     60, 30),
(2, 4, '16:00', '2 h',   'Museo de Orsay',            'Quai d\'Orsay',        'Cultura',     'landmark', 52, 56),
(2, 5, '19:30', '2 h',   'Cena en Le Marais',         'Rue des Rosiers',      'Gastronomía', 'utensils', 72, 62);

INSERT INTO notes (day_id, position, icon, tone, title, text) VALUES
(2, 1, 'ticket', 'lila',     'Entrada online',  'Louvre · acceso prioritario 11:00 (pirámide)'),
(2, 2, 'bell',   'rosa',     'Recordatorio',    'Orsay cierra a las 18:00 — no te demores en Tullerías'),
(2, 3, 'mappin', 'lavender', 'Transporte',      'Metro L1 → Tuileries, 4 paradas desde el hotel');

-- ── Día 3 ────────────────────────────────────────────────────
INSERT INTO days (trip_id, position, label, weekday, date, theme)
VALUES (1, 3, 'Día 3', 'Sáb', '14 Jun', 'Montmartre & bohemia');

INSERT INTO activities (day_id, position, time, duration, name, place, category, icon, pin_x, pin_y) VALUES
(3, 1, '10:00', '1.5 h', 'Basílica Sacré-Cœur',       'Montmartre',         'Cultura',     'landmark', 40, 22),
(3, 2, '12:00', '1 h',   'Place du Tertre',            'Montmartre',         'Ocio',        'palette',  46, 30),
(3, 3, '13:30', '1.5 h', 'Almuerzo en La Maison Rose', 'Rue de l\'Abreuvoir','Gastronomía', 'utensils', 34, 36),
(3, 4, '15:30', '45 min','Moulin Rouge (foto)',         'Blvd de Clichy',     'Ocio',        'camera',   58, 28),
(3, 5, '18:00', '2 h',   'Galeries Lafayette',         'Blvd Haussmann',     'Compras',     'bag',      66, 58);

INSERT INTO notes (day_id, position, icon, tone, title, text) VALUES
(3, 1, 'bell',   'rosa',     'Recordatorio', 'Sube en el funicular de Montmartre — evita las escaleras'),
(3, 2, 'ticket', 'lila',     'Reserva',      'La Maison Rose · 13:30 · terraza'),
(3, 3, 'mappin', 'lavender', 'Tip',          'Sube a la azotea de Lafayette: vista 360° gratis');

-- ── Día 4 ────────────────────────────────────────────────────
INSERT INTO days (trip_id, position, label, weekday, date, theme)
VALUES (1, 4, 'Día 4', 'Dom', '15 Jun', 'Versalles & despedida');

INSERT INTO activities (day_id, position, time, duration, name, place, category, icon, pin_x, pin_y) VALUES
(4, 1, '08:30', '40 min', 'Tren RER a Versalles',  'Estación Invalides',    'Transporte',  'train',    24, 40),
(4, 2, '10:00', '3 h',    'Palacio de Versalles',  'Versalles',             'Cultura',     'landmark', 40, 30),
(4, 3, '13:00', '1.5 h',  'Picnic en los jardines','Jardines de Versalles', 'Gastronomía', 'tree',     52, 46),
(4, 4, '16:00', '40 min', 'Regreso a París',        'RER C',                'Transporte',  'train',    64, 36),
(4, 5, '19:00', '2 h',    'Cena de despedida',      'Le Comptoir · Odéon',  'Gastronomía', 'utensils', 74, 64);

INSERT INTO notes (day_id, position, icon, tone, title, text) VALUES
(4, 1, 'ticket', 'lila',     'Pase Passport', 'Versalles · Palacio + jardines · 10:00'),
(4, 2, 'bell',   'rosa',     'Recordatorio',  'Valida el billete RER antes de subir al andén'),
(4, 3, 'mappin', 'lavender', 'Despedida',     'Reserva Le Comptoir confirmada · 19:00 · 2 pax');
