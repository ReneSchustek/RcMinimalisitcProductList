# RcMinimalisticProductList

Shopware 6 Plugin — schaltet pro Kategorie ein minimalistisches Produktlisten-Layout ein.

---

## Was das Plugin macht

Shopware-Produktlistings haben ein festes Layout, das sich nur über Theme-Änderungen umstellen lässt — und dann global für alle Kategorien gilt. Manchmal soll jedoch nur eine einzelne Kategorie (z. B. eine Sonderseite oder ein spezieller Sortiment-Bereich) ein abweichendes, reduziertes Layout bekommen.

Dieses Plugin liest per `ProductListingResultEvent` das Custom Field `rc_show_minimalistic_productlist` der aktuellen Navigationskategorie und hängt das Ergebnis als Extension `rcMinimalisticLayout` am Listing-Result an. Das Twig-Template kann diese Extension auswerten und bei `active = true` das minimalistisch gestaltete Layout laden.

Funktioniert sowohl beim initialen Seitenaufruf als auch beim AJAX-Reload bei Filterung oder Sortierung.

---

## Voraussetzungen

- Shopware 6.7 oder 6.8
- PHP 8.2+

---

## Installation

```bash
php bin/console plugin:refresh
php bin/console plugin:install --activate RcMinimalisticProductList
php bin/console cache:clear
```

---

## Konfiguration

Im Admin unter **Kategorien → [Kategorie] → Individuelle Felder**:

| Feld | Beschreibung |
|------|-------------|
| Minimalistic Productlist anzeigen | Aktiviert das alternative Listing-Layout für diese Kategorie |

---

## Update

```bash
php bin/console plugin:refresh
php bin/console plugin:update RcMinimalisticProductList
php bin/console cache:clear
```

---

## Entwicklung

```bash
composer install
composer quality   # cs-check + phpstan + test
```

---

Entwickelt von [Ruhrcoder](https://ruhrcoder.de)
