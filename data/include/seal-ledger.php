<!-- ================================================================
     Fair Law Firm LTD — Seal & Ledger Instrument
     Dashboard signature element — §2.7
     Wired to existing $userCount, $totalProperties, $rentalProperties,
     $blogCount variables from dashboard.php (no new queries).
     ================================================================ -->
<?php
/* Compute registry health from existing dashboard variables.
   Healthy = active users + total properties + blog posts.
   Assume "healthy" means these counts exist and are > 0.
   Total potential = all content items (properties + blog + users).
   Health % = items with active status / total items. */
$_sl_activeUsers     = max(0, (int)($userCount ?? 0));
$_sl_totalProperties = max(0, (int)($totalProperties ?? 0));
$_sl_totalItems      = $_sl_activeUsers + $_sl_totalProperties + max(0, (int)($blogCount ?? 0));
$_sl_healthPct       = $_sl_totalItems > 0 ? min(100, round(($_sl_activeUsers + $_sl_totalProperties) / max(1, $_sl_totalItems) * 100)) : 0;

/* Ledger bar split: Legal (blog) vs Property (listings) */
$_sl_blogCount   = max(0, (int)($blogCount ?? 0));
$_sl_legalTotal  = $_sl_blogCount;
$_sl_propTotal   = $_sl_totalProperties;
$_sl_ledgerTotal = $_sl_legalTotal + $_sl_propTotal;
$_sl_legalPct    = $_sl_ledgerTotal > 0 ? round($_sl_legalTotal / $_sl_ledgerTotal * 100) : 50;
$_sl_propPct     = 100 - $_sl_legalPct;
?>

<div class="fl-seal-ledger" role="region" aria-label="Registry Health">

    <!-- The Seal — fills with sage as health rises -->
    <div class="fl-seal-ledger__seal-wrap">
        <div class="fl-seal fl-seal--dashboard" role="img"
             aria-label="Registry health <?php echo $_sl_healthPct; ?> percent">
            <svg class="fl-seal__svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <path id="fl-dash-seal-path" d="M100,100 m-72,0 a72,72 0 1,1 144,0 a72,72 0 1,1 -144,0" />
                    <clipPath id="fl-seal-health-clip">
                        <rect x="0" y="<?php echo 200 - (200 * $_sl_healthPct / 100); ?>"
                              width="200" height="<?php echo 200 * $_sl_healthPct / 100; ?>" />
                    </clipPath>
                </defs>
                <!-- Background ring -->
                <circle cx="100" cy="100" r="95" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.15" />
                <!-- Health fill (sage rising from bottom) -->
                <circle cx="100" cy="100" r="88" fill="var(--fl-sage-600)" opacity="0.12"
                        clip-path="url(#fl-seal-health-clip)" />
                <!-- Perimeter text -->
                <text class="fl-seal__perimeter-text">
                    <textPath href="#fl-dash-seal-path" startOffset="0%">REGISTRY HEALTH · <?php echo $_sl_healthPct; ?>%% · </textPath>
                </text>
                <!-- Percentage center -->
                <text x="100" y="96" text-anchor="middle" dominant-baseline="central"
                      class="fl-seal__center-value"><?php echo $_sl_healthPct; ?>%</text>
                <text x="100" y="116" text-anchor="middle" dominant-baseline="central"
                      class="fl-seal__center-label">HEALTH</text>
            </svg>
        </div>
    </div>

    <!-- The Ledger Bar — Legal vs Property split -->
    <div class="fl-seal-ledger__ledger">
        <div class="fl-seal-ledger__ledger-label">
            <span class="fl-kicker">Practice Ledger</span>
        </div>
        <div class="fl-ledger-bar" role="img"
             aria-label="Legal <?php echo $_sl_legalPct; ?> percent, Property <?php echo $_sl_propPct; ?> percent">
            <div class="fl-ledger-bar__segment fl-ledger-bar__segment--legal"
                 style="width: <?php echo $_sl_legalPct; ?>%;"
                 title="Legal: <?php echo $_sl_legalTotal; ?> items">
            </div>
            <div class="fl-ledger-bar__segment fl-ledger-bar__segment--property"
                 style="width: <?php echo $_sl_propPct; ?>%;"
                 title="Property: <?php echo $_sl_propTotal; ?> items">
            </div>
        </div>
        <div class="fl-seal-ledger__ledger-legend">
            <span class="fl-seal-ledger__legend-item">
                <span class="fl-seal-ledger__legend-dot fl-seal-ledger__legend-dot--legal"></span>
                Legal <span class="fl-data"><?php echo $_sl_legalTotal; ?></span>
            </span>
            <span class="fl-seal-ledger__legend-item">
                <span class="fl-seal-ledger__legend-dot fl-seal-ledger__legend-dot--property"></span>
                Property <span class="fl-data"><?php echo $_sl_propTotal; ?></span>
            </span>
        </div>
    </div>

    <!-- Registry Watch — pending items -->
    <div class="fl-seal-ledger__watch">
        <span class="fl-kicker">Registry Watch</span>
        <div class="fl-seal-ledger__watch-items">
            <div class="fl-seal-ledger__watch-item">
                <span class="fl-seal-ledger__watch-value fl-data-lg"><?php echo $userCount ?? 0; ?></span>
                <span class="fl-seal-ledger__watch-label">Active Users</span>
            </div>
            <div class="fl-seal-ledger__watch-item">
                <span class="fl-seal-ledger__watch-value fl-data-lg"><?php echo $totalProperties ?? 0; ?></span>
                <span class="fl-seal-ledger__watch-label">Properties</span>
            </div>
            <div class="fl-seal-ledger__watch-item">
                <span class="fl-seal-ledger__watch-value fl-data-lg"><?php echo $rentalProperties ?? 0; ?></span>
                <span class="fl-seal-ledger__watch-label">Rentals</span>
            </div>
        </div>
    </div>

</div>
