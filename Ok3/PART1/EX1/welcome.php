<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EX2 WELCOME</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --border-width: 3px;
            --border-color: #000000;
            --shadow-offset: 6px;
            --bg-color: #f0f0f0;
            --card-color: #ffffff;
            
            /* Palette màu Neo Pop */
            --color-purple: #a855f7;
            --color-yellow: #facc15;
            --color-blue: #3b82f6;
            --color-pink: #f472b6;
            --color-green: #4ade80;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Lexend', sans-serif;
            background-color: #e0e7ff;
            /* Tạo hoạ tiết chấm bi nền */
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* --- Main Container --- */
        .main-card {
            display: flex;
            width: 100%;
            max-width: 800px;
            background: var(--card-color);
            border: 4px solid var(--border-color); /* Viền bao ngoài cực dày */
            border-radius: 20px;
            /* Bóng đổ cứng (Hard Shadow) */
            box-shadow: 10px 10px 0px var(--border-color);
            overflow: hidden;
            flex-wrap: wrap; /* Để responsive trên mobile */
        }

        /* --- Cột Trái (Màu nền đậm) --- */
        .left-panel {
            flex: 2;
            min-width: 300px;
            background: var(--color-purple);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 4px solid var(--border-color);
            position: relative;
        }

        .brand-badge {
            background: black;
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 12px;
            width: fit-content;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .greeting-section h1 {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 10px;
            text-shadow: 3px 3px 0px #000; /* Chữ cũng có bóng cứng */
        }

        .greeting-section p {
            font-size: 18px;
            font-weight: 500;
            opacity: 1;
            color: #f3e8ff;
        }

        .big-icon {
            font-size: 80px;
            margin-top: 30px;
            filter: drop-shadow(5px 5px 0px rgba(0,0,0,0.3));
            animation: bounce 3s infinite ease-in-out;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* --- Cột Phải (Thông tin) --- */
        .right-panel {
            flex: 3;
            min-width: 320px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Các khối thông tin con (Info Boxes) */
        .info-box {
            background: white;
            border: var(--border-width) solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow-offset) var(--shadow-offset) 0px var(--border-color);
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        /* Hiệu ứng nhấn nút khi hover */
        .info-box:hover {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0px var(--border-color);
        }

        .box-icon {
            width: 50px;
            height: 50px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-right: 15px;
            color: black;
        }

        .content h3 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 2px;
        }

        .content p {
            font-size: 22px;
            font-weight: 800;
            color: black;
        }

        /* Màu riêng cho từng box */
        .box-yellow { background-color: var(--color-yellow); }
        .box-blue { background-color: var(--color-blue); color: white; }
        .box-pink { background-color: var(--color-pink); }
        
        /* Box tiến độ (Full width) */
        .progress-wrapper {
            background: white;
            border: var(--border-width) solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow-offset) var(--shadow-offset) 0px var(--border-color);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .progress-track {
            width: 100%;
            height: 24px;
            background: #e5e7eb;
            border: 2px solid var(--border-color);
            border-radius: 50px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: var(--color-green);
            border-right: 2px solid var(--border-color); /* Viền phải của thanh load */
            width: 50%;
            transition: width 1s ease;
            
            /* Hoạ tiết sọc chéo cho thanh progress */
            background-image: linear-gradient(
                45deg, 
                rgba(0,0,0,0.1) 25%, 
                transparent 25%, 
                transparent 50%, 
                rgba(0,0,0,0.1) 50%, 
                rgba(0,0,0,0.1) 75%, 
                transparent 75%, 
                transparent
            );
            background-size: 20px 20px;
        }

        /* Responsive Mobile */
        @media (max-width: 768px) {
            .left-panel { border-right: none; border-bottom: 4px solid var(--border-color); }
        }

    </style>
</head>
<body>

    <div class="main-card">
        
        <div class="left-panel" id="panelBg">
            <div>
                <div class="brand-badge">DAILY TRACKER</div>
                <div class="greeting-section">
                    <h1 id="greeting">HELLO<br>THERE!</h1>
                    <p id="timeText">Chúc một ngày tốt lành</p>
                </div>
            </div>
            <div class="big-icon" id="weatherIcon">
                <i class="fa-solid fa-sun"></i>
            </div>
        </div>

        <div class="right-panel">
            
            <div class="info-box">
                <div class="box-icon box-yellow">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div class="content">
                    <h3>Giờ hiện tại</h3>
                    <p id="clock">00:00</p>
                </div>
            </div>

            <div class="info-box">
                <div class="box-icon box-blue">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div class="content">
                    <h3>Hôm nay là</h3>
                    <p id="fullDate">...</p>
                </div>
            </div>

            <div class="progress-wrapper">
                <div class="progress-header">
                    <span>⏳ Còn lại: <span id="daysLeft" style="color: var(--color-purple);">...</span></span>
                    <span id="percentText">0%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" id="monthProgress"></div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function updateUI() {
            const now = new Date();

            // 1. Cập nhật Giờ
            document.getElementById('clock').innerText = now.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});

            // 2. Logic Chào hỏi & Màu sắc
            const h = now.getHours();
            let greet = "CHÀO BUỔI SÁNG";
            let sub = "Năng lượng đỉnh cao! ⚡";
            let icon = '<i class="fa-solid fa-cloud-sun"></i>';
            let color = "#8b5cf6"; // Tím mặc định

            if (h >= 5 && h < 12) {
                // Sáng giữ nguyên tím
            } else if (h >= 12 && h < 18) {
                greet = "CHÀO BUỔI CHIỀU";
                sub = "Giữ vững phong độ nhé! 🔥";
                icon = '<i class="fa-solid fa-sun"></i>';
                color = "#3b82f6"; // Xanh dương
                
            } else {
                greet = "CHÀO BUỔI TỐI";
                sub = "Thư giãn thôi nào 🌙";
                icon = '<i class="fa-solid fa-moon"></i>';
                color = "#1e293b"; // Xanh đen
            }

            document.getElementById('greeting').innerHTML = greet.replace(" ", "<br>"); // Xuống dòng cho đẹp
            document.getElementById('timeText').innerText = sub;
            document.getElementById('weatherIcon').innerHTML = icon;
            document.getElementById('panelBg').style.background = color;

            // 3. Ngày tháng
            const days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
            const dateStr = `${days[now.getDay()]}, ${now.getDate()}/${now.getMonth()+1}`;
            document.getElementById('fullDate').innerText = dateStr;

            // 4. Tiến độ tháng
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
            const today = now.getDate();
            const left = lastDay - today;
            const percent = Math.round((today / lastDay) * 100);

            document.getElementById('daysLeft').innerText = `${left} ngày`;
            document.getElementById('percentText').innerText = `${percent}%`;
            document.getElementById('monthProgress').style.width = `${percent}%`;
        }

        setInterval(updateUI, 1000);
        updateUI();
    </script>
</body>
</html>