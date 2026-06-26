<?php
// app/Views/atendimentos/index.php

$tituloPagina = 'Atendimentos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Atendimentos</h1>
        <p class="text-muted small mb-0">Registro e acompanhamento dos atendimentos acadêmicos.</p>
    </div>
    <button class="btn btn-success" onclick="novoAtendimento()">
        Novo atendimento
    </button>
</div>

<div id="alerta"></div>

<div id="cardFormulario" class="card shadow-sm mb-4 d-none">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-success" id="tituloFormulario">Registrar Novo Atendimento</h6>
    </div>
    <div class="card-body">
        <form id="formAtendimento">
            <input type="hidden" name="id" id="atendimentoId">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pessoa <span class="text-danger">*</span></label>
                    <select class="form-select" name="pessoa_id" id="pessoaSelect" required>
                        <option value="">Carregando opções...</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo de Atendimento <span class="text-danger">*</span></label>
                    <select class="form-select" name="tipo_atendimento_id" id="tipoSelect" required>
                        <option value="">Carregando opções...</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data <span class="text-danger">*</span></label>
                    <input type="date" class="form-select" name="data_atendimento" id="dataInput" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Horário <span class="text-danger">*</span></label>
                    <input type="time" class="form-select" name="horario_atendimento" id="horarioInput" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" name="status" id="statusSelect">
                        <option value="aberto" selected>Aberto</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="concluido">Concluído</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descrição do Caso <span class="text-danger">*</span></label>
                <textarea class="form-control" name="descricao" id="descricaoTextarea" rows="3" placeholder="Detalhes do atendimento..." required></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light" onclick="fecharFormulario()">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar Atendimento</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Pessoa</th>
                    <th>Tipo</th>
                    <th>Responsável</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th style="width: 100px;" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaAtendimentos">
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Carregando atendimentos...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const formAtendimento = document.getElementById('formAtendimento');
const cardFormulario = document.getElementById('cardFormulario');
const tituloFormulario = document.getElementById('tituloFormulario');
let listaAtendimentosLocal = []; // Guarda os registros vindos do banco

function novoAtendimento() {
    document.getElementById('atendimentoId').value = ''; 
    tituloFormulario.textContent = 'Registrar Novo Atendimento';
    formAtendimento.reset();
    cardFormulario.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function fecharFormulario() {
    cardFormulario.classList.add('d-none');
    formAtendimento.reset();
    document.getElementById('atendimentoId').value = '';
}

function labelRegistro(obj, ...keys) {
    for (const key of keys) {
        if (obj[key] !== undefined && obj[key] !== null) {
            return obj[key];
        }
    }
    return '';
}

async function carregarCombos() {
    try {
        const [pessoasResp, tiposResp] = await Promise.all([
            AtendeLabApi.get('pessoas', 'listar'),
            AtendeLabApi.get('tiposatendimentos', 'listar')
        ]);

        const pessoas = AtendeLabApi.toList(pessoasResp).filter(p => p.status !== 'inativo');
        const tipos = AtendeLabApi.toList(tiposResp).filter(t => t.status !== 'inativo');

        document.getElementById('pessoaSelect').innerHTML =
            '<option value="">Selecione</option>' +
            pessoas.map(p => `<option value="${Number(p.id)}">${AtendeLabApi.escape(p.nome)}</option>`).join('');

        document.getElementById('tipoSelect').innerHTML =
            '<option value="">Selecione</option>' +
            tipos.map(t => `<option value="${Number(t.id)}">${AtendeLabApi.escape(t.nome)}</option>`).join('');
    } catch (error) {
        console.error("Erro nos combos:", error);
    }
}

async function carregarAtendimentos() {
    try {
        const resposta = await AtendeLabApi.get('atendimentos', 'listar');
        listaAtendimentosLocal = AtendeLabApi.toList(resposta);
        const tbody = document.getElementById('tabelaAtendimentos');

        if (!tbody) return;

        if (!listaAtendimentosLocal || !listaAtendimentosLocal.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">Nenhum atendimento registrado.</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = listaAtendimentosLocal.map(atendimento => {
            const pessoa = labelRegistro(atendimento, 'pessoa_nome', 'pessoa');
            const tipo = labelRegistro(atendimento, 'tipo_nome', 'tipo_atendimento', 'tipo');
            const responsavel = labelRegistro(atendimento, 'responsavel_nome', 'usuario', 'responsavel');
            const data = labelRegistro(atendimento, 'data_atendimento', 'data');

            const statusFormatado = (atendimento.status || 'aberto').toLowerCase();
            let classStatus = 'text-bg-primary';
            
            if (statusFormatado === 'concluido') classStatus = 'text-bg-success';
            else if (statusFormatado === 'em_andamento') classStatus = 'text-bg-warning';

            return `
                <tr>
                    <td>${AtendeLabApi.escape(atendimento.id)}</td>
                    <td>${AtendeLabApi.escape(pessoa)}</td>
                    <td>${AtendeLabApi.escape(tipo)}</td>
                    <td>${AtendeLabApi.escape(responsavel)}</td>
                    <td>${AtendeLabApi.escape(data)}</td>
                    <td>
                        <span class="badge ${classStatus}">
                            ${AtendeLabApi.escape(atendimento.status)}
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary" onclick="abrirPainelEdicao(${Number(atendimento.id)})">
                            Status
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

    } catch (error) {
        console.error("Erro na tabela:", error);
        const tbody = document.getElementById('tabelaAtendimentos');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-4">Erro ao carregar dados da API.</td>
                </tr>
            `;
        }
    }
}

// Abre o painel (card) de edição com todos os dados preenchidos ao clicar em "Status"
function abrirPainelEdicao(id) {
    const atendimento = listaAtendimentosLocal.find(a => Number(a.id) === id);
    if (!atendimento) return;

    tituloFormulario.textContent = 'Atualizar Status / Editar Atendimento';
    
    // Preenche os campos do formulário principal
    document.getElementById('atendimentoId').value = atendimento.id;
    document.getElementById('pessoaSelect').value = atendimento.pessoa_id || '';
    document.getElementById('tipoSelect').value = atendimento.tipo_atendimento_id || '';
    document.getElementById('dataInput').value = atendimento.data_atendimento || '';
    document.getElementById('horarioInput').value = atendimento.horario_atendimento || '';
    document.getElementById('statusSelect').value = atendimento.status || 'aberto';
    document.getElementById('descricaoTextarea').value = atendimento.descricao || '';

    // Mostra o card e rola a tela até ele
    cardFormulario.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Envio dinâmico do formulário principal (Cria ou Edita)
formAtendimento.addEventListener('submit', async event => {
    event.preventDefault();
    const id = document.getElementById('atendimentoId').value;
    const rotaAction = id ? 'editar' : 'criar'; 
    
    try {
        await AtendeLabApi.post('atendimentos', rotaAction, new FormData(formAtendimento));
        AtendeLabApi.showAlert('alerta', id ? 'Atendimento atualizado com sucesso.' : 'Atendimento registrado com sucesso.', 'success');
        fecharFormulario();
        await carregarAtendimentos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    await carregarCombos();
    await carregarAtendimentos();
});
</script>