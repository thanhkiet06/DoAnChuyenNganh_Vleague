<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();

if (!$team) {
    echo "<div class='container mt-5'><p>Bạn chưa có đội bóng.</p><a href='index.php' class='btn btn-secondary mt-2'>Quay lại</a></div>";
    exit;
}

$id_doi = $team['ID_DOI_BONG'];

// Lấy danh sách trận đấu để liên kết tài liệu
$trandau = $conn->query("
    SELECT t.ID_TRAN_DAU, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE t.ID_DOI_1 = $id_doi OR t.ID_DOI_2 = $id_doi
    ORDER BY t.NGAY_THI_DAU DESC
");

// Lấy danh sách tài liệu
$tailieu = $conn->query("
    SELECT tl.*, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TAI_LIEU_CHIEN_THUAT tl
     JOIN TRAN_DAU t ON tl.ID_TRAN_DAU = t.ID_TRAN_DAU
     JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
     JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE tl.ID_DOI_BONG = $id_doi
    ORDER BY tl.NGAY_TAO DESC
");

// Xử lý upload tài liệu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['them_tailieu'])) {
    $id_tran = !empty($_POST['id_tran']) ? intval($_POST['id_tran']) : null;
    $loai_tailieu = $_POST['loai_tailieu'];
    $mo_ta = trim($_POST['mo_ta']);
    $ngay_tao = date('Y-m-d');
    $error = $success = '';

    if ($loai_tailieu === 'YouTube' && !empty($_POST['link_youtube'])) {
        $duong_dan = trim($_POST['link_youtube']);
        // Chuyển đổi link YouTube sang định dạng nhúng
        if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $duong_dan, $matches)) {
            $duong_dan = "https://www.youtube.com/embed/" . $matches[2];
        }
        $stmt = $conn->prepare("INSERT INTO TAI_LIEU_CHIEN_THUAT (ID_DOI_BONG, ID_TRAN_DAU, LOAI_TAI_LIEU, DUONG_DAN, MO_TA, NGAY_TAO) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $id_doi, $id_tran, $loai_tailieu, $duong_dan, $mo_ta, $ngay_tao);
        if ($stmt->execute()) {
            $success = "Thêm tài liệu thành công!";
        } else {
            $error = "Lỗi khi thêm tài liệu.";
        }
    } elseif (isset($_FILES['file_tailieu']) && $_FILES['file_tailieu']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file_tailieu'];
        $allowed_extensions = ['pdf', 'doc', 'docx', 'mp4'];
        $allowed_mime_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'video/mp4'
        ];
        $max_size = 100 * 1024 * 1024; // 100MB

        // Lấy thông tin file
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_mime = mime_content_type($file_tmp);

        // Kiểm tra extension và MIME type
        if (in_array($file_ext, $allowed_extensions) && 
            in_array($file_mime, $allowed_mime_types) && 
            $file_size <= $max_size) {
            
            // Tạo tên file an toàn
            $safe_filename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $file_name);
            $filename = uniqid() . '_' . $safe_filename;
            $upload_dir = '../Uploads/tailieu/';
            $upload_path = $upload_dir . $filename;

            // Đảm bảo thư mục tồn tại
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Di chuyển file vào thư mục đích
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $duong_dan = "Uploads/tailieu/" . $filename;
                $stmt = $conn->prepare("INSERT INTO TAI_LIEU_CHIEN_THUAT (ID_DOI_BONG, ID_TRAN_DAU, LOAI_TAI_LIEU, DUONG_DAN, MO_TA, NGAY_TAO) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissss", $id_doi, $id_tran, $loai_tailieu, $duong_dan, $mo_ta, $ngay_tao);
                if ($stmt->execute()) {
                    $success = "Upload tài liệu thành công!";
                } else {
                    $error = "Lỗi khi lưu thông tin tài liệu vào CSDL: " . $conn->error;
                }
            } else {
                $error = "Không thể di chuyển file vào thư mục đích. Kiểm tra quyền ghi trên thư mục.";
            }
        } else {
            $error = "File không hợp lệ. Chỉ chấp nhận: PDF, Word (doc/docx), MP4, tối đa 100MB.";
        }
    } else {
        $error = "Vui lòng chọn file hoặc nhập link YouTube hợp lệ.";
        if (isset($_FILES['file_tailieu'])) {
            $error .= " Mã lỗi: " . $_FILES['file_tailieu']['error'];
        }
    }
}

