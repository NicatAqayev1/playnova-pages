<?php
require __DIR__ . '/lib.php';
$schema = require __DIR__ . '/schema.php';
$pages = $schema['pages'];
$acc = admin_account();
$self = 'index.php';

/* ---------- Çıxış ---------- */
if (isset($_GET['logout'])) { session_destroy(); header('Location: ' . $self); exit; }

/* ---------- Giriş ---------- */
if (!is_logged()) {
    $err = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
        if (($_SESSION['lock'] ?? 0) > time()) {
            $err = 'Çox sayda uğursuz cəhd. Bir dəqiqə sonra yenidən yoxlayın.';
        } elseif (hash_equals((string)$acc['user'], (string)($_POST['user'] ?? '')) && password_verify((string)($_POST['pass'] ?? ''), (string)$acc['hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = 1; $_SESSION['fails'] = 0;
            header('Location: ' . $self); exit;
        } else {
            $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1;
            if ($_SESSION['fails'] >= 5) { $_SESSION['lock'] = time() + 60; $_SESSION['fails'] = 0; }
            $err = 'İstifadəçi adı və ya şifrə yanlışdır.';
            usleep(600000);
        }
    }
    ?><!doctype html><html lang="az"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Giriş — <?= e(brand()) ?> Admin</title>
<link rel="stylesheet" href="admin.css"></head><body class="login-body">
<form class="login" method="post" autocomplete="on"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
<div class="login-brand"><span class="mark"><?= e((site()['settings']['brand_symbol'] ?? '♠')) ?></span><strong><?= e(brand()) ?></strong> <small>Admin</small></div>
<?php if ($err): ?><p class="msg err"><?= e($err) ?></p><?php endif; ?>
<label class="fld"><span>İstifadəçi adı</span><input name="user" required autofocus autocomplete="username"></label>
<label class="fld"><span>Şifrə</span><input type="password" name="pass" required autocomplete="current-password"></label>
<button class="btn-main" type="submit">Daxil ol</button></form></body></html><?php
    exit;
}

/* ---------- Yedək yüklə ---------- */
if (isset($_GET['download'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="playnova-data-' . date('Y-m-d') . '.json"');
    readfile(DATA_FILE); exit;
}

/* ---------- POST əməliyyatları ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    $back = $self . (isset($_POST['p']) && preg_match('/^[a-z]+$/', $_POST['p']) ? '?p=' . $_POST['p'] : '');
    if (!csrf_ok()) { flash('err', 'Sessiya vaxtı bitib. Səhifəni yeniləyib yenidən cəhd edin.'); header('Location: ' . $back); exit; }

    if ($act === 'save' && isset($pages[$_POST['p'] ?? ''])) {
        $key = $_POST['p'];
        $data = site();
        $data[$key] = coerce($pages[$key]['fields'], $_POST['d'][$key] ?? []);
        $ok = save_site($data);
        flash($ok ? 'ok' : 'err', $ok ? '“' . $pages[$key]['title'] . '” yadda saxlanıldı.' : 'Yazmaq mümkün olmadı: data/ qovluğunun yazma icazəsini yoxlayın.');
    } elseif ($act === 'password') {
        $new = (string)($_POST['new'] ?? '');
        if (!password_verify((string)($_POST['current'] ?? ''), (string)$acc['hash'])) flash('err', 'Cari şifrə yanlışdır.');
        elseif (mb_strlen($new) < 8) flash('err', 'Yeni şifrə ən azı 8 simvol olmalıdır.');
        elseif ($new !== ($_POST['confirm'] ?? '')) flash('err', 'Yeni şifrələr üst-üstə düşmür.');
        else {
            $user = trim((string)($_POST['user'] ?? '')) ?: $acc['user'];
            file_put_contents(ADMIN_FILE, json_encode(['user' => $user, 'hash' => password_hash($new, PASSWORD_DEFAULT), 'must_change' => false], JSON_PRETTY_PRINT), LOCK_EX);
            flash('ok', 'Giriş məlumatları yeniləndi.');
        }
    } elseif ($act === 'import') {
        $j = isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']) ? json_decode((string)file_get_contents($_FILES['file']['tmp_name']), true) : null;
        $need = array_keys($pages);
        if (!is_array($j) || array_diff($need, array_keys($j))) flash('err', 'Fayl uyğun deyil: bütün qrupları (' . implode(', ', $need) . ') ehtiva etməlidir.');
        else { $ok = save_site($j); flash($ok ? 'ok' : 'err', $ok ? 'Məlumatlar bərpa edildi.' : 'Yazmaq mümkün olmadı.'); }
    }
    header('Location: ' . $back); exit;
}

/* ---------- Görünüş ---------- */
$p = $_GET['p'] ?? 'dashboard';
$valid = $p === 'dashboard' || $p === 'account' || $p === 'backup' || isset($pages[$p]);
if (!$valid) $p = 'dashboard';
$data = site();
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$title = $p === 'dashboard' ? 'İcmal' : ($p === 'account' ? 'Hesab' : ($p === 'backup' ? 'Yedək' : $pages[$p]['title']));
$nav = function (string $key, string $icon, string $label) use ($p, $self) {
    return '<a class="nav-i' . ($p === $key ? ' on' : '') . '" href="' . $self . '?p=' . $key . '"><i>' . $icon . '</i>' . e($label) . '</a>';
};
?><!doctype html>
<html lang="az"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<title><?= e($title) ?> — <?= e(brand()) ?> Admin</title><link rel="stylesheet" href="admin.css"></head>
<body>
<aside class="side">
  <div class="side-brand"><span class="mark"><?= e($data['settings']['brand_symbol'] ?? '♠') ?></span><div><strong><?= e(brand()) ?></strong><small>İdarəetmə paneli</small></div></div>
  <nav>
    <?= $nav('dashboard', '▦', 'İcmal') ?>
<?php foreach ($schema['groups'] as $gname => $keys): ?>
    <p class="grp"><?= e($gname) ?></p>
<?php foreach ($keys as $k) echo '    ' . $nav($k, $pages[$k]['icon'], $pages[$k]['title']) . "\n"; ?>
<?php endforeach; ?>
    <p class="grp">Sistem</p>
    <?= $nav('account', '⚿', 'Hesab və şifrə') ?>
    <?= $nav('backup', '⇅', 'Yedək və bərpa') ?>
  </nav>
  <div class="side-foot"><a href="../index.php" target="_blank" rel="noopener">Saytı aç ↗</a><a href="?logout=1">Çıxış</a></div>
</aside>

<main class="main">
<header class="top"><h1><?= e($title) ?></h1><?php if (isset($pages[$p])): ?><p><?= e($pages[$p]['desc']) ?></p><?php endif; ?></header>
<?php if ($flash): ?><p class="msg <?= e($flash[0]) ?>" role="status"><?= e($flash[1]) ?></p><?php endif; ?>

<?php if ($p === 'dashboard'): ?>
  <?php if (!empty($acc['must_change'])): ?><p class="msg err">Standart şifrə (<b>admin / admin123</b>) hələ dəyişdirilməyib. <a href="?p=account">İndi dəyişin →</a></p><?php endif; ?>
  <div class="cards">
<?php foreach ($schema['groups'] as $gname => $keys): ?>
    <section class="card"><h2><?= e($gname) ?></h2>
<?php foreach ($keys as $k): $pg = $pages[$k]; $cnt = count($data[$k][$pg['stat'][0]] ?? []);
      if ($k === 'casinos') $cnt = count(array_filter($data[$k]['items'] ?? [], fn($x) => !empty($x['active']))); ?>
      <a class="row" href="?p=<?= $k ?>"><i><?= e($pg['icon']) ?></i><span><b><?= e($pg['title']) ?></b><small><?= $cnt ?> <?= e($pg['stat'][1]) ?></small></span><em>→</em></a>
<?php endforeach; ?>
    </section>
<?php endforeach; ?>
    <section class="card"><h2>Vəziyyət</h2>
      <p class="kv"><span>Son dəyişiklik</span><b><?= is_file(DATA_FILE) ? e(date('d.m.Y H:i', filemtime(DATA_FILE))) : '—' ?></b></p>
      <p class="kv"><span>Məlumat faylı</span><b><?= is_writable(DATA_FILE) ? 'Yazıla bilir' : 'Yazma icazəsi yoxdur' ?></b></p>
      <p class="kv"><span>Avtomatik yedək</span><b><?= is_file(ROOT . '/data/site.backup.json') ? 'Mövcuddur' : 'Hələ yoxdur' ?></b></p>
    </section>
  </div>

<?php elseif ($p === 'account'): ?>
  <form class="card narrow" method="post"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><input type="hidden" name="action" value="password"><input type="hidden" name="p" value="account">
    <div class="grid">
      <label class="fld w-full"><span>İstifadəçi adı</span><input name="user" value="<?= e($acc['user']) ?>" autocomplete="username"></label>
      <label class="fld w-full"><span>Cari şifrə</span><input type="password" name="current" required autocomplete="current-password"></label>
      <label class="fld w-half"><span>Yeni şifrə (min. 8 simvol)</span><input type="password" name="new" required minlength="8" autocomplete="new-password"></label>
      <label class="fld w-half"><span>Yeni şifrə təkrar</span><input type="password" name="confirm" required minlength="8" autocomplete="new-password"></label>
    </div><button class="btn-main" type="submit">Şifrəni yenilə</button></form>

<?php elseif ($p === 'backup'): ?>
  <div class="cards two">
    <section class="card"><h2>Yedəyi yüklə</h2><p class="hint">Bütün sayt məlumatı bir JSON faylında. Böyük dəyişikliklərdən əvvəl yükləyin.</p><a class="btn-main" href="?download=1">data.json yüklə</a></section>
    <form class="card" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><input type="hidden" name="action" value="import"><input type="hidden" name="p" value="backup">
      <h2>Yedəkdən bərpa et</h2><p class="hint">Cari məlumatın üzərinə yazılır. Əvvəlki versiya <code>data/site.backup.json</code> faylında qalır.</p>
      <input type="file" name="file" accept="application/json,.json" required><button class="btn-main" type="submit" data-confirm="Cari məlumatın üzərinə yazılsın?">Bərpa et</button></form>
  </div>

<?php else: $pg = $pages[$p]; ?>
  <form method="post" id="editor" class="editor"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="p" value="<?= e($p) ?>">
    <div class="card"><?= render_form($pg['fields'], $p, $data[$p] ?? []) ?></div>
    <div class="savebar"><span class="dirty" hidden>Yadda saxlanmamış dəyişikliklər var</span><a class="btn-ghost" href="../<?= in_array($p, ['terms','privacy','cookies']) ? $p . '.php' : 'index.php' ?>" target="_blank" rel="noopener">Saytda bax ↗</a><button class="btn-main" type="submit">Yadda saxla</button></div>
  </form>
<?php endif; ?>
</main>
<script src="admin.js"></script>
</body></html>
