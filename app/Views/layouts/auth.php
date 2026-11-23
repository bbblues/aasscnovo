<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema AASSC — <?= esc($title ?? 'Autenticação') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #EAEAEA; }
        .card-auth { max-width: 440px; margin: 60px auto 20px; }
        .btn-primary { background-color: #17a2b8; border-color: #17a2b8; }
        .btn-primary:hover { background-color: #138496; border-color: #117a8b; }
    </style>
</head>
<body>
    <div class="container">
        <?= view('_flash') ?>
        <?= $this->renderSection('content') ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
