<?php
require __DIR__ . '/inc/layout.php';
$S = site();
$h = $S['home'] ?? []; $c = $S['casinos'] ?? []; $g = $S['guides'] ?? []; $gm = $S['games'] ?? []; $l = $S['legal'] ?? [];
$guideCls = ['guide-one', 'guide-two', 'guide-three'];
$gameCls = ['game-one', 'game-two', 'game-three'];
$casinos = array_values(array_filter($c['items'] ?? [], fn($x) => !empty($x['active'])));

render_head(['title' => $h['seo_title'] ?? brand(), 'desc' => $h['seo_description'] ?? '', 'lang' => 'en']);
render_header('index.php#home');
?>

  <main id="home">
    <section class="hero">
      <div class="hero-glow"></div><div class="hero-orbit orbit-a"></div><div class="hero-orbit orbit-b"></div>
      <div class="hero-copy">
        <div class="crown">♕</div>
        <h1><?= e($h['h1_a'] ?? '') ?><br><em><?= e($h['h1_b'] ?? '') ?></em></h1>
        <p><?= nl($h['subtitle'] ?? '') ?></p>
        <div class="hero-actions"><a class="btn btn-primary btn-large" href="<?= e($h['cta_href'] ?? '#') ?>"><?= e($h['cta_label'] ?? '') ?> <span>→</span></a><div class="independent"><span class="shield">♢</span><span><?= nl($h['badge'] ?? '') ?></span></div></div>
      </div>
      <div class="hero-art" aria-hidden="true">
        <img src="<?= e($h['hero_image'] ?? 'background.png') ?>" alt="">
      </div>
    </section>

    <section class="trust-strip" aria-label="<?= e(brand()) ?> benefits">
<?php foreach ($h['trust'] ?? [] as $t): ?>
      <div><span class="gold-icon"><?= e($t['icon'] ?? '') ?></span><span><?= nl($t['text'] ?? '') ?></span></div>
<?php endforeach; ?>
    </section>

    <section class="casino-section section-wrap" id="casinos">
      <div class="section-heading"><h2><?= e($c['heading'] ?? '') ?> <span class="heading-dash"></span></h2><a href="<?= e($c['link_href'] ?? '#') ?>" class="text-link"><?= e($c['link_label'] ?? '') ?> <span>→</span></a></div>
      <div class="casino-layout"><div class="casino-list">
<?php foreach ($casinos as $i => $x): $u = $x['url'] ?? '#'; ?>
        <article class="casino-card"><span class="rank">#<?= $i + 1 ?></span><div class="casino-logo <?= e($x['logo_class'] ?? '') ?>"><?= ($x['logo_html'] ?? '') !== '' ? $x['logo_html'] : e($x['name'] ?? '') ?></div><div class="rating"><span>★</span> <?= e(number_format((float)($x['rating'] ?? 0), 1)) ?> <small>/5</small></div><a class="btn btn-primary play-btn" href="<?= e($u) ?>"<?= link_attrs($u) ?>><?= e($c['play_label'] ?? 'Play Now') ?> <span>→</span></a></article>
<?php endforeach; ?>
        <article class="casino-card teaser-card"><div class="teaser-chip">✦</div><span class="teaser-label"><?= nl($c['teaser_label'] ?? '') ?></span><a href="<?= e($c['teaser_href'] ?? '#') ?>" aria-label="<?= e($c['teaser_label'] ?? '') ?>">→</a></article>
      </div>
      <aside class="bonus-banner"><div class="bonus-copy"><span><?= e($c['bonus_pre'] ?? '') ?></span><strong><?= e($c['bonus_amount'] ?? '') ?></strong><b><?= e($c['bonus_label'] ?? '') ?></b><a class="btn btn-gold" href="<?= e($c['bonus_href'] ?? '#') ?>"><?= e($c['bonus_btn'] ?? '') ?> <span>→</span></a></div><div class="gift-art"><div class="gift-bow"></div><div class="gift-box"></div><div class="gift-coin">✦</div></div></aside></div>
    </section>

    <section class="lower-grid section-wrap" id="guides">
      <article class="content-panel"><div class="panel-heading"><h3><span><?= e($g['icon'] ?? '') ?></span> <?= e($g['heading'] ?? '') ?></h3><a href="<?= e($g['link_href'] ?? '#') ?>"><?= e($g['link_label'] ?? '') ?> →</a></div><div class="guide-items">
