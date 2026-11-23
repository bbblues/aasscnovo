<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema AASSC — <?= esc($title ?? '') ?></title>
    <link rel="icon" href="<?= base_url('assets/aassc-icon.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/aassc.css') ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-aassc">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
                <img src="<?= base_url('assets/aassc-icon.png') ?>" alt="">
                Sistema AASSC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Alternar navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pessoas</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= site_url('pessoas') ?>">Listar Pessoas</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('solicitacoes/add_pessoa') ?>">Cadastrar Pessoa</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Solicitações</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= site_url('solicitacoes') ?>">Lista Geral</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('solicitacoes/abertas') ?>">Solicitações em Aberto</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Relatórios</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= site_url('relatorios') ?>">Relatório de Atividades</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Cadastros</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= site_url('cidades') ?>">Cidades</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('procedimentos') ?>">Procedimentos</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('catpessoas') ?>">Categorias de Pessoa</a></li>
                        </ul>
                    </li>
                </ul>

                <form class="d-flex me-3" action="<?= site_url('pessoas') ?>" method="get" role="search">
                    <input class="form-control form-control-sm me-1" type="search" name="pes_nome" placeholder="Buscar pessoa" aria-label="Buscar pessoa">
                    <button class="btn btn-sm btn-light" type="submit">Buscar</button>
                </form>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= esc(session()->get('username') ?: session()->get('email')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= site_url('auth/change_password') ?>">Trocar senha</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= site_url('auth/logout') ?>">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-3">
        <?= view('_flash') ?>
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="rodape-aassc">
        <div class="container-fluid text-center">Sistema AASSC</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
