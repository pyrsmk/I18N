<?php

use Symfony\Component\ClassLoader\Psr4ClassLoader;

########################################################### Prepare

error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/vendor/autoload.php';

$loader = new Psr4ClassLoader;
$loader->addPrefix('Translator\\', '../src');
$loader->register();

########################################################### Basics

$minisuite = new MiniSuite\Suite('Basics');

$minisuite->hydrate(function($minisuite) {
	if(php_sapi_name() == 'apache2handler') {
		$minisuite['translator'] = new Translator\Http('it');
		echo '<pre>';
	}
	else {
		$minisuite['translator'] = new Translator\Cli('it');
	}
});

$minisuite->expects('Normalize locale')
		  ->that($minisuite['translator']->normalizeLocale('sl_Latn-IT_nedis'))
		  ->equals('sl_IT');

$minisuite->expects('No translations loaded')
		  ->that(function($minisuite) {
			  return $minisuite['translator']->getLocale();
		  })
		  ->equals('it');

$minisuite->expects('Translations loaded')
		  ->that(function($minisuite) {
			  $minisuite['translator']->load('fr_FR', json_decode(file_get_contents('langs/fr_FR.json')));
			  return $minisuite['translator']->getLocale();
		  })
		  ->equals('fr_FR');

$minisuite->expects('Force locale')
		  ->that(function($minisuite) {
			  $minisuite['translator']->setLocale('ru');
			  return $minisuite['translator']->getLocale();
		  })
		  ->equals('ru');

########################################################### Translations

$minisuite = new MiniSuite\Suite('Translations');

$minisuite->hydrate(function($minisuite) {
	if(php_sapi_name() == 'apache2handler') {
		$minisuite['translator'] = new Translator\Http('it');
		echo '<pre>';
	}
	else {
		$minisuite['translator'] = new Translator\Cli('it');
	}
	$minisuite['translator']->load('en', json_decode(file_get_contents('langs/en.json')));
	$minisuite['translator']->load('fr_FR', json_decode(file_get_contents('langs/fr_FR.json')));
});

$minisuite->expects('Translate')
		  ->that($minisuite['translator']->translate('test'))
		  ->equals('Le chat noir, {name}, est dans le jardin.');

$minisuite->expects('Translate with forced locale')
		  ->that(function($minisuite) {
			  $minisuite['translator']->setLocale('en');
			  return $minisuite['translator']->translate('test');
		  })
		  ->equals('The black cat, {name}, is in the garden.');

$minisuite->expects('Translate with parameters')
		  ->that($minisuite['translator']->translate('test', ['name' => 'Fanion']))
		  ->equals('Le chat noir, Fanion, est dans le jardin.');