<?php foreach (array_slice($g['items'] ?? [], 0, 6) as $i => $x): ?>
        <a class="guide-tile <?= $guideCls[$i % 3] ?>" href="<?= e($x['href'] ?? '#') ?>"><span><?= e($x['label'] ?? '') ?></span><b><?= e($x['title'] ?? '') ?></b></a>
<?php endforeach; ?>
      </div></article>
      <article class="content-panel" id="games"><div class="panel-heading"><h3><span><?= e($gm['icon'] ?? '') ?></span> <?= e($gm['heading'] ?? '') ?></h3><a href="<?= e($gm['link_href'] ?? '#') ?>"><?= e($gm['link_label'] ?? '') ?> →</a></div><div class="game-items">
<?php foreach (array_slice($gm['items'] ?? [], 0, 6) as $i => $x): ?>
        <a class="game-tile <?= $gameCls[$i % 3] ?>" href="<?= e($x['href'] ?? '#') ?>"><span><?= e($x['symbol'] ?? '') ?></span><b><?= e($x['name'] ?? '') ?></b></a>
<?php endforeach; ?>
      </div></article>
      <a class="responsible-panel" href="#responsible" aria-label="<?= e($l['panel_title'] ?? '') ?>"><div class="age-badge"><?= e($l['age_badge'] ?? '21+') ?></div><div><h3><?= e($l['panel_title'] ?? '') ?></h3><p><?= e($l['panel_text'] ?? '') ?></p></div><div class="responsible-shield">✓</div></a>
    </section>

    <section class="about-section section-wrap" id="about"><div><span class="eyebrow"><?= e($h['about_eyebrow'] ?? '') ?></span><h2><?= e($h['about_t1'] ?? '') ?><br><em><?= e($h['about_t2'] ?? '') ?></em></h2></div><p><?= e($h['about_text'] ?? '') ?></p></section>

    <section class="legal-section section-wrap" id="legal" lang="az" aria-labelledby="legal-title">
      <div class="section-heading">
        <h2 id="legal-title"><?= e($l['heading'] ?? '') ?> <span class="heading-dash"></span></h2>
        <a href="#responsible" class="text-link"><?= e($l['link_label'] ?? '') ?> <span>→</span></a>
      </div>
      <div class="legal-layout">
        <article class="license-card" aria-label="<?= e($l['license_small'] ?? '') ?>">
          <div class="license-head"><span class="license-seal" aria-hidden="true">✓</span><div><small><?= e($l['license_small'] ?? '') ?></small><strong><?= e($l['license_code'] ?? '') ?></strong></div></div>
          <dl class="license-facts">
<?php foreach ($l['facts'] ?? [] as $f): ?>
            <div><dt><?= e($f['term'] ?? '') ?></dt><dd><?= e($f['value'] ?? '') ?></dd></div>
<?php endforeach; ?>
          </dl>
          <a class="btn btn-primary" href="<?= e($l['verify_url'] ?? '#') ?>"<?= link_attrs($l['verify_url'] ?? '') ?>><?= e($l['verify_label'] ?? '') ?> <span>→</span></a>
        </article>

        <div class="legal-copy">
<?php foreach ($l['blocks'] ?? [] as $b): ?>
          <article class="legal-block" id="<?= e($b['id'] ?? '') ?>">
            <h3><span>◆</span> <?= e($b['title'] ?? '') ?></h3>
            <?= $b['body'] ?? '' ?>

          </article>
<?php endforeach; ?>
        </div>
      </div>

      <aside class="responsible-detail" id="responsible" aria-labelledby="responsible-title">
        <div class="age-badge" aria-hidden="true"><?= e($l['age_badge'] ?? '21+') ?></div>
        <div class="responsible-copy">
          <h3 id="responsible-title"><?= e($l['resp_title'] ?? '') ?></h3>
          <p><?= e($l['resp_p1'] ?? '') ?></p>
          <p><?= e($l['resp_p2'] ?? '') ?></p>
        </div>
        <div class="help-block">
          <p><?= e($l['help_intro'] ?? '') ?></p>
          <ul class="help-list">
<?php foreach ($l['help'] ?? [] as $x): $u = $x['href'] ?? '#'; ?>
            <li><span><?= e($x['label'] ?? '') ?></span><a href="<?= e($u) ?>"<?= link_attrs($u) ?>><b><?= e($x['text'] ?? '') ?></b></a></li>
<?php endforeach; ?>
          </ul>
        </div>
      </aside>
    </section>
  </main>
<?php
render_footer('');
render_end();
