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
            $this->_locales = $this->_guessLocales();
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
	abstract protected function _guessLocales();
    
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
        Translate a string

        Parameters
            string $name
			array $data

        Return
            string
    */
    public function translate($name, array $data = array()) {
        // Prepare data
        $available_translations = array_keys($this->_translations);
        // Guess the locale to use
        foreach($this->_locales as $locale) {
            if($lc = locale_lookup($available_translations, $locale)) {
                // Translate the provided string
				$translation = $this->_translations[$lc][$name];
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
            }
        }
        throw new \Exception("There's no translation for the '$name' string");
    }
}
