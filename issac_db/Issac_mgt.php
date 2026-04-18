<?php
session_start();

// ==========================================
// 0. LOGIN SESSION
// ==========================================
$isLoggedIn = $_SESSION['issac_logged_in'] ?? false;
$loginError = '';
$loginUsername = '';
$loginPassword = '';
$validUser = 'admin';
$validPass = 'issac2026';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $loginUsername = trim($_POST['username'] ?? '');
        $loginPassword = $_POST['password'] ?? '';
        if ($loginUsername === $validUser && $loginPassword === $validPass) {
            $_SESSION['issac_logged_in'] = true;
            $_SESSION['issac_user'] = $loginUsername;
            $isLoggedIn = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        $loginError = 'Tên đăng nhập hoặc mật khẩu không đúng. Vui lòng thử lại.';
    }
    if ($_POST['action'] === 'logout') {
        session_unset();
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Nếu chưa đăng nhập, hiển thị form login và dừng render dashboard.
if (!$isLoggedIn) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ISSAC | Đăng nhập</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%); }
            .glass-card { background: rgba(255,255,255,0.82); backdrop-filter: blur(18px); border: 1px solid rgba(255,255,255,0.64); }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-lg glass-card rounded-[32px] shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-10 text-white text-center">
                <div class="text-4xl font-black mb-3">ISSAC CLUB</div>
                <div class="text-sm uppercase tracking-[4px] text-blue-200">Hệ thống quản lý thành viên</div>
            </div>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 mb-4">Đăng nhập</h2>
                <p class="text-sm text-slate-500 mb-6">Nhập tài khoản để tiếp tục truy cập hệ thống quản lý ISSAC.</p>
                <?php if ($loginError): ?>
                    <div class="mb-6 rounded-3xl bg-red-50 border border-red-200 text-red-700 px-5 py-4"><?= htmlspecialchars($loginError) ?></div>
                <?php endif; ?>
                <form action="" method="POST" class="space-y-5">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="text-[11px] font-black uppercase tracking-[2px] text-slate-400">Tài khoản</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($loginUsername) ?>" required class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200" placeholder="admin">
                    </div>
                    <div>
                        <label class="text-[11px] font-black uppercase tracking-[2px] text-slate-400">Mật khẩu</label>
                        <input type="password" name="password" required class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200" placeholder="••••••••">
                    </div>
                    <button type="submit" class="w-full rounded-3xl bg-blue-900 text-white py-4 font-black uppercase tracking-[2px] hover:bg-blue-800 transition">Đăng nhập</button>
                </form>
                <div class="mt-6 text-center text-slate-500 text-xs">Tài khoản mẫu: <span class="font-bold">admin</span> / <span class="font-bold">issac2026</span></div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ==========================================
// 1. KẾT NỐI DATABASE
// ==========================================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "issac_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("<div style='color:red; padding:20px;'>Lỗi kết nối: " . $conn->connect_error . " - Hãy đảm bảo bạn đã tạo database 'issac_db'</div>");
}
mysqli_set_charset($conn, "utf8mb4");

// ==========================================
// 2. XỬ LÝ LOGIC ĐÁNH GIÁ (KHI SUBMIT FORM)
// ==========================================
$message = "";
$alertType = "";
$mailResult = "";
$dept = isset($_GET['dept']) ? $_GET['dept'] : 'all';

$departmentOptions = ['Ban Chủ nhiệm', 'Ban Truyền thông'];
$departments = ['all'];
$deptRes = $conn->query("SELECT DISTINCT department FROM members ORDER BY department ASC");
$seenDepartments = [];
while ($d = $deptRes->fetch_assoc()) {
    $label = trim($d['department']);
    if ($label === '') {
        continue;
    }
    $key = mb_strtolower($label, 'UTF-8');
    if (!isset($seenDepartments[$key])) {
        $departments[] = $label;
        $seenDepartments[$key] = true;
    }
}
foreach ($departmentOptions as $defaultDept) {
    $key = mb_strtolower(trim($defaultDept), 'UTF-8');
    if (!isset($seenDepartments[$key])) {
        $departments[] = $defaultDept;
        $seenDepartments[$key] = true;
    }
}

