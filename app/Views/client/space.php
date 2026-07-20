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
        <div class="balance-card enhanced-balance">
            <div class="balance-top">
                <div class="balance-label">
                    <i class="bi bi-wallet2 balance-icon"></i>
                    <div class="balance-title">Votre Solde Actuel</div>
                </div>
             
            </div>
            <div class="balance-main">
                <div class="balance-amount">
                    <?= number_format($compte['solde'], 2, ',', ' ') ?> <span class="balance-currency">Ar</span>
                </div>
                <div class="balance-sub">Disponible</div>
            </div>
            <div class="balance-footer">
                <div class="balance-note">Transactions récentes affichées ci-dessous</div>
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
                    <button class="tab-btn" data-tab="transfert-multiple">
                        <i class="bi bi-send-plus"></i>
                        Transfert Multiple
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
                        <div class="form-group" style="margin-top: 15px;">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" name="inclure_frais_retrait" value="1" id="inclure-frais-retrait">
                                <span style="font-size: 14px; color: #2c3e50;">Inclure les frais dans le montant à retirer</span>
                            </label>
                            <small style="color: #7f8c8d; font-size: 12px; margin-left: 24px;">Si coché, les frais seront déduits du montant demandé</small>
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
                        <div class="amount-received" id="retrait-recu" style="display: none; background: #e8f5e9; padding: 12px; border-radius: 8px; margin-top: 15px;">
                            <span class="received-label">Montant reçu :</span>
                            <span class="received-amount" style="color: #66BB6A; font-weight: 700;">0 Ar</span>
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

                <div class="tab-content" id="transfert-multiple">
                    <form id="form-transfert-multiple" action="<?= base_url('client/transfert-multiple') ?>" method="POST">
                        <div class="form-group">
                            <label class="form-label">Destinataires</label>
                            <div id="multi-rows" style="display: grid; gap:10px;">
                                <div class="multi-row" style="display:flex; gap:8px; align-items:center;">
                                    <input type="tel" name="destinataires[]" class="form-control" placeholder="Numéro (Ex: 037XXXXXXX)" required style="flex:3;">
                                    <button type="button" class="btn btn-danger remove-row" style="flex:0 0 auto; padding:6px 10px;">×</button>
                                </div>
                            </div>
                            <div style="margin-top:10px; display:flex; gap:10px;">
                                <button type="button" id="add-row" class="btn btn-outline-primary" style="padding:8px 12px;"><i class="bi bi-plus-lg"></i> Ajouter</button>
                                <button type="button" id="clear-rows" class="btn btn-outline-secondary" style="padding:8px 12px;">Effacer</button>
                            </div>
                            <small style="color: #7f8c8d; font-size: 12px;">Un champ par destinataire. Le montant saisi plus bas sera divisé automatiquement entre tous les numéros.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Montant total à transférer (Ar)</label>
                            <input type="number" name="montant_total" class="form-control" placeholder="Montant total" required min="100" id="montant-total-multiple">
                        </div>

                        <div id="preview-multiple" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                            <div style="font-weight: 600; color: #2c3e50; margin-bottom: 10px;">
                                <i class="bi bi-info-circle"></i> Prévisualisation
                            </div>
                            <div style="font-size: 14px; color: #7f8c8d;">
                                <div>Nombre de destinataires: <strong id="nb-destinataires">0</strong></div>
                                <div>Montant par destinataire: <strong id="montant-par-destinataire">0 Ar</strong></div>
                                <div>Frais total estimé: <strong id="frais-total-preview">0 Ar</strong></div>
                                <div>Total à débiter: <strong id="total-debiter-preview">0 Ar</strong></div>
                            </div>
                        </div>

                        <div class="fee-display" id="transfert-multiple-fee" style="display: none;">
                            <span class="fee-label">Détails frais :</span>
                            <span class="fee-amount">0 Ar</span>
                            <span class="fee-info"></span>
                        </div>

                        <div style="margin-top:12px; display:flex; gap:10px; align-items:center;">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send-plus"></i>
                                Envoyer
                            </button>
                            <small style="color:#7f8c8d;">Le montant total sera réparti équitablement entre les destinataires.</small>
                        </div>

                        <div style="margin-top:10px;">
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                <input type="checkbox" name="inclure_frais_retrait" value="1" id="inclure-frais-transfert-multi">
                                <span style="font-size:14px; color:#2c3e50;">Inclure les frais de retrait dans les montants envoyés</span>
                            </label>
                            <small style="color:#7f8c8d; font-size:12px;">Si coché, chaque montant est considéré TTC (les frais seront déduits du montant envoyé).</small>
                        </div>
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
    const retraitRecu = document.getElementById('retrait-recu');
    const inclureFraisRetrait = document.getElementById('inclure-frais-retrait');
    
    function updateRetraitCalculs() {
        const montant = parseFloat(retraitMontant?.value) || 0;
        const inclureFrais = inclureFraisRetrait?.checked || false;
        
        if (montant > 0) {
            const frais = calculerFrais(montant, baremesRetrait);
            let total, recu;
            
            if (inclureFrais) {
                total = montant;
                recu = montant - frais;
            } else {
                total = montant + frais;
                recu = montant;
            }
            
            retraitFee.style.display = 'flex';
            retraitFee.querySelector('.fee-amount').textContent = formatMontant(frais);
            retraitFee.querySelector('.fee-info').textContent = frais > 0 ? '' : '(Gratuit)';
            
            retraitTotal.style.display = 'flex';
            retraitTotal.querySelector('.total-amount').textContent = formatMontant(total);
            
            retraitRecu.style.display = 'block';
            retraitRecu.querySelector('.received-amount').textContent = formatMontant(recu);
        } else {
            retraitFee.style.display = 'none';
            retraitTotal.style.display = 'none';
            retraitRecu.style.display = 'none';
        }
    }
    
    if (retraitMontant) {
        retraitMontant.addEventListener('input', updateRetraitCalculs);
    }
    
    if (inclureFraisRetrait) {
        inclureFraisRetrait.addEventListener('change', updateRetraitCalculs);
    }

    // Transfert multiple (lignes dynamiques)
    const multiRows = document.getElementById('multi-rows');
    const addRowBtn = document.getElementById('add-row');
    const clearRowsBtn = document.getElementById('clear-rows');
    const montantTotalMultiple = document.getElementById('montant-total-multiple');
    const previewMultiple = document.getElementById('preview-multiple');
    const nbDestinataires = document.getElementById('nb-destinataires');
    const montantParDestinataire = document.getElementById('montant-par-destinataire');
    const fraisTotalPreview = document.getElementById('frais-total-preview');
    const totalDebiterPreview = document.getElementById('total-debiter-preview');
    const transfertMultipleFee = document.getElementById('transfert-multiple-fee');

    function normalizePhoneNumber(value) {
        let v = String(value || '').replace(/\s+/g, '');
        if (v.startsWith('+261')) v = v.substring(4);
        if (v.startsWith('261')) v = v.substring(3);
        if (v.length > 0 && !v.startsWith('0')) v = '0' + v;
        return v;
    }

    function extractPrefix(value) {
        const normalized = normalizePhoneNumber(value);
        return normalized.length >= 3 ? normalized.substring(0, 3) : '';
    }

    function createRow(number = '', amount = '') {
        const row = document.createElement('div');
        row.className = 'multi-row';
        row.style.display = 'flex';
        row.style.gap = '8px';
        row.style.alignItems = 'center';

        const inputNum = document.createElement('input');
        inputNum.type = 'tel';
        inputNum.name = 'destinataires[]';
        inputNum.placeholder = 'Numéro (Ex: 037XXXXXXX)';
        inputNum.required = true;
        inputNum.className = 'form-control';
        inputNum.style.flex = '3';
        inputNum.value = number;

        // validation: même opérateur entre les destinataires
        function validatePrefix() {
            const val = inputNum.value.trim();
            if (!val) { inputNum.classList.remove('is-invalid'); return true; }
            const rows = Array.from(multiRows.querySelectorAll('.multi-row'));
            const refRow = rows.find(r => {
                const otherValue = r.querySelector('input[name="destinataires[]"]').value.trim();
                return otherValue !== '';
            });
            const referencePrefix = refRow ? extractPrefix(refRow.querySelector('input[name="destinataires[]"]').value) : extractPrefix(val);
            const pref = extractPrefix(val);

            if (referencePrefix && pref && pref !== referencePrefix) {
                inputNum.classList.add('is-invalid');
                inputNum.title = 'Tous les numéros doivent appartenir au même opérateur';
                return false;
            } else {
                inputNum.classList.remove('is-invalid');
                inputNum.title = '';
                return true;
            }
        }

        inputNum.addEventListener('blur', function(){ validatePrefix(); updateMultiPreview(); });

        const btnRemove = document.createElement('button');
        btnRemove.type = 'button';
        btnRemove.className = 'btn btn-danger remove-row';
        btnRemove.style.flex = '0 0 auto';
        btnRemove.style.padding = '6px 10px';
        btnRemove.textContent = '×';

        btnRemove.addEventListener('click', function() {
            row.remove();
            updateMultiPreview();
        });

        inputNum.addEventListener('input', function(){ validatePrefix(); updateMultiPreview(); });

        row.appendChild(inputNum);
        row.appendChild(btnRemove);

        return row;
    }

    function addRow(number = '', amount = '') {
        const row = createRow(number, amount);
        multiRows.appendChild(row);
        updateMultiPreview();
    }

    function clearRows() {
        // Remove all and add one empty
        multiRows.innerHTML = '';
        addRow();
    }

    function updateMultiPreview() {
        const rows = Array.from(multiRows.querySelectorAll('.multi-row'));
        const pairs = [];
        const montantTotal = parseFloat(montantTotalMultiple?.value) || 0;
        let totalMontant = 0;
        let totalFrais = 0;
        let referencePrefix = null;
        let sameOperator = true;

        rows.forEach(r => {
            const num = normalizePhoneNumber(r.querySelector('input[name="destinataires[]"]').value.trim());
            if (num) {
                pairs.push({num});
            }
        });

        if (pairs.length > 0 && montantTotal > 0) {
            const montantPar = montantTotal / pairs.length;
            totalMontant = montantTotal;
            pairs.forEach(() => {
                const frais = calculerFrais(montantPar, baremesTransfert);
                totalFrais += frais;
            });

            pairs.forEach(pair => {
                const prefix = extractPrefix(pair.num);
                if (!referencePrefix) {
                    referencePrefix = prefix;
                } else if (prefix && prefix !== referencePrefix) {
                    sameOperator = false;
                }
            });
        }

        if (pairs.length > 0) {
            previewMultiple.style.display = 'block';
            nbDestinataires.textContent = pairs.length;
            montantParDestinataire.textContent = pairs.length > 0 && montantTotal > 0 ? formatMontant(montantTotal / pairs.length) : '0 Ar';
            fraisTotalPreview.textContent = formatMontant(totalFrais);
            totalDebiterPreview.textContent = formatMontant(totalMontant + totalFrais);

            transfertMultipleFee.style.display = 'flex';
            transfertMultipleFee.querySelector('.fee-amount').textContent = formatMontant(totalFrais);
            transfertMultipleFee.querySelector('.fee-info').textContent = sameOperator ? '' : 'Les numéros doivent être du même opérateur';
        } else {
            previewMultiple.style.display = 'none';
            transfertMultipleFee.style.display = 'none';
        }
    }

    // initialisation
    (function initMulti() {
        // attach handlers to existing controls
        if (addRowBtn) addRowBtn.addEventListener('click', () => addRow());
        if (clearRowsBtn) clearRowsBtn.addEventListener('click', clearRows);
        if (montantTotalMultiple) montantTotalMultiple.addEventListener('input', updateMultiPreview);

        // ensure at least one row
        if (multiRows.querySelectorAll('.multi-row').length === 0) addRow();
    })();

    // Formatter les champs téléphoniques de la page
    function formatPhoneRaw(value) {
        let v = String(value || '').replace(/\s+/g, '');
        // remove +261 or leading country code
        if (v.startsWith('261')) v = v.substring(3);
        if (v.startsWith('+261')) v = v.substring(4);
        // ensure leading 0 if prefix looks like 32..
        if (v.length > 0 && !v.startsWith('0')) {
            const firstTwo = v.substring(0,2);
            const validPrefixes = ['32','33','34','38'];
            if (validPrefixes.includes(firstTwo)) v = '0' + v;
        }
        // Format: 0XX XX XXX XX
        let formatted = '';
        for (let i = 0; i < v.length; i++) {
            if (i === 3 || i === 5 || i === 8) formatted += ' ';
            formatted += v[i];
        }
        return formatted;
    }

    function attachPhoneFormatting(input) {
        if (!input) return;
        input.addEventListener('input', function(e){
            const pos = input.selectionStart;
            const before = input.value;
            const formatted = formatPhoneRaw(before);
            input.value = formatted;
        });
        input.addEventListener('blur', function(){
            input.value = formatPhoneRaw(input.value);
        });
    }

    // apply to existing tel inputs
    document.querySelectorAll('input[type="tel"]').forEach(i => attachPhoneFormatting(i));

    // ensure dynamic rows get formatting when created
    const origAddRow = addRow;
    window.addRowWithFormat = function(number = '', amount = '') { const r = origAddRow(number, amount); const last = multiRows.lastElementChild; const tel = last.querySelector('input[name="destinataires[]"]'); attachPhoneFormatting(tel); return r; };
    // override addRow usage in this file
    addRow = function(n,a){ return window.addRowWithFormat(n,a); };

    // Validation à la soumission: exiger un même opérateur entre les destinataires
    const formMulti = document.getElementById('form-transfert-multiple');
    if (formMulti) {
        formMulti.addEventListener('submit', function(e) {
            // vérifier au moins une ligne valide et un opérateur commun
            const rows = Array.from(multiRows.querySelectorAll('.multi-row'));
            let anyValid = false;
            let referencePrefix = null;
            for (const r of rows) {
                const num = normalizePhoneNumber(r.querySelector('input[name="destinataires[]"]').value.trim());
                if (num) {
                    anyValid = true;
                    const pref = extractPrefix(num);
                    if (!referencePrefix) {
                        referencePrefix = pref;
                    } else if (pref && pref !== referencePrefix) {
                        e.preventDefault();
                        alert('Tous les numéros du transfert multiple doivent appartenir au même opérateur.');
                        return false;
                    }
                }
            }
            if (!anyValid) {
                e.preventDefault();
                alert('Veuillez renseigner au moins un destinataire valide avec montant.');
                return false;
            }

            const montant = parseFloat(montantTotalMultiple?.value) || 0;
            if (montant <= 0) {
                e.preventDefault();
                alert('Veuillez saisir un montant total valide.');
                return false;
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