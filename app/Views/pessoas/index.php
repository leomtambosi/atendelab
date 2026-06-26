<?php
$tituloPagina = 'Pessoas atendidas';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Pessoas atendidas</h1>
        <p class="text-secondary mb-0">
            Cadastro, edição e inativação sem excluir o histórico.
        </p>
    </div>
    
    <button class="btn btn-success" type="button" onclick="novaPessoa()">
        Nova pessoa
    </button>
</div>

<div id="alerta"></div>

<div class="card border-0 shadow-sm mb-4 d-none" id="cardFormulario">
    <div class="card-body">
        <h2 class="h5 mb-3" id="formTitulo">Nova pessoa</h2>
        
        <form id="formPessoa">
            <input type="hidden" name="id" id="pessoaId">
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nome *</label>
                    <input class="form-control" type="text" name="nome" id="pessoaNome" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">CPF *</label>
                    <input class="form-control" type="text" name="cpf" id="pessoaDocumento" 
                           maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                           placeholder="Apenas 11 números" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Telefone</label>
                    <input class="form-control" type="text" name="telefone" id="pessoaTelefone" 
                           maxlength="14" placeholder="(99) 9999-9999">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">E-mail *</label>
                    <input class="form-control" type="email" name="email" id="pessoaEmail" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Curso</label>
                    <input class="form-control" type="text" name="curso" id="pessoaCurso">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Período</label>
                    <input class="form-control" type="text" name="periodo" id="pessoaPeriodo">
                </div>

                <div class="col-12">
                    <label class="form-label">Observações</label>
                    <textarea class="form-control" name="observacoes" id="pessoaObservacoes" rows="3"></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="pessoaStatus">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
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
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>E-mail</th>
                    <th>Curso</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th class="text-end px-4">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaPessoas">
                <tr>
                    <td colspan="8" class="text-center py-4">
                        Carregando...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const formPessoa = document.getElementById('formPessoa');
const cardFormulario = document.getElementById('cardFormulario');
const formTitulo = document.getElementById('formTitulo');

function novaPessoa() {
    formTitulo.textContent = 'Nova pessoa';
    document.getElementById('pessoaId').value = '';
    formPessoa.reset();
    document.getElementById('pessoaStatus').value = 'ativo';
    cardFormulario.classList.remove('d-none');
    
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function fecharFormulario() {
    cardFormulario.classList.add('d-none');
    formPessoa.reset();
}

async function carregarPessoas() {
    try {
        const resposta = await AtendeLabApi.get('pessoas', 'listar');
        const ambassadors = AtendeLabApi.toList(resposta);
        const tbody = document.getElementById('tabelaPessoas');
        
        if (!ambassadors.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        Nenhuma pessoa cadastrada.
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = ambassadors.map(pessoa => {
            const badgeClass = Math.abs(pessoa.status) === 0 || pessoa.status === 'inativo' ? 'text-bg-danger' : 'text-bg-success';
            
            return `
                <tr>
                    <td>${AtendeLabApi.escape(pessoa.id)}</td>
                    <td class="fw-semibold">${AtendeLabApi.escape(pessoa.nome)}</td>
                    <td>${AtendeLabApi.escape(pessoa.cpf || pessoa.documento || '-')}</td>
                    <td>${AtendeLabApi.escape(pessoa.email)}</td>
                    <td>${AtendeLabApi.escape(pessoa.curso || '-')}</td>
                    <td>${AtendeLabApi.escape(pessoa.periodo || '-')}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${AtendeLabApi.escape(pessoa.status)}
                        </span>
                    </td>
                    <td class="text-end px-4">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editarPessoa(${Number(pessoa.id)})">
                                Editar
                            </button>
                            ${pessoa.status !== 'inativo' && pessoa.status !== '0' ? `
                                <button class="btn btn-sm btn-outline-danger" onclick="inativarPessoa(${Number(pessoa.id)})">
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

async function editarPessoa(id) {
    try {
        const resposta = await AtendeLabApi.get('pessoas', 'buscar', { id: id });
        const pessoa = resposta.dados || resposta;
        
        formTitulo.textContent = 'Editar pessoa';
        
        document.getElementById('pessoaId').value = pessoa.id;
        document.getElementById('pessoaNome').value = pessoa.nome;
        document.getElementById('pessoaDocumento').value = pessoa.cpf || pessoa.documento || '';
        document.getElementById('pessoaTelefone').value = pessoa.telefone || '';
        document.getElementById('pessoaEmail').value = pessoa.email;
        document.getElementById('pessoaCurso').value = pessoa.curso || '';
        document.getElementById('pessoaPeriodo').value = pessoa.periodo || '';
        document.getElementById('pessoaObservacoes').value = pessoa.observacoes || '';
        document.getElementById('pessoaStatus').value = pessoa.status || 'ativo';
        
        cardFormulario.classList.remove('d-none');
        
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', 'Erro ao carregar dados para edição: ' + error.message, 'danger');
    }
}

formPessoa.addEventListener('submit', async event => {
    event.preventDefault();
    
    const id = document.getElementById('pessoaId').value;
    const acao = id ? 'atualizar' : 'criar';
    const mensagemSucesso = id ? 'Dados atualizados com sucesso.' : 'Pessoa cadastrada com sucesso.';
    
    const nome = document.getElementById('pessoaNome').value;
    const documento = document.getElementById('pessoaDocumento').value;
    const telefone = document.getElementById('pessoaTelefone').value;
    const email = document.getElementById('pessoaEmail').value;
    const curso = document.getElementById('pessoaCurso').value;
    const periodo = document.getElementById('pessoaPeriodo').value;
    const observacoes = document.getElementById('pessoaObservacoes').value;
    const status = document.getElementById('pessoaStatus').value;

    const dadosPayload = {
        id: id,
        nome: nome,
        email: email,
        telefone: telefone,
        periodo: periodo,
        observacoes: observacoes,
        status: status,
        cpf: documento,
        documento: documento,
        curso: curso,
        vinculo: curso
    };
    
    try {
        await AtendeLabApi.post(
            'pessoas',
            acao,
            dadosPayload,
            id ? { id: id } : {}
        );
        
        AtendeLabApi.showAlert('alerta', mensagemSucesso, 'success');
        fecharFormulario();
        await carregarPessoas();
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', 'Erro ao salvar: ' + error.message, 'danger');
    }
});

async function inativarPessoa(id) {
    const confirmou = confirm('Deseja realmente inativar esta pessoa?');
    if (!confirmou) {
        return;
    }
    
    try {
        await AtendeLabApi.post('pessoas', 'inativar', { id });
        AtendeLabApi.showAlert('alerta', 'Pessoa inativada com sucesso.', 'success');
        await carregarPessoas();
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', carregarPessoas);
</script>

</main> 

<script src="/atendelab/assets/js/api.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>