<?php

class DashboardController {
    
    public function index() {
        // Garante que a sessão está ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se não estiver logado, manda de volta para o login
        if (empty($_SESSION['usuario_id'])) {
            header('Location: /atendelab/public/?controller=auth&action=login');
            exit;
        }

        // Carrega a View do Dashboard
        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}