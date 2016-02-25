<?php

########################################################### Prepare

error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/vendor/autoload.php';

$minisuite = new MiniSuite('Translator');

########################################################### Tests

if(php_sapi_name() == 'apache2handler') {
	$translator = new Translator\Http('en_EN');
	echo '<pre>';
}
else {
	$translator = new Translator\Cli('en_EN');
}

$minisuite->expects('Build with default locale')
		  ->that($translator['locales'])
		  ->isDefined('fr_FR')
		  ->isDefined('en_EN');

$translator = new Translator\Cli('en_EN', true);

$minisuite->expects('Build with forced locale')
		  ->that($translator['locales'])
		  ->isTheSameAs(array('en_EN'));

$minisuite->expects('Normalizing locale')
		  ->that($translator->normalizeLocale('sl_Latn-IT_nedis'))
		  ->equals('sl_IT');

$translator = new Translator\Cli('en_EN');

foreach(lessdir('./langs/') as $file) {
	$file = './langs/'.$file;
	$translator->load(pathinfo($file, PATHINFO_FILENAME), json_decode(file_get_contents($file)));
}

$minisuite->expects('Loading translations')
		  ->that($translator['translations'])
		  ->isTheSameAs(array(
		  	'en' => array('test' => 'The black cat, {name}, is in the garden.'),
			'fr_FR' => array('test' => 'Le chat noir, {name}, est dans le jardin.')
		  ));

$minisuite->expects('Translate')
		  ->that($translator->translate('test'))
		  ->equals('Le chat noir, {name}, est dans le jardin.');

$minisuite->expects('Replacements')
		  ->that($translator->translate('test', array(
		  	'name' => 'Moustaches'
		  )))
		  ->equals('Le chat noir, Moustaches, est dans le jardin.');

$minisuite->expects('Guess locale : found')
		  ->that($translator->guessLocale(array('en_EN', 'fr_FR'), true))
		  ->equals('fr');

$minisuite->expects('Guess locale : default locale')
		  ->that($translator->guessLocale(array('en_US')))
		  ->isTheSameAs('en_EN');

$translator->forceLocale('en_EN');

$minisuite->expects('Force locale')
		  ->that($translator->translate('test'))
		  ->equals('The black cat, {name}, is in the garden.');