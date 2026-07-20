<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Opérateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">⚙️ Mobile Money - Administration Opérateur</span>
        <a href="<?= base_url('client/login') ?>" class="btn btn-sm btn-outline-light">Espace Client →</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row g-4">
        
        <!-- SECTION 1 : CONFIGURATION PREFIXES -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 fw-bold text-dark">Configuration des Préfixes Valides</div>
                <div class="card-body">
                    <form action="<?= base_url('operateur/prefixe') ?>" method="POST" class="d-flex gap-2 mb-3">
                        <input type="text" name="prefixe" class="form-control" placeholder="Ex: 034" required maxlength="5">
                        <button type="submit" class="btn btn-primary text-nowrap">Ajouter</button>
                    </form>
                    
                    <ul class="list-group">
                        <?php foreach($prefixes as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-secondary">Préfixe autorisé :</span>
                                <span class="badge bg-secondary fs-6"><?= esc($p['prefixe']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- SECTION 2 : SITUATION GAINS -->
            <div class="card border-0 shadow-sm text-white bg-dark">
                <div class="card-header bg-transparent border-0 pt-4 px-4 fw-bold text-white-50 text-uppercase">Situation globale des Gains</div>
                <div class="card-body px-4 pb-4">
                    <?php 
                        $totalGlobal = 0;
                        if(!empty($gains)) {
                            foreach($gains as $g) { $totalGlobal += $g['total_gains']; }
                        }
                    ?>
                    <h2 class="display-6 fw-bold mb-3"><?= number_format($totalGlobal, 2, ',', ' ') ?> <span class="fs-5">Ar</span></h2>
                    
                    <div class="small">
                        <?php if(empty($gains)): ?>
                            <span class="text-white-50">Aucun frais perçu pour le moment.</span>
                        <?php else: ?>
                            <?php foreach($gains as $g): ?>
                                <div class="d-flex justify-content-between border-top border-secondary py-2">
                                    <span class="text-white-50">Gains via <?= ucfirst(esc($g['nom'])) ?> :</span>
                                    <span class="fw-bold"><?= number_format($g['total_gains'], 0, ',', ' ') ?> Ar</span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3 : SITUATION COMPTES CLIENTS -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 fw-bold text-dark">Situation des Comptes Clients</div>
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th># ID</th>
                                <th>Numéro de Téléphone</th>
                                <th class="text-end">Solde (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($comptes)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Aucun compte créé pour le moment.</td></tr>
                            <?php else: ?>
                                <?php foreach($comptes as $c): ?>
                                    <tr>
                                        <td>#<?= esc($c['id']) ?></td>
                                        <td class="fw-semibold text-secondary"><?= esc($c['numero_telephone']) ?></td>
                                        <td class="text-end fw-bold text-success"><?= number_format($c['solde'], 2, ',', ' ') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>