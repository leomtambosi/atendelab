<?php

$controllerAtual = $_GET['controller'] ?? 'frontend';
$actionAtual = $_GET['action'] ?? 'dashboard';


if ($controllerAtual === 'frontend') {
    $controllerAtual = $actionAtual;
}

$usuario = $_SESSION['usuario'] ?? [];
?>
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <span class="brand-mark"><i class="bi bi-chat-square-text"></i></span>
        <div>
            <strong>AtendeLab</strong>
            <small>Academic Desk</small>
        </div>
    </div>
    
    <nav class="nav flex-column gap-1">
        <a class="nav-link <?= ($controllerAtual === 'dashboard') ? 'active' : '' ?>" 
           href="<?= $baseUrl ?>?controller=frontend&action=dashboard">
            <i class="bi bi-grid"></i> Dashboard
        </a>
        
        <a class="nav-link <?= ($controllerAtual === 'pessoas') ? 'active' : '' ?>" 
           href="<?= $baseUrl ?>?controller=frontend&action=pessoas">
            <i class="bi bi-people"></i> Pessoas atendidas
        </a>
        
        <a class="nav-link <?= ($controllerAtual === 'tipos' || $controllerAtual === 'tipos-atendimentos') ? 'active' : '' ?>" 
           href="<?= $baseUrl ?>?controller=frontend&action=tipos">
            <i class="bi bi-tags"></i> Tipos de atendimento
        </a>
        
        <a class="nav-link <?= ($controllerAtual === 'atendimentos') ? 'active' : '' ?>" 
           href="<?= $baseUrl ?>?controller=frontend&action=atendimentos">
            <i class="bi bi-journal-check"></i> Atendimentos
        </a>
        
        <?php if (($usuario['perfil'] ?? '') === 'administrador'): ?>
        <a class="nav-link <?= ($controllerAtual === 'usuarios') ? 'active' : '' ?>" 
           href="<?= $baseUrl ?>?controller=frontend&action=usuarios">
            <i class="bi bi-person-gear"></i> Usuários
        </a>
        <?php endif; ?>
    </nav>
    
    <a class="nav-link logout-link text-danger mt-auto" href="<?= $baseUrl ?>?controller=auth&action=sair">
        <i class="bi bi-box-arrow-left"></i> Sair
    </a>
</aside>

<main class="app-content p-4">