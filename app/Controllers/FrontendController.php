<?php

class FrontendController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            header('Location: /atendelab/public/?controller=auth&action=login');
            exit;
        }
    }

    public function pessoas() {
        require_once __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tipos() {
        require_once __DIR__ . '/../Views/tipos-atendimentos/index.php';
    }

    public function atendimentos() {
        require_once __DIR__ . '/../Views/atendimentos/index.php';
    }
}