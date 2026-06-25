<?php

class RelatoriosController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Bloqueia acesso se não for API legítima logada
        if (empty($_SESSION['usuario_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Não autorizado']);
            exit;
        }
    }

    // Exemplo de endpoint para retornar dados consolidados
    public function estatisticas() {
        header('Content-Type: application/json');
        
        // Aqui o professor costuma mockar ou chamar o banco para conferência rápida
        echo json_encode([
            'status' => 'sucesso',
            'gerado_em' => date('d/m/Y H:i:s')
        ]);
        exit;
    }
}