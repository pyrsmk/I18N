Translator 1.0.0
================

Translator is a small I18N/L10N solution with simplicity and flexibility in mind.

Install
-------

Pick up the source or install it with [Composer](https://getcomposer.org/) :

```json
composer require pyrsmk/translator
```

Define your default locale
--------------------------

First, we need to define the default locale of our website (often `en_EN`) :

``` php
$translator = new Translator\Http('en_EN');
```

If you want to force your default locale to be the only one of the site, set the second parameter to `true` :

```php
$translator = new Translator\Http('fr_FR', true);
```

Notes :

- there is also a `Translator\Cli` object for CLI environment
- registered locales are accessible via `$translator['locales']`

Load your translations files
----------------------------

Translator doesn't implement adapters to load your files, it just expect the name of the locale and the translation strings. Then you can use any library you want to load your files or do it by hand. Here's how to do it with JSON :

```json
/* fr_FR.json */
{
	"cat": "chat",
	"dog": "chien",
	"turtle": "tortue",
	"hello": "Bonjour, M. {name}!"
}
```

```php
// Load the JSON file
$translator->load('fr_FR', json_decode(file_get_contents('fr_FR.json')));
```

Notes :

- loaded translations are accessible via `$translator['translations']`

Translate!
----------

Simply :

```php
// Prints 'chien'
echo $translator->translate('dog');
```

Translator can also replace strings in your translations :

```php
	// Prints 'Bonjour, M. Philippe!'
echo $translator->translate('hello', array(
	'name' => 'Philippe'
));
```

Bonus : normalize a locale
--------------------------

If needed, you can normalize any locale you want with :

```php
// Prints 'sl_IT'
echo $translator->normalizeLocale('sl_Latn-IT_nedis');
```

License
-------

This library is released under the [MIT license](http://dreamysource.mit-license.org).