if (!in_array($dept, $departments)) {
    $dept = 'all';
}

$activityOrder = [];
$members = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'evaluate') {
        $id = (int)$_POST['m_id'];
        $on_time = (int)$_POST['on_time'];
        $quality = (int)$_POST['quality'];
        $late = (int)$_POST['late'];
        $support = (int)$_POST['support'];
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);

        // Tính tổng điểm dựa trên tiêu chí ảnh 2
        $score_this_time = $on_time + $quality + $late + $support;

        // Cập nhật điểm cộng dồn vào bảng members
        $update_sql = "UPDATE members SET total_points = total_points + $score_this_time WHERE id = $id";
        
        if ($conn->query($update_sql)) {
            // Lưu vào lịch sử đánh giá
            $conn->query("INSERT INTO evaluations (member_id, on_time_score, quality_score, late_penalty, support_score, comment) 
                          VALUES ($id, $on_time, $quality, $late, $support, '$comment')");

            $message = "success";
            $alertType = "success";

            // Thử gửi mail thông báo nội bộ
            $recipient = 'no-reply@issac.local';
            $subject = 'Đánh giá thành viên ISSAC';
            $body = "Thành viên ID $id đã được đánh giá. Điểm cộng: $score_this_time. Ý kiến: $comment";
            $headers = "From: issac@localhost\r\n";
            if (@mail($recipient, $subject, $body, $headers)) {
                $mailResult = "Đã gửi mail thông báo nội bộ.";
            } else {
                $mailResult = "Mail chưa gửi: cần cấu hình server SMTP/Sendmail.";
            }
        } else {
            $message = "error";
            $alertType = "error";
        }
    }
    
    if ($_POST['action'] == 'add_member') {
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $class_name = mysqli_real_escape_string($conn, $_POST['class_name']);
        $department = mysqli_real_escape_string($conn, $_POST['department']);

        $insert_sql = "INSERT INTO members (fullname, student_id, class_name, department, total_points) 
                       VALUES ('$fullname', '$student_id', '$class_name', '$department', 0)";
        
        if ($conn->query($insert_sql)) {
            $message = "add_success";
            $alertType = "success";
        } else {
            $message = "error";
            $alertType = "error";
        }
    }

    if ($_POST['action'] == 'edit_member') {
        $id = (int)$_POST['m_id'];
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $class_name = mysqli_real_escape_string($conn, $_POST['class_name']);
        $department = mysqli_real_escape_string($conn, $_POST['department']);

        $update_sql = "UPDATE members SET fullname = '$fullname', student_id = '$student_id', class_name = '$class_name', department = '$department' WHERE id = $id";
        if ($conn->query($update_sql)) {
            $message = "edit_success";
            $alertType = "success";
        } else {
            $message = "error";
            $alertType = "error";
        }
    }

    if ($_POST['action'] == 'delete_member') {
        $id = (int)$_POST['m_id'];
        $delete_sql = "DELETE FROM members WHERE id = $id";
        if ($conn->query($delete_sql)) {
            $message = "delete_success";
            $alertType = "success";
        } else {
            $message = "error";
            $alertType = "error";
        }
    }
}

$deptFilter = $conn->real_escape_string($dept);
$query = "SELECT * FROM members";
if ($deptFilter !== 'all') {
    $query .= " WHERE department = '$deptFilter'";
}
$query .= " ORDER BY total_points DESC, fullname ASC";
$res = $conn->query($query);
while ($row = $res->fetch_assoc()) {
    $members[] = $row;
}