// Xử lý xóa tài liệu
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_tailieu = intval($_GET['delete']);
    $tailieu_info = $conn->query("SELECT LOAI_TAI_LIEU, DUONG_DAN FROM TAI_LIEU_CHIEN_THUAT WHERE ID_TAI_LIEU = $id_tailieu AND ID_DOI_BONG = $id_doi")->fetch_assoc();

    if ($tailieu_info && $tailieu_info['LOAI_TAI_LIEU'] !== 'YouTube') {
        $file_path = '../' . $tailieu_info['DUONG_DAN'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $conn->query("DELETE FROM TAI_LIEU_CHIEN_THUAT WHERE ID_TAI_LIEU = $id_tailieu AND ID_DOI_BONG = $id_doi");
    header('Location: tailieu_chienthuat.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tài liệu chiến thuật - HLV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f7f9fb;
    }

    .container {
        padding: 40px 20px;
        max-width: 1100px;
    }

    h2 {
        font-family: 'Bebas Neue', cursive;
        font-size: 40px;
        color: #d90429;
        margin-bottom: 30px;
    }

    .form-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .btn-them {
        background-color: #d90429;
        color: white;
    }

    .btn-them:hover {
        background-color: #b40322;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #b02a37;
    }

    .table thead {
        background-color: #d90429;
        color: white;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>📚 Tài liệu chiến thuật</h2>

        <!-- Form thêm tài liệu -->
        <div class="form-section">
            <h4>Thêm tài liệu/video mới</h4>
            <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php } elseif (isset($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label class="form-label">Loại tài liệu</label>
                    <select name="loai_tailieu" class="form-select" required>
                        <option value="PDF">PDF</option>
                        <option value="Word">Word</option>
                        <option value="Video">Video MP4</option>
                        <option value="YouTube">Link YouTube</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Liên kết với trận đấu (tùy chọn)</label>
                    <select name="id_tran" class="form-select">
                        <option value="">Không liên kết</option>
                        <?php while ($row = $trandau->fetch_assoc()) { ?>
                        <option value="<?= $row['ID_TRAN_DAU'] ?>">
                            <?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                            (<?= $row['NGAY_THI_DAU'] ?>)
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">File tài liệu (PDF, Word, MP4) hoặc link YouTube</label>
                    <input type="file" name="file_tailieu" class="form-control" accept=".pdf,.doc,.docx,.mp4">
                    <input type="url" name="link_youtube" class="form-control mt-2"
                        placeholder="VD: https://www.youtube.com/watch?v=xxx">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="mo_ta" class="form-control" rows="3"
                        placeholder="VD: Phân tích chiến thuật đội khách..."></textarea>
                </div>
                <button type="submit" name="them_tailieu" class="btn btn-them">Thêm tài liệu</button>
            </form>
        </div>

        <!-- Danh sách tài liệu -->
        <h4>Danh sách tài liệu</h4>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Loại</th>
                        <th>Trận đấu</th>
                        <th>Mô tả</th>
                        <th>Link/File</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $tailieu->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['LOAI_TAI_LIEU'] ?></td>
                        <td>
                            <?php if ($row['ID_TRAN_DAU']) { ?>
                            <?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                            (<?= $row['NGAY_THI_DAU'] ?>)
                            <?php } else { ?>
                            Không liên kết
                            <?php } ?>
                        </td>
                        <td><?= htmlspecialchars($row['MO_TA'] ?? '') ?></td>
                        <td>
                            <?php if ($row['LOAI_TAI_LIEU'] === 'YouTube') { ?>
                            <a href="<?= htmlspecialchars($row['DUONG_DAN']) ?>" target="_blank">Xem video</a>
                            <?php } else { ?>
                            <a href="../<?= htmlspecialchars($row['DUONG_DAN']) ?>" target="_blank">Tải file</a>
                            <?php } ?>
                        </td>
                        <td><?= $row['NGAY_TAO'] ?></td>
                        <td>
                            <a href="tailieu_chienthuat.php?delete=<?= $row['ID_TAI_LIEU'] ?>"
                                class="btn btn-delete btn-sm" onclick="return confirm('Xóa tài liệu này?')">Xóa</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <a href="index.php" class="btn btn-secondary mt-3">← Quay lại trang chính</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>
</body>

</html>