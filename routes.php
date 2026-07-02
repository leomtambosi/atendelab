<?php
// routes.php

require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';
require_once __DIR__ . '/app/Controllers/RelatoriosController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch ($controller) {

    case 'frontend':
        exigirAutenticacao();

        switch ($action) {
            case 'dashboard':
                require_once __DIR__ . '/app/Views/dashboard/index.php';
                break;

            case 'pessoas':
                require_once __DIR__ . '/app/Views/pessoas/index.php';
                break;

            case 'tipos': 
                require_once __DIR__ . '/app/Views/tipos-atendimentos/index.php';
                break;

            case 'atendimentos':
                require_once __DIR__ . '/app/Views/atendimentos/index.php';
                break;

            default:
                http_response_code(404);
                echo 'Página visual não encontrada no frontend.';
        }
        break;

    case 'relatorios':
        exigirAutenticacao();
        $relatoriosController = new RelatoriosController();

        switch ($action) {
            case 'estatisticas':
                $relatoriosController->estatisticas();
                break;

            default:
                http_response_code(404);
                echo 'Ação de relatório não encontrada.';
        }
        break;

    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;

            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                exigirAutenticacao();
                require_once __DIR__ . '/app/Views/dashboard/index.php';
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo 'Ação de autenticação não encontrada.';
        }
        break;

    case 'usuarios':
        exigirAutenticacao();

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
                http_response_code(404);
                echo 'Ação de usuários não encontrada.';
        }
        break;

    case 'pessoas':
        exigirAutenticacao();

        $pessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;

            case 'buscar':
                $pessoasController->buscarPorId();
                break;

            case 'criar':
                $pessoasController->criar();
                break;

            case 'atualizar':
                $pessoasController->atualizar();
                break;

            case 'inativar':
                $pessoasController->inativar();
                break;

            case 'excluir':
                $pessoasController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Ação de pessoas não encontrada.';
        }
        break;

    case 'tiposatendimentos':
        exigirAutenticacao();

        $tiposController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;

            case 'buscar':
                $tiposController->buscarPorId();
                break;

            case 'criar':
                $tiposController->criar();
                break;

            case 'atualizar':
                $tiposController->atualizar();
                break;

            case 'inativar':
                $tiposController->inativar();
                break;

            case 'excluir':
                $tiposController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Ação de tipos de Atendimento não encontrada.';
        }
        break;

    case 'atendimentos':
        exigirAutenticacao();

        $atendimentosController = new AtendimentosController();

        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;

            case 'visualizar':
                $atendimentosController->visualizar();
                break;

            case 'criar':
                $atendimentosController->criar();
                break;

            case 'atualizar':
                $atendimentosController->atualizar();
                break;

            case 'editar':
                $atendimentosController->editar();
                break;

            case 'alterarStatus':
                $atendimentosController->alterarStatus();
                break;

            default:
                http_response_code(404);
                echo 'Ação de Atendimentos não encontrada.';
        }
        break;

    default:
        http_response_code(404);
        echo 'Controller não encontrado.';
}