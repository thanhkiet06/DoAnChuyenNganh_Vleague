<?php
require '../connect.php';

$keyword = $_GET['q'] ?? '';
$vitri = $_GET['vitri'] ?? '';

$results = [];

if ($keyword != '') {

    $keyword = "{$keyword}";
    
    $sql = "
        SELECT 'team' AS type, d.ID_DOI_BONG, d.TEN_DOI_BONG, d.HUAN_LUYEN_VIEN, d.LOGO, NULL AS HO_TEN, NULL AS VI_TRI, NULL AS ANH_DAI_DIEN
        FROM DOI_BONG d
        WHERE d.TEN_DOI_BONG LIKE ? OR d.HUAN_LUYEN_VIEN LIKE ?
        UNION
        SELECT 'player' AS type, d.ID_DOI_BONG, d.TEN_DOI_BONG, d.HUAN_LUYEN_VIEN, d.LOGO, c.HO_TEN, c.VI_TRI, c.ANH_DAI_DIEN
        FROM CAU_THU c
        JOIN DOI_BONG d ON c.ID_DOI_BONG = d.ID_DOI_BONG
        WHERE c.HO_TEN LIKE ?
    ";
    
    if ($vitri != '') {
        $sql .= " AND c.VI_TRI = ?";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($vitri != '') {
        $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $vitri);
    } else {
        $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Organize results by team
    while ($row = $result->fetch_assoc()) {
        $team_id = $row['ID_DOI_BONG'];
        if (!isset($results[$team_id])) {
            $results[$team_id] = [
                'team_name' => $row['TEN_DOI_BONG'],
                'coach' => $row['HUAN_LUYEN_VIEN'],
                'logo' => $row['LOGO'],
                'players' => []
            ];
        }
        if ($row['type'] === 'player') {
            $results[$team_id]['players'][] = [
                'name' => $row['HO_TEN'],
                'position' => $row['VI_TRI'],
                'avatar' => $row['ANH_DAI_DIEN']
            ];
        }
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm đội bóng hoặc cầu thủ - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f5f7fa;
    }

    .container {
        padding: 40px 20px;
        max-width: 960px;
    }

    .title {
        font-family: 'Bebas Neue', cursive;
        font-size: 42px;
        color: #d90429;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-control {
        font-size: 18px;
    }

    .btn-search {
        background-color: #d90429;
        color: white;
        font-weight: 500;
    }

    .btn-search:hover {
        background-color: #b40221;
    }

    .result-box {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-top: 30px;
    }

    .result-box h4 {
        font-family: 'Bebas Neue', cursive;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .logo,
    .avatar {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 10px;
    }

    .item-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .player-row {
        margin-left: 60px;
        margin-bottom: 8px;
    }

    .coach-info {
        font-weight: 600;
        color: #333;
    }

    .btn-back {
        margin-top: 40px;
        background-color: #d90429;
        color: white;
    }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="title">🔍 Tìm kiếm đội bóng hoặc cầu thủ</h1>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($keyword) ?>"
                    placeholder="Nhập tên đội/cầu thủ/HLV..." required>
            </div>
            <div class="col-md-3">
                <select name="vitri" class="form-select">
                    <option value="">-- Tất cả vị trí --</option>
                    <option value="Thủ môn" <?= $vitri == 'Thủ môn' ? 'selected' : '' ?>>Thủ môn</option>
                    <option value="Hậu vệ" <?= $vitri == 'Hậu vệ' ? 'selected' : '' ?>>Hậu vệ</option>
                    <option value="Tiền vệ" <?= $vitri == 'Tiền vệ' ? 'selected' : '' ?>>Tiền vệ</option>
                    <option value="Tiền đạo" <?= $vitri == 'Tiền đạo' ? 'selected' : '' ?>>Tiền đạo</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-search w-100">Tìm kiếm</button>
            </div>
        </form>

        <?php if ($keyword != ''): ?>
        <div class="result-box">
            <h4>Kết quả tìm kiếm:</h4>
            <?php if (!empty($results)): ?>
            <?php foreach ($results as $team): ?>
            <div class="item-row">
                <?php if (!empty($team['logo'])): ?>
                <img src="<?= (str_starts_with($team['logo'], 'http') ? $team['logo'] : '../' . $team['logo']) ?>"
                    class="logo" alt="logo">
                <?php endif; ?>
                <div>
                    <strong><?= htmlspecialchars($team['team_name']) ?></strong><br>
                    <span class="coach-info">Huấn luyện viên:
                        <?= htmlspecialchars($team['coach'] ?? 'Chưa có thông tin') ?></span>
                </div>
            </div>
            <?php if (!empty($team['players'])): ?>
            <?php foreach ($team['players'] as $player): ?>
            <div class="player-row item-row">
                <?php if (!empty($player['avatar'])): ?>
                <img src="<?= (str_starts_with($player['avatar'], 'http') ? $player['avatar'] : '../' . $player['avatar']) ?>"
                    class="avatar" alt="avatar">
                <?php endif; ?>
                <div>
                    <strong><?= htmlspecialchars($player['name']) ?></strong> -
                    <?= htmlspecialchars($player['position']) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php else: ?>
            <p class="text-muted">Không tìm thấy đội bóng hoặc cầu thủ nào.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>