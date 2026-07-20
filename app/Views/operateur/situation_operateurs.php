<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 32px; font-weight: 700; color: #2c3e50; margin: 0;">
        <i class="bi bi-cash-stack" style="color: #66BB6A;"></i> Situation des Montants
    </h2>
    <p style="color: #7f8c8d; margin-top: 8px; font-size: 16px;">Montants à envoyer à chaque opérateur externe</p>
</div>

<div class="cards-grid">
    <!-- Carte Résumé -->
    <div class="balance-card" style="grid-column: span 12; padding: 40px;">
        <div class="balance-label">
            <i class="bi bi-wallet2"></i>
            Total à Transférer aux Opérateurs
        </div>
        <div class="balance-amount">
            <?php 
                $totalGlobal = 0;
                if(!empty($montants)) { 
                    foreach($montants as $m) { 
                        $totalGlobal += $m['total_montant']; 
                    } 
                }
            ?>
            <?= number_format($totalGlobal, 0, ',', ' ') ?> <span class="balance-currency">Ar</span>
        </div>
        <div style="margin-top: 15px; opacity: 0.9; font-size: 14px;">
            <i class="bi bi-info-circle"></i> Somme des montants transférés vers les autres opérateurs
        </div>
    </div>

    <!-- Liste des opérateurs -->
    <?php if(empty($montants)): ?>
        <div class="operations-card" style="grid-column: span 12;">
            <div class="card-body" style="text-align: center; padding: 60px;">
                <i class="bi bi-inbox" style="font-size: 64px; color: #95a5a6; margin-bottom: 20px;"></i>
                <h4 style="color: #7f8c8d;">Aucun transfert vers d'autres opérateurs</h4>
                <p style="color: #95a5a6; margin-top: 10px;">Les montants apparaîtront ici après les premiers transferts externes</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($montants as $index => $m): ?>
            <?php
                $colors = ['#66BB6A', '#2196F3', '#FF9800', '#9C27B0', '#F44336'];
                $color = $colors[$index % count($colors)];
            ?>
            <div class="operations-card" style="grid-column: span 4; min-height: auto; transition: all 0.3s ease; border-left: 5px solid <?= $color ?>;">
                <div class="card-body" style="padding: 30px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <div style="width: 55px; height: 55px; background: <?= $color ?>20; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-building" style="font-size: 26px; color: <?= $color ?>;"></i>
                        </div>
                        <div>
                            <h6 style="margin: 0; color: #2c3e50; font-weight: 700; font-size: 16px;">
                                <?= esc($m['nom']) ?>
                            </h6>
                            <span style="font-size: 12px; color: #95a5a6;">
                                Commission: <?= $m['commission_pourcentage'] ?>%
                            </span>
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px;">
                        <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 5px;">Montant Total</div>
                        <div style="font-size: 32px; font-weight: 700; color: <?= $color ?>;">
                            <?= number_format($m['total_montant'], 0, ',', ' ') ?>
                        </div>
                        <div style="font-size: 14px; color: #7f8c8d;">Ariary</div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                        <div style="text-align: center; padding: 15px; background: #e8f5e9; border-radius: 8px;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px;">Transferts</div>
                            <div style="font-size: 20px; font-weight: 700; color: #66BB6A;">
                                <?= $m['nombre_transferts'] ?>
                            </div>
                        </div>
                        <div style="text-align: center; padding: 15px; background: #f3e5f5; border-radius: 8px;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px;">Commission</div>
                            <div style="font-size: 20px; font-weight: 700; color: #9C27B0;">
                                <?= number_format($m['total_commission'], 0, ',', ' ') ?>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: center; padding: 12px; background: <?= $color ?>10; border-radius: 8px; border: 1px dashed <?= $color ?>;">
                        <i class="bi bi-arrow-right-circle" style="color: <?= $color ?>; margin-right: 5px;"></i>
                        <span style="font-size: 13px; color: <?= $color ?>; font-weight: 600;">
                            À envoyer: <?= number_format($m['total_montant'] + $m['total_commission'], 0, ',', ' ') ?> Ar
                        </span>
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
