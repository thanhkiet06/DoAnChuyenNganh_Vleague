<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

// Fetch all tournaments
$result = $conn->query("SELECT * FROM GIAI_DAU");
// Fetch all teams for the modal
$teams = $conn->query("SELECT * FROM DOI_BONG");

// Handle form submission to add team to tournament
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_team_to_tournament'])) {
    $id_giai_dau = $_POST['id_giai_dau'];
    $id_doi_bong = $_POST['id_doi_bong'];
    
    // Check if the team is already participating in the tournament
    $check = $conn->prepare("SELECT * FROM THAM_GIA_GIAI WHERE ID_GIAI_DAU = ? AND ID_DOI_BONG = ?");
    $check->bind_param("ii", $id_giai_dau, $id_doi_bong);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO THAM_GIA_GIAI (ID_GIAI_DAU, ID_DOI_BONG) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_giai_dau, $id_doi_bong);
        $stmt->execute();
    }
    // Redirect to avoid form resubmission
    header("Location: giaidau.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý giải đấu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
        padding: 40px;
    }

    .heading {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 36px;
        color: #2c3e50;
    }

    .btn-sm {
        font-size: 14px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="heading mb-4"><i class="bi bi-award-fill me-2"></i>Quản lý giải đấu</h1>

        <div class="mb-3">
            <a href="them_giaidau.php" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Thêm giải đấu
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm float-end">
                <i class="bi bi-arrow-left"></i> Về trang admin
            </a>
        </div>

        <table class="table table-bordered table-hover align-middle shadow-sm bg-white">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên giải</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Địa điểm</th>
                    <th style="width: 200px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['ID_GIAI_DAU'] ?></td>
                    <td><strong><?= $row['TEN_GIAI_DAU'] ?></strong></td>
                    <td><i class="bi bi-calendar-event text-primary me-1"></i><?= $row['NGAY_BAT_DAU'] ?></td>
                    <td><i class="bi bi-calendar-check text-success me-1"></i><?= $row['NGAY_KET_THUC'] ?></td>
                    <td><i class="bi bi-geo-alt text-danger me-1"></i><?= $row['DIA_DIEM'] ?></td>
                    <td>
                        <a href="sua_giaidau.php?id=<?= $row['ID_GIAI_DAU'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </a>
                        <a href="xoa_giaidau.php?id=<?= $row['ID_GIAI_DAU'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Xoá giải này?')">
                            <i class="bi bi-trash"></i> Xoá
                        </a>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addTeamModal<?= $row['ID_GIAI_DAU'] ?>">
                            <i class="bi bi-shield-fill-plus"></i> Thêm đội
                        </button>
                    </td>
                </tr>

                <!-- Modal for adding teams to this tournament -->
                <div class="modal fade" id="addTeamModal<?= $row['ID_GIAI_DAU'] ?>" tabindex="-1"
                    aria-labelledby="addTeamModalLabel<?= $row['ID_GIAI_DAU'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTeamModalLabel<?= $row['ID_GIAI_DAU'] ?>">
                                    Thêm đội vào giải: <?= $row['TEN_GIAI_DAU'] ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="id_giai_dau" value="<?= $row['ID_GIAI_DAU'] ?>">
                                    <div class="mb-3">
                                        <label for="id_doi_bong_<?= $row['ID_GIAI_DAU'] ?>" class="form-label">Chọn đội
                                            bóng</label>
                                        <select name="id_doi_bong" class="form-select"
                                            id="id_doi_bong_<?= $row['ID_GIAI_DAU'] ?>" required>
                                            <option value="">-- Chọn đội bóng --</option>
                                            <?php 
                                        $teams->data_seek(0); // Reset team result pointer
                                        while ($team = $teams->fetch_assoc()) { 
                                            // Check if team is already in this tournament
                                            $check_team = $conn->prepare("SELECT * FROM THAM_GIA_GIAI WHERE ID_GIAI_DAU = ? AND ID_DOI_BONG = ?");
                                            $check_team->bind_param("ii", $row['ID_GIAI_DAU'], $team['ID_DOI_BONG']);
                                            $check_team->execute();
                                            if ($check_team->get_result()->num_rows == 0) {
                                        ?>
                                            <option value="<?= $team['ID_DOI_BONG'] ?>"><?= $team['TEN_DOI_BONG'] ?>
                                            </option>
                                            <?php } 
                                        } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" name="add_team_to_tournament" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i> Thêm đội
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>