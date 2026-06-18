<?php

require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch ($controller) {

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
                $authController->dashboard();
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
                echo 'Ação de tipos de atendimento não encontrada.';
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

            default:
                http_response_code(404);
                echo 'Ação de atendimentos não encontrada.';
        }
        break;

    default:
        http_response_code(404);
        echo 'Controller não encontrado.';
}