foreach ($members as $index => $member) {
    $activityOrder[$member['id']] = $index + 1;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISSAC | Member Evaluation Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #edf2ff 0%, #f8fafc 100%);
            color: #0f172a;
        }

        .issac-sidebar {
            background: linear-gradient(180deg, #102a68 0%, #1e40af 100%);
        }

        .gold-glow {
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.3);
        }

        .member-row:hover {
            background-color: rgba(30, 64, 175, 0.06);
            transform: translateX(2px);
            transition: all 0.2s ease;
        }

        .card-surface {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
        }

        .btn-soft {
            transition: all 0.2s ease;
        }

        .btn-soft:hover {
            transform: translateY(-1px);
        }

        .table-heading {
            background: #f8fafc;
        }

        .modal-active { display: flex !important; }

        .bg-image-sidebar {
            background-image: url('RS&CBK.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="min-h-screen flex overflow-hidden">

    <aside class="w-72 issac-sidebar text-white flex flex-col shadow-2xl bg-image-sidebar relative overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
        <div class="relative z-10 p-8 text-center border-b border-white/10">
            <div class="w-16 h-16 bg-white/20 border border-white/20 rounded-2xl mx-auto flex items-center justify-center mb-4 rotate-3 shadow-lg backdrop-blur-sm">
                <i class="fas fa-paper-plane text-blue-200 text-3xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tighter">ISSAC <span class="text-yellow-400">CLUB</span></h1>
            <p class="text-[10px] uppercase tracking-[3px] text-blue-200 mt-1 font-bold">Ambassadors System</p>
        </div>

        <nav class="p-6 flex-1 space-y-3">
            <a href="#" class="flex items-center p-4 bg-white/10 rounded-2xl text-yellow-400 border-l-4 border-yellow-400">
                <i class="fas fa-chart-pie mr-3"></i> <span class="font-bold">Tổng quan</span>
            </a>
            <div class="pt-4 pb-2 px-4 text-[10px] font-black text-blue-300 uppercase tracking-widest">Ban chuyên môn</div>
            <?php foreach ($departments as $label): ?>
                <a href="?dept=<?= urlencode($label) ?>" class="flex items-center p-4 rounded-2xl transition <?= $dept === $label ? 'bg-white/10 border-l-4 border-yellow-400 text-yellow-300' : 'hover:bg-white/5 text-white' ?>">
                    <?php if ($label === 'all'): ?>
                        <i class="fas fa-globe mr-3"></i> Tất cả ban
                    <?php elseif (strpos($label, 'Truyền thông') !== false): ?>
                        <i class="fas fa-bullhorn mr-3"></i> <?= $label ?>
                    <?php elseif (strpos($label, 'Chủ nhiệm') !== false): ?>
                        <i class="fas fa-user-tie mr-3"></i> <?= $label ?>
                    <?php else: ?>
                        <i class="fas fa-users mr-3"></i> <?= $label ?>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        
        <div class="p-6 text-center text-[10px] text-blue-300 opacity-50">© 2026 VNUIS Ambassadors Club</div>
    </aside>

    <main class="flex-1 flex flex-col h-screen">
        
        <header class="h-20 bg-white/50 backdrop-blur-md border-b flex items-center justify-between px-10">
            <div class="flex items-center bg-white rounded-full px-4 py-2 border shadow-sm w-96">
                <i class="fas fa-search text-gray-300 mr-2"></i>
                <input type="text" placeholder="Tìm tên thành viên hoặc MSSV..." class="bg-transparent outline-none text-sm w-full">
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-xs font-black text-blue-900">ADMIN ISSAC</p>
                    <p class="text-[10px] text-gray-400">Hệ thống đánh giá v3.0</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center font-bold text-blue-900">AD</div>
                <form action="" method="POST" class="ml-2">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-[2px] hover:bg-slate-200 transition">Đăng xuất</button>
                </form>
            </div>
        </header>

        <div class="p-10 flex-1 overflow-y-auto">
            
            <?php if($message == "success"): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 flex flex-col gap-2 animate-pulse">
                <div class="flex justify-between items-center">
                    <span><i class="fas fa-check-circle mr-2"></i> Đã cập nhật điểm thành công!</span>
                    <button onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
                <?php if ($mailResult): ?>
                <div class="text-sm text-slate-700 pl-7"><?= $mailResult ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="flex flex-col lg:flex-row justify-between items-end mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-800">Danh sách thành viên <?= $dept === 'all' ? '' : '- ' . $dept ?></h2>
                    <p class="text-slate-400 text-sm">Quản lý dữ liệu thành viên và đánh giá trong ISSAC.</p>
                </div>
                <button onclick="openAddModal()" class="px-6 py-3 rounded-3xl bg-yellow-400 text-blue-900 font-black uppercase tracking-[2px] hover:bg-yellow-500 transition">Thêm thành viên</button>
            </div>

            <div class="flex flex-wrap gap-3 items-center mb-8">
                    <?php foreach ($departments as $label): ?>
                    <a href="?dept=<?= urlencode($label) ?>" class="px-4 py-2 rounded-2xl font-black text-xs uppercase tracking-[2px] <?= $dept === $label ? 'bg-blue-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        <?= $label === 'all' ? 'Tất cả' : $label ?>
                    </a>
                    <?php endforeach; ?>
                </div>

            <?php if ($message === 'add_success'): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 flex justify-between items-center">
                <span><i class="fas fa-check-circle mr-2"></i> Đã thêm thành viên mới thành công!</span>
                <button type="button" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php elseif ($message === 'edit_success'): ?>
            <div class="bg-blue-100 border border-blue-200 text-blue-700 px-6 py-4 rounded-2xl mb-6 flex justify-between items-center">
                <span><i class="fas fa-check-circle mr-2"></i> Cập nhật thông tin thành viên thành công!</span>
                <button type="button" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php elseif ($message === 'delete_success'): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 flex justify-between items-center">
                <span><i class="fas fa-check-circle mr-2"></i> Xóa thành viên thành công!</span>
                <button type="button" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php elseif ($message === 'error'): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 flex justify-between items-center">
                <span><i class="fas fa-exclamation-circle mr-2"></i> Có lỗi xảy ra, vui lòng thử lại.</span>
                <button type="button" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50 border-b">
                            <th class="p-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-widest">Thành viên</th>
                            <th class="p-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-widest">Ban</th>
                            <th class="p-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-widest">Top hoạt động</th>
                            <th class="p-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-widest">Tổng điểm</th>
                            <th class="p-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($members as $row): ?>
                            <tr class="member-row transition cursor-default">
                                    <td class="p-6">
                                        <div class="flex items-center">
                                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-800 text-white flex items-center justify-center font-bold mr-4 shadow-lg">
                                                <?= substr($row['fullname'], 0, 1) ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-base"><?= $row['fullname'] ?></div>
                                                <div class="text-[11px] font-bold text-blue-400 uppercase"><?= $row['student_id'] ?> • <?= $row['class_name'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-[10px] font-black uppercase tracking-wider border border-blue-100">
                                            <?= $row['department'] ?>
                                        </span>
                                    </td>
                                    <td class="p-6">
                                        <?php $rank = $activityOrder[$row['id']] ?? null; ?>
                                        <?php if ($rank === 1): ?>
                                            <span class="px-4 py-2 rounded-2xl bg-yellow-400 text-blue-900 font-black uppercase tracking-widest text-[10px]">Top 1</span>
                                        <?php elseif ($rank !== null): ?>
                                            <span class="px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 font-black uppercase tracking-widest text-[10px]">Top <?= $rank ?></span>
                                        <?php else: ?>
                                            <span class="px-4 py-2 rounded-2xl bg-slate-100 text-slate-500 font-black uppercase tracking-widest text-[10px]">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-6">
                                        <div class="text-xl font-black text-slate-800 tracking-tighter">
                                            <?= $row['total_points'] ?><span class="text-[10px] text-slate-300 ml-1">đ</span>
                                        </div>
                                    </td>
                                    <td class="p-6 text-center flex flex-nowrap justify-center items-center gap-3 overflow-x-auto">
                                        <button type="button" onclick='openModal(<?= json_encode($row) ?>)' 
                                                class="bg-blue-900 hover:bg-yellow-400 hover:text-blue-900 text-white px-4 py-2.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition shadow-md flex items-center group whitespace-nowrap">
                                            <i class="fas fa-star-half-alt mr-2 group-hover:rotate-12 transition"></i> Đánh giá
                                        </button>
                                        <button type="button" onclick='openEditModal(<?= json_encode($row) ?>)' 
                                                class="bg-slate-100 hover:bg-slate-200 text-slate-800 px-5 py-2.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition shadow-sm flex items-center whitespace-nowrap">
                                            <i class="fas fa-edit mr-2"></i> Sửa
                                        </button>
                                        <form action="" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này?');">
                                            <input type="hidden" name="action" value="delete_member">
                                            <input type="hidden" name="m_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition shadow-sm flex items-center whitespace-nowrap">
                                                <i class="fas fa-trash-alt mr-2"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="evalModal" class="fixed inset-0 bg-blue-900/60 backdrop-blur-xl hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-[40px] w-full max-w-xl shadow-2xl overflow-hidden border border-white/20 animate-in fade-in zoom-in duration-300">
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 p-8 text-white relative">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[4px] text-blue-300 mb-2">Đánh giá thành viên</p>
                    <h3 class="text-3xl font-black" id="m_name">Họ Tên</h3>
                    <p class="text-sm opacity-70" id="m_info">MSSV - Lớp</p>
                </div>
                <button onclick="closeModal()" class="absolute top-8 right-8 text-white/50 hover:text-white transition text-2xl">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <form action="" method="POST" class="p-8">
                <input type="hidden" name="action" value="evaluate">
                <input type="hidden" name="m_id" id="m_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Điểm đúng deadline</label>
                        <input type="number" name="on_time" min="-10" max="10" step="1" value="2" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-blue-900 focus:ring-2 focus:ring-yellow-400 transition" placeholder="Ví dụ: 2 hoặc 0 hoặc -1">
                        <p class="text-[10px] text-slate-500">Nhập số điểm cộng/trừ cho đúng deadline.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Điểm chất lượng</label>
                        <input type="number" name="quality" min="-10" max="10" step="1" value="2" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-blue-900 focus:ring-2 focus:ring-yellow-400 transition" placeholder="Ví dụ: 2 hoặc 0 hoặc -1">
                        <p class="text-[10px] text-slate-500">Nhập điểm đánh giá chất lượng công việc.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-red-400 uppercase tracking-widest ml-2">Điểm trễ deadline</label>
                        <input type="number" name="late" min="-20" max="0" step="1" value="0" class="w-full bg-red-50/50 border-none rounded-2xl p-4 font-bold text-red-600 focus:ring-2 focus:ring-red-400 transition" placeholder="Ví dụ: -4 hoặc 0">
                        <p class="text-[10px] text-slate-500">Nhập điểm trừ khi không đúng hạn.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-green-500 uppercase tracking-widest ml-2">Điểm hỗ trợ</label>
                        <input type="number" name="support" min="0" max="20" step="1" value="0" class="w-full bg-green-50/50 border-none rounded-2xl p-4 font-bold text-green-700 outline-none focus:ring-2 focus:ring-green-400 transition" placeholder="Ví dụ: 5">
                        <p class="text-[10px] text-slate-500">Nhập điểm cộng khi hỗ trợ ban khác.</p>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-2 block">Góp ý từ Ban điều hành ISSAC</label>
                    <textarea name="comment" rows="3" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none placeholder:text-slate-300" placeholder="Nhập lời khuyên hoặc lý do cộng/trừ điểm..."></textarea>
                </div>

                <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-black py-5 rounded-3xl shadow-xl shadow-yellow-400/20 transition-all uppercase tracking-widest text-xs flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-3"></i> Lưu kết quả & Gửi Mail
                </button>
            </form>
        </div>
    </div>

    <div id="addMemberModal" class="fixed inset-0 bg-blue-900/60 backdrop-blur-xl hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-[40px] w-full max-w-2xl shadow-2xl overflow-hidden border border-white/20 animate-in fade-in zoom-in duration-300">
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 p-8 text-white relative">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[4px] text-blue-300 mb-2" id="memberModalLabel">Thêm thành viên</p>
                    <h3 class="text-3xl font-black" id="memberModalTitle">Thêm dữ liệu thành viên mới</h3>
                    <p class="text-sm opacity-70" id="memberModalSubtitle">Chọn ban và nhập thông tin để thêm thành viên vào hệ thống.</p>
                </div>
                <button type="button" onclick="closeAddModal()" class="absolute top-8 right-8 text-white/50 hover:text-white transition text-2xl">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <form action="" method="POST" class="p-8 grid gap-6" id="memberForm">
                <input type="hidden" name="action" value="add_member" id="memberAction">
                <input type="hidden" name="m_id" id="memberId" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Họ và tên</label>
                        <input id="fullname" name="fullname" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nhập họ tên">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">MSSV</label>
                        <input id="student_id" name="student_id" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nhập MSSV">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Lớp</label>
                        <input id="class_name" name="class_name" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nhập tên lớp">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ban</label>
                        <select id="department" name="department" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($departments as $label): ?>
                                <?php if ($label === 'all') continue; ?>
                                <option value="<?= htmlspecialchars($label) ?>" <?= ($dept !== 'all' && $dept === $label) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-black py-5 rounded-3xl shadow-xl shadow-yellow-400/20 transition-all uppercase tracking-widest text-xs flex items-center justify-center" id="memberSubmitBtn">
                    <i class="fas fa-user-plus mr-3"></i> Thêm thành viên
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal(data) {
            document.getElementById('m_name').innerText = data.fullname;
            document.getElementById('m_info').innerText = data.student_id + " • " + data.class_name;
            document.getElementById('m_id').value = data.id;
            document.getElementById('evalModal').classList.add('modal-active');
        }
        function closeModal() {
            document.getElementById('evalModal').classList.remove('modal-active');
        }

        function openAddModal() {
            document.getElementById('memberModalLabel').innerText = 'Thêm thành viên';
            document.getElementById('memberModalTitle').innerText = 'Thêm dữ liệu thành viên mới';
            document.getElementById('memberModalSubtitle').innerText = 'Chọn ban và nhập thông tin để thêm thành viên vào hệ thống.';
            document.getElementById('memberAction').value = 'add_member';
            document.getElementById('memberId').value = '';
            document.getElementById('fullname').value = '';
            document.getElementById('student_id').value = '';
            document.getElementById('class_name').value = '';
            document.getElementById('department').value = 'Ban Chủ nhiệm';
            document.getElementById('memberSubmitBtn').innerHTML = '<i class="fas fa-user-plus mr-3"></i> Thêm thành viên';
            document.getElementById('addMemberModal').classList.add('modal-active');
        }

        function openEditModal(data) {
            document.getElementById('memberModalLabel').innerText = 'Sửa thành viên';
            document.getElementById('memberModalTitle').innerText = 'Chỉnh sửa thông tin thành viên';
            document.getElementById('memberModalSubtitle').innerText = 'Cập nhật dữ liệu khi thông tin bị sai.';
            document.getElementById('memberAction').value = 'edit_member';
            document.getElementById('memberId').value = data.id;
            document.getElementById('fullname').value = data.fullname;
            document.getElementById('student_id').value = data.student_id;
            document.getElementById('class_name').value = data.class_name;
            document.getElementById('department').value = data.department;
            document.getElementById('memberSubmitBtn').innerHTML = '<i class="fas fa-save mr-3"></i> Lưu thay đổi';
            document.getElementById('addMemberModal').classList.add('modal-active');
        }

        function closeAddModal() {
            document.getElementById('addMemberModal').classList.remove('modal-active');
        }

        // Đóng modal khi click ra ngoài
        window.onclick = function(event) {
            if (event.target == document.getElementById('evalModal')) {
                closeModal();
            }
            if (event.target == document.getElementById('addMemberModal')) {
                closeAddModal();
            }
        }
    </script>
</body>
</html>