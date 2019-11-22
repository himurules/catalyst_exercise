<?php
/**
 * Created by PhpStorm.
 * User: himu
 * Date: 22/11/19
 * Time: 11:15 AM
 */


$args = __getArguments([],['file:', 'create_table', 'dry_run:', 'u:', 'p:','h:','help']);

if(count($args) ==0 || isset($args['help'])){

    echo("Usage: user_upload.php [options] ".PHP_EOL);
    print("  Options:".PHP_EOL);
    print("          --file     File to upload to database with path.". PHP_EOL);
    print("          --dry_run      Execute all function without inserting into Database.".PHP_EOL);
    print("          --u            Database username.".PHP_EOL);
    print("          --p            Database password.".PHP_EOL);
    print("          --h            Database host.".PHP_EOL);
    print("          --create_table Create/Rebuild user table.".PHP_EOL);
    print("          --help         Display Help Options.".PHP_EOL);
    print("  Example:".PHP_EOL);
    print("          php user_upload.php --create_table Creates user table in the database.".PHP_EOL);
    print("          php user_upload.php --file users.csv --dry_run    Parses user.csv file without inserting into user table.".PHP_EOL);
    print("          php user_upload.php --file users.csv --u himu --p hdhdhhd -h sample   Parses user.csv file and inserts the parsed records to user table.".PHP_EOL);
    die();
}

$db_host = @$args['h'];
$db_user = @$args['u'];
$db_password = @$args['p'];
if(!$db_host || !$db_user || !$db_password) {
    print('missing db-host, db-user or db-password options'.PHP_EOL);
    die();
}




function __getArguments($short_options = array() , $long_options = array('help'))
{
    $short_options = implode('', $short_options);
    return getopt($short_options, $long_options);
}