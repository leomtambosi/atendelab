<?php
$tituloPagina = 'Tipos de atendimento';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Tipos de atendimento</h1>
        <p class="text-secondary mb-0">
            Categorias utilizadas nos registros de atendimento.
        </p>
    </div>
    
    <button class="btn btn-success" type="button" onclick="novoTipo()">
        Novo tipo
    </button>
</div>

<div id="alerta"></div>

<div class="card border-0 shadow-sm mb-4 d-none" id="cardFormulario">
    <div class="card-body">
        <h2 class="h5 mb-3" id="formTitulo">Novo tipo</h2>
        
        <form id="formTipo">
            <input type="hidden" name="id" id="tipoId">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome *</label>
                    <input class="form-control" type="text" name="nome" id="tipoNome" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="tipoStatus">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Descrição *</label>
                    <textarea class="form-control" name="descricao" id="tipoDescricao" rows="3" required></textarea>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
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
                    <th style="width: 25%">Nome</th>
                    <th style="width: 45%">Descrição</th>
                    <th style="width: 15%">Status</th>
                    <th class="text-center" style="width: 15%">Ações</th>
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

let rotaCorretaTipos = null;

function novoTipo() {
    formTitulo.textContent = 'Novo tipo';
    document.getElementById('tipoId').value = '';
    formTipo.reset();
    document.getElementById('tipoStatus').value = 'ativo';
    cardFormulario.classList.remove('d-none');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function fecharFormulario() {
    cardFormulario.classList.add('d-none');
    formTipo.reset();
}

async function descobrirRotaEEfetuarGet(action, params = {}) {
    if (rotaCorretaTipos) {
        return await AtendeLabApi.get(rotaCorretaTipos, action, params);
    }

    const possiveisRotas = ['tipos-atendimentos', 'tipos', 'tiposatendimentos', 'tipo_atendimento'];
    
    for (const rota of possiveisRotas) {
        try {
            const res = await AtendeLabApi.get(rota, action, params);
            rotaCorretaTipos = rota; // Sucesso! Guarda a rota certa para as próximas chamadas
            return res;
        } catch (err) {
            if (!err.message.includes('Controller') && !err.message.includes('encontrado')) {
                throw err;
            }
        }
    }
    throw new Error('Nenhuma rota de Controller mapeada foi encontrada no backend.');
}

async function carregarTipos() {
    try {
        const resposta = await descobrirRotaEEfetuarGet('listar');
        const tipos = AtendeLabApi.toList(resposta);
        const tbody = document.getElementById('tabelaTipos');
        
        if (!tipos.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        Nenhum tipo de atendimento cadastrado.
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = tipos.map(tipo => {
            const statusLower = String(tipo.status).toLowerCase();
            const badgeClass = statusLower === 'inativo' || statusLower === '0' ? 'text-bg-danger' : 'text-bg-success';
            
            return `
                <tr>
                    <td class="fw-semibold">${AtendeLabApi.escape(tipo.nome)}</td>
                    <td class="text-secondary">${AtendeLabApi.escape(tipo.descricao || '-')}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${AtendeLabApi.escape(tipo.status)}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editarTipo(${Number(tipo.id)})">
                                Editar
                            </button>
                            ${statusLower !== 'inativo' && statusLower !== '0' ? `
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
        AtendeLabApi.showAlert('alerta', 'Erro ao listar: ' + error.message, 'danger');
    }
}

async function editarTipo(id) {
    try {
        const resposta = await descobrirRotaEEfetuarGet('buscar', { id: id });
        const tipo = resposta.dados || resposta;
        
        formTitulo.textContent = 'Editar tipo';
        document.getElementById('tipoId').value = tipo.id;
        document.getElementById('tipoNome').value = tipo.nome;
        document.getElementById('tipoDescricao').value = tipo.descricao || '';
        document.getElementById('tipoStatus').value = tipo.status || 'ativo';
        
        cardFormulario.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        AtendeLabApi.showAlert('alerta', 'Erro ao carregar: ' + error.message, 'danger');
    }
}

formTipo.addEventListener('submit', async event => {
    event.preventDefault();
    
    const id = document.getElementById('tipoId').value;
    const acao = id ? 'atualizar' : 'criar';
    const mensagemSucesso = id ? 'Tipo atualizado com sucesso.' : 'Tipo cadastrado com sucesso.';
    
    const dadosPayload = {
        id: id,
        nome: document.getElementById('tipoNome').value,
        descricao: document.getElementById('tipoDescricao').value,
        status: document.getElementById('tipoStatus').value
    };
    
    try {
        const rotaDestino = rotaCorretaTipos || 'tipos-atendimentos';
        await AtendeLabApi.post(rotaDestino, acao, dadosPayload, id ? { id: id } : {});
        
        AtendeLabApi.showAlert('alerta', mensagemSucesso, 'success');
        fecharFormulario();
        await carregarTipos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', 'Erro ao salvar: ' + error.message, 'danger');
    }
});

async function inativarTipo(id) {
    if (!confirm('Deseja realmente inativar este tipo?')) return;
    try {
        const rotaDestino = rotaCorretaTipos || 'tipos-atendimentos';
        await AtendeLabApi.post(rotaDestino, 'inativar', { id });
        AtendeLabApi.showAlert('alerta', 'Inativado com sucesso.', 'success');
        await carregarTipos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', carregarTipos);
</script>

</main> 

<script src="/atendelab/assets/js/api.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>