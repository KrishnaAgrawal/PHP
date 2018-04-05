
<?php

require_once 'C:\xampp\htdocs\FingerTips\vendor\autoload.php';

//  Used for having unlimited loading time.
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
$spreadsheetId = '1F1eWJmWdoyU1pYaMs4tD-hodAad1IDwwBeTPY0HIQtY';
//$range_1 = 'Sheet1!A:G';

$arrSprdSheets = $service->spreadsheets->get($spreadsheetId);
$arrSheets = $arrSprdSheets->getSheets();
$strResponse = [];
if (empty($arrSheets)) {
    echo 'No data Found';
}
//  ******************************************************************************


//$provider = new \yii\data\ArrayDataProvider([
//    'allModels' => [0=>['id'=>'1', 'username'=>'Krishna', 'email'=>'login4skag@gmail.com']],
////    'sort' => [
////        'attributes' => ['id'=>'1', 'username'=>'Krishna', 'email'=>'login4skagg@gmail.com'],
////    ],
//    'pagination' => [
//        'pageSize' => 10,
//    ],
//]);
//echo yii\grid\GridView::widget([
//    'dataProvider'=>$provider
//]);exit;
//




//  ******************************************************************************




$arrTable = [];

    $arrDatabaseTableName = Yii::$app->db->schema->tableNames;
    foreach ($arrDatabaseTableName as $key => $value) {
       if (strpos($value, "tbl_") === false) {
           unset($arrDatabaseTableName[$key]);
       }
    }
            
            
foreach ($arrSheets as $sheets) {
    $range_1 = $sheets->properties->title . '!A2:G';
    $response_1 = $service->spreadsheets_values->get($spreadsheetId, $range_1);
//echo "<pre>";print_r($service->spreadsheets);exit;
    $arrData = $response_1->values;


//  ********************************************************************
//  Sheet to Data Comparison

//echo "<pre>";print_r($arrData);
//$arrData
//while(empty($arrData[0][0])){
//    foreach ($arrData as $key => $value){
//        foreach ($value as $subKey => $subvalue){
//            
//        }
//    }
//}







$arrFieldNameFoundInSheetNotInDb = [];
$arrFieldNameFoundInDbNotInSheet = [];

$arrNonExsistingTableInEitherSide = [];
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
                
                $arrTableFoundInSheet[] = $strTableName;
                
                $arrNonExsistingTableInEitherSide[$strTableName][0] = 'Table "' . $strTableName . '" does not Exists in Database<br>';
                $currentTblName = '';
//        continue;
            } else {
                $arrTableFoundInSheet[] = $strTableName;
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
                $strResponse[$currentTblName][$strFieldName] = 'Field "' . $strFieldName . '" does not Exists in Table "' . $currentTblName . '"<br>';
                $arrFieldNameFoundInSheetNotInDb[$currentTblName][] = $strFieldName;
                continue;
            }
            else {
                $arrFieldNameFoundInDbNotInSheet[$currentTblName][] = $strFieldName;
                $strResponse[$currentTblName][$strFieldName] = $strFieldName;   
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
                }else{
                    $strResponse[$currentTblName][$strFieldName . "#type"] = 'Type/Size is correct at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
                }
            }

            //  check if null is allowed
            if (!empty($arrTable->columns[$strFieldName]) && $arrTable->columns[$strFieldName]->allowNull != 1 && strtolower($flagAllowedNull) == "yes") {
                $strResponse[$currentTblName][$strFieldName . "#Null"] = 'Null type mis-match at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
            }else{
                $strResponse[$currentTblName][$strFieldName . "#type"] = 'Type/Size is correct at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
            }

            //  check for primary keys
            if (!empty($arrTable->columns[$strFieldName]) && $arrTable->columns[$strFieldName]->isPrimaryKey != 1 && strtolower($strKeyType) == "primary") {
                $strResponse[$currentTblName][$strFieldName . "#Primary"] = 'Primary Key mis-match at column ' . $strFieldName . ' in Table ' . $currentTblName . '<br>';
            }else{
                if(strtolower($strKeyType) == "primary"){
                    $strResponse[$currentTblName][$strFieldName . "#Primary"] = 'Match';
                }else{
                    $strResponse[$currentTblName][$strFieldName . "#Primary"] = '--';
                }
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
                    }else if(strtolower($strKeyType) == "foreign"){
                        $strResponse[$currentTblName][$strFieldName . "#Foreign"] = 'Match';
                    }else{
                        $strResponse[$currentTblName][$strFieldName . "#Foreign"] = '--';
                    }
                }
