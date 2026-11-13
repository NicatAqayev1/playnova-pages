<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/bootstrap.php';

session_name('pn_admin');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

function csrf_ok(): bool { return hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? '')); }
function flash(string $type, string $msg): void { $_SESSION['flash'] = [$type, $msg]; }

/* ---------- Giriş / hesab ---------- */
function admin_account(): array {
    if (!is_file(ADMIN_FILE)) {
        $a = ['user' => 'admin', 'hash' => password_hash('admin123', PASSWORD_DEFAULT), 'must_change' => true];
        file_put_contents(ADMIN_FILE, json_encode($a, JSON_PRETTY_PRINT), LOCK_EX);
        return $a;
    }
    return json_decode((string)file_get_contents(ADMIN_FILE), true) ?: [];
}
function is_logged(): bool { return !empty($_SESSION['uid']); }

/* ---------- Form yaradıcısı ---------- */
function attr_w(array $f): string { return 'fld w-' . ($f['w'] ?? 'full'); }

function render_field(string $name, array $f, $val): string {
    $t = $f['t']; $l = e($f['l']);
    switch ($t) {
        case 'heading': return '<h4 class="subhead">' . $l . '</h4>';
        case 'check':
            return '<div class="' . attr_w($f) . '"><label class="check"><input type="hidden" name="' . e($name) . '" value="0"><input type="checkbox" name="' . e($name) . '" value="1"' . ($val ? ' checked' : '') . '><span>' . $l . '</span></label></div>';
        case 'textarea': case 'html':
            $rows = $f['rows'] ?? ($t === 'html' ? 9 : 3);
            return '<label class="' . attr_w($f) . '"><span>' . $l . '</span><textarea name="' . e($name) . '" rows="' . $rows . '" class="' . ($t === 'html' ? 'code' : '') . '"' . (($f['title_field'] ?? false) ? ' data-title' : '') . '>' . e($val) . '</textarea></label>';
        case 'select':
            $o = ''; foreach ($f['opts'] as $k => $lab) $o .= '<option value="' . e($k) . '"' . ((string)$val === (string)$k ? ' selected' : '') . '>' . e($lab) . '</option>';
            return '<label class="' . attr_w($f) . '"><span>' . $l . '</span><select name="' . e($name) . '">' . $o . '</select></label>';
        case 'list': return render_list($name, $f, is_array($val) ? $val : []);
        default:
            $type = $t === 'number' ? 'number" step="0.1" min="0" max="5' : 'text';
            return '<label class="' . attr_w($f) . '"><span>' . $l . '</span><input type="' . $type . '" name="' . e($name) . '" value="' . e($val) . '"' . (($f['title_field'] ?? false) ? ' data-title' : '') . '></label>';
    }
}

function render_item(string $base, array $list, array $item, int $i, bool $tpl = false): string {
    $title = (string)($item[$list['title']] ?? '');
    $h = '<details class="item"' . ($tpl ? ' open' : '') . '><summary><span class="grip">⠿</span><span class="item-no">' . (!empty($list['numbered']) ? '' : '') . '</span><span class="item-title">' . ($title !== '' ? e($title) : 'Yeni element') . '</span>'
       . '<span class="item-tools"><button type="button" class="ib" data-act="up" title="Yuxarı">↑</button><button type="button" class="ib" data-act="down" title="Aşağı">↓</button><button type="button" class="ib danger" data-act="del" title="Sil">✕</button></span></summary><div class="grid">';
    foreach ($list['f'] as $sf) {
        if ($sf['n'] === $list['title']) $sf['title_field'] = true;
        $h .= render_field($base . '[' . $i . '][' . $sf['n'] . ']', $sf, $item[$sf['n']] ?? ($sf['t'] === 'check' ? false : ''));
    }
    return $h . '</div></details>';
}

function render_list(string $name, array $f, array $items): string {
    $h = '<div class="list-wrap fld w-full" data-max="' . (int)($f['max'] ?? 0) . '"><div class="list-head"><span>' . e($f['l']) . ' <em class="count"></em></span><button type="button" class="btn-add">+ Əlavə et</button></div>'
       . '<div class="list" data-name="' . e($name) . '">';
    foreach (array_values($items) as $i => $it) $h .= render_item($name, $f, $it, $i);
    $h .= '</div><template>' . render_item($name, $f, $f['def'] ?? [], 9999, true) . '</template></div>';
    return $h;
}

function render_form(array $fields, string $key, array $data): string {
    $h = '<div class="grid">';
    foreach ($fields as $f) $h .= render_field('d[' . $key . '][' . $f['n'] . ']', $f, $data[$f['n']] ?? ($f['t'] === 'check' ? false : ''));
    return $h . '</div>';
}

/* ---------- Gələn məlumatın tiplərə çevrilməsi ---------- */
function coerce(array $fields, $in): array {
    $in = is_array($in) ? $in : [];
    $out = [];
    foreach ($fields as $f) {
        if ($f['t'] === 'heading') continue;
        $k = $f['n']; $v = $in[$k] ?? null;
        switch ($f['t']) {
            case 'check': $out[$k] = ($v === '1'); break;
            case 'number': $out[$k] = is_numeric($v) ? max(0, min(5, round((float)$v, 1))) : 0; break;
            case 'select': $out[$k] = array_key_exists((string)$v, $f['opts']) ? (string)$v : array_key_first($f['opts']); break;
            case 'list':
                $items = []; $raw = is_array($v) ? $v : []; ksort($raw);
                foreach ($raw as $it) {
                    $c = coerce($f['f'], $it);
                    $empty = true;
                    foreach ($f['f'] as $sf) if (in_array($sf['t'], ['text', 'textarea', 'html'], true) && ($c[$sf['n']] ?? '') !== '') { $empty = false; break; }
                    if (!$empty) $items[] = $c;
                }
                if (!empty($f['max'])) $items = array_slice($items, 0, (int)$f['max']);
                $out[$k] = $items; break;
            default: $out[$k] = trim(str_replace("\r\n", "\n", (string)($v ?? '')));
        }
    }
    return $out;
}
