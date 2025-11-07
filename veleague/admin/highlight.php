<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

// Lấy danh sách highlight
$highlights = $conn->query("
    SELECT h.*, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM HIGHLIGHT h
    JOIN TRAN_DAU t ON h.ID_TRAN_DAU = t.ID_TRAN_DAU
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    ORDER BY t.NGAY_THI_DAU DESC
");

// Lấy danh sách trận đấu để chọn khi thêm highlight
$trandau = $conn->query("
    SELECT t.ID_TRAN_DAU, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    ORDER BY t.NGAY_THI_DAU DESC
");

// Xử lý thêm highlight
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_highlight'])) {
    $id_tran = intval($_POST['id_tran']);
    $link_video = trim($_POST['link_video']);
    $link_video = filter_var($link_video, FILTER_SANITIZE_URL);
    $error = $success = '';

    if (!empty($link_video)) {
        // Chuyển đổi link YouTube sang định dạng nhúng
        if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $link_video, $matches)) {
            $link_video = "https://www.youtube.com/embed/" . $matches[2];
        }
        // Kiểm tra link hợp lệ
        $headers = @get_headers($link_video);
        if ($headers && strpos($headers[0], '200') !== false) {
            $stmt = $conn->prepare("INSERT INTO HIGHLIGHT (ID_TRAN_DAU, LINK_VIDEO) VALUES (?, ?)");
            $stmt->bind_param("is", $id_tran, $link_video);
            if ($stmt->execute()) {
                $success = "Thêm highlight thành công!";
            } else {
                $error = "Lỗi khi thêm highlight.";
            }
        } else {
            $error = "Link YouTube không hợp lệ hoặc không thể nhúng.";
        }
    } elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        // Xử lý upload file MP4
        $file = $_FILES['video_file'];
        $allowed_types = ['video/mp4'];
        $max_size = 100 * 1024 * 1024; // 100MB

        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $filename = uniqid() . '_' . basename($file['name']);
            $upload_path = '../Uploads/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $stmt = $conn->prepare("INSERT INTO HIGHLIGHT (ID_TRAN_DAU, LINK_VIDEO) VALUES (?, ?)");
                $stmt->bind_param("is", $id_tran, $filename);
                if ($stmt->execute()) {
                    $success = "Upload video thành công!";
                } else {
                    $error = "Lỗi khi lưu thông tin video.";
                }
            } else {
                $error = "Lỗi khi upload file.";
            }
        } else {
            $error = "File không hợp lệ (chỉ chấp nhận MP4, tối đa 100MB).";
        }
    } else {
        $error = "Vui lòng cung cấp link video hoặc file MP4.";
    }
}

// Xử lý xóa highlight
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_highlight = intval($_GET['delete']);
    $highlight = $conn->query("SELECT LINK_VIDEO FROM HIGHLIGHT WHERE ID_HIGHLIGHT = $id_highlight")->fetch_assoc();

    if ($highlight && !filter_var($highlight['LINK_VIDEO'], FILTER_VALIDATE_URL)) {
        // Xóa file MP4 nếu không phải link
        $file_path = '../uploads/' . $highlight['LINK_VIDEO'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $conn->query("DELETE FROM HIGHLIGHT WHERE ID_HIGHLIGHT = $id_highlight");
    header('Location: highlight.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Highlight - V.League 2025</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f2f4f8;
    }

    .container {
        padding: 40px 20px;
    }

    .title {
        font-family: 'Bebas Neue', cursive;
        font-size: 44px;
        color: #d90429;
    }

    .highlight-card {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .form-section {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .btn-add {
        background-color: #d90429;
        color: white;
    }

    .btn-add:hover {
        background-color: #b40221;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #b02a37;
    }

    .btn-back {
        background-color: #d90429;
        color: white;
    }

    .btn-back:hover {
        background-color: #b40221;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center title">🎥 Quản lý Highlight</h1>
        <p class="text-center text-muted mb-4">Thêm hoặc xóa highlight trận đấu</p>

        <!-- Form thêm highlight -->
        <div class="form-section">
            <h4>Thêm Highlight Mới</h4>
            <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php } elseif (isset($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Chọn trận đấu</label>
                    <select name="id_tran" class="form-select" required>
                        <option value="">Chọn trận đấu</option>
                        <?php while ($row = $trandau->fetch_assoc()) { ?>
                        <option value="<?= $row['ID_TRAN_DAU'] ?>">
                            <?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                            (<?= $row['NGAY_THI_DAU'] ?>)
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Link video (YouTube)</label>
                    <input type="url" name="link_video" class="form-control"
                        placeholder="VD: https://www.youtube.com/watch?v=xxx hoặc https://youtu.be/xxx">
                    <small class="form-text text-muted">Đảm bảo video cho phép nhúng.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hoặc upload file MP4</label>
                    <input type="file" name="video_file" class="form-control" accept="video/mp4">
                </div>
                <button type="submit" name="add_highlight" class="btn btn-add">Thêm Highlight</button>
            </form>
        </div>

        <!-- Danh sách highlight -->
        <h4 class="mb-3">Danh sách Highlight</h4>
        <?php if ($highlights->num_rows > 0) { ?>
        <div class="row g-3">
            <?php while ($row = $highlights->fetch_assoc()) { ?>
            <div class="col-12">
                <div class="highlight-card">
                    <h5><?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                        (<?= $row['NGAY_THI_DAU'] ?>)</h5>
                    <p>Video:
                        <?php if (filter_var($row['LINK_VIDEO'], FILTER_VALIDATE_URL)) { ?>
                        <a href="<?= htmlspecialchars($row['LINK_VIDEO']) ?>" target="_blank">Link YouTube</a>
                        <?php } else { ?>
                        File MP4: <?= htmlspecialchars($row['LINK_VIDEO']) ?>
                        <?php } ?>
                    </p>
                    <a href="highlight.php?delete=<?= $row['ID_HIGHLIGHT'] ?>" class="btn btn-delete btn-sm"
                        onclick="return confirm('Bạn có chắc muốn xóa highlight này?')">Xóa</a>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <p class="text-muted text-center">Chưa có highlight nào.</p>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-back px-4">← Về trang quản trị</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>