//            exit;
            }
        } else {
            continue;
        }
    }
    
    //  Column in database but not in sheets
    foreach ($arrFieldNameFoundInDbNotInSheet as $key => $value){
        foreach ($value as $subKey => $subValue){
//            echo '<pre>';print_r($key);exit;
            $arrColumnNames = Yii::$app->db->getTableSchema($key)->columnNames;
            $arrColumnNotInSheet[$key] = array_diff($arrColumnNames,$value);
        }
    }
//    echo '<pre>';print_r($arrColumnNotInSheet);exit;
    foreach ($arrColumnNotInSheet as $key => $value){
        foreach ($value as $subKey => $subValue){
            $strResponse[$key][$subValue] = 'Field "' . $subValue . '" does not Exists in sheet "' . $key . '"<br>';
        }
    }
//   echo "<pre>";print_r($strResponse);exit;
    $previousKey = '';
    $arrRes = [];
    $count = 0;
//    echo "<pre>";print_r($arrData);exit;
    
    foreach ($strResponse as $key => $value){
        $strTempTableName = $key;
        
        foreach ($value as $subKey => $subValue){
            if(!empty($arrRes[$count - 1 ]["Column Name"])){
                $lastColumnName = $arrRes[$count - 1 ]["Column Name"];
            }
            
            $arrSubKey = explode('#', $subKey);
            $strTempColumnName = $arrSubKey[0];
            $arrRes[$count]["Table Name"] = $strTempTableName;
            $arrRes[$count]["Column Name"] = $strTempColumnName;
            
            if(strpos($subValue, "Field") !== false){
                $arrRes[$count]["Column Name Exists or Not"] = $subValue;
            }
            else{
                $arrRes[$count]["Column Name Exists or Not"] = "--";
            }
            
            if(!empty($arrSubKey[1])){
                $columnKey = $arrSubKey[1];
                if(strpos($columnKey, 'type') !== false){
                    $arrRes[$count][$columnKey] = $subValue;
                }
                if(strpos($columnKey, 'Null') !== false){
                    $arrRes[$count][$columnKey] = $subValue;
                }
                if(strpos($columnKey, 'Primary') !== false){
                    $arrRes[$count][$columnKey] = $subValue;
                }
                if(strpos($columnKey, 'Foreign') !== false){
                    $arrRes[$count][$columnKey] = $subValue;
                }
            }else{
                    $arrRes[$count]['type'] = "--";
                    $arrRes[$count]['Null'] = "--";
                    $arrRes[$count]['Primary'] = "--";
                    $arrRes[$count]['Foreign'] = "--";
            }
             if(!empty($lastColumnName) && $lastColumnName == $strTempColumnName){
//                 echo "<pre>";    print_r($value);exit;
//                 echo "<pre>";    print_r($arrRes[$count]);
//                 
                 
//                                  echo "<pre>";    print_r(array_merge($arrRes[$count-1],$arrRes[$count]));exit;
                $arrRes[$count] = array_merge($arrRes[$count-1],$arrRes[$count]);
                unset($arrRes[$count-1]);
            }
            $count++;
        }
        
    }
    
    
    
    echo "<pre>";    print_r($arrRes);exit;

//    Sheet to Data Comparison
//  ******************************************************************************************************************************************
//  ******************************************************************************************************************************************
//    DataBase to Sheet Comparison

