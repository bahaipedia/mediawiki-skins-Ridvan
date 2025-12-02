# Skin:Ridvan

Ridvan is a modern, responsive MediaWiki skin originally developed for [Bahaipedia.org](https://bahaipedia.org). It is a rewrite of Skin:Chameleon using modern standards and features an adaption of the Skin:Citizen search suggestions.

## Credits

* **Author:** Sarah Haslip
* **Search Module:** Adapted from [Skin:Citizen](https://www.mediawiki.org/wiki/Skin:Citizen) (Thanks to Alistair3149 & Octfx).

## Requirements

* MediaWiki 1.41+

## Installation

1.  Download the skin files and place them in the `skins/Ridvan` directory.
2.  Add the following line to your `LocalSettings.php`:

```php
wfLoadSkin( 'Ridvan' );
````

## Configuration
### Custom Search Suggestions

Ridvan includes a custom, fast "Typeahead" search suggestion module (czsearch) enabled by default.

If you are running **Wikibase** or simply prefer the default MediaWiki search behavior, you can disable the custom module by adding this to your `LocalSettings.php`:

```php
// Disable Ridvan's custom search (Reverts to standard MediaWiki suggestions)
// Recommended for Wikibase instances.
$wgSearchSuggestionsReplacement = false;
```

## License
GPL-3.0-or-later
