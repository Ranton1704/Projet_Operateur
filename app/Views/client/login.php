<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>E-Money - Connexion</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/login.js"></script>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <!-- Logo -->
        <div class="logo-container">
            <svg class="logo-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 7H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm0 12H4V9h16v10zm-8-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm0 4c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/>
            </svg>
        </div>

        <h1 class="login-title">Bienvenue sur E-Money</h1>
        <p class="login-subtitle">Vos services financiers mobiles, simples et sécurisés.</p>

        <!-- Gestion des erreurs flash -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('client/login') ?>" method="POST">
            <div class="form-group">
                <label for="numero_telephone" class="form-label">Numéro de Téléphone</label>
                <div class="phone-input-group">
                    <input type="tel" name="numero_telephone" class="phone-input" id="numero_telephone" placeholder="033 00 000 00" required>
                </div>
                <div class="form-help">Pas d'inscription requise. Login automatique si le préfixe est valide.</div>
            </div>
            <button type="submit" class="btn-submit">Continuer →</button>
        </form>
    </div>
    <a href="<?= base_url('operateur') ?>" class="operator-link">Vous êtes <strong>opérateur</strong> ? Connectez-vous ici</a>
</div>
</body>
</html>