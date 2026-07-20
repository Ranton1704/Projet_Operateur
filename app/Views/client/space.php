<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace Client - E-Money</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-content">
        <div class="navbar-brand">
            <i class="bi bi-wallet2"></i>
            E-Money Client
        </div>
        <div class="navbar-user">
            <div class="user-phone">
                <i class="bi bi-phone"></i>
                <?= esc($compte['numero_telephone']) ?>
            </div>
            <a href="<?= base_url('client/logout') ?>" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="space-container">
    <!-- Messages Alertes -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="cards-grid">
        <!-- BLOC SOLDE ACTUEL -->
        <div class="balance-card">
            <div class="balance-label">
                <i class="bi bi-wallet"></i>
                Votre Solde Actuel
            </div>
            <div class="balance-amount">
                <?= number_format($compte['solde'], 2, ',', ' ') ?> <span class="balance-currency">Ar</span>
            </div>
        </div>

        <!-- BLOC FAIRE UNE OPÉRATION -->
        <div class="operations-card">
            <div class="card-header">
                <i class="bi bi-arrow-left-right"></i>
                Effectuer une opération
            </div>
            <div class="card-body">
                <!-- Formulaires par onglets -->
                <div class="tabs-nav">
                    <button class="tab-btn active" data-tab="depot">
                        <i class="bi bi-download"></i>
                        Dépôt
                    </button>
                    <button class="tab-btn" data-tab="retrait">
                        <i class="bi bi-upload"></i>
                        Retrait
                    </button>
                    <button class="tab-btn" data-tab="transfert">
                        <i class="bi bi-send"></i>
                        Transfert
                    </button>
                </div>

                <div class="tab-content active" id="depot">
                    <form action="<?= base_url('client/transaction') ?>" method="POST">
                        <input type="hidden" name="type" value="depot">
                        <div class="form-group">
                            <label class="form-label">Montant du dépôt (Ar)</label>
                            <input type="number" name="montant" class="form-control" placeholder="Montant" required min="1" id="depot-montant">
                        </div>
                        <div class="fee-display" id="depot-fee" style="display: none;">
                            <span class="fee-label">Frais appliqués :</span>
                            <span class="fee-amount">0 Ar</span>
                            <span class="fee-info">(Gratuit)</span>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i>
                            Valider le dépôt (Automatique)
                        </button>
                    </form>
                </div>

                <div class="tab-content" id="retrait">
                    <form action="<?= base_url('client/transaction') ?>" method="POST">
                        <input type="hidden" name="type" value="retrait">
                        <div class="form-group">
                            <label class="form-label">Montant à retirer (Ar)</label>
                            <input type="number" name="montant" class="form-control" placeholder="Montant" required min="100" id="retrait-montant">
                        </div>
                        <div class="fee-display" id="retrait-fee" style="display: none;">
                            <span class="fee-label">Frais appliqués :</span>
                            <span class="fee-amount">0 Ar</span>
                            <span class="fee-info"></span>
                        </div>
                        <div class="total-display" id="retrait-total" style="display: none;">
                            <span class="total-label">Total à débiter :</span>
                            <span class="total-amount">0 Ar</span>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i>
                            Confirmer le Retrait
                        </button>
                    </form>
                </div>

                <div class="tab-content" id="transfert">
                    <form action="<?= base_url('client/transaction') ?>" method="POST">
                        <input type="hidden" name="type" value="transfert">
                        <div class="form-group">
                            <label class="form-label">Numéro du destinataire</label>
                            <input type="tel" name="destinataire" class="form-control" placeholder="Ex: 037XXXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Montant à transférer (Ar)</label>
                            <input type="number" name="montant" class="form-control" placeholder="Montant" required min="100" id="transfert-montant">
                        </div>
                        <div class="fee-display" id="transfert-fee" style="display: none;">
                            <span class="fee-label">Frais appliqués :</span>
                            <span class="fee-amount">0 Ar</span>
                            <span class="fee-info"></span>
                        </div>
                        <div class="total-display" id="transfert-total" style="display: none;">
                            <span class="total-label">Total à débiter :</span>
                            <span class="total-amount">0 Ar</span>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                            Envoyer l'argent
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- LISTE HISTORIQUE -->
        <div class="history-card" style="grid-column: span 12;">
            <div class="card-header">
                <i class="bi bi-clock-history"></i>
                Historique des transactions
            </div>
            <div style="padding: 20px;">
                <!-- Filtres -->
                <form action="<?= base_url('client/space') ?>" method="GET" style="margin-bottom: 20px; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                    <div class="cards-grid" style="margin-bottom: 0; gap: 15px;">
                        <div style="grid-column: span 3;">
                            <label style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; display: block;">Date de début</label>
                            <input type="date" name="date_debut" class="form-control" value="<?= isset($_GET['date_debut']) ? esc($_GET['date_debut']) : '' ?>">
                        </div>
                        <div style="grid-column: span 3;">
                            <label style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; display: block;">Date de fin</label>
                            <input type="date" name="date_fin" class="form-control" value="<?= isset($_GET['date_fin']) ? esc($_GET['date_fin']) : '' ?>">
                        </div>
                        <div style="grid-column: span 3;">
                            <label style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; display: block;">Type d'opération</label>
                            <select name="type_operation" class="form-control">
                                <option value="">Tous</option>
                                <option value="1" <?= isset($_GET['type_operation']) && $_GET['type_operation'] == '1' ? 'selected' : '' ?>>Dépôt</option>
                                <option value="2" <?= isset($_GET['type_operation']) && $_GET['type_operation'] == '2' ? 'selected' : '' ?>>Retrait</option>
                                <option value="3" <?= isset($_GET['type_operation']) && $_GET['type_operation'] == '3' ? 'selected' : '' ?>>Transfert</option>
                            </select>
                        </div>
                        <div style="grid-column: span 3;">
                            <label style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; display: block;">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" style="flex: 1;">
                                    <i class="bi bi-funnel"></i> Filtrer
                                </button>
                                <a href="<?= base_url('client/space') ?>" class="btn btn-secondary" style="padding: 14px 20px; text-decoration: none;">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <div style="max-height: 500px; overflow-y: auto;">
                    <table class="history-table" id="history-table">
                        <thead>
                            <tr>
                                <th>Date & Heure</th>
                                <th>Type</th>
                                <th>Expéditeur</th>
                                <th>Destinataire</th>
                                <th>Montant (Ar)</th>
                                <th>Frais Appliqués (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($historique)): ?>
                                <tr><td colspan="6" class="empty-state">Aucune transaction enregistrée.</td></tr>
                            <?php else: ?>
                                <?php foreach($historique as $op): ?>
                                    <tr data-date="<?= $op['date_operation'] ?>" data-type="<?= $op['id_type_operation'] ?>">
                                        <td><?= esc($op['date_operation']) ?></td>
                                        <td>
                                            <?php 
                                                if($op['id_type_operation'] == 1) echo '<span class="badge badge-depot"><i class="bi bi-download"></i> Dépôt</span>';
                                                elseif($op['id_type_operation'] == 2) echo '<span class="badge badge-retrait"><i class="bi bi-upload"></i> Retrait</span>';
                                                else echo '<span class="badge badge-transfert"><i class="bi bi-send"></i> Transfert</span>';
                                            ?>
                                        </td>
                                        <td><?= esc($op['numero_expediteur']) ?></td>
                                        <td><?= $op['numero_destinataire'] ? esc($op['numero_destinataire']) : '-' ?></td>
                                        <td class="fw-bold"><?= number_format($op['montant'], 0, ',', ' ') ?></td>
                                        <td><?= $op['frais'] > 0 ? '+ ' . number_format($op['frais'], 0, ',', ' ') : '0' ?></td>
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

