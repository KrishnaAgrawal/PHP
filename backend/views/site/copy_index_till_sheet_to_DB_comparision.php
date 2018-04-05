
<?php

require_once 'C:\xampp\htdocs\FingerTips\vendor\autoload.php';
ini_set('max_execution_time', -1);

define('APPLICATION_NAME', 'Google Sheets API PHP Quickstart');
define('CREDENTIALS_PATH', 'token.json');
define('CLIENT_SECRET_PATH', 'client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/sheets.googleapis.com-php-quickstart.json
define('SCOPES', implode(' ', array(
    Google_Service_Sheets::SPREADSHEETS_READONLY)
));

date_default_timezone_set('America/New_York'); // Prevent DateTime tz exception
//if (php_sapi_name() != 'cli') {
//  throw new Exception('This application must be run on the command line.');
//}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->setScopes(SCOPES);
    $client->setAuthConfig(CLIENT_SECRET_PATH);
    $client->setAccessType('offline');

    // Load previously authorized credentials from a file.
    $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
    if (file_exists($credentialsPath)) {
        $accessToken = json_decode(file_get_contents($credentialsPath), true);
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

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
function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
        $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
    }
    return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Sheets($client);

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
$spreadsheetId = '1jlQZ-AzkN0-x-1O6w1PCYHY4duwFOj2cGqyO_wpPmWo';
//$range_1 = 'Sheet1!A:G';

$arrSprdSheets = $service->spreadsheets->get($spreadsheetId);
$arrSheets = $arrSprdSheets->getSheets();
$strResponse = [];
if (empty($arrSheets)) {
    echo 'No data Found';
}
$arrTable = [];
foreach ($arrSheets as $sheets) {
    $range_1 = $sheets->properties->title . '!A2:G';
    $response_1 = $service->spreadsheets_values->get($spreadsheetId, $range_1);
//echo "<pre>";print_r($service->spreadsheets);exit;
$arrData = $response_1->values;
    

//$range_1 = 'Sheet2!A:G';
//$response_1 = $service->spreadsheets_values->get($spreadsheetId, $range_1);
//
//$arrData_2 = $response_1->getValues();
//$arrData = array_merge($arrData_1, $arrData_2);
//echo "<pre>";print_r($arrData);exit;



    



// Traversing through the Sheet
    foreach ($arrData as $key => $recData) {

        // For Setting Empty Fields
        for ($counter = 0; $counter <= 6; $counter++) {
            if (empty($recData[$counter])) {
                $recData[$counter] = '';
            }
        }

        $strTableName = '';
        $strFieldName = '';
        $strFieldSize = '';
        $flagAllowedNull = '';
        $strKeyType = '';
        $strRemarks = '';

        list($strTableName, $strFieldName, $strFieldType, $strFieldSize, $flagAllowedNull, $strKeyType, $strRemarks) = $recData;

//    if(){
//    }
        if (!empty($strTableName)) {
            $arrTable = Yii::$app->db->getTableSchema($strTableName);

//        echo "<pre>";
//        print_r($arrTable);
        }
        if (empty($strTableName) && empty($currentTblName)) {
            continue;
        }

//    echo "$strFieldName";
        // Check Table Exists or Not
        if (!empty($strTableName)) {

            if (empty($arrTable)) {
                $strResponse[$strTableName][$strTableName] = 'Table "' . $strTableName . '" does not Exists in Database<br>';
                $currentTblName = '';
//        continue;
            } else {
                $currentTblName = $strTableName;
            }
//        continue;
        }
        // To be Removed
//    if($currentTblName != 'tbl_address'){
//        continue;
//    }
        // !empty
        if ((!empty($currentTblName)) && ((!empty($strFieldName)))) {

            // Check in Database $strFieldName exists or not
            if (empty($arrTable->columns[$strFieldName])) {
                $strResponse[$currentTblName][$strFieldName] = 'Field "' . $strFieldName . '" does not Exists in Table ' . $currentTblName . '<br>';
                continue;
            }

            // Check in Database $strFieldType and $strFieldSize exits or not
            if (empty($arrTable->columns[$strFieldName]->dbType)) {
                $strResponse[$currentTblName][$strFieldName . "#type"] = 'Type/Size does not Exists at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
            } else {

                //  Check if the type is int and having size 11 or not
                if (strtolower($strFieldType) == "int") {
                    $strFieldType = $strFieldType . "(11)";
                    //  Check if the type is varchar and size is $strFieldSize or not
                } else if (strtolower($strFieldType) == "varchar") {
                    $strFieldType = $strFieldType . "(" . $strFieldSize . ")";
                } else if (strtolower($strFieldType) == "boolean") {
                    $strFieldType = "smallint(6)";
                } else if (strtolower($strFieldType) == "decimal") {
                    $strFieldType = $strFieldType . "(" . $strFieldSize . ")";
                } else if (strtolower($strFieldType) == "blob") {
//                $strFieldType = $strFieldType. "(" . $strFieldSize . ")";
                }
                if ($arrTable->columns[$strFieldName]->dbType != $strFieldType) {
                    $strResponse[$currentTblName][$strFieldName . "#type"] = 'Type/Size mis-match at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
                }
            }

            //  check if null is allowed
            if ($arrTable->columns[$strFieldName]->allowNull != 1 && strtolower($flagAllowedNull) == "yes") {
                $strResponse[$currentTblName][$strFieldName . "#Null"] = 'Null type mis-match at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
            }

            //  check for primary keys
            if ($arrTable->columns[$strFieldName]->isPrimaryKey != 1 && strtolower($strKeyType) == "primary") {
                $strResponse[$currentTblName][$strFieldName . "#Primary"] = 'Primary Key mis-match at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
            }

            // check for foreign keys
            $arrForeignKey = $arrTable->foreignKeys;
            if (!empty($arrForeignKey) && strtolower($strKeyType) == "foreign") {

                foreach ($arrForeignKey as $key => $value) {

                    $value = array_map(function($value) {
                        return implode('', array_filter(array_keys($value)));
                    }, $arrForeignKey);

//                echo "<pre>";print_r($value);

                    if (!in_array(strtolower($strFieldName), $value)) {
                        $strResponse[$currentTblName][$strFieldName . "#Foreign"] = 'Foreign Key mis-match at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
                    }
                }
//            exit;
            }
        } else {
            continue;
        }
    }
}

//echo "<pre>";print_r($strResponse);
//exit;
$strHtml = "<table class='table table-bordered table-striped'><tr><th>Table Name</th>"
        . "<th>Column Name</th>"
        . "<th>Column Name Exists or Not</th>"
        . "<th>Type / Size</th>"
        . "<th>Null</th>"
        . "<th>Primary Key</th>"
        . "<th>Foreign Key</th></tr>";

$counter = 0;
$strCurrentTableName = '';
$strPreviousTableName = '';
$tableName = '';
$strPre = '';
$tempKey = '';
$arrTableName = array_keys($strResponse);

//echo "<pre>";print_r(key($strResponse));exit;
foreach($strResponse as $key => $recKey){

    foreach ($recKey as $value => $recValue){

        $strHtml .= "<tr>";
                    
        
        
        //  Table Name does not exists
        if (strpos($recValue, 'does not Exists in Database') !== false) {
            $strHtml .= "<tr style='background-color: lightgray;'><td> " . $recValue . " </td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "</tr>";
            continue;
        }
        else if($tempKey != $key){
            $strHtml .= "<tr style='background-color: lightgray;'><td> " . $key . " </td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "<td></td>"
                    . "</tr>";
            $tempKey = $key;
            continue;
//            $strHtml .= "<td> </td>";
        }else{
            $strHtml .= "<td>  </td>";
        }
        
        
        //  Column Name
            $strHtml .= "<td> " . explode("#",$value)[0]  . " </td>";
        
        
        //  Column Name Exists or Not
        if (strpos($recValue, 'Field') !== false) {
            $strHtml .= "<td> " . $recValue  . " </td>";
        }
        else{
            $strHtml .= "<td> </td>";
        }
        
        // Type / Size
        if (strpos($recValue, 'Type/Size mis-match at column') !== false) {
            $strHtml .= "<td> " . $recValue  . " </td>";
        }
        else{
            $strHtml .= "<td> </td>";
        }
        
        //  Null Check
        if (strpos($recValue, 'Null type mis-match at column') !== false) {
            $strHtml .= "<td> " . $recValue  . " </td>";
        }
        else{
            $strHtml .= "<td> </td>";
        }
        
        //  Primary Key
        if (strpos($recValue, 'Primary Key mis-match at column') !== false) {
            $strHtml .= "<td> " . $recValue  . " </td>";
        }
        else{
            $strHtml .= "<td> </td>";
        }
        
        //  Foreign Key
        if (strpos($recValue, 'Foreign Key mis-match at column') !== false) {
            $strHtml .= "<td> " . $recValue  . " </td>";
        }
        else{
            $strHtml .= "<td> </td>";
        }
    $strHtml .= "</tr>";
    }
}
$strHtml .= "</table>";
echo $strHtml;exit;

