<?php
// Logging Helper Functions

function logAktivitas($conn, $id_user, $aktivitas, $tabel = null, $id_referensi = null) {
    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (id_user, aktivitas, tabel, id_referensi) VALUES (?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("issi", $id_user, $aktivitas, $tabel, $id_referensi);
        return $stmt->execute();
    }
    return false;
}

function getRecentLogs($conn, $limit = 10) {
    $query = "SELECT 
        l.id_log,
        l.id_user,
        u.nama as nama_user,
        l.aktivitas,
        l.tabel,
        l.id_referensi,
        l.waktu
        FROM log_aktivitas l
        JOIN users u ON l.id_user = u.id_user
        ORDER BY l.waktu DESC
        LIMIT ?";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    return null;
}

function formatWaktu($waktu) {
    $now = time();
    $timestamp = strtotime($waktu);
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return "baru saja";
    } else if ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . " menit lalu";
    } else if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " jam lalu";
    } else if ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " hari lalu";
    } else {
        return date('d M Y H:i', $timestamp);
    }
}
?>
