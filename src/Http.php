<?php

namespace Translator;

/*
    Determine the client's locale on HTTP environment
*/
class Http extends AbstractTranslator {

    /*
		Try to guess the client's locales
		
		Return
			array
	*/
    protected function _guessClientLocales() {
        // Get the locales
        $locales = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        // Format the locales
        foreach($locales as &$locale) {
            if($pos = strpos($locale,';')) {
                $locale = substr($locale, 0, $pos);
            }
            $locale = $this->normalizeLocale($locale);
        }
        // Return the locales
        return $locales;
    }

}
