<?php

require_once __DIR__ . '/app/Controllers/UsuarioController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controller === 'usuarios') {
    $usuariosController = new UsuariosController();

    switch ($action) {
        case 'listar':
            $usuariosController->listar();
            break;

        case 'buscar':
            $usuariosController->buscarPorId();
            break;    

        case 'criar':
            $usuariosController->criar();
            break;

        case 'atualizar':
            $usuariosController->atualizar();
            break;

        case 'excluir':
            $usuariosController->excluir();
            break;
        
        default:
            echo 'Ação de usuários não encontrada.';
            break;
    }
} else {
    echo '<h1>Atendelab</h1>';
    echo '<p>Projeto em execução. Use ?controller=usuario&action=listar para testar.</p>';
}