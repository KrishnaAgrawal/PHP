<?php

namespace backend\models;

use Yii;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DbCompare {

    const APPLICATION_NAME = 'test';
    const CREDENTIALS_PATH = 'token.json';
    const CLIENT_SECRET_PATH = 'client_secret.json';
    const LABEL_YES = '<span class="label label-success" id="yes">Yes</span>';
    const LABEL_NO = '<span class="label label-danger" id="no">No</span>';
    /*
     * Processing $strResponse
     */

    public function processResponse($strResponse) {
        $count = 0;
        $field_tbl = "str_table";
        $field_tbl_column = "str_table_column";

        $arrRes = [];
//  Iterating "$strResponse" which contains all details in 2D array        
        foreach ($strResponse as $key => $value) {
            $strTempTableName = $key;
            foreach ($value as $subKey => $subValue) {
//  Unseting previous keys
                if (!empty($arrRes[$count - 1][$field_tbl_column])) {
                    $lastColumnName = $arrRes[$count - 1][$field_tbl_column];
                }
//  Breaking Keys into two parts from "#" and storing into array
                $arrSubKey = explode('#', $subKey);
                $strTempColumnName = $arrSubKey[0];
                $arrRes[$count][$field_tbl] = $strTempTableName;
                $arrRes[$count][$field_tbl_column] = $strTempColumnName;

//  Keys having "#" in it
                if (!empty($arrSubKey[1])) {
                    $columnKey = $arrSubKey[1];
//                if(strpos($columnKey, "Field") !== false){
//                    $arrRes[$count][$field_tbl_column_flag] = $subValue;
//                }
                    if (strpos($columnKey, "sheetField") !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, "dbField") !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }

                    if (strpos($columnKey, 'dbtypesize') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, 'sheettypesize') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, 'sheetNull') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, 'dbNull') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, 'SheetPrimary') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, 'DbPrimary') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, 'SheetForeign') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                    if (strpos($columnKey, 'DbForeign') !== false) {
                        $arrRes[$count][$columnKey] = $subValue;
                    }
                }

                if (!empty($lastColumnName) && $lastColumnName == $strTempColumnName) {

                    $arrRes[$count] = array_merge($arrRes[$count - 1], $arrRes[$count]);
                    unset($arrRes[$count - 1]);
                }
                $count++;
            }
        }
        return $arrRes;
    }

    /*
     *  All Tables from Database 
     */

    public function getDatabaseTableNames($db = null) {
//  Fetching all table names from database
        $arrDatabaseTableName = Yii::$app->db->schema->tableNames;
//echo "<pre>";print_r($arrDatabaseTableName);exit;
//        foreach ($arrDatabaseTableName as $key => $value) {
//            if (strpos($value, "tbl_") === false) {
//                unset($arrDatabaseTableName[$key]);
//            }
//        }
//  removing views from "$arrDatabaseTableName" (from list of tables)
        foreach ($arrDatabaseTableName as $key => $value) {
            if (strpos($value, "vw_") !== false) {
                unset($arrDatabaseTableName[$key]);
            }
        }

        return $arrDatabaseTableName;
    }

    /*
     *   Storing tables, sheet tables and database tables        
     */

    public function getComparedTables($arrTableInBoth, $arrTableFoundInSheet, $arrDatabaseTableName) {
        $arrNew = [];
        $counter = 0;

//  Storing tables, sheet tables and database tables        
        foreach ($arrTableInBoth as $key => $value) {
//  Check Table Name in Sheet
            $valueToSet = 0;
            if (in_array($value, $arrTableFoundInSheet)) {
                $valueToSet = 1;
            }

//  Check Table Name in Sheet
            $valueToSetDb = 0;
            if (in_array($value, $arrDatabaseTableName)) {
                $valueToSetDb = 1;
            }

            $arrNew[$counter]["Table"] = $value;
            $arrNew[$counter]["Sheet_Table"] = $valueToSet;
            $arrNew[$counter]["Db_Table"] = $valueToSetDb;
            $counter++;
        }

        return $arrNew;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public function getClient() {
        $client = new \Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setScopes(implode(' ', array(
            \Google_Service_Sheets::SPREADSHEETS_READONLY)
        ));
        $client->setAuthConfig(self::CLIENT_SECRET_PATH);
        $client->setAccessType('offline');

// Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory(self::CREDENTIALS_PATH);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
// Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
//            $authCode = trim(fgets(STDIN));

// Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

// Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

// Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    public function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

    public function getSheets() {
        // Get the API client and construct the service object.
        // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
        $service = $this->getService();
        $arrSprdSheets = $service->spreadsheets->get($this->getSheetId());
        return $arrSprdSheets->getSheets();
    }

    public function getService() {
        $client = $this->getClient();
        $service = new \Google_Service_Sheets($client);
        return $service;
    }

    public function getSheetId($spreadsheetId = '1jlQZ-AzkN0-x-1O6w1PCYHY4duwFOj2cGqyO_wpPmWo') {
        return $spreadsheetId;
    }

    public function getComparisonType() {
        return [
            'sheet_to_db' => "Sheet to Db",
            'db_to_sheet' => "Db to Sheet",
        ];
    }

    public function getActionDbSheet() {
        return [
            'modify_in_both' => 'Modify in Both',
            'modify_in_database' => 'Modify in Database',
            'modify_in_sheet' => 'Modify in Sheet',
            'remove_from_both' => 'Remove from Both',
            'remove_from_database' => 'Remove from Database',
            'remove_from_sheet' => 'Remove from Sheet',
            'done' => 'Done',
        ];
    }

    public function getActionDbNoSheet() {
        return [
            'add_to_sheet' => 'Add to Sheet',
            'modify_in_database' => 'Modify in Database',
            'remove_from_database' => 'Remove from Database',
            'done' => 'Done',
        ];
    }

    public function getActionNoDbSheet() {
        return [
            'add_to_database' => 'Add to Database',
            'modify_in_sheet' => 'Modify in Sheet',
            'remove_from_sheet' => 'Remove from Sheet',
            'done' => 'Done',
        ];
    }
        
    public function getActionMatchTypeToShow() {
        return [
            'All' => "All",
            "Matching" => "Matching",
            "Non-Matching" => "Non-Matching"
        ];
    }
    
    
    public function getFieldType($strFieldType = '', $strFieldSize = '') {

        
        if (strtolower($strFieldType) == "int") {
            $strFieldType = $strFieldType . "(11)";
            //  Check if the type is varchar and size is $strFieldSize or not
        } 
        if (strtolower($strFieldType) == "varchar") {
            $strFieldType = $strFieldType . "(" . $strFieldSize . ")";
        } 
        if (strtolower($strFieldType) == "boolean") {
            $strFieldType = "smallint(6)";
        } 
        if (strtolower($strFieldType) == "decimal") {
            $strFieldType = $strFieldType . "(" . $strFieldSize . ")";
        } 
        if (strtolower($strFieldType) == "blob") {
            $strFieldType = $strFieldType . "(" . $strFieldSize . ")";
        }
        if (strtolower($strFieldType) == "date/time") {
            $strFieldType = $strFieldType . "(" . $strFieldSize . ")";
        }
        if (strtolower($strFieldType) == "smallint") {
            $strFieldType = $strFieldType . "(" . $strFieldSize . ")";
        }
        if(empty($strFieldType)){
            $strFieldType ='--';
        }
        return $strFieldType;
    }

    public function getDatabaseName(){
        $db_type = '';
        $dsn = '';
        $db_type = Yii::$app->db->driverName;       // mysql/sqlite
        $dsn = Yii::$app->db->dsn;                  // sqlite:C:\Users\shria\Desktop\saarthi1_retail_user.db || mysql:host=192.168.10.230;dbname=saathi_001_00002_dev
        $dbName = '';
        if (!empty($db_type) && $db_type == "mysql") {
            $dbName = explode('dbname=', $dsn);
            if (!empty($dbName[1])) {
                $dbName = $dbName[1];
            }
        }

        if (!empty($db_type) && $db_type == "sqlite") {
            $dbName = '';
            $dbName = substr(basename($dsn), 0, -3);
        }
        
        return [
            'dbName' => $dbName,
            'dsn' => $dsn,
            'db_type' => $db_type,
        ];
    }
    
    public function getAllDatabaseNameFromJson($arrTableReport){
        
    // making all databases array
        foreach($arrTableReport as $key => $value){
            $arrAllDatabaseName[$key] = $key;
        }
        array_unshift($arrAllDatabaseName, "All");
        return $arrAllDatabaseName;
    }
}
