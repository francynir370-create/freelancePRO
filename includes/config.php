<?php
// config.php - VERSION CORRIGÉE

// VÉRIFIER SI LA FONCTION EXISTE DÉJÀ
if (!function_exists('get_base_url')) {
    function get_base_url() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);
        
        // Si on est à la racine, dirname('/') = '/' → on corrige
        if ($script_dir === '/' || $script_dir === '\\') {
            $script_dir = '';
        }
        
        return $protocol . '://' . $host . $script_dir;
    }
}

// Déclarer la variable seulement si elle n'existe pas
if (!isset($base_url)) {
    $base_url = get_base_url();
}
?>