const express = require('express');
const cors = require('cors');
const forgotPasswordRoutes = require('./routes/forgotPassword');

const app = express();

// Middleware
app.use(cors());
app.use(express.json());

// Routes
app.use('/api/forgot-password', forgotPasswordRoutes);

// Error handling
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({
    success: false,
    message: 'Đã xảy ra lỗi, vui lòng thử lại sau'
  });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
}); 