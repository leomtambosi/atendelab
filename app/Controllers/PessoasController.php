<?php

class PessoasController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../../config/database.php';
    }

    public function listar(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        $sql = 'SELECT
                    id,
                    nome,
                    cpf,
                    telefone,
                    email,
                    curso,
                    periodo,
                    observacoes,
                    status,
                    criado_em,
                    atualizado_em
                FROM pessoas
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
                    cpf,
                    telefone,
                    email,
                    curso,
                    periodo,
                    observacoes,
                    status,
                    criado_em,
                    atualizado_em
                FROM pessoas
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
            return;
        }

        echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function inativar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = "UPDATE pessoas
                    SET status = 'inativo'
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
             'mensagem' => 'Pessoa inativada com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao inativar pessoa.',
                'detalhes' => $e->getMessage()
            ]);
        }
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if ($nome === '' || $cpf === '' || $email === '') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Nome, CPF e e-mail são obrigatórios.'
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'E-mail inválido.'
            ]);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas
                    (
                        nome,
                        cpf,
                        telefone,
                        email,
                        curso,
                        periodo,
                        observacoes,
                        status
                    )
                    VALUES
                    (
                        :nome,
                        :cpf,
                        :telefone,
                        :email,
                        :curso,
                        :periodo,
                        :observacoes,
                        :status
                    )';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':curso', $curso);
            $stmt->bindValue(':periodo', $periodo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);

            $stmt->execute();

            http_response_code(201);

            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao cadastrar pessoa.',
                'detalhes' => $e->getMessage()
            ]);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = trim($_POST['status'] ?? '');

        if (
            !$id ||
            $nome === '' ||
            $cpf === '' ||
            $email === '' ||
            $status === ''
        ) {
            http_response_code(400);

            echo json_encode([
                'erro' => 'ID, nome, CPF, e-mail e status são obrigatórios.'
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);

            echo json_encode([
                'erro' => 'E-mail inválido.'
            ]);
            return;
        }

        try {
            $sql = 'UPDATE pessoas
                    SET
                        nome = :nome,
                        cpf = :cpf,
                        telefone = :telefone,
                        email = :email,
                        curso = :curso,
                        periodo = :periodo,
                        observacoes = :observacoes,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':curso', $curso);
            $stmt->bindValue(':periodo', $periodo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Dados atualizados com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao atualizar dados da pessoa.',
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

        try {
            $sql = 'DELETE FROM pessoas WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Pessoa excluída com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao excluir registro.',
                'detalhes' => $e->getMessage()
            ]);
        }
    }
}