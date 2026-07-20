<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Connexion</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-body p-5 text-center">
                    <h2 class="fw-bold mb-2 text-primary">Portail Opérateur</h2>
                    <p class="text-muted mb-4">Authentification administrative</p>

                    <!-- Affichage de l'erreur si les identifiants échouent -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger text-start py-2" role="alert">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('operateur/login') ?>" method="POST">
                        <div class="mb-3 text-start">
                            <label class="form-label text-secondary fw-semibold">Identifiant de l'administrateur</label>
                            <!-- Valeur pré-remplie -->
                            <input type="text" name="username" class="form-control form-control-lg" value="admin" required>
                        </div>
                        <div class="mb-4 text-start">
                            <label class="form-label text-secondary fw-semibold">Mot de passe</label>
                            <!-- Valeur pré-remplie -->
                            <input type="password" name="password" class="form-control form-control-lg" value="admin123" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Se connecter</button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="<?= base_url('/') ?>" class="text-decoration-none text-secondary btn btn-link btn-sm">← Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>