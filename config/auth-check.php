<?php

// Auth Check Helper Functions

/**
 * Cek apakah user sudah login dan memiliki role yang sesuai
 * Jika tidak, redirect ke login
 *
 * @param string $required_role Role yang diperlukan (admin, petugas, peminjam)
 */
function checkAuth($required_role = null)
{
    if (!isset($_SESSION['id_user'])) {
        $_SESSION['auth_error'] = 'Anda harus login terlebih dahulu!';
        header("Location: ../../index.php");
        exit();
    }

    if ($required_role && !isset($_SESSION['role'])) {
        $_SESSION['auth_error'] = 'Role tidak ditentukan!';
        header("Location: ../../index.php");
        exit();
    }

    if ($required_role && $_SESSION['role'] !== $required_role) {
        $_SESSION['auth_error'] = 'Anda tidak memiliki akses ke halaman ini! Hanya ' . ucfirst($required_role) . ' yang dapat mengakses.';
        header("Location: ../../index.php");
        exit();
    }
}

/**
 * Cek apakah user adalah petugas
 */
function isPetugas()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'petugas';
}

/**
 * Cek apakah user adalah admin
 */
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Cek apakah user adalah peminjam
 */
function isPeminjam()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'peminjam';
}

/**
 * Get user data by ID
 */
function getUserById($conn, $id_user)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return null;
}
