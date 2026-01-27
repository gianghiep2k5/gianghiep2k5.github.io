<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Cá Nhân - Nguyễn Thị Thu Trang</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #4f46e5; /* Indigo */
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #818cf8 100%);
            --bg-gradient: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
            --text-main: #1f2937;
            --text-secondary: #6b7280;
            --card-bg: #ffffff;
            --item-bg: #f9fafb;
            --shadow: 0 10px 40px -10px rgba(79, 70, 229, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-main);
        }
        
        .container {
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: var(--shadow);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        
        /* Header thiết kế lại nhẹ nhàng hơn */
        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 50px 30px 40px;
            text-align: center;
            position: relative;
            clip-path: ellipse(150% 100% at 50% 0%);
        }
        
        .avatar-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 15px;
        }

        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--primary-color);
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 14px;
            font-weight: 300;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .info-section {
            padding: 20px 30px;
            margin-top: 10px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 16px;
            margin-bottom: 12px;
            background: var(--item-bg);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            transition: all 0.2s ease-in-out;
        }
        
        .info-item:hover {
            background: white;
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .icon {
            width: 42px;
            height: 42px;
            background: rgba(79, 70, 229, 0.1); /* Màu primary nhạt */
            color: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
            transition: all 0.3s;
        }

        .info-item:hover .icon {
            background: var(--primary-color);
            color: white;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 11px;
            color: var(--text-secondary);
            margin-bottom: 2px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 15px;
            color: var(--text-main);
            font-weight: 600;
        }
        
        .current-time {
            text-align: center;
            padding: 10px 30px 30px;
            border-top: 1px solid #f3f4f6;
            margin-top: 10px;
        }

        .current-time .info-label {
            margin-top: 20px;
        }
        
        .time-display {
            font-size: 26px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            margin-top: 5px;
            letter-spacing: -0.5px;
            font-variant-numeric: tabular-nums; /* Giúp số không bị nhảy khi thay đổi giây */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="avatar-wrapper">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
            <h1>Giang Lê Hiệp</h1>
            <p>Hồ sơ sinh viên</p>
        </div>
        
        <div class="info-section">
            <div class="info-item">
                <div class="icon"><i class="fa-solid fa-signature"></i></div>
                <div class="info-content">
                    <div class="info-label">Họ và Tên</div>
                    <div class="info-value">Nguyễn Thị Thu Trang</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon"><i class="fa-solid fa-cake-candles"></i></div>
                <div class="info-content">
                    <div class="info-label">Ngày Sinh</div>
                    <div class="info-value">20/02/2005</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="info-content">
                    <div class="info-label">Quê Quán</div>
                    <div class="info-value">Nghệ An, Việt Nam</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon"><i class="fa-solid fa-heart"></i></div>
                <div class="info-content">
                    <div class="info-label">Sở Thích</div>
                    <div class="info-value">Đọc sách, Lập trình, Du lịch, Âm nhạc</div>
                </div>
            </div>
        </div>
        
        <div class="current-time">
            <div class="info-label">Thời gian thực</div>
            <div class="time-display" id="currentTime">--:--:--</div>
        </div>
    </div>
    
    <script>
        function updateTime() {
            const now = new Date();
            // Viết hoa chữ cái đầu cho Thứ
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'numeric', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            // Format lại chuỗi thời gian cho gọn gàng hơn
            let timeString = now.toLocaleDateString('vi-VN', options);
            
            // Tùy chỉnh hiển thị nếu cần (Ví dụ: Thứ Hai, 14/07/2005 - 10:30:45)
            // Hoặc giữ nguyên mặc định
            document.getElementById('currentTime').textContent = timeString;
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>