<script>
    // Barèmes de frais depuis PHP
    const baremesRetrait = <?php echo json_encode($baremes_retrait ?? []); ?>;
    const baremesTransfert = <?php echo json_encode($baremes_transfert ?? []); ?>;

    // Fonction pour calculer les frais selon le barème
    function calculerFrais(montant, baremes) {
        if (!baremes || baremes.length === 0) return 0;
        
        for (const bareme of baremes) {
            if (montant >= bareme.montant_min && montant <= bareme.montant_max) {
                return parseFloat(bareme.frais);
            }
        }
        return 0;
    }

    // Fonction pour formater les montants
    function formatMontant(montant) {
        return new Intl.NumberFormat('fr-FR').format(montant) + ' Ar';
    }

    // Écouteur pour le dépôt
    const depotMontant = document.getElementById('depot-montant');
    const depotFee = document.getElementById('depot-fee');
    
    if (depotMontant) {
        depotMontant.addEventListener('input', function() {
            const montant = parseFloat(this.value) || 0;
            if (montant > 0) {
                depotFee.style.display = 'flex';
                depotFee.querySelector('.fee-amount').textContent = '0 Ar';
                depotFee.querySelector('.fee-info').textContent = '(Gratuit)';
            } else {
                depotFee.style.display = 'none';
            }
        });
    }

    // Écouteur pour le retrait
    const retraitMontant = document.getElementById('retrait-montant');
    const retraitFee = document.getElementById('retrait-fee');
    const retraitTotal = document.getElementById('retrait-total');
    
    if (retraitMontant) {
        retraitMontant.addEventListener('input', function() {
            const montant = parseFloat(this.value) || 0;
            if (montant > 0) {
                const frais = calculerFrais(montant, baremesRetrait);
                const total = montant + frais;
                
                retraitFee.style.display = 'flex';
                retraitFee.querySelector('.fee-amount').textContent = formatMontant(frais);
                retraitFee.querySelector('.fee-info').textContent = frais > 0 ? '' : '(Gratuit)';
                
                retraitTotal.style.display = 'flex';
                retraitTotal.querySelector('.total-amount').textContent = formatMontant(total);
            } else {
                retraitFee.style.display = 'none';
                retraitTotal.style.display = 'none';
            }
        });
    }

    // Écouteur pour le transfert
    const transfertMontant = document.getElementById('transfert-montant');
    const transfertFee = document.getElementById('transfert-fee');
    const transfertTotal = document.getElementById('transfert-total');
    
    if (transfertMontant) {
        transfertMontant.addEventListener('input', function() {
            const montant = parseFloat(this.value) || 0;
            if (montant > 0) {
                const frais = calculerFrais(montant, baremesTransfert);
                const total = montant + frais;
                
                transfertFee.style.display = 'flex';
                transfertFee.querySelector('.fee-amount').textContent = formatMontant(frais);
                transfertFee.querySelector('.fee-info').textContent = frais > 0 ? '' : '(Gratuit)';
                
                transfertTotal.style.display = 'flex';
                transfertTotal.querySelector('.total-amount').textContent = formatMontant(total);
            } else {
                transfertFee.style.display = 'none';
                transfertTotal.style.display = 'none';
            }
        });
    }

    // Filtrage en temps réel de l'historique
    const dateDebut = document.querySelector('input[name="date_debut"]');
    const dateFin = document.querySelector('input[name="date_fin"]');
    const typeOperation = document.querySelector('select[name="type_operation"]');
    const historyTable = document.getElementById('history-table');
    
    function filterHistory() {
        if (!historyTable) return;
        
        const rows = historyTable.querySelectorAll('tbody tr');
        const debut = dateDebut ? dateDebut.value : '';
        const fin = dateFin ? dateFin.value : '';
        const type = typeOperation ? typeOperation.value : '';
        
        rows.forEach(row => {
            if (row.classList.contains('empty-state')) return;
            
            const rowDate = row.getAttribute('data-date');
            const rowType = row.getAttribute('data-type');
            
            let visible = true;
            
            // Filtre par date de début
            if (debut && rowDate) {
                const rowDateObj = new Date(rowDate);
                const debutObj = new Date(debut);
                if (rowDateObj < debutObj) visible = false;
            }
            
            // Filtre par date de fin
            if (fin && rowDate && visible) {
                const rowDateObj = new Date(rowDate);
                const finObj = new Date(fin);
                finObj.setHours(23, 59, 59, 999);
                if (rowDateObj > finObj) visible = false;
            }
            
            // Filtre par type d'opération
            if (type && rowType && visible) {
                if (rowType !== type) visible = false;
            }
            
            row.style.display = visible ? '' : 'none';
        });
    }
    
    if (dateDebut) dateDebut.addEventListener('change', filterHistory);
    if (dateFin) dateFin.addEventListener('change', filterHistory);
    if (typeOperation) typeOperation.addEventListener('change', filterHistory);
</script>
<script src="/assets/js/space.js"></script>
</body>
</html>