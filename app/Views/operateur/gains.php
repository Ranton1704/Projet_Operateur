<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="cards-grid">
    <!-- Carte Total -->
    <div class="balance-card" style="grid-column: span 12; padding: 50px;">
        <div class="balance-label">
            <i class="bi bi-trophy"></i>
            Revenus Totaux Générés
        </div>
        <div class="balance-amount">
            <?php 
                $totalGlobal = 0;
                if(!empty($gains)) { foreach($gains as $g) { $totalGlobal += $g['total_gains']; } }
            ?>
            <?= number_format($totalGlobal, 2, ',', ' ') ?> <span class="balance-currency">Ar</span>
        </div>
        <div style="margin-top: 20px; opacity: 0.9; font-size: 15px;">
            <i class="bi bi-graph-up-arrow"></i> Cumul de tous les frais perçus
        </div>
    </div>

    <!-- Cartes par type -->
    <?php if(empty($gains)): ?>
        <div class="operations-card" style="grid-column: span 12;">
            <div class="card-body" style="text-align: center; padding: 60px;">
                <i class="bi bi-inbox" style="font-size: 64px; color: #95a5a6; margin-bottom: 20px;"></i>
                <h4 style="color: #7f8c8d;">Aucun revenu enregistré</h4>
                <p style="color: #95a5a6; margin-top: 10px;">Les gains apparaîtront ici après les premières transactions</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($gains as $index => $g): ?>
            <?php
                $colors = ['#66BB6A', '#2196F3', '#FF9800', '#9C27B0', '#F44336'];
                $color = $colors[$index % count($colors)];
                $icons = ['bi-arrow-down-circle', 'bi-arrow-right-circle', 'bi-arrow-up-circle', 'bi-currency-dollar', 'bi-percent'];
                $icon = $icons[$index % count($icons)];
            ?>
            <div class="operations-card" style="grid-column: span 4; min-height: auto; transition: all 0.3s ease; border-left: 5px solid <?= $color ?>;">
                <div class="card-body" style="padding: 35px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <div style="width: 60px; height: 60px; background: <?= $color ?>20; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi <?= $icon ?>" style="font-size: 28px; color: <?= $color ?>;"></i>
                        </div>
                        <div>
                            <h6 style="margin: 0; color: #2c3e50; font-weight: 700; font-size: 15px;">Frais <?= ucfirst(esc($g['nom'])) ?></h6>
                            <span style="font-size: 12px; color: #95a5a6;">Type d'opération</span>
                        </div>
                    </div>
                    <div style="text-align: center; padding: 20px 0; border-top: 2px solid #f0f0f0;">
                        <h3 style="font-size: 42px; font-weight: 700; color: <?= $color ?>; margin: 0;">
                            <?= number_format($g['total_gains'], 0, ',', ' ') ?>
                        </h3>
                        <span style="color: #7f8c8d; font-size: 14px;">Ariary</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.operations-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
}

.balance-card::before {
    display: none !important;
}
</style>

<?= $this->endSection() ?>
