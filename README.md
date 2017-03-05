Translator 2.0.3
================

Translator is a small I18N/L10N library, based on [Chernozem](https://github.com/pyrsmk/Chernozem) (we advise you to read its documentation). Here's the available options through Chernozem :

- client_locales : the client's locales
- delimiters : set the start/end delimiter for string replacement (default : `['start' => '{', 'end' => '}']`)
- translations : the loaded translations

Install
-------

Pick up the source or install it with [Composer](https://getcomposer.org/) :

```
composer require pyrsmk/translator
```

Basics
------

Translator is available for a website (`Translator\Http`) and a CLI application (`Translator\Cli`). For the next examples, we'll use `Translator\Http`.

First, we need to define the default locale at instantiation. This locale will be used when no client's locale is supported by your application.

```php
$translator = new Translator\Http('en_EN');
```

Translator will find what locale to use by comparing the client's locales and the defined available locales (based on the loaded translations)

If needed, you can force Translator to use a specific locale :

```php
$translator->setLocale('fr');
```

And you can retrieve what locale Translator will use for its translations with :

```php
$translator->getLocale();
```

Load your translations files
----------------------------

Translator doesn't implement adapters to load your files, it just expect the name of the locale and the translation strings. Then you can use any library you want to load your files or do it by hand. Here's how to do it with JSON :

```json
/* fr_FR.json */
{
	"cat": "chat",
	"dog": "chien",
	"turtle": "tortue"
}
```

```php
// Load the JSON file
$translator->load('fr_FR', json_decode(file_get_contents('fr_FR.json')));
```

Translate!
----------

Simply :

```php
// Prints 'chien'
echo $translator->translate('dog');
```

Translator can also replace strings in your translations :

```json
/* fr_FR.json */
{
	"hello": "Bonjour, M. {name}!"
}
```

```php
// Prints 'Bonjour, M. Philippe!'
echo $translator->translate('hello', [
	'name' => 'Philippe'
]);
```

Normalize a locale
------------------

If needed, you can normalize any locale you want with :

```php
// Prints 'sl_IT'
echo $translator->normalizeLocale('sl_Latn-IT_nedis');
```

License
-------

This library is released under the [MIT license](http://dreamysource.mit-license.org).
