<?php

namespace Translator;

use Chernozem\Container as Chernozem;

/*
    Translation class
*/
abstract class AbstractTranslator extends Chernozem {

    /*
        Constructor

        Parameters
            string $default
			boolean $force
    */
    public function __construct($default) {
        // Guess the client locales
        $client_locales = $this->_guessClientLocales();
        // Add default locale
        $client_locales[] = $this->normalizeLocale((string)$default);
        // Set locales for the whole environment
        setlocale(LC_ALL, $client_locales);
        // Define normalize locales function
        $normalizeLocales = function($locales) {
            foreach($locales as &$locale) {
                $locale = $this->normalizeLocale($locale);
            }
            return $locales;
        };
        // Define client locales
        $this['client_locales'] = [];
        $this->hint('client_locales', 'array');
        $this->setter('client_locales', $normalizeLocales);
        $this['client_locales'] = $client_locales;
        // Define delimiters
        $this['delimiters'] = ['start' => '{', 'end' => '}'];
        $this->hint('delimiters', 'array');
        $this->setter('delimiters', function($delimiters) {
            if(!isset($delimiters['start']) || !isset($delimiters['end'])) {
                throw new \Exception("the 'start' and 'end' delimiters must be defined");
            }
            return $delimiters;
        });
        // Define translations
        $this['translations'] = [];
    }

    /*
		Try to guess the client's locales

		Return
			array
	*/
    abstract protected function _guessClientLocales();

    /*
		Force the provided locale

        Parameters
            string $locale

        Return
            Translator
	*/
    public function setLocale($locale) {
        $this['client_locales'] = [(string)$locale];
        return $this;
    }

    /*
		Guess the locale to use

        Return
            string
	*/
    public function getLocale() {
        // Search and return the best locale to use
        foreach($this['client_locales'] as $locale) {
            if(function_exists('locale_lookup')) {
                $lc = locale_lookup(array_keys($this['translations']), $locale);
            }
            else {
                foreach(array_keys($this['translations']) as $available_locale) {
                    if(strpos($locale, $available_locale) === 0) {
                        $lc = $available_locale;
                    }
                }
            }
            if($lc) {
                return $lc;
            }
        }
        // Return the default locale
        return $this['client_locales'][count($this['client_locales']) - 1];
    }

    /*
        Load translations

        Parameters
            string $locale
            array|object $translations

        Return
            Translator
    */
    public function load($locale, $translations) {
        $t = $this['translations'];
        if(isset($t[$locale])) {
            $t[$locale] = array_merge($t[$locale], (array)$translations);
        }
        else {
            $t[$locale] = (array)$translations;
        }
        $this['translations'] = $t;
        return $this;
    }

    /*
        Translate a string

        Parameters
            string $id
			array $data

        Return
            string
    */
    public function translate($id, array $data = []) {
        // Guess the locale to use
        $locale = $this->getLocale();
        // Translate the provided string
        if(isset($this['translations'][$locale]) && isset($this['translations'][$locale][$id])) {
            // Compute search values
            $searches = [];
            $replacements = [];
            foreach($data as $name => $value) {
                $searches[] = $this['delimiters']['start'].$name.$this['delimiters']['end'];
                $replacements[] = $value;
            }
            // Replace some values
            $translation = str_replace($searches, $replacements, $this['translations'][$locale][$id]);
            // Return the final translation
            return $translation;
        }
        else {
            throw new \Exception("There's no translation for the '$id' string");
        }
    }

    /*
        Normalize the provided locale

        Parameters
            string $locale

        Return
            string
    */
    public function normalizeLocale($locale) {
        $locale = str_replace('_', '-', (string)$locale);
        if(function_exists('locale_parse')) {
            $pieces = locale_parse($locale);
        }
        else {
            $pieces = explode('-', $locale);
            $pieces['language'] = $pieces[0];
            if(isset($pieces[1])) {
                $pieces['region'] = $pieces[1];
            }
        }
        if(isset($pieces['region'])) {
            return $pieces['language'].'_'.$pieces['region'];
        }
        else {
            return $pieces['language'];
        }
    }
}
