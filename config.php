<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// Obtener configuraciones de la base de datos
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {
    // Si hay error, se manejará de forma silenciosa
}

// Definir variables de configuración global
$whatsapp_number = isset($settings['whatsapp_number']) ? $settings['whatsapp_number'] : '526121368478';
$site_name = "ArriagaMarket";
$site_tagline = "Soluciones y Equipos WISP";

// Función para verificar si el admin ha iniciado sesión
function isAdmin() {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

// Función para formatear el precio en moneda local
function formatPrice($amount) {
    return '$' . number_format($amount, 2, '.', ',');
}

// Asegurarse de que el directorio de imágenes exista
$upload_dir = __DIR__ . '/assets/uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
?>
