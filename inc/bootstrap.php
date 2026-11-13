<?php
declare(strict_types=1);

const ROOT = __DIR__ . '/..';
const DATA_FILE = ROOT . '/data/site.json';
const ADMIN_FILE = ROOT . '/data/admin.json';

mb_internal_encoding('UTF-8');

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function nl($v): string { return nl2br(e($v), false); }

/** Bütün sayt məlumatı (data/site.json). */
function site(): array {
    static $d = null;
    if ($d === null) {
        $d = is_file(DATA_FILE) ? (json_decode((string)file_get_contents(DATA_FILE), true) ?: []) : [];
    }
    return $d;
}

function save_site(array $d): bool {
    $json = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json === false) return false;
    if (is_file(DATA_FILE)) @copy(DATA_FILE, ROOT . '/data/site.backup.json');
    $tmp = DATA_FILE . '.tmp';
    return file_put_contents($tmp, $json, LOCK_EX) !== false && rename($tmp, DATA_FILE);
}

function brand(): string {
    $s = site()['settings'] ?? [];
    return ($s['brand_a'] ?? 'Play') . ($s['brand_b'] ?? 'Nova');
}

function az_date(string $iso): string {
    $m = ['yanvar','fevral','mart','aprel','may','iyun','iyul','avqust','sentyabr','oktyabr','noyabr','dekabr'];
    $t = strtotime($iso);
    return $t ? ((int)date('j', $t) . ' ' . $m[(int)date('n', $t) - 1] . ' ' . date('Y', $t)) : $iso;
}

/** Xarici keçidlər yeni tabda açılır. */
function link_attrs(string $url): string {
    return preg_match('~^https?://~i', $url) ? ' target="_blank" rel="noopener noreferrer"' : '';
}
