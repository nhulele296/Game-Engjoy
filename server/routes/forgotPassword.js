const express = require('express');
const router = express.Router();
const { sendOTPEmail } = require('../services/emailService');

// Lưu trữ OTP tạm thời (trong thực tế nên lưu vào database)
const otpStore = new Map();

// API gửi OTP
router.post('/send-otp', async (req, res) => {
  try {
    const { email } = req.body;
    
    // Kiểm tra email tồn tại trong hệ thống
    // TODO: Thêm logic kiểm tra email trong database
    
    // Gửi OTP qua email
    const { otp } = await sendOTPEmail(email);
    
    // Lưu OTP và thời gian tạo
    otpStore.set(email, {
      otp,
      createdAt: new Date(),
      attempts: 0
    });
    
    res.json({ 
      success: true, 
      message: 'OTP đã được gửi đến email của bạn' 
    });
  } catch (error) {
    console.error('Error in send-otp:', error);
    res.status(500).json({ 
      success: false, 
      message: error.message || 'Không thể gửi mã xác nhận' 
    });
  }
});

// API xác thực OTP và đổi mật khẩu
router.post('/reset', async (req, res) => {
  try {
    const { email, otp, newPassword } = req.body;
    
    // Kiểm tra OTP
    const storedOTPData = otpStore.get(email);
    if (!storedOTPData) {
      return res.status(400).json({
        success: false,
        message: 'OTP không hợp lệ hoặc đã hết hạn'
      });
    }

    // Kiểm tra số lần thử
    if (storedOTPData.attempts >= 3) {
      otpStore.delete(email);
      return res.status(400).json({
        success: false,
        message: 'Bạn đã nhập sai quá nhiều lần. Vui lòng yêu cầu OTP mới'
      });
    }

    // Kiểm tra thời gian hết hạn (5 phút)
    const now = new Date();
    const timeDiff = now - storedOTPData.createdAt;
    if (timeDiff > 5 * 60 * 1000) {
      otpStore.delete(email);
      return res.status(400).json({
        success: false,
        message: 'OTP đã hết hạn'
      });
    }

    // Kiểm tra OTP có đúng không
    if (storedOTPData.otp !== otp) {
      storedOTPData.attempts++;
      return res.status(400).json({
        success: false,
        message: 'OTP không chính xác'
      });
    }

    // TODO: Cập nhật mật khẩu mới trong database
    // const hashedPassword = await bcrypt.hash(newPassword, 10);
    // await User.updatePassword(email, hashedPassword);

    // Xóa OTP đã sử dụng
    otpStore.delete(email);

    res.json({
      success: true,
      message: 'Mật khẩu đã được đặt lại thành công'
    });
  } catch (error) {
    console.error('Error in reset-password:', error);
    res.status(500).json({
      success: false,
      message: error.message || 'Không thể đặt lại mật khẩu'
    });
  }
});

module.exports = router; 