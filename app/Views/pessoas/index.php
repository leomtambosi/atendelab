<?php
$tituloPagina = 'Pessoas';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Pessoas</h1>
        <p class="text-secondary mb-0">
            Cadastro e listagem de pessoas vinculadas aos atendimentos.
        </p>
    </div>
    
    <button class="btn btn-success" type="button" onclick="novaPessoa()">
        Nova pessoa
    </button>
</div>

<div id="alerta"></div>

<div class="card border-0 shadow-sm mb-4 d-none" id="cardFormulario">
    <div class="card-body">
        <h2 class="h5" id="formTitulo">Nova pessoa</h2>
        
        <form id="formPessoa">
            <input type="hidden" name="id" id="pessoaId">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome *</label>
                    <input class="form-control" type="text" name="nome" id="pessoaNome" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">E-mail *</label>
                    <input class="form-control" type="email" name="email" id="pessoaEmail" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Telefone</label>
                    <input class="form-control" type="tel" name="telefone" id="pessoaTelefone">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Vínculo *</label>
                    <select class="form-select" name="vinculo" id="pessoaVinculo" required>
                        <option value="">Selecione</option>
                        <option value="estudante">Estudante</option>
                        <option value="professor">Professor</option>
                        <option value="comunidade">Comunidade</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Identificador *</label>
                    <input class="form-control" type="text" name="identificador" id="pessoaIdentificador" required>
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
                    <th>Nome</th>
                    <th>Vínculo</th>
                    <th>Identificador</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaPessoas">
                <tr>
                    <td colspan="7" class="text-center py-4">
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
        const pessoas = AtendeLabApi.toList(resposta);
        const tbody = document.getElementById('tabelaPessoas');
        
        if (!pessoas.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        Nenhuma pessoa cadastrada.
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = pessoas.map(pessoa => {
            const badgeClass = pessoa.status === 'inativo' ? 'text-bg-danger' : 'text-bg-success';
            
            return `
                <tr>
                    <td>${AtendeLabApi.escape(pessoa.id)}</td>
                    <td>
                        <div class="fw-semibold">${AtendeLabApi.escape(pessoa.nome)}</div>
                        <div class="text-secondary small">${AtendeLabApi.escape(pessoa.email)}</div>
                    </td>
                    <td>
                        <span class="badge text-bg-light border">
                            ${AtendeLabApi.escape(pessoa.vinculo)}
                        </span>
                    </td>
                    <td>${AtendeLabApi.escape(pessoa.identificador)}</td>
                    <td>${AtendeLabApi.escape(pessoa.telefone || '-')}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${AtendeLabApi.escape(pessoa.status)}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editarPessoa(${Number(pessoa.id)})">
                                Editar
                            </button>
                            ${pessoa.status !== 'inativo' ? `
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
        const resposta = await AtendeLabApi.get('pessoas', 'obter', { id });
        const pessoa = resposta.dados || resposta;
        
        formTitulo.textContent = 'Editar pessoa';
        document.getElementById('pessoaId').value = pessoa.id;
        document.getElementById('pessoaNome').value = pessoa.nome;
        document.getElementById('pessoaEmail').value = pessoa.email;
        document.getElementById('pessoaTelefone').value = pessoa.telefone || '';
        document.getElementById('pessoaVinculo').value = pessoa.vinculo;
        document.getElementById('pessoaIdentificador').value = pessoa.identificador;
        
        cardFormulario.classList.remove('d-none');
        
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

formPessoa.addEventListener('submit', async event => {
    event.preventDefault();
    
    const id = document.getElementById('pessoaId').value;
    const acao = id ? 'editar' : 'criar';
    const mensagemSucesso = id ? 'Dados atualizados com sucesso.' : 'Pessoa cadastrada com sucesso.';
    
    try {
        await AtendeLabApi.post(
            'pessoas',
            acao,
            new FormData(formPessoa)
        );
        
        AtendeLabApi.showAlert('alerta', mensagemSucesso, 'success');
        fecharFormulario();
        await carregarPessoas();
        
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>