const router = require('express').Router();
const auth   = require('../middleware/auth');
const db     = require('../db');

// Todos los endpoints requieren auth
router.use(auth);

/* ── GET /api/trips ──────────────────────────────────────── */
router.get('/', async (req, res) => {
  try {
    const [trips] = await db.query(
      'SELECT * FROM trips WHERE user_id = ? ORDER BY created_at DESC',
      [req.userId]
    );
    res.json(trips);
  } catch (err) {
    res.status(500).json({ error: 'Error al obtener viajes' });
  }
});

/* ── GET /api/trips/:id (viaje completo con días/actividades/notas) */
router.get('/:id', async (req, res) => {
  try {
    const [trips] = await db.query(
      'SELECT * FROM trips WHERE id = ? AND user_id = ?',
      [req.params.id, req.userId]
    );
    if (!trips.length) return res.status(404).json({ error: 'Viaje no encontrado' });

    const trip = trips[0];

    const [days] = await db.query(
      'SELECT * FROM days WHERE trip_id = ? ORDER BY position',
      [trip.id]
    );

    for (const day of days) {
      const [activities] = await db.query(
        'SELECT * FROM activities WHERE day_id = ? ORDER BY position',
        [day.id]
      );
      const [notes] = await db.query(
        'SELECT * FROM notes WHERE day_id = ? ORDER BY position',
        [day.id]
      );
      day.activities = activities;
      day.notes      = notes;
    }

    trip.days = days;
    res.json(trip);
  } catch (err) {
    res.status(500).json({ error: 'Error al obtener el viaje' });
  }
});

/* ── POST /api/trips ─────────────────────────────────────── */
router.post('/', async (req, res) => {
  const { title, subtitle, date_range, city, travelers } = req.body;
  if (!title) return res.status(400).json({ error: 'El título es obligatorio' });

  try {
    const [result] = await db.query(
      'INSERT INTO trips (user_id, title, subtitle, date_range, city, travelers) VALUES (?,?,?,?,?,?)',
      [req.userId, title, subtitle, date_range, city, travelers || 1]
    );
    const [rows] = await db.query('SELECT * FROM trips WHERE id = ?', [result.insertId]);
    res.status(201).json(rows[0]);
  } catch (err) {
    res.status(500).json({ error: 'Error al crear el viaje' });
  }
});

/* ── PUT /api/trips/:id ──────────────────────────────────── */
router.put('/:id', async (req, res) => {
  const { title, subtitle, date_range, city, travelers } = req.body;
  try {
    const [check] = await db.query(
      'SELECT id FROM trips WHERE id = ? AND user_id = ?',
      [req.params.id, req.userId]
    );
    if (!check.length) return res.status(404).json({ error: 'Viaje no encontrado' });

    await db.query(
      'UPDATE trips SET title=?, subtitle=?, date_range=?, city=?, travelers=? WHERE id=?',
      [title, subtitle, date_range, city, travelers, req.params.id]
    );
    res.json({ message: 'Viaje actualizado' });
  } catch (err) {
    res.status(500).json({ error: 'Error al actualizar el viaje' });
  }
});

/* ── DELETE /api/trips/:id ───────────────────────────────── */
router.delete('/:id', async (req, res) => {
  try {
    const [check] = await db.query(
      'SELECT id FROM trips WHERE id = ? AND user_id = ?',
      [req.params.id, req.userId]
    );
    if (!check.length) return res.status(404).json({ error: 'Viaje no encontrado' });

    await db.query('DELETE FROM trips WHERE id = ?', [req.params.id]);
    res.json({ message: 'Viaje eliminado' });
  } catch (err) {
    res.status(500).json({ error: 'Error al eliminar el viaje' });
  }
});

/* ── POST /api/trips/:id/days ────────────────────────────── */
router.post('/:id/days', async (req, res) => {
  const { label, weekday, date, theme, position } = req.body;
  try {
    const [check] = await db.query(
      'SELECT id FROM trips WHERE id = ? AND user_id = ?',
      [req.params.id, req.userId]
    );
    if (!check.length) return res.status(404).json({ error: 'Viaje no encontrado' });

    const [result] = await db.query(
      'INSERT INTO days (trip_id, position, label, weekday, date, theme) VALUES (?,?,?,?,?,?)',
      [req.params.id, position, label, weekday, date, theme]
    );
    const [rows] = await db.query('SELECT * FROM days WHERE id = ?', [result.insertId]);
    res.status(201).json(rows[0]);
  } catch (err) {
    res.status(500).json({ error: 'Error al crear el día' });
  }
});

/* ── POST /api/trips/:id/days/:dayId/activities ──────────── */
router.post('/:id/days/:dayId/activities', async (req, res) => {
  const { time, duration, name, place, category, icon, pin_x, pin_y, position } = req.body;
  if (!name) return res.status(400).json({ error: 'El nombre es obligatorio' });

  try {
    const [result] = await db.query(
      `INSERT INTO activities (day_id, position, time, duration, name, place, category, icon, pin_x, pin_y)
       VALUES (?,?,?,?,?,?,?,?,?,?)`,
      [req.params.dayId, position, time, duration, name, place, category, icon, pin_x, pin_y]
    );
    const [rows] = await db.query('SELECT * FROM activities WHERE id = ?', [result.insertId]);
    res.status(201).json(rows[0]);
  } catch (err) {
    res.status(500).json({ error: 'Error al crear la actividad' });
  }
});

/* ── PUT /api/trips/:id/days/:dayId/activities/:actId ─────── */
router.put('/:id/days/:dayId/activities/:actId', async (req, res) => {
  const { time, duration, name, place, category, icon, pin_x, pin_y, position } = req.body;
  try {
    await db.query(
      `UPDATE activities SET time=?, duration=?, name=?, place=?, category=?, icon=?, pin_x=?, pin_y=?, position=?
       WHERE id=? AND day_id=?`,
      [time, duration, name, place, category, icon, pin_x, pin_y, position, req.params.actId, req.params.dayId]
    );
    res.json({ message: 'Actividad actualizada' });
  } catch (err) {
    res.status(500).json({ error: 'Error al actualizar la actividad' });
  }
});

/* ── DELETE /api/trips/:id/days/:dayId/activities/:actId ──── */
router.delete('/:id/days/:dayId/activities/:actId', async (req, res) => {
  try {
    await db.query('DELETE FROM activities WHERE id = ? AND day_id = ?',
      [req.params.actId, req.params.dayId]);
    res.json({ message: 'Actividad eliminada' });
  } catch (err) {
    res.status(500).json({ error: 'Error al eliminar la actividad' });
  }
});

module.exports = router;
