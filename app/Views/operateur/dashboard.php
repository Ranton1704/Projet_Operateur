<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Opérateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">⚙️ Administration Opérateur</span>
        <div>
            <a href="<?= base_url('client/login') ?>" class="btn btn-sm btn-outline-light me-2">Espace Client →</a>
            <a href="<?= base_url('operateur/logout') ?>" class="btn btn-sm btn-light fw-bold text-primary">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row g-4">
        
        <!-- PRÉFIXES & GAINS -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 fw-bold text-secondary">Préfixes Valides</div>
                <div class="card-body">
                    <form action="<?= base_url('operateur/prefixe') ?>" method="POST" class="d-flex gap-2 mb-3">
                        <input type="text" name="prefixe" class="form-control" placeholder="Ex: 034" required maxlength="5">
                        <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
                    </form>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach($prefixes as $p): ?>
                            <span class="badge bg-secondary fs-6 p-2">📱 <?= esc($p['prefixe']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 fw-bold text-white-50 text-uppercase">Situation globale des Gains</div>
                <div class="card-body px-4 pb-4">
                    <?php 
                        $totalGlobal = 0;
                        if(!empty($gains)) { foreach($gains as $g) { $totalGlobal += $g['total_gains']; } }
                    ?>
                    <h2 class="display-6 fw-bold mb-3"><?= number_format($totalGlobal, 2, ',', ' ') ?> <span class="fs-5">Ar</span></h2>
                    <div class="small">
                        <?php if(empty($gains)): ?>
                            <span class="text-white-50">Aucun frais perçu.</span>
                        <?php else: ?>
                            <?php foreach($gains as $g): ?>
                                <div class="d-flex justify-content-between border-top border-white-50 py-2">
                                    <span class="text-white-50">Frais <?= ucfirst(esc($g['nom'])) ?> :</span>
                                    <span class="fw-bold"><?= number_format($g['total_gains'], 0, ',', ' ') ?> Ar</span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SITUATION DES COMPTES (AVEC FILTRE/RECHERCHE) -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-secondary">Situation des Comptes Clients</span>
                    <input type="text" id="search-compte" class="form-control form-control-sm w-50" placeholder="🔍 Rechercher un numéro...">
                </div>
                <div class="table-responsive" style="max-height: 290px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="table-comptes">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Numéro de Téléphone</th>
                                <th class="text-end">Solde actuel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($comptes)): ?>
                                <tr><td colspan="2" class="text-center text-muted py-3">Aucun compte actif.</td></tr>
                            <?php else: ?>
                                <?php foreach($comptes as $c): ?>
                                    <tr>
                                        <td class="fw-semibold text-secondary phone-number"><?= esc($c['numero_telephone']) ?></td>
                                        <td class="text-end fw-bold text-success"><?= number_format($c['solde'], 2, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- CRUD : BARÈME DES FRAIS SÉPARÉ ET FILTRABLE -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-secondary">Gestion du Barème des Frais</span>
                    <!-- Filtre par Type global -->
                    <select id="filter-bareme" class="form-select form-select-sm w-25" onchange="switchBaremeView(this.value)">
                        <option value="all">Afficher Tout</option>
                        <option value="retrait">Frais de Retrait uniquement</option>
                        <option value="transfert">Frais de Transfert uniquement</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Formulaire dynamique -->
                        <div class="col-md-4 border-end">
                            <h6 class="fw-bold text-primary mb-3" id="form-title">Ajouter une tranche</h6>
                            <form action="<?= base_url('operateur/frais/enregistrer') ?>" method="POST" id="frais-form">
                                <input type="hidden" name="id" id="frais-id">
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Type d'opération</label>
                                    <select name="id_type_operation" id="frais-type" class="form-select form-select-sm" required>
                                        <option value="2">Retrait</option>
                                        <option value="3">Transfert</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Montant Min (Ar)</label>
                                    <input type="number" name="montant_min" id="frais-min" class="form-control form-control-sm" required min="0">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Montant Max (Ar)</label>
                                    <input type="number" name="montant_max" id="frais-max" class="form-control form-control-sm" required min="1">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Frais (Ar)</label>
                                    <input type="number" name="frais" id="frais-val" class="form-control form-control-sm" required min="0">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100" id="btn-submit">Ajouter la tranche</button>
                                    <button type="button" class="btn btn-secondary btn-sm d-none" id="btn-cancel" onclick="resetFraisForm()">Annuler</button>
                                </div>
                            </form>
                        </div>

                        <!-- Les Tableaux Séparés -->
                        <div class="col-md-8">
                            <!-- Bloc Retrait -->
                            <div id="section-retrait" class="mb-4">
                                <h6 class="fw-bold text-warning mb-2">Barème des Retraits</h6>
                                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tranche de montant</th>
                                                <th>Frais</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($baremes_retrait as $b): ?>
                                                <tr>
                                                    <td>Entre <?= number_format($b['montant_min'], 0, ',', ' ') ?> et <?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                                    <td class="fw-bold text-danger"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-info py-0 px-2" onclick="editFrais(<?= $b['id'] ?>, 2, <?= $b['montant_min'] ?>, <?= $b['montant_max'] ?>, <?= $b['frais'] ?>)">Modifier</button>
                                                        <a href="<?= base_url('operateur/frais/supprimer/'.$b['id']) ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Supprimer ?')">Supprimer</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Bloc Transfert -->
                            <div id="section-transfert">
                                <h6 class="fw-bold text-primary mb-2">Barème des Transferts</h6>
                                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tranche de montant</th>
                                                <th>Frais</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($baremes_transfert as $b): ?>
                                                <tr>
                                                    <td>Entre <?= number_format($b['montant_min'], 0, ',', ' ') ?> et <?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                                    <td class="fw-bold text-danger"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-info py-0 px-2" onclick="editFrais(<?= $b['id'] ?>, 3, <?= $b['montant_min'] ?>, <?= $b['montant_max'] ?>, <?= $b['frais'] ?>)">Modifier</button>
                                                        <a href="<?= base_url('operateur/frais/supprimer/'.$b['id']) ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Supprimer ?')">Supprimer</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // 1. Filtrer dynamiquement l'affichage des sections Retrait / Transfert
    function switchBaremeView(value) {
        const retrait = document.getElementById('section-retrait');
        const transfert = document.getElementById('section-transfert');
        
        if (value === 'retrait') {
            retrait.classList.remove('d-none');
            transfert.classList.add('d-none');
        } else if (value === 'transfert') {
            retrait.classList.add('d-none');
            transfert.classList.remove('d-none');
        } else {
            retrait.classList.remove('d-none');
            transfert.classList.remove('d-none');
        }
    }

    // 2. Recherche dynamique en temps réel pour la liste des comptes clients
    document.getElementById('search-compte').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#table-comptes tbody tr');
        
        rows.forEach(row => {
            let phoneCell = row.querySelector('.phone-number');
            if (phoneCell) {
                let text = phoneCell.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    });

    // Fonctions CRUD existantes adaptées
    function editFrais(id, type, min, max, frais) {
        document.getElementById('frais-id').value = id;
        document.getElementById('frais-type').value = type;
        document.getElementById('frais-min').value = min;
        document.getElementById('frais-max').value = max;
        document.getElementById('frais-val').value = frais;

        document.getElementById('form-title').innerText = "Modifier la tranche #" + id;
        document.getElementById('btn-submit').innerText = "Enregistrer les modifications";
        document.getElementById('btn-submit').className = "btn btn-success btn-sm w-100";
        document.getElementById('btn-cancel').classList.remove('d-none');
    }

    function resetFraisForm() {
        document.getElementById('frais-form').reset();
        document.getElementById('frais-id').value = "";
        document.getElementById('form-title').innerText = "Ajouter une tranche";
        document.getElementById('btn-submit').innerText = "Ajouter la tranche";
        document.getElementById('btn-submit').className = "btn btn-primary btn-sm w-100";
        document.getElementById('btn-cancel').classList.add('d-none');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>