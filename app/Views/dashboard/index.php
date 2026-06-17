<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard - AtendeLab</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #8ab6eb;
    }

    .navbar {
        background-color: #ff0000 !important;
    }

    .card {
        border: none;
        border-radius: 15px;
    }

    .card-body {
        padding: 35px;
    }

    h1 {
        color: #0d6efd;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .btn-primary {
        padding: 10px 20px;
        border-radius: 8px;
    }
</style>
</head>


<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">AtendeLab</span>

            <a class="btn btn-outline-light btn-sm" href="?controller=auth&action=logout">
                Sair
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4">Area restrita</h1>

                <p class="mb-1">
                    Bem-vindo,
                    <strong>
                        <?= htmlspecialchars(
                            $usuario['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>.
                </p>

                <p class="text-muted">
                    Perfil:
                    <?= htmlspecialchars(
                        $usuario['perfil'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <?php if ($usuario['perfil'] === 'admin'): ?>
                    <div class="alert alert-success">
                        <strong>Painel Administrativo</strong><br>
                         Você possui acesso completo ao sistema.
                    </div>
                <?php endif; ?>

                <a class="btn btn-primary" href="?controller=usuarios&action=listar">
                    Testar rota protegida de usuarios
                </a>

                <hr>

                <h5>Módulos que serão desenvolvidos futuramente</h5>

                <ul>
                    <li>Cadastro de Pessoas</li>
                    <li>Cadastro de Usuários</li>
                    <li>Tipos de Atendimento</li>
                    <li>Controle de Atendimentos</li>
                </ul>
            </div>
        </div>
    </div>

</body>

</html>