//    $arrTableName_1 = [];
//    $arrNewTableName = [];
//    $arrTable = [];
//    $arrData_1 = $arrData;
//    $arrDataNew = [];
//    $strResponse_1 = [];
//    $arrTableDifference = [];
//    $arrColumnName = [];
//    $counter_1 = 0;
//    $tempCounter = 0;
////echo "<pre>";print_r($arrData);exit;
//// Traversing through the Sheet
////    $arrDatabaseTableName = Yii::$app->db->schema->tableNames;
////     foreach ($arrDatabaseTableName as $key => $value) {
////        if (strpos($value, "tbl_") === false) {
////            unset($arrDatabaseTableName[$key]);
////        }
////    }
//    
//    
//    foreach ($arrData_1 as $key => $recData) {
//$tempCounter = 0;
//        // For Setting Empty Fields
//        for ($counter = 0; $counter <= 6; $counter++) {
//            if (empty($recData[$counter])) {
//                $recData[$counter] = '';
//            }
//        }
//
//        $strTableName = '';
//        $strFieldName = '';
//        $strFieldSize = '';
//        $flagAllowedNull = '';
//        $strKeyType = '';
//        $strRemarks = '';
//
//        //  Filling details in given variables
//        list($strTableName, $strFieldName, $strFieldType, $strFieldSize, $flagAllowedNull, $strKeyType, $strRemarks) = $recData;
//        if (!empty($strTableName)) {
////            $arrTable[$strTableName] = "";
//            $strPreviousTableName = $strTableName;
//            $counter_1 = 0;
//            
//            //  making 2D array of sheet
//        }else if(empty($strTableName) && !empty ($strFieldName)){
//            $arrTable[$strPreviousTableName][$counter_1][$tempCounter++] = $strFieldName; 
//            $arrTable[$strPreviousTableName][$counter_1][$tempCounter++] = $strFieldType; 
//            $arrTable[$strPreviousTableName][$counter_1][$tempCounter++] = $strFieldSize; 
//            $arrTable[$strPreviousTableName][$counter_1][$tempCounter++] = $flagAllowedNull; 
//            $arrTable[$strPreviousTableName][$counter_1][$tempCounter++] = $strKeyType; 
//            $arrTable[$strPreviousTableName][$counter_1++][$tempCounter++] = $strRemarks; 
//        }
//        
//        //  Extracting all table names from database
//        
//        
//        
//          
//          
//        //  Storing Table names in array
//       
////        echo '<pre>';print_r($arrTableName_1);exit;
//    }
//    
//    
////    echo "<pre>";print_r($arrNewTableName);exit;
//    $count = 0;
//    $arrSheetTableName = array_keys($arrTable);
//    $arrTableNotFoundInDatabase = array_diff($arrDatabaseTableName, $arrSheetTableName);
//    
//    
//   
//    
//
//    
////    foreach ($arrData_1 as $key => $value){
////        foreach ($value as $subKey => $subValue){
//////            for($count = 0; $count<5; $count++){
////                if(!empty($subKey)){
////                    $arrDataNew[$subValue][$count] = $subValue;
////                    $count++;
////                }
//////            }
////        }
////    }
////        echo "<pre>";print_r($arrDataNew);exit;
// 
//    
//    
//    // Check Table Exists or Not
////    $arrDifferenceArray = array_diff($arrNewTableName, $arrTableName);
//    $counter = 0;
//    foreach ($arrTableNotFoundInDatabase as $key => $strTableName){
//        $strResponse[$strTableName] = 'Table "' . $strTableName . '" does not Exists in Database<br>';
//    }
//     
//    
//    $arrCommonTable = array_intersect_key(array_flip($arrSheetTableName),array_flip($arrDatabaseTableName));
//    $arrTableToProcess = array_flip($arrCommonTable);
//      echo "<pre>";print_r();exit;
//    echo "<pre>";print_r($strResponse);exit;
////   
////   
//    //  Extracting all column names of particular table  
//        $arrColumnName = Yii::$app->db->getTableSchema('tbl_type')->columnNames;
//          
//    foreach ($arrTableName as $key => $value){
//        
//    }
//    
      

    //  Check for difference in columns in a table
//        echo "<pre>";print_r(Yii::$app->db->getTableSchema('tbl_type')->columnNames);exit;
    
//    echo "<pre>";    print_r($strFieldName);exit;

//    echo "<pre>";
//    print_r($strResponse);
//    exit;




//    Yii::$app->db->schema->tableNames
//    echo "<pre>";print_r(Yii::$app->db->getTableSchema('tbl_type')->columnNames);exit;
//    DataBase to Sheet Comparison
//  ******************************************************************************************************************************************
}
//$arrTableFoundInDb = array_unique(yii\helpers\ArrayHelper::merge($arrDatabaseTableName, $arrTableFoundInDb));
        
