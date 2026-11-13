<?php
/** Admin panelin strukturu: qruplar → səhifələr → sahələr. Hər səhifə data/site.json-dakı bir açara bağlıdır. */
function F(string $n, string $l, string $t = 'text', array $o = []): array { return ['n' => $n, 'l' => $l, 't' => $t] + $o; }
function H(string $l): array { return ['n' => '', 'l' => $l, 't' => 'heading']; }
function L(string $n, string $l, string $title, array $f, array $o = []): array { return ['n' => $n, 'l' => $l, 't' => 'list', 'title' => $title, 'f' => $f] + $o; }

function policy_fields(bool $cookies): array {
    $f = [
        H('SEO'), F('seo_title', 'Səhifə başlığı'), F('seo_description', 'Təsvir (meta description)', 'textarea'),
        H('Giriş hissəsi'), F('h1_plain', 'Başlıq (adi hissə)', 'text', ['w' => 'half']), F('h1_em', 'Başlıq (vurğulanan hissə)', 'text', ['w' => 'half']),
        F('intro', 'Qısa izah', 'textarea'),
        F('updated', 'Son yenilənmə tarixi (YYYY-AA-GG)', 'text', ['w' => 'half']), F('operator', 'Operator', 'text', ['w' => 'half']),
    ];
    if ($cookies) {
        array_push($f,
            H('Kuki seçim paneli'),
            F('c_nec_title', '“Zəruri” başlığı', 'text', ['w' => 'half']), F('c_nec_tag', '“Zəruri” nişanı', 'text', ['w' => 'half']), F('c_nec_desc', '“Zəruri” izahı', 'textarea'),
            F('c_an_title', '“Analitik” başlığı'), F('c_an_desc', '“Analitik” izahı', 'textarea'),
            F('c_mk_title', '“Marketinq” başlığı'), F('c_mk_desc', '“Marketinq” izahı', 'textarea'),
            F('btn_save', 'Düymə: yadda saxla', 'text', ['w' => 'third']), F('btn_necessary', 'Düymə: yalnız zəruri', 'text', ['w' => 'third']), F('btn_all', 'Düymə: hamısı', 'text', ['w' => 'third'])
        );
    } else {
        $f[] = F('toc_title', 'Məzmun bloku başlığı', 'text', ['w' => 'half']);
    }
    $f[] = H('Bölmələr');
    $f[] = L('sections', 'Siyasət bölmələri', 'title', array_merge(
        [F('title', 'Bölmə başlığı'), F('id', 'Link açarı (ASCII, boşluqsuz)', 'text', ['w' => 'half'])],
        $cookies ? [] : [F('toc', 'Məzmundakı qısa ad', 'text', ['w' => 'half'])],
        [F('body', 'Mətn (HTML icazəlidir: p, ul, a, table)', 'html')]
    ), ['numbered' => true]);
    return $f;
}

