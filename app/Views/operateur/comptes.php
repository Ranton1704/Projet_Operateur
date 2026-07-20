<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="cards-grid">
    <!-- SITUATION DES COMPTES -->
    <div class="history-card" style="grid-column: span 12;">
        <div class="card-header">
            <i class="bi bi-people"></i>
            Situation des Comptes Clients
        </div>
        <div style="padding: 20px;">
            <input type="text" id="search-compte" class="form-control" placeholder="🔍 Rechercher un numéro..." style="margin-bottom: 15px;">
            <div style="max-height: 500px; overflow-y: auto;">
                <table class="history-table" id="table-comptes">
                    <thead>
                        <tr>
                            <th>Numéro de Téléphone</th>
                            <th>Solde actuel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($comptes)): ?>
                            <tr><td colspan="2" class="empty-state">Aucun compte actif.</td></tr>
                        <?php else: ?>
                            <?php foreach($comptes as $c): ?>
                                <tr>
                                    <td class="phone-number"><?= esc($c['numero_telephone']) ?></td>
                                    <td class="fw-bold" style="color: #4CAF50;"><?= number_format($c['solde'], 2, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('search-compte').addEventListener('keyup', function() {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('#table-comptes tbody tr');
    rows.forEach(row => {
        const phone = row.querySelector('.phone-number')?.textContent.toLowerCase() || '';
        row.style.display = phone.includes(search) ? '' : 'none';
    });
});
</script>

<?= $this->endSection() ?>
