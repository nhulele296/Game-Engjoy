const nodemailer = require('nodemailer');
require('dotenv').config();

// Cấu hình transporter cho nodemailer
const transporter = nodemailer.createTransport({
  service: 'gmail',
  auth: {
    user: process.env.EMAIL_USER,
    pass: process.env.EMAIL_APP_PASSWORD
  }
});

// Tạo mã OTP ngẫu nhiên
function generateOTP() {
  return Math.floor(100000 + Math.random() * 900000).toString();
}

// Hàm gửi email
async function sendOTPEmail(email) {
  const otp = generateOTP();
  
  const mailOptions = {
    from: process.env.EMAIL_USER,
    to: email,
    subject: 'Mã xác nhận đặt lại mật khẩu',
    html: `
      <h2>Đặt lại mật khẩu</h2>
      <p>Mã xác nhận của bạn là: <strong>${otp}</strong></p>
      <p>Mã này sẽ hết hạn sau 5 phút.</p>
      <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
    `
  };

  try {
    await transporter.sendMail(mailOptions);
    return { success: true, otp };
  } catch (error) {
    console.error('Error sending email:', error);
    throw new Error('Không thể gửi email');
  }
}

module.exports = {
  sendOTPEmail
}; 