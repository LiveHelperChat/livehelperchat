#!/usr/bin/php5
<?php

if (php_sapi_name() != 'cli') {
    echo "PHP code to execute directly on the command line\n";
    exit(-1);
}

ini_set('error_reporting', E_ALL);
ini_set('register_globals', 0);
ini_set('display_errors', 1);

ini_set("max_execution_time", "3600");

require_once "lib/core/lhcore/password.php";
require_once "ezcomponents/Base/src/base.php"; // dependent on installation method, see below
require_once 'cli/lib/install.php';

ezcBase::addClassRepository( './','./lib/autoloads');

spl_autoload_register(array('ezcBase','autoload'), true, false);

#erLhcoreClassSystem::init();

// your code here
ezcBaseInit::setCallback(
 'ezcInitDatabaseInstance',
 'erLhcoreClassLazyDatabaseConfiguration'
);

$cfgSite = erConfigClassLhConfig::getInstance();

if ($cfgSite->getSetting( 'site', 'installed' ) == true)
{
    print('Live helper chat installation complete');
    exit;
}

$instance = erLhcoreClassSystem::instance();

function validate_args($argv, $argc) {

    if ($argc != 2) {
        echo "Wrong number of parameters.\n";
        return(1);
    }

    if ($argv[1] == '--help') {
        print_help();

        return(1);
    } else {
        if (!file_exists($argv[1])) {
            echo "File does not exists: {$argv[1]}\n";
            return(1);
        }
    }
    return(0);
}

function print_help() {
    echo "Usage:\n";
    echo "\tphp install-cli.php settings.ini\n";
    return(1);
}

function main($ini_file) {

    $install = new Install($ini_file);

    $response = $install->step1();
    if (is_array($response)) {
        $install->print_errors($response);
    }

    $response = $install->step2();
    if (is_array($response)) {
        $install->print_errors($response);
    }

    $response = $install->step3();
    if (is_array($response)) {
        $install->print_errors($response);
    }

    $response = $install->step4();
    if (is_array($response)) {
        $install->print_errors($response);
    }

    exit(1);
}

if (validate_args($argv, $argc)) {
    echo "There are some erros with the parameters.\n";
    echo "Run --help to print the help.\n";
    exit(-1);
}

$ini_file = $argv[1];
main($ini_file);
