<?php

class UsuariosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT 
                    id, 
                    nome, 
                    email, 
                    perfil, 
                    status, 
                    criado_em,
                    atualizado_em
                FROM usuarios
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($usuarios, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT 
                    id, 
                    nome, 
                    email, 
                    perfil, 
                    status, 
                    criado_em,
                    atualizado_em
                FROM usuarios
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['erro' => 'Usuário não encontrado.']);
            return;
        }

        echo json_encode($usuario, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome   = trim($_POST['nome'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $senha  = $_POST['senha'] ?? '';
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '' || $email === '' || $senha === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos obrigatórios faltando.']);
            return;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = 'INSERT INTO usuarios 
                    (nome, email, senha, perfil, status)
                    VALUES
                    (:nome, :email, :senha, :perfil, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senhaHash);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);

            echo json_encode([
                'mensagem' => 'Usuário criado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'erro' => 'Erro ao criar usuário',
                'detalhes' => $e->getMessage()
            ]);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome   = trim($_POST['nome'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados obrigatórios faltando.']);
            return;
        }

        try {
            $sql = 'UPDATE usuarios
                    SET nome = :nome,
                        email = :email,
                        perfil = :perfil,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Usuário atualizado com sucesso.']);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'erro' => 'Erro ao atualizar usuário',
                'detalhes' => $e->getMessage()
            ]);
        }
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'DELETE FROM usuarios WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['mensagem' => 'Usuário excluído com sucesso.']);
    }
}