return [
 'groups' => [
    'Əsas' => ['settings'],
    'Ana səhifə' => ['home', 'casinos', 'guides', 'games'],
    'Hüquqi və siyasətlər' => ['legal', 'terms', 'privacy', 'cookies'],
 ],
 'pages' => [
  'settings' => ['title' => 'Sayt parametrləri', 'icon' => '⚙', 'desc' => 'Loqo, naviqasiya və footer bütün səhifələrdə eynidir.', 'stat' => ['nav', 'menyu keçidi'], 'fields' => [
    H('Brend'), F('brand_a', 'Ad (1-ci hissə)', 'text', ['w' => 'third']), F('brand_b', 'Ad (rəngli hissə)', 'text', ['w' => 'third']), F('brand_symbol', 'Loqo simvolu', 'text', ['w' => 'third']),
    F('theme_color', 'Brauzer rəngi (#hex)', 'text', ['w' => 'half']), F('email', 'Əlaqə e-poçtu', 'text', ['w' => 'half']),
    H('Yuxarı menyu'),
    L('nav', 'Menyu keçidləri', 'label', [F('label', 'Ad', 'text', ['w' => 'third']), F('href', 'Ünvan', 'text', ['w' => 'third']), F('icon', 'İkon (ixtiyari)', 'text', ['w' => 'third'])]),
    F('cta_label', 'Yuxarı düymə mətni', 'text', ['w' => 'half']), F('cta_href', 'Yuxarı düymə ünvanı', 'text', ['w' => 'half']),
    H('Footer'),
    F('footer_tagline', 'Şüar'),
    L('footer_links', 'Footer keçidləri', 'label', [F('label', 'Ad', 'text', ['w' => 'half']), F('href', 'Ünvan', 'text', ['w' => 'half'])]),
    F('footer_small', 'Alt qeyd (müəllif hüququ)', 'textarea'),
  ]],
  'home' => ['title' => 'Hero və haqqımızda', 'icon' => '◆', 'desc' => 'Ana səhifənin yuxarı hissəsi, üstünlüklər zolağı və “haqqımızda” bloku.', 'stat' => ['trust', 'üstünlük'], 'fields' => [
    H('SEO'), F('seo_title', 'Səhifə başlığı'), F('seo_description', 'Təsvir', 'textarea'),
    H('Hero'), F('h1_a', 'Başlıq (1-ci sətir)', 'text', ['w' => 'half']), F('h1_b', 'Başlıq (qızılı sətir)', 'text', ['w' => 'half']),
    F('subtitle', 'Alt mətn (sətir sonu qəbul olunur)', 'textarea'),
    F('cta_label', 'Düymə mətni', 'text', ['w' => 'half']), F('cta_href', 'Düymə ünvanı', 'text', ['w' => 'half']),
    F('badge', 'Müstəqillik nişanı', 'textarea', ['w' => 'half']), F('hero_image', 'Şəkil faylı', 'text', ['w' => 'half']),
    H('Üstünlüklər zolağı'),
    L('trust', 'Üstünlüklər', 'text', [F('icon', 'İkon', 'text', ['w' => 'third']), F('text', 'Mətn', 'textarea', ['w' => 'twothird'])], ['max' => 4]),
    H('Haqqımızda'), F('about_eyebrow', 'Üst yazı'), F('about_t1', 'Başlıq (1-ci sətir)', 'text', ['w' => 'half']), F('about_t2', 'Başlıq (qızılı sətir)', 'text', ['w' => 'half']), F('about_text', 'Mətn', 'textarea'),
  ]],
  'casinos' => ['title' => 'Kazinolar', 'icon' => '♠', 'desc' => 'Reytinq kartları sıra ilə #1, #2… nömrələnir. Passiv kazino saytda görünmür.', 'stat' => ['items', 'kazino'], 'fields' => [
    H('Bölmə'), F('heading', 'Başlıq', 'text', ['w' => 'third']), F('link_label', 'Keçid mətni', 'text', ['w' => 'third']), F('link_href', 'Keçid ünvanı', 'text', ['w' => 'third']),
    F('play_label', 'Kart düyməsi', 'text', ['w' => 'third']), F('teaser_label', 'Son kart mətni', 'textarea', ['w' => 'third']), F('teaser_href', 'Son kart ünvanı', 'text', ['w' => 'third']),
    H('Kazino siyahısı (sürüşdürərək sıranı dəyişin)'),
    L('items', 'Kazinolar', 'name', [
        F('name', 'Ad', 'text', ['w' => 'half']), F('rating', 'Reytinq (0–5)', 'number', ['w' => 'quarter']), F('active', 'Saytda göstər', 'check', ['w' => 'quarter']),
        F('url', 'Tərəfdaş keçidi', 'text'),
        F('logo_class', 'Loqo üslubu', 'select', ['w' => 'half', 'opts' => ['plain' => 'Sadə mətn', 'bet365' => 'bet365', 'eight' => '888', 'leo' => 'LeoVegas', 'william' => 'William Hill']]),
        F('logo_html', 'Loqo HTML (boşdursa ad yazılır)', 'html', ['w' => 'half', 'rows' => 3]),
    ], ['def' => ['active' => true, 'rating' => 4.5, 'url' => '#contact', 'logo_class' => 'plain']]),
    H('Bonus banneri'), F('bonus_pre', 'Üst yazı', 'text', ['w' => 'quarter']), F('bonus_amount', 'Məbləğ', 'text', ['w' => 'quarter']), F('bonus_label', 'Ad', 'text', ['w' => 'quarter']), F('bonus_btn', 'Düymə', 'text', ['w' => 'quarter']),
    F('bonus_href', 'Düymə ünvanı'),
  ]],
  'guides' => ['title' => 'Bələdçilər', 'icon' => '▣', 'desc' => 'Ana səhifədəki “Latest Guides” paneli (ən çox 6 kart).', 'stat' => ['items', 'bələdçi'], 'fields' => [
    F('heading', 'Panel başlığı', 'text', ['w' => 'third']), F('icon', 'İkon', 'text', ['w' => 'third']), F('link_label', 'Keçid mətni', 'text', ['w' => 'third']), F('link_href', 'Keçid ünvanı'),
    L('items', 'Bələdçi kartları', 'title', [F('title', 'Başlıq'), F('label', 'Kateqoriya yazısı', 'text', ['w' => 'half']), F('href', 'Ünvan', 'text', ['w' => 'half'])], ['max' => 6]),
  ]],
  'games' => ['title' => 'Oyunlar', 'icon' => '♛', 'desc' => 'Ana səhifədəki “Popular Games” paneli (ən çox 6 kart).', 'stat' => ['items', 'oyun'], 'fields' => [
    F('heading', 'Panel başlığı', 'text', ['w' => 'third']), F('icon', 'İkon', 'text', ['w' => 'third']), F('link_label', 'Keçid mətni', 'text', ['w' => 'third']), F('link_href', 'Keçid ünvanı'),
    L('items', 'Oyun kartları', 'name', [F('name', 'Ad', 'text', ['w' => 'third']), F('symbol', 'Simvol', 'text', ['w' => 'third']), F('href', 'Ünvan', 'text', ['w' => 'third'])], ['max' => 6]),
  ]],
  'legal' => ['title' => 'Hüquqi məlumat', 'icon' => '⚖', 'desc' => 'Lisenziya kartı, hüquqi bloklar və məsuliyyətli oyun yardım xətləri.', 'stat' => ['blocks', 'hüquqi blok'], 'fields' => [
    H('Bölmə başlığı'), F('heading', 'Başlıq', 'text', ['w' => 'half']), F('link_label', 'Keçid mətni', 'text', ['w' => 'half']),
    H('Lisenziya kartı'), F('license_small', 'Kiçik yazı', 'text', ['w' => 'half']), F('license_code', 'Lisenziya kodu', 'text', ['w' => 'half']),
    L('facts', 'Məlumat sətirləri', 'term', [F('term', 'Ad', 'text', ['w' => 'half']), F('value', 'Dəyər', 'text', ['w' => 'half'])]),
    F('verify_label', 'Yoxlama düyməsi', 'text', ['w' => 'half']), F('verify_url', 'Yoxlama ünvanı', 'text', ['w' => 'half']),
    H('Hüquqi bloklar'),
    L('blocks', 'Bloklar', 'title', [F('title', 'Başlıq', 'text', ['w' => 'half']), F('id', 'Link açarı', 'text', ['w' => 'half']), F('body', 'Mətn (HTML)', 'html')]),
    H('Məsuliyyətli oyun'), F('age_badge', 'Yaş nişanı', 'text', ['w' => 'quarter']), F('resp_title', 'Başlıq', 'text', ['w' => 'threeq']),
    F('resp_p1', '1-ci abzas', 'textarea'), F('resp_p2', '2-ci abzas', 'textarea'), F('help_intro', 'Yardım xətti giriş cümləsi'),
    L('help', 'Yardım xətləri', 'label', [F('label', 'Təsvir', 'text', ['w' => 'third']), F('text', 'Göstərilən mətn', 'text', ['w' => 'third']), F('href', 'Ünvan (tel:, https://)', 'text', ['w' => 'third'])]),
    H('Kiçik “Play Responsibly” paneli'), F('panel_title', 'Başlıq', 'text', ['w' => 'half']), F('panel_text', 'Mətn', 'text', ['w' => 'half']),
  ]],
  'terms' => ['title' => 'İstifadə Şərtləri', 'icon' => '§', 'desc' => 'terms.php — bölmələr nömrələnir və məzmun avtomatik qurulur.', 'stat' => ['sections', 'bölmə'], 'fields' => policy_fields(false)],
  'privacy' => ['title' => 'Məxfilik Siyasəti', 'icon' => '🔒', 'desc' => 'privacy.php — bölmələr nömrələnir və məzmun avtomatik qurulur.', 'stat' => ['sections', 'bölmə'], 'fields' => policy_fields(false)],
  'cookies' => ['title' => 'Kuki Parametrləri', 'icon' => '◉', 'desc' => 'cookies.php — seçim paneli və kuki siyasəti.', 'stat' => ['sections', 'bölmə'], 'fields' => policy_fields(true)],
 ],
];
