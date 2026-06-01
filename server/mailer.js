const nodemailer = require('nodemailer');
require('dotenv').config();

const transporter = nodemailer.createTransport({
  host:   process.env.SMTP_HOST,
  port:   Number(process.env.SMTP_PORT) || 587,
  secure: process.env.SMTP_SECURE === 'true',
  auth: {
    user: process.env.SMTP_USER,
    pass: process.env.SMTP_PASS,
  },
});

async function sendPasswordReset({ to, name, resetUrl }) {
  await transporter.sendMail({
    from:    `"waydi" <${process.env.SMTP_FROM || process.env.SMTP_USER}>`,
    to,
    subject: 'Recupera tu contraseña · waydi',
    html: `
      <div style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;max-width:480px;margin:auto;padding:32px;background:#F6F4FC;border-radius:24px;">
        <h1 style="font-size:24px;color:#332E45;margin-bottom:8px;">Hola, ${name} 👋</h1>
        <p style="color:#807A95;font-size:15px;line-height:1.6;">
          Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>waydi</strong>.
        </p>
        <a href="${resetUrl}"
           style="display:inline-block;margin:24px 0;padding:14px 28px;background:#332E45;color:#DCD0FF;
                  border-radius:14px;text-decoration:none;font-weight:700;font-size:15px;">
          Restablecer contraseña
        </a>
        <p style="color:#A9A3BC;font-size:13px;">
          Este enlace expira en <strong>1 hora</strong>. Si no solicitaste el cambio, ignora este email.
        </p>
        <hr style="border:none;border-top:1px solid #ECE6FB;margin:24px 0;" />
        <p style="color:#A9A3BC;font-size:11px;text-align:center;">
          waydi · planifica suave, viaja ligero
        </p>
      </div>
    `,
  });
}

module.exports = { sendPasswordReset };
