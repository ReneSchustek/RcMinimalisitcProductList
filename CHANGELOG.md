# Changelog

## [1.1.0] - 2026-03-31

> **Deployment:** `php bin/console cache:clear`

### Geaendert
- Shopware 6.8 Kompatibilitaet hinzugefuegt
- Plugin-Label auf Kurzform vereinheitlicht
- Null-Check fuer navigation.active.customFields im Preis-Template

## [1.0.0] - 2026-03-28

> **Deployment:** `php bin/console plugin:install --activate RcMinimalisticProductList && php bin/console cache:clear`

### Hinzugefuegt
- Minimalistisches Produktlisten-Layout pro Kategorie aktivierbar
- Custom Field `rc_show_minimalistic_productlist` auf Kategorien
- Reduzierte Produktbox: nur Bild, Titel, Preis
- AJAX-Listing-Kompatibilitaet (Filterung, Sortierung)
