<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Portail Opérateur - Connexion</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <!-- Logo -->
        <div class="logo-container">
            <svg class="logo-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        </div>

        <h1 class="login-title">Portail Opérateur</h1>
        <p class="login-subtitle">Authentification administrative</p>

        <!-- Affichage de l'erreur si les identifiants échouent -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-circle"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('operateur/login') ?>" method="POST">
            <div class="form-group">
                <label for="username" class="form-label">Identifiant de l'administrateur</label>
                <div class="phone-input-group">
                    <input type="text" name="username" class="phone-input" id="username" value="admin" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="phone-input-group">
                    <input type="password" name="password" class="phone-input" id="password" value="admin123" required>
                </div>
            </div>
            <button type="submit" class="btn-submit">
                <i class="bi bi-box-arrow-in-right"></i>
                Se connecter
            </button>
        </form>
    </div>
    <a href="<?= base_url('/') ?>" class="operator-link">
        <i class="bi bi-arrow-left"></i>
        Retour à l'accueil
    </a>
</div>
</body>
</html>