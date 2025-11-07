<?php
function update_bxh($conn, $id_doi1, $id_doi2, $bt1, $bt2) {
    $data = [
        $id_doi1 => ['tran' => 1, 'thang' => 0, 'hoa' => 0, 'thua' => 0, 'bt' => $bt1, 'bb' => $bt2],
        $id_doi2 => ['tran' => 1, 'thang' => 0, 'hoa' => 0, 'thua' => 0, 'bt' => $bt2, 'bb' => $bt1],
    ];

    if ($bt1 > $bt2) {
        $data[$id_doi1]['thang'] = 1;
        $data[$id_doi2]['thua'] = 1;
    } elseif ($bt1 < $bt2) {
        $data[$id_doi2]['thang'] = 1;
        $data[$id_doi1]['thua'] = 1;
    } else {
        $data[$id_doi1]['hoa'] = 1;
        $data[$id_doi2]['hoa'] = 1;
    }

    foreach ($data as $id_doi => $info) {
        $check = $conn->query("SELECT * FROM BANG_XEP_HANG WHERE ID_DOI_BONG = $id_doi");
        $diem = $info['thang'] * 3 + $info['hoa'];
        $hieuso = $info['bt'] - $info['bb'];

        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO BANG_XEP_HANG 
                (MUAGIAI, ID_DOI_BONG, DIEM_SO, SO_TRAN, SO_THANG, SO_HOA, SO_THA, BAN_THANG, BAN_THA, HIEU_SO)
                VALUES ('2025', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiiiiii", $id_doi, $diem, $info['tran'], $info['thang'], $info['hoa'], $info['thua'], $info['bt'], $info['bb'], $hieuso);
        } else {
            $stmt = $conn->prepare("UPDATE BANG_XEP_HANG SET 
                DIEM_SO = DIEM_SO + ?, SO_TRAN = SO_TRAN + ?, 
                SO_THANG = SO_THANG + ?, SO_HOA = SO_HOA + ?, SO_THA = SO_THA + ?, 
                BAN_THANG = BAN_THANG + ?, BAN_THA = BAN_THA + ?, HIEU_SO = HIEU_SO + ? 
                WHERE ID_DOI_BONG = ?");
            $stmt->bind_param("iiiiiiiii", $diem, $info['tran'], $info['thang'], $info['hoa'], $info['thua'], $info['bt'], $info['bb'], $hieuso, $id_doi);
        }

        $stmt->execute();
    }
}

function rollback_bxh($conn, $id_doi1, $id_doi2, $bt1, $bt2) {
    $data = [
        $id_doi1 => ['tran' => 1, 'thang' => 0, 'hoa' => 0, 'thua' => 0, 'bt' => $bt1, 'bb' => $bt2],
        $id_doi2 => ['tran' => 1, 'thang' => 0, 'hoa' => 0, 'thua' => 0, 'bt' => $bt2, 'bb' => $bt1],
    ];

    if ($bt1 > $bt2) {
        $data[$id_doi1]['thang'] = 1;
        $data[$id_doi2]['thua'] = 1;
    } elseif ($bt1 < $bt2) {
        $data[$id_doi2]['thang'] = 1;
        $data[$id_doi1]['thua'] = 1;
    } else {
        $data[$id_doi1]['hoa'] = 1;
        $data[$id_doi2]['hoa'] = 1;
    }

    foreach ($data as $id_doi => $info) {
        $diem = $info['thang'] * 3 + $info['hoa'];
        $hieuso = $info['bt'] - $info['bb'];

        $stmt = $conn->prepare("UPDATE BANG_XEP_HANG SET 
            DIEM_SO = DIEM_SO - ?, SO_TRAN = SO_TRAN - ?, 
            SO_THANG = SO_THANG - ?, SO_HOA = SO_HOA - ?, SO_THA = SO_THA - ?, 
            BAN_THANG = BAN_THANG - ?, BAN_THA = BAN_THA - ?, HIEU_SO = HIEU_SO - ? 
            WHERE ID_DOI_BONG = ?");
        $stmt->bind_param("iiiiiiiii", $diem, $info['tran'], $info['thang'], $info['hoa'], $info['thua'], $info['bt'], $info['bb'], $hieuso, $id_doi);
        $stmt->execute();
    }
}
?>
