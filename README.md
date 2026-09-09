# Schilliger Claude

Entwicklungskopie des WordPress-Themes für [michaelschilliger.ch](https://michaelschilliger.ch/),
abgezweigt von [schilliger-theme](https://github.com/mschilliger/schilliger-theme) zur
Weiterentwicklung mit Claude.

## Deployment

Kein automatisiertes Deployment – die Website wird selbst gehostet. Zum
Veröffentlichen den Inhalt dieses Repos in `wp-content/themes/schilliger-claude/`
auf dem Server hochladen (z. B. per SFTP).

## Struktur

- `style.css` – Theme-Header (Name, Version, Beschreibung)
- `functions.php` – Theme-Setup, Hooks, Custom Post Types etc.
- `header.php`, `footer.php`, `index.php`, `page.php`, `single.php` – Basis-Templates
- `front-page.php`, `home.php` – Startseite
- `page-*.php`, `single-*.php`, `archive-*.php` – Templates für einzelne Seiten/Post-Types
- `assets/css`, `assets/js`, `assets/img` – Styles, Scripts, Bilder
