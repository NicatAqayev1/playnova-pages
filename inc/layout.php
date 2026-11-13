<?php
require_once __DIR__ . '/bootstrap.php';

function render_head(array $o): void {
    $s = site()['settings'] ?? [];
    $title = $o['title'] . (!empty($o['suffix']) ? ' — ' . brand() : '');
    $v = @filemtime(ROOT . '/styles.css') ?: 1;
    ?><!doctype html>
<html lang="<?= e($o['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="<?= e($s['theme_color'] ?? '#080713') ?>" />
  <meta name="description" content="<?= e($o['desc'] ?? '') ?>" />
  <title><?= e($title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css?v=<?= $v ?>" />
</head>
<body>
  <div class="ambient ambient-one"></div><div class="ambient ambient-two"></div>
<?php }

function brand_html(array $s): string {
    return '<span class="brand-mark">' . e($s['brand_symbol'] ?? '♠') . '</span><span>' . e($s['brand_a'] ?? 'Play') . '<span>' . e($s['brand_b'] ?? 'Nova') . '</span></span>';
}

function render_header(string $activeHref = ''): void {
    $s = site()['settings'] ?? []; ?>
  <header class="site-header" lang="en">
    <a class="brand" href="index.php#home" aria-label="<?= e(brand()) ?> home"><?= brand_html($s) ?></a>
    <button class="mobile-toggle" aria-label="Open navigation" aria-expanded="false">☰</button>
    <nav class="main-nav" aria-label="Main navigation">
<?php foreach ($s['nav'] ?? [] as $n): ?>
      <a class="nav-link<?= ($n['href'] ?? '') === $activeHref ? ' active' : '' ?>" href="<?= e($n['href'] ?? '#') ?>"><?php if (!empty($n['icon'])): ?><span class="home-icon"><?= e($n['icon']) ?></span> <?php endif; ?><?= e($n['label'] ?? '') ?></a>
<?php endforeach; ?>
    </nav>
    <div class="header-actions"><button class="search-btn" aria-label="Search">⌕</button><a class="btn btn-primary compare-top" href="<?= e($s['cta_href'] ?? '#') ?>"><?= e($s['cta_label'] ?? '') ?> <span>→</span></a></div>
  </header>
<?php }

function render_footer(string $current = ''): void {
    $s = site()['settings'] ?? []; ?>
  <footer id="contact" lang="en"><a class="brand" href="index.php#home"><?= brand_html($s) ?></a><p><?= e($s['footer_tagline'] ?? '') ?></p><div>
<?php foreach ($s['footer_links'] ?? [] as $l): $h = $l['href'] ?? '#'; ?>
<a href="<?= e($h) ?>"<?= $current !== '' && $h === $current ? ' aria-current="page"' : '' ?>><?= e($l['label'] ?? '') ?></a>
<?php endforeach; ?>
</div><small><?= e($s['footer_small'] ?? '') ?></small></footer>
  <div class="search-modal" role="dialog" aria-modal="true" aria-label="Search <?= e(brand()) ?>"><div class="search-box"><button class="close-search" aria-label="Close search">×</button><label for="site-search">What are you looking for?</label><input id="site-search" type="search" placeholder="Search casinos, games, guides..."/><p>Try “casino reviews” or “bonus guide”</p></div></div>
<?php }

function render_end(array $extraScripts = []): void {
    echo "  <script src=\"script.js\"></script>\n";
    foreach ($extraScripts as $js) echo '  <script src="' . e($js) . "\"></script>\n";
    echo "</body>\n</html>\n";
}

/** terms.php, privacy.php, cookies.php üçün ortaq şablon. */
function render_policy(string $key): void {
    $p = site()[$key] ?? [];
    render_head(['title' => $p['seo_title'] ?? '', 'suffix' => true, 'desc' => $p['seo_description'] ?? '', 'lang' => 'az']);
    render_header('');
    $secs = $p['sections'] ?? [];
    $isCookies = $key === 'cookies'; ?>

  <main class="policy-page">
    <section class="policy-hero section-wrap" lang="az">
      <h1><?= e($p['h1_plain'] ?? '') ?> <em><?= e($p['h1_em'] ?? '') ?></em></h1>
      <p><?= e($p['intro'] ?? '') ?></p>
      <div class="policy-meta"><span>Son yenilənmə: <time datetime="<?= e($p['updated'] ?? '') ?>"><?= e(az_date($p['updated'] ?? '')) ?></time></span><span>Operator: <?= e($p['operator'] ?? '') ?></span></div>
    </section>
<?php if ($isCookies): ?>

    <section class="consent-wrap section-wrap" lang="az" aria-label="Kuki seçimləri">
      <div class="consent-panel" data-consent>
        <div class="consent-row">
          <div class="consent-text"><h3 id="lbl-necessary"><?= e($p['c_nec_title'] ?? '') ?> <span class="consent-tag"><?= e($p['c_nec_tag'] ?? '') ?></span></h3><p><?= e($p['c_nec_desc'] ?? '') ?></p></div>
          <button type="button" class="switch" role="switch" data-category="necessary" aria-checked="true" aria-labelledby="lbl-necessary" aria-disabled="true"><span class="switch-thumb"></span></button>
        </div>
        <div class="consent-row">
          <div class="consent-text"><h3 id="lbl-analytics"><?= e($p['c_an_title'] ?? '') ?></h3><p><?= e($p['c_an_desc'] ?? '') ?></p></div>
          <button type="button" class="switch" role="switch" data-category="analytics" aria-checked="false" aria-labelledby="lbl-analytics"><span class="switch-thumb"></span></button>
        </div>
        <div class="consent-row">
          <div class="consent-text"><h3 id="lbl-marketing"><?= e($p['c_mk_title'] ?? '') ?></h3><p><?= e($p['c_mk_desc'] ?? '') ?></p></div>
          <button type="button" class="switch" role="switch" data-category="marketing" aria-checked="false" aria-labelledby="lbl-marketing"><span class="switch-thumb"></span></button>
        </div>
        <div class="consent-actions">
          <button type="button" class="btn btn-primary" data-action="save"><?= e($p['btn_save'] ?? '') ?></button>
          <button type="button" class="btn btn-ghost" data-action="necessary-only"><?= e($p['btn_necessary'] ?? '') ?></button>
          <button type="button" class="btn btn-ghost" data-action="accept-all"><?= e($p['btn_all'] ?? '') ?></button>
        </div>
        <p class="consent-status" role="status" aria-live="polite"></p>
      </div>
    </section>
<?php endif; ?>

    <section class="legal-section policy-section section-wrap" lang="az" aria-label="<?= e($p['h1_plain'] ?? '') ?>">
<?php if (!$isCookies): ?>
      <div class="legal-layout">
        <nav class="license-card toc-card" aria-label="<?= e($p['toc_title'] ?? '') ?>">
          <h2><?= e($p['toc_title'] ?? '') ?></h2>
          <ol class="toc-list">
<?php foreach ($secs as $s): ?>
            <li><a href="#<?= e($s['id']) ?>"><?= e($s['toc'] ?: $s['title']) ?></a></li>
<?php endforeach; ?>
          </ol>
        </nav>
<?php endif; ?>
        <div class="legal-copy<?= $isCookies ? ' cookie-copy' : '' ?>">
<?php foreach ($secs as $i => $s): ?>
          <article class="legal-block" id="<?= e($s['id']) ?>">
            <h3><span><?= $i + 1 ?></span> <?= e($s['title']) ?></h3>
            <?= $s['body'] ?>

          </article>
<?php endforeach; ?>
        </div>
<?php if (!$isCookies): ?>
      </div>
<?php endif; ?>
    </section>
  </main>
<?php
    render_footer(basename($_SERVER['SCRIPT_NAME']));
    render_end($isCookies ? ['cookie-settings.js'] : []);
}
