<?php

class FrontendController {

    public function __construct() {
        // Proteção padrão de autenticação para as telas visuais
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            header('Location: /atendelab/public/?controller=auth&action=login');
            exit;
        }
    }

    // Método que abre a listagem de Pessoas
    public function pessoas() {
        require_once __DIR__ . '/../Views/pessoas/index.php';
    }

    // Método que abre os Tipos de Atendimento
    public function tipos() {
        require_once __DIR__ . '/../Views/tipos-atendimentos/index.php';
    }

    // Método que abre os Registros de Atendimentos
    public function atendimentos() {
        require_once __DIR__ . '/../Views/atendimentos/index.php';
    }
}