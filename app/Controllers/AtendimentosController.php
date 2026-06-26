<?php
// app/Controllers/AtendimentosController.php
class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
         global $pdo;
        if (!isset($pdo)) {
            die('Erro: $pdo não foi definido em database.php');
        }
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    // Listagem com JOIN para exibir nomes em vez de IDs
    public function listar(): void
    {
        $sql = 'SELECT a.id, p.nome AS pessoa_nome, t.nome AS tipo_nome,
                       u.nome AS responsavel_nome, a.descricao, a.status,
                       a.data_atendimento, a.horario_atendimento, a.observacao_final,
                       a.pessoa_id, a.tipo_atendimento_id /* Incluídos IDs para o mapeamento da edição */
                FROM atendimentos a
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u ON u.id = a.usuario_id
                ORDER BY a.id DESC';
        $stmt = $this->pdo->query($sql);
        $this->json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Buscar um atendimento específico por ID
    public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $sql = 'SELECT a.*, p.nome AS pessoa_nome, t.nome AS tipo_nome,
                       u.nome AS responsavel_nome
                FROM atendimentos a
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u ON u.id = a.usuario_id
                WHERE a.id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }
        $this->json($atendimento);
    }

    // Criar um novo atendimento
    public function criar(): void
    {
        $pessoa_id           = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_id             = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $descricao           = trim($_POST['descricao'] ?? '');
        $data_atendimento    = $_POST['data_atendimento'] ?? '';
        $horario_atendimento = $_POST['horario_atendimento'] ?? '';
        $status              = $_POST['status'] ?? 'aberto';

        // Pega o usuário da sessão
        $usuario_id = $_SESSION['usuario']['id'] ?? null;

        if (!$pessoa_id || !$tipo_id || !$usuario_id || $descricao === '' || $data_atendimento === '' || $horario_atendimento === '') {
            $this->json(['erro' => 'Todos os campos obrigatórios devem ser preenchidos.'], 422);
            return;
        }
        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'Status inicial inválido.'], 422);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos
                    (pessoa_id, tipo_atendimento_id, usuario_id, descricao,
                     status, data_atendimento, horario_atendimento)
                    VALUES
                    (:pessoa_id, :tipo_id, :usuario_id, :descricao,
                     :status, :data_atendimento, :horario_atendimento)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'pessoa_id'           => $pessoa_id,
                'tipo_id'             => $tipo_id,
                'usuario_id'          => $usuario_id,
                'descricao'           => $descricao,
                'status'              => $status,
                'data_atendimento'    => $data_atendimento,
                'horario_atendimento' => $horario_atendimento
            ]);
            $this->json(['mensagem' => 'Atendimento registrado com sucesso.'], 201);
        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao registrar atendimento. Verifique se os IDs existem.'], 400);
        }
    }

    public function editar(): void
    {
        $id                  = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $pessoa_id           = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_id             = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $descricao           = trim($_POST['descricao'] ?? '');
        $data_atendimento    = $_POST['data_atendimento'] ?? '';
        $horario_atendimento = $_POST['horario_atendimento'] ?? '';
        $status              = $_POST['status'] ?? '';

        if (!$id) {
            $this->json(['erro' => 'ID do atendimento não foi fornecido ou é inválido.'], 422);
            return;
        }

        if (!$pessoa_id || !$tipo_id || $descricao === '' || $data_atendimento === '' || $horario_atendimento === '' || $status === '') {
            $this->json(['erro' => 'Todos os campos obrigatórios devem ser preenchidos para salvar as alterações.'], 422);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos 
                    SET pessoa_id = :pessoa_id,
                        tipo_atendimento_id = :tipo_id,
                        descricao = :descricao,
                        data_atendimento = :data_atendimento,
                        horario_atendimento = :horario_atendimento,
                        status = :status
                    WHERE id = :id';
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id'                  => $id,
                'pessoa_id'           => $pessoa_id,
                'tipo_id'             => $tipo_id,
                'descricao'           => $descricao,
                'data_atendimento'    => $data_atendimento,
                'horario_atendimento' => $horario_atendimento,
                'status'              => $status
            ]);

            $this->json(['mensagem' => 'Atendimento e status atualizados com sucesso.']);
        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao salvar alterações no banco de dados.'], 400);
        }
    }

    public function alterarStatus(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacao_final = trim($_POST['observacao_final'] ?? '');

        if (!$id || !in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'ID ou status inválido.'], 422);
            return;
        }
        if ($status === 'concluido' && $observacao_final === '') {
            $this->json(['erro' => 'Para concluir o atendimento, informe a observação final.'], 422);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET status = :status,
                        observacao_final = :observacao_final
                    WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'status' => $status,
                'observacao_final' => $observacao_final ?: null
            ]);
            $this->json(['mensagem' => 'Status do atendimento atualizado com sucesso.']);
        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao atualizar status.'], 400);
        }
    }
}