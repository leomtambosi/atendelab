<?php
$tituloPagina = 'Tipos de Atendimento';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Tipos de Atendimento</h1>
        <p class="text-secondary mb-0">
            Configuração das categorias de atendimentos do sistema.
        </p>
    </div>
    
    <button class="btn btn-success" type="button" onclick="novoTipo()">
        Novo tipo
    </button>
</div>

<div id="alerta"></div>

<div class="card border-0 shadow-sm mb-4 d-none" id="cardFormulario">
    <div class="card-body">
        <h2 class="h5" id="formTitulo">Novo tipo</h2>
        
        <form id="formTipo">
            <input type="hidden" name="id" id="tipoId">
            
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Nome da Categoria / Tipo *</label>
                    <input class="form-control" type="text" name="nome" id="tipoNome" required>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success" type="submit">
                    Salvar
                </button>
                <button class="btn btn-outline-secondary" type="button" onclick="fecharFormulario()">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome da Categoria / Tipo</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaTipos">
                <tr>
                    <td colspan="4" class="text-center py-4">
                        Carregando...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const formTipo = document.getElementById('formTipo');
const cardFormulario = document.getElementById('cardFormulario');
const formTitulo = document.getElementById('formTitulo');

function novoTipo() {
    formTitulo.textContent = 'Novo tipo';
    document.getElementById('tipoId').value = '';
    formTipo.reset();
    cardFormulario.classList.remove('d-none');
    
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function fecharFormulario() {
    cardFormulario.classList.add('d-none');
    formTipo.reset();
}

async function carregarTipos() {
    try {
        const resposta = await AtendeLabApi.get('tipos', 'listar');
        const tipos = AtendeLabApi.toList(resposta);
        const tbody = document.getElementById('tabelaTipos');
        
        if (!tipos.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        Nenhum tipo cadastrado.
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = tipos.map(tipo => {
            const badgeClass = tipo.status === 'inativo' ? 'text-bg-danger' : 'text-bg-success';
            
            return `
                <tr>
                    <td>${AtendeLabApi.escape(tipo.id)}</td>
                    <td>
                        <div class="fw-semibold">${AtendeLabApi.escape(tipo.nome)}</div>
                    </td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${AtendeLabApi.escape(tipo.status)}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editarTipo(${Number(tipo.id)})">
                                Editar
                            </button>
                            ${tipo.status !== 'inativo' ? `
                                <button class="btn btn-sm btn-outline-danger" onclick="inativarTipo(${Number(tipo.id)})">
                                    Inativar
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

async function editarTipo(id) {
    try {
        const resposta = await AtendeLabApi.get('tipos', 'obter', { id });
        const tipo = resposta.dados || resposta;
        
        formTitulo.textContent = 'Editar tipo';
        document.getElementById('tipoId').value = tipo.id;
        document.getElementById('tipoNome').value = tipo.nome;
        
        cardFormulario.classList.remove('d-none');
        
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

formTipo.addEventListener('submit', async event => {
    event.preventDefault();
    
    const id = document.getElementById('tipoId').value;
    const acao = id ? 'editar' : 'criar';
    const mensagemSucesso = id ? 'Tipo atualizado com sucesso.' : 'Tipo cadastrado com sucesso.';
    
    try {
        await AtendeLabApi.post(
            'tipos',
            acao,
            new FormData(formTipo)
        );
        
        AtendeLabApi.showAlert('alerta', mensagemSucesso, 'success');
        fecharFormulario();
        await carregarTipos();
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
});

async function inativarTipo(id) {
    const confirmou = confirm('Deseja realmente inativar este tipo?');
    if (!confirmou) {
        return;
    }
    
    try {
        await AtendeLabApi.post('tipos', 'inativar', { id });
        AtendeLabApi.showAlert('alerta', 'Tipo inativado com sucesso.', 'success');
        await carregarTipos();
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', carregarTipos);
</script>

<?php require __DIR__ . '/../layouts/sidebar.php'; ?>