<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = "
            SELECT
                a.id,
                p.nome AS pessoa,
                t.nome AS tipo_atendimento,
                u.nome AS usuario,
                a.descricao,
                a.observacao_final,
                a.status,
                a.criado_em
            FROM atendimentos a
            INNER JOIN pessoas p ON a.pessoa_id = p.id
            INNER JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
            INNER JOIN usuarios u ON a.usuario_id = u.id
            ORDER BY a.id DESC
        ";

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(
            $atendimentos,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function visualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = "
            SELECT
                a.*,
                p.nome AS pessoa,
                t.nome AS tipo_atendimento,
                u.nome AS usuario
            FROM atendimentos a
            INNER JOIN pessoas p ON a.pessoa_id = p.id
            INNER JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
            INNER JOIN usuarios u ON a.usuario_id = u.id
            WHERE a.id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode([
                'erro' => 'Atendimento não encontrado.'
            ]);
            return;
        }

        echo json_encode(
            $atendimento,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function criar(): void
{
    header('Content-Type: application/json; charset=utf-8');

    $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
    $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
    $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'aberto');
    $observacao_final = trim($_POST['observacao_final'] ?? '');

    if (
        !$pessoa_id ||
        !$tipo_atendimento_id ||
        !$usuario_id ||
        $descricao === ''
    ) {
        http_response_code(400);

        echo json_encode([
            'erro' => 'Pessoa, tipo de atendimento, usuário e descrição são obrigatórios.'
        ]);

        return;
    }

    try {

        $sql = "
            INSERT INTO atendimentos
            (
                pessoa_id,
                tipo_atendimento_id,
                usuario_id,
                descricao,
                observacao_final,
                status
            )
            VALUES
            (
                :pessoa_id,
                :tipo_atendimento_id,
                :usuario_id,
                :descricao,
                :observacao_final,
                :status
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
        $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':observacao_final', $observacao_final);
        $stmt->bindValue(':status', $status);

        $stmt->execute();

        http_response_code(201);

        echo json_encode([
            'mensagem' => 'Atendimento criado com sucesso.',
            'id' => $this->pdo->lastInsertId()
        ], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            'erro' => 'Erro ao criar atendimento.'
        ]);
    }
}

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? '');
        $observacao_final = trim($_POST['observacao_final'] ?? '');

        if (!$id || $status === '') {

            http_response_code(400);

            echo json_encode([
                'erro' => 'ID e status são obrigatórios.'
            ]);

            return;
        }

        try {

            $sql = "
                UPDATE atendimentos
                SET
                    status = :status,
                    observacao_final = :observacao_final
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':observacao_final', $observacao_final);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Atendimento atualizado com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao atualizar atendimento.'
            ]);
        }
    }
}