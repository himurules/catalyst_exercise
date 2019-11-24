<?php
/**
 * Created by PhpStorm.
 * User: himu
 * Date: 22/11/19
 * Time: 11:15 AM
 */

$invalid = true;
$args = __getArguments([],['file:', 'create_table', 'dry_run', 'u:', 'p:','h:','help']);

if(isset($args['create_table'])){
    $invalid = false;
    __checkDatabaseParams($args);
    $mysqli = __connectDatabase($args);
    $created = __createUserTable($mysqli);
    mysqli_close($mysqli);
    $created === true ? print('User Table created successfully!').PHP_EOL:print('Problem creating user table!').PHP_EOL;
    die();
}

if(isset($args['file'])){
    $invalid = false;
    __checkInputFile($args);
    __parseInputFile($args);
    print("File {$args['file']} processed successfully!").PHP_EOL;
    die();
}

if(count($args) ==0 || isset($args['help']) || $invalid){

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


function __getArguments($short_options = array() , $long_options = array('help'))
{
    $short_options = implode('', $short_options);
    return getopt($short_options, $long_options);
}

function __connectDatabase($args){
    $db = "catalyst";
    try {
        $mysqli = new mysqli($args['h'], $args['u'], $args['p'], $db);
    } catch (mysqli_sql_exception $e) {
        print ($e->getMessage().PHP_EOL);
        die();
    }
    return $mysqli;
}

function __createUserTable($mysqli){
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
      `name` varchar(30) NOT NULL,   
      `surname` varchar(30 )NOT NULL,       
      `email` varchar(255)  NOT NULL,
       UNIQUE KEY unique_email (`email`)
    )";

    if(mysqli_query($mysqli, $sql))
        return true;
    return false;
}

function __checkDatabaseParams($args){
    $db_host = @$args['h'];
    $db_user = @$args['u'];
    $db_password = @$args['p'];
    if(!$db_host || !$db_user || !$db_password) {
        print('missing db-host, db-user or db-password options. See --help for explanation.'.PHP_EOL);
        die();
    }
}

function __checkInputFile($args){
    $filename = $args['file'];
    if(!file_exists($filename) || !is_readable($filename) || !is_file($filename)) {
        print "Invalid File, Upload Failed!".PHP_EOL;
        die();
    }
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(strtolower($ext) !== 'csv'){
        print "Invalid file. Please upload file in csv format!";
        die();
    }
}

function __parseInputFile($args){
    //starting line
    $row = 0;
    // opens the file
    if ( ($file_handle = fopen($args['file'],'r')) == false ) {
        print "Unable to open input file!".PHP_EOL;
        die();
    }
    $dryRun = isset($args['dry_run']) ? true: false;
    if(!$dryRun)
        $mysqli = __connectDatabase($args);
    // while file has content
    while(!feof($file_handle)) {
        $row++;
        //first row is headers
        if($row-1 == 0)
            continue;

        $csv_line = fgetcsv($file_handle); // fetches the csv line
        //print_r($csv_line);exit;
        if ($csv_line === false) {
            print("Empty Record at row {$row}!") . PHP_EOL;
            continue;
        }
        // checks whatever the format is correct
        if (!is_array($csv_line) && (count($csv_line) != 3)) {
            print "Invalid data at row {$row}. Skipping record!".PHP_EOL;
            continue;
        }
        //check for email if Valid Or Not
        if (!filter_var($csv_line[2], FILTER_VALIDATE_EMAIL)) {
            print("Invalid email format at row {$row}. Skipping Record!").PHP_EOL;
            continue;
        }
        if(!$dryRun)
            $user = [mysqli_real_escape_string($mysqli,ucfirst($csv_line[0])), mysqli_real_escape_string($mysqli, ucfirst($csv_line[1])), mysqli_real_escape_string($mysqli,strtolower($csv_line[2]))];
        else
            $user = [ucfirst($csv_line[0]), ucfirst($csv_line[1]), strtolower($csv_line[2])];
        $sql = "INSERT INTO `users` VALUES ('".implode("','",$user)."')";
        if(!$dryRun) {
            mysqli_escape_string($mysqli,$sql);
            if (mysqli_query($mysqli, $sql) === TRUE)
                print ("User Record Inserted for row {$row}") . PHP_EOL;
            else
                print ("Error inserting record for row {$row}") . PHP_EOL;
        }else
            print $sql.PHP_EOL;
    }
    if(!$dryRun)
        mysqli_close($mysqli);
}