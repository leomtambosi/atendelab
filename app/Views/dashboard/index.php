<?php
$tituloPagina = 'Dashboard';
require __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <h1 class="h3 mb-1">Dashboard</h1>
    <p class="text-secondary mb-0">Resumo simples para validar a integração com o backend.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-secondary small mb-1">Pessoas cadastradas</p>
                <h2 class="display-5 fw-bold mb-0" id="totalPessoas">...</h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-secondary small mb-1">Tipos de atendimento</p>
                <h2 class="display-5 fw-bold mb-0" id="totalTipos">...</h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-secondary small mb-1">Atendimentos registrados</p>
                <h2 class="display-5 fw-bold mb-0" id="totalAtendimentos">...</h2>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-1">Acesso rápido</h5>
        <p class="card-text text-secondary small mb-3">
            Use os módulos abaixo para cadastrar e consultar dados reais do banco.
        </p>
        
        <div class="d-flex flex-wrap gap-2">
            <a href="?controller=frontend&action=pessoas" class="btn btn-success px-3">
                Gerenciar pessoas
            </a>
            <a href="?controller=frontend&action=tipos" class="btn btn-outline-success px-3">
                Gerenciar tipos
            </a>
            <a href="?controller=frontend&action=atendimentos" class="btn btn-outline-success px-3">
                Registrar atendimentos
            </a>
        </div>
    </div>
</div>

<script>
async function carregarDadosDashboard() {
    // 1. Contagem de Pessoas
    try {
        const resPessoas = await AtendeLabApi.get('pessoas', 'listar');
        document.getElementById('totalPessoas').textContent = AtendeLabApi.toList(resPessoas).length;
    } catch (e) { 
        document.getElementById('totalPessoas').textContent = '0'; 
    }

    // 2. Contagem de Tipos (Com Autodetecção Inteligente de Rota)
    let tiposCarregados = false;
    const possiveisRotasTipos = ['tipos-atendimentos', 'tipos', 'tiposatendimentos'];
    
    for (const rota of possiveisRotasTipos) {
        try {
            const resTipos = await AtendeLabApi.get(rota, 'listar');
            // Converte com segurança para lista (tratando se vier dentro de resTipos.dados ou direto)
            const listaTipos = AtendeLabApi.toList(resTipos);
            
            document.getElementById('totalTipos').textContent = listaTipos.length;
            tiposCarregados = true;
            break; // Se funcionou, sai do laço
        } catch (e) {
            // Ignora o erro e testa a próxima variação de rota
        }
    }
    if (!tiposCarregados) {
        document.getElementById('totalTipos').textContent = '0';
    }

    // 3. Contagem de Atendimentos
    try {
        const resAtendimentos = await AtendeLabApi.get('atendimentos', 'listar');
        document.getElementById('totalAtendimentos').textContent = AtendeLabApi.toList(resAtendimentos).length;
    } catch (e) { 
        document.getElementById('totalAtendimentos').textContent = '0'; 
    }
}

document.addEventListener('DOMContentLoaded', carregarDadosDashboard);
</script>

</main> 

<script src="/atendelab/assets/js/api.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>