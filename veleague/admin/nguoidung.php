<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$filter = isset($_GET['vaitro']) ? $_GET['vaitro'] : '';
if ($filter && in_array($filter, ['admin', 'hlv', 'viewer'])) {
    $stmt = $conn->prepare("SELECT * FROM NGUOI_DUNG WHERE VAI_TRO = ?");
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM NGUOI_DUNG");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            padding: 30px;
        }
        .heading {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #e9ecef;
        }
        .btn-sm {
            font-size: 14px;
        }
        .action-links a {
            margin-right: 6px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="heading"><i class="bi bi-person-lines-fill me-2"></i>Danh sách người dùng</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="them_nguoidung.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Thêm người dùng</a>

        <form method="GET" class="d-flex gap-2 align-items-center">
            <label for="vaitro" class="me-2">Lọc theo vai trò:</label>
            <select name="vaitro" id="vaitro" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="admin" <?= $filter == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="hlv" <?= $filter == 'hlv' ? 'selected' : '' ?>>HLV</option>
                <option value="viewer" <?= $filter == 'viewer' ? 'selected' : '' ?>>Viewer</option>
            </select>
        </form>

        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Về trang admin</a>
    </div>

    <table class="table table-bordered table-hover align-middle shadow-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { 
                $role = strtolower($row['VAI_TRO']);
                $badgeColor = match ($role) {
                    'admin' => 'danger',
                    'hlv' => 'warning',
                    'viewer' => 'secondary',
                    default => 'light'
                };
            ?>
            <tr>
                <td><?= $row['ID_NGUOI_DUNG'] ?></td>
                <td><?= $row['TEN_DANG_NHAP'] ?></td>
                <td><?= $row['EMAIL'] ?></td>
                <td><span class="badge bg-<?= $badgeColor ?>"><?= strtoupper($row['VAI_TRO']) ?></span></td>
                <td class="action-links">
                    <a href="sua_nguoidung.php?id=<?= $row['ID_NGUOI_DUNG'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i> Sửa</a>
                    <a href="xoa_nguoidung.php?id=<?= $row['ID_NGUOI_DUNG'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xoá người dùng này?')"><i class="bi bi-trash"></i> Xoá</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
