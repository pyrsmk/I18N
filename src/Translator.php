<?php

/*
    Translation class
*/
abstract class Translator extends \Chernozem {
    
    /*
        array $_locales
        array $_translations
		string $start_delimiter
		string $end_delimiter
    */
    protected $_locales = array();
    protected $_translations = array();
	protected $start_delimiter = '{';
	protected $end_delimiter = '}';

    /*
        Constructor

        Parameters
            string $default
			boolean $force
    */
    public function __construct($default, $force = false) {
		// Guess the client's locale
        if(!$force) {
            $this->_locales = $this->_guessClientLocales();
        }
		// Add default locale
        $this->_locales[] = $this->normalizeLocale((string)$default);
        // Set locales for the environment
        setlocale(LC_ALL, $this->_locales);
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
	public function forceLocale($locale) {
		$this->_locales = array((string)$locale);
		return $this;
	}
    
    /*
        Normalize the provided locale

        Parameters
            string $locale

        Return
            string
    */
    public function normalizeLocale($locale) {
        $pieces = locale_parse(str_replace('_', '-', (string)$locale));
        if(isset($pieces['region'])) {
            return $pieces['language'].'_'.$pieces['region'];
        }
        else {
            return $pieces['language'];
        }
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
		if(isset($this->_translations[$locale])) {
			$this->_translations[$locale] = array_merge($this->_translations[$locale], (array)$translations);
		}
		else {
			$this->_translations[$locale] = (array)$translations;
		}
		return $this;
    }
	
	/*
		Guess the locale to use

        Parameters
            boolean $format : true to return a simple locale like 'fr' instead of 'fr_FR'

        Return
            string, null
	*/
	public function guessLocale($locales, $force = false) {
		foreach($this->_locales as $locale) {
            if($lc = locale_lookup($locales, $locale)) {
				if($force) {
					list($lc,) = explode('_', $lc);
				}
                return $lc;
            }
        }
		return $this->_locales[count($this->_locales) - 1];
	}

    /*
        Translate a string

        Parameters
            string $name
			array $data

        Return
            string
    */
    public function translate($name, array $data = array()) {
        // Guess the locale to use
		$locale = $this->guessLocale(array_keys($this->_translations));
		// Translate the provided string
		$translation = $this->_translations[$locale][$name];
		if($translation) {
			// Compute search values
			$searches = array();
			$replacements = array();
			foreach($data as $name => $value) {
				$searches[] = $this->start_delimiter.$name.$this->end_delimiter;
				$replacements[] = $value;
			}
			// Replace some values
			$translation = str_replace($searches, $replacements, $translation);
			// Return the final translation
			return $translation;
		}
		else {
        	throw new \Exception("There's no translation for the '$name' string");
		}
    }
}
