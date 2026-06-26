<?php

class RelatoriosController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Não autorizado']);
            exit;
        }
    }

    public function estatisticas() {
        header('Content-Type: application/json');

        echo json_encode([
            'status' => 'sucesso',
            'gerado_em' => date('d/m/Y H:i:s')
        ]);
        exit;
    }
}