# PlayNova (PHP + admin panel)

## İşə salmaq
    cd playnova
    php -S localhost:8000
- Sayt: http://localhost:8000/
- Admin: http://localhost:8000/admin/  (ilk giriş: `admin` / `admin123` → dərhal "Hesab və şifrə" bölməsindən dəyişin)

Tələblər: PHP 7.4+. `data/` qovluğu PHP üçün yazıla bilən olmalıdır (admin.json və site.json orada saxlanır).

## Quruluş
- `data/site.json` — bütün məzmun qruplar üzrə (settings, home, casinos, guides, games, legal, terms, privacy, cookies)
- `index.php, terms.php, privacy.php, cookies.php` — səhifələr, məlumatı `site.json`-dan oxuyur
- `inc/` — ortaq header/footer/şablon
- `admin/schema.php` — admin formalarının təsviri: yeni sahə əlavə etmək üçün burada sətir əlavə etmək kifayətdir
- Hər yadda saxlamada əvvəlki versiya `data/site.backup.json`-a kopyalanır

## Təhlükəsizlik
- Apache üçün `data/.htaccess` hazırdır. Nginx-də `location /data/ { deny all; }` əlavə edin.
- Admin HTML sahələri (siyasət mətnləri) təmizlənmir — yalnız etibarlı adminlərə icazə verin, HTTPS istifadə edin.
# playnova-pages
