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
        <h6 class="m-0 fw-bold text-success">Registrar Novo Atendimento</h6>
    </div>
    <div class="card-body">
        <form id="formAtendimento">
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
                    <input type="date" class="form-select" name="data_atendimento" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Horário <span class="text-danger">*</span></label>
                    <input type="time" class="form-select" name="horario_atendimento" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status Inicial</label>
                    <select class="form-select" name="status">
                        <option value="aberto" selected>Aberto</option>
                        <option value="em_andamento">Em Andamento</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descrição do Caso <span class="text-danger">*</span></label>
                <textarea class="form-control" name="descricao" rows="3" placeholder="Detalhes do atendimento..." required></textarea>
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
                    <th>Data e Hora</th>
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

<div class="modal fade" id="modalStatus" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formStatus" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Atualizar Status do Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="statusId">
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alterar para:</label>
                    <select class="form-select" name="status" required>
                        <option value="aberto">Aberto</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="concluido">Concluído</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Observações Finais / Resolução</label>
                    <textarea class="form-control" name="observacao_final" rows="3" placeholder="Obrigatório caso mude o status para Concluído..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Atualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
const formAtendimento = document.getElementById('formAtendimento');
const cardFormulario = document.getElementById('cardFormulario');

const statusModal = () => {
    if (typeof bootstrap === 'undefined') {
        console.error("Erro fatal: O JavaScript do Bootstrap não foi carregado no header.php");
        alert("Erro no sistema: O script do Bootstrap está ausente no cabeçalho.");
        return null;
    }
    return bootstrap.Modal.getOrCreateInstance(document.getElementById('modalStatus'));
};

function novoAtendimento() {
    cardFormulario.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function fecharFormulario() {
    cardFormulario.classList.add('d-none');
    formAtendimento.reset();
    configurarDataMinima();
}

// Configura o atributo min com a data atual (formato YYYY-MM-DD)
function configurarDataMinima() {
    const inputData = document.querySelector('#formAtendimento input[name="data_atendimento"]');
    if (inputData) {
        const hoje = new Date().toISOString().split('T')[0];
        inputData.setAttribute('min', hoje);
    }
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
        const atendimentos = AtendeLabApi.toList(resposta);
        const tbody = document.getElementById('tabelaAtendimentos');

        if (!tbody) return;

        if (!atendimentos || !atendimentos.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">Nenhum atendimento registrado.</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = atendimentos.map(atendimento => {
            const pessoa = labelRegistro(atendimento, 'pessoa_nome', 'pessoa');
            const tipo = labelRegistro(atendimento, 'tipo_nome', 'tipo_atendimento', 'tipo');
            const responsavel = labelRegistro(atendimento, 'responsavel_nome', 'usuario', 'responsavel');
            
            const data = labelRegistro(atendimento, 'data_atendimento', 'data');
            const horario = labelRegistro(atendimento, 'horario_atendimento', 'horario', 'hora');

            // Formata a exibição combinada de data e hora na tabela
            const dataHoraExibicao = horario ? `${data} às ${horario.substring(0, 5)}` : data;

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
                    <td>${AtendeLabApi.escape(dataHoraExibicao)}</td>
                    <td>
                        <span class="badge ${classStatus}">
                            ${AtendeLabApi.escape(atendimento.status)}
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary" onclick="abrirStatus(${Number(atendimento.id)}, '${AtendeLabApi.escapeAttr(atendimento.status)}')">
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

formAtendimento.addEventListener('submit', async event => {
    event.preventDefault();
    try {
        await AtendeLabApi.post('atendimentos', 'criar', new FormData(formAtendimento));
        AtendeLabApi.showAlert('alerta', 'Atendimento registrado com sucesso.', 'success');
        fecharFormulario();
        await carregarAtendimentos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
});

function abrirStatus(id, status) {
    document.getElementById('statusId').value = id;
    
    const selectStatus = document.querySelector('#formStatus [name="status"]');
    if (selectStatus) {
        selectStatus.value = status || 'aberto';
    }
    
    const txtObservacao = document.querySelector('#formStatus [name="observacao_final"]');
    if (txtObservacao) {
        txtObservacao.value = '';
    }
    
    const modal = statusModal();
    if (modal) modal.show();
}

document.getElementById('formStatus').addEventListener('submit', async event => {
    event.preventDefault();
    try {
        await AtendeLabApi.post('atendimentos', 'alterarStatus', new FormData(event.target));
        const modal = statusModal();
        if (modal) modal.hide();
        AtendeLabApi.showAlert('alerta', 'Status e dados salvos com sucesso.', 'success');
        await carregarAtendimentos();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    configurarDataMinima();
    await carregarCombos();
    await carregarAtendimentos();
});
</script>