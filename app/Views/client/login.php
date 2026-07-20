<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Connexion</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-body p-5 text-center">
                    <h2 class="fw-bold mb-2 text-primary">Mobile Money</h2>
                    <p class="text-muted mb-4">Accès rapide à votre espace client</p>

                    <!-- Gestion des erreurs flash -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger text-start py-2" role="alert">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('client/login') ?>" method="POST">
                        <div class="mb-4 text-start">
                            <label for="numero_telephone" class="form-label text-secondary fw-semibold">Numéro de Téléphone</label>
                            <input type="tel" name="numero_telephone" class="form-control form-control-lg" id="numero_telephone" placeholder="Ex: 033XXXXXXXX" required>
                            <div class="form-text text-muted">Pas d'inscription requise. Login automatique si le préfixe est valide.</div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Se connecter</button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="<?= base_url('operateur') ?>" class="text-decoration-none text-secondary btn btn-link btn-sm">Accéder au côté Opérateur →</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>