<section>
    <h2 class="w3-text-secondary w3-large">Abstimmungsergebnis für das Logo</h2>
    <div class="flex-container w3-section" style="justify-content: space-around;">
        <div class="flex-item" style="flex-flow: column; width: 30%;">
            <img src="<?= Env::BASE_URL ?><?= Abstimmung::LOGO1 ?>" class="w3-image">
            <div class="w3-padding">
                <p class="w3-xxlarge w3-text-primary"><?=round( ($logo['logo1'] ?? 0) / $num_stimmen * 100)?>%</p>
            </div>
        </div>
        <div class="flex-item"  style="flex-flow: column; width: 30%;">
            <img src="<?= Env::BASE_URL ?><?= Abstimmung::LOGO2 ?>" class="w3-image">
            <div class="w3-padding">
                <p class="w3-xxlarge w3-text-primary"><?=round( ($logo['logo2'] ?? 0) / $num_stimmen * 100)?>%</p>
            </div>
        </div>
    </div>
</section>