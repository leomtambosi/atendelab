<?php
$baseUrl = '/atendelab/public/';
$tituloPagina = 'Dashboard';

// Carrega apenas o cabeçalho superior (barra verde)
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">Resumo simples para validar a integração com o backend.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="text-secondary small mb-1">Pessoas cadastradas</div>
                    <div class="display-5 fw-semibold" id="totalPessoas">-</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="text-secondary small mb-1">Tipos de atendimento</div>
                    <div class="display-5 fw-semibold" id="totalTipos">-</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="text-secondary small mb-1">Atendimentos registrados</div>
                    <div class="display-5 fw-semibold" id="totalAtendimentos">-</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-2">Acesso rápido</h2>
            <p class="text-secondary mb-4">Use os módulos abaixo para cadastrar e consultar dados reais do banco.</p>
            
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-success" href="<?= $baseUrl ?>?controller=frontend&action=pessoas">Gerenciar pessoas</a>
                <a class="btn btn-outline-success" href="<?= $baseUrl ?>?controller=frontend&action=tipos">Gerenciar tipos</a>
                <a class="btn btn-outline-success" href="<?= $baseUrl ?>?controller=frontend&action=atendimentos">Registrar atendimentos</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const targets = {
        pessoas: document.getElementById('totalPessoas'),
        tipos: document.getElementById('totalTipos'),
        atendimentos: document.getElementById('totalAtendimentos')
    };

    for (const [controller, element] of Object.entries(targets)) {
        try {
            if (typeof AtendeLabApi !== 'undefined') {
                const response = await AtendeLabApi.get(controller, 'listar');
                element.textContent = AtendeLabApi.toList(response).length;
            } else {
                element.textContent = '0';
            }
        } catch (error) {
            element.textContent = '!';
            element.title = error.message;
        }
    }
});
</script>
