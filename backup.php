<?php
//ENTER THE RELEVANT INFO BELOW
# niko is the best!
//MySQL server and database
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'dbname';
$tables = '*';

$tables = [
    'activity_log',
    'additional_services',
    'agencies',
    'agency_payment_apps',
    'agency_payments',
    'call_couriers',
    'cargo_add_services',
    'cargo_bag_details',
    'cargo_bags',
    'cargo_cancellation_applications',
    'cargo_collections',
    'cargo_movement_contents',
    'cargo_movements',
    'cargo_part_details',
    'cargoes',
    'cities',
    'current_prices',
    'currents',
    'debits',
    'department_roles',
    'departments',
    'desi_lists',
    'distance_prices',
    'districts',
    'failed_jobs',
    'file_prices',
    'htf_damage_details',
    'htf_damage_types',
    'htf_piece_details',
    'htf_reports',
    'htf_transaction_details',
    'htf_transactions_made',
    'local_locations',
    'migrations',
    'module_groups',
    'modules',
    'motivation_lyrics',
    'neighborhoods',
    'notifications',
    'oauth_access_tokens',
    'oauth_auth_codes',
    'oauth_clients',
    'oauth_personal_access_clients',
    'oauth_refresh_tokens',
    'official_report_movements',
    'official_reports',
    'password_resets',
    'places',
    'receivers',
    'regional_directorates',
    'regional_districts',
    'reports',
    'role_modules',
    'role_permissions',
    'roles',
    'security_codes',
    'sent_sms',
    'settings',
    'sms_contents',
    'sub_modules',
    'system_updates',
    'tax_offices',
    'tc_cars',
    'tc_cars_all_data',
    'telescope_entries',
    'telescope_entries_tags',
    'telescope_monitoring',
    'theme_system',
    'ticket_details',
    'tickets',
    'transshipment_center_agencies',
    'transshipment_center_districts',
    'transshipment_centers',
    'trasfer_cars',
    'users',
    'utf_impropriety_details',
    'utf_impropriety_types',
    'variouses',
];


//Call the core function
backup_tables($dbhost, $dbuser, $dbpass, $dbname, $tables);

//Core function
function backup_tables($host, $user, $pass, $dbname, $tables = '*')
{
    $link = mysqli_connect($host, $user, $pass, $dbname);

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit;
    }

    mysqli_query($link, "SET NAMES 'utf8'");

    //get all of the tables
    if ($tables == '*') {
        $tables = array();
        $result = mysqli_query($link, 'SHOW TABLES');
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }

    $return = '';
    //cycle through
    foreach ($tables as $table) {
        $result = mysqli_query($link, 'SELECT * FROM ' . $table);
        $num_fields = mysqli_num_fields($result);
        $num_rows = mysqli_num_rows($result);

        $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
        $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE ' . $table));
        $return .= "\n\n" . $row2[1] . ";\n\n";
        $counter = 1;



        // echo "<pre>";
        // print_r($tables);
        // echo "</pre>";

        // return false;



        if (substr($table, 0, 4) == 'view')
            continue;

        echo $table;
        echo "<br>";


        //Over tables
        for ($i = 0; $i < $num_fields; $i++) {   //Over rows
            while ($row = mysqli_fetch_row($result)) {
                if ($counter == 1) {
                    $return .= 'INSERT INTO ' . $table . ' VALUES(';
                } else {
                    $return .= '(';
                }

                //Over fields
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }

                if ($num_rows == $counter) {
                    $return .= ");\n";
                } else {
                    $return .= "),\n";
                }
                ++$counter;
            }
        }
        $return .= "\n\n\n";
    }

    //save file
    $fileName = 'db-backup-' . time() . '-' . (md5(implode(',', $tables))) . '.sql';
    $handle = fopen($fileName, 'w+');
    fwrite($handle, $return);
    if (fclose($handle)) {
        echo "Done, the file name is: " . $fileName;
        exit;
    }
}