//$arrTableFoundInDbNotInSheets = array_diff($arrDatabaseTableName, $arrTableFoundInSheet);
//foreach ($arrTableFoundInDbNotInSheets as $key => $value){
//        $arrNonExsistingTableInEitherSide[$value][0] = 'Table "' . $value . '" does not exists in Sheet.<br>';
//}
//// echo "<pre>";print_r($strResponse);exit;
////echo '<pre>';print_r($arrTableFoundInDbNotInSheets);
////echo '<pre>';print_r($arrTableFoundInSheetNotInDb);exit;
//
////  ******************************************************************************************************************************************
////    Sheet to Data Comparison
//$strHtml = "<table border='1' class='table table-bordered table-striped'><tr>"
//        . "<th>Table Name</th>"
//        . "<th>Column Name</th>"
//        . "<thead>"
//        . "<th colspan='2'>Table Exists or Not</th>"
//        . "</thead>"
//        . "<th colspan='2'>Column Exists or Not</th>"
//        . "<th>Type / Size</th>"
//        . "<th colspan='2'>Null</th>"
//        . "<th colspan='2'>Primary Key</th>"
//        . "<th colspan='2'>Foreign Key</th>"
//        . "</tr>"
//        . "";
////echo $strHtml;exit;
////$strHtml .= "<tr>"
////        . "<td></td>"
////        . "<td></td>"
////        . "<td>Sheet</td>"
////        . "<td>Database</td>"
////        . "<td>Sheet</td>"
////        . "<td>Database</td>"
////        . "<td></td>"
////        . ""
////        . "</tr>";
//$counter = 0;
//$strCurrentTableName = '';
//$strPreviousTableName = '';
//$tableName = '';
//$strPre = '';
//$tempKey = '';
//$strPreviousKey = '';
//$arrTableName = array_keys($strResponse);
////echo "<pre>";print_r(($strResponse));exit;
//
//if(array_keys(array_keys($strResponse)) === 0){
////echo "<pre>";print_r(($strResponse));exit;
////    $arrNonExsistingTableInEitherSide = $arrTableName[];
//}
////echo "<pre>";print_r($arrNonExsistingTableInEitherSide);exit;
//
////$strResponse = array_diff($strResponse, $arrNonExsistingTableInEitherSide);
////
//
//
//
//
//foreach($strResponse as $key => $recKey){
//
//    foreach ($recKey as $value => $recValue){
//
//        $strHtml .= "<tr>";
//        
////        if($value == 0 && $strPreviousKey != $key){
////            $arrNonExsistingTableInEitherSide[$counter++] = $key;
////            $strPreviousKey = $key;
////        }
//        
//        //  Table Name does not exists
//        if (strpos($recValue, 'does not Exists in Database') !== false) {
//            $strHtml .= "<tr style='background-color: lightgray;'><td> " . $recValue . " </td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "</tr>";
//            continue;
//        }
//        else if($tempKey != $key){
//            $strHtml .= "<tr style='background-color: lightgray;'><td> " . $key . " </td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "<td></td>"
//                    . "</tr>";
//            $tempKey = $key;
//            continue;
////            $strHtml .= "<td> </td>";
//        }else{
//            $strHtml .= "<td>  </td>";
//        }
//        
//        
//        //  Column Name
//            $strHtml .= "<td> " . explode("#",$value)[0]  . " </td>";
//        
//        
//        //  Column Name Exists or Not
//        if (strpos($recValue, 'Field') !== false) {
//            $strHtml .= "<td> " . $recValue  . " </td>";
//        }
//        else{
//            $strHtml .= "<td> </td>";
//        }
//        
//        // Type / Size
//        if (strpos($recValue, 'Type/Size mis-match at column') !== false) {
//            $strHtml .= "<td> " . $recValue  . " </td>";
//        }
//        else{
//            $strHtml .= "<td> </td>";
//        }
//        
//        //  Null Check
//        if (strpos($recValue, 'Null type mis-match at column') !== false) {
//            $strHtml .= "<td> " . $recValue  . " </td>";
//        }
//        else{
//            $strHtml .= "<td> </td>";
//        }
//        
//        //  Primary Key
//        if (strpos($recValue, 'Primary Key mis-match at column') !== false) {
//            $strHtml .= "<td> " . $recValue  . " </td>";
//        }
//        else{
//            $strHtml .= "<td> </td>";
//        }
//        
//        //  Foreign Key
//        if (strpos($recValue, 'Foreign Key mis-match at column') !== false) {
//            $strHtml .= "<td> " . $recValue  . " </td>";
//        }
//        else{
//            $strHtml .= "<td> </td>";
//        }
//    $strHtml .= "</tr>";
//    }
//}
//$strHtml .= "</table>";
//echo "<pre>";
////print_r($arrNonExsistingTableInEitherSide);exit;
////echo $strHtml;
////    Sheet to Data Comparison
////  ******************************************************************************************************************************************
////echo "<pre>";print_r($arrNonExsistingTableInEitherSide);
//
////
////exit;
//
