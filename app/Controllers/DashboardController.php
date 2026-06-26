<?php

class DashboardController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            header('Location: /atendelab/public/?controller=auth&action=login');
            exit;
        }

        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}