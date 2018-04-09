<?php

namespace backend\controllers;

use backend\models\DbCompare;
use common\models\LoginForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['logout', 'index'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {

        $arrCommonResponseColumnName = [];
        $strTableToShow = '';
        $sheet_db = 0;
        $preferenceOption = '';
        $common_field = '';
        $filename = "commonFile.json";
        // making object of dbCompare Class
        $objDbCompare = new DbCompare();

        // getting matching type array 
        $arrMatchTypeToShow = $objDbCompare->getActionMatchTypeToShow();
        // getting preference option array
        $arrpreferenceOption = $objDbCompare->getComparisonType();
        $arrResult = $this->compareSheetDb();
        $arrAll = $arrResult['arrTablesFields'];
        $arrParams = Yii::$app->request->queryParams;
        if (!empty($arrParams["common_field"])) {
            $common_field = $arrParams["common_field"];
        }
        //  getting file content
        $arrTableAction = [];
        if (file_exists($filename)) {
            $strJson = file_get_contents($filename);
            $arrTableAction = Json::decode($strJson, true);
            $arrDatabaseDetail = $objDbCompare->getDatabaseName();
            $arrTableAction = $arrTableAction[$arrDatabaseDetail['dbName']]['columns'];
        }
        else{
            $handle = fopen($filename, "w"); 
        }
        // getting value by name for matching type
        if (!empty($arrParams["tables"])) {
            $strTableToShow = $arrParams["tables"];
        }

        // getting value by name for matching type
        if (!empty($arrParams["preferenceOption"])) {
            $preferenceOption = $arrParams["preferenceOption"];
        }

        // getting value by name for matching type
        if (!empty($arrParams["sheet_db"])) {
            $sheet_db = $arrParams["sheet_db"];
        }


        //  Filter at Table Name
        if (!empty($arrParams['str_table'])) {
            $strTableSearched = $arrParams['str_table'];
            foreach ($arrAll as $key => $value) {
                if (strpos($value["str_table"], $strTableSearched) === false) {
                    unset($arrAll[$key]);
                }
            }
        }
//        
        //  Filter at Column Name
        if (!empty($arrParams['str_table_column'])) {
            $strTableSearched = $arrParams['str_table_column'];
            foreach ($arrAll as $key => $value) {
                if (strpos($value["str_table_column"], $strTableSearched) === false) {
                    unset($arrAll[$key]);
                }
            }
        }

        //  Matching 
        $totalColumnNonMatchCount = 0;
        if (!empty($arrParams['tables'])) {
            if ($arrParams['tables'] == "Matching") {
                foreach ($arrAll as $key => $value) {
                    $flagFieldCompare = $value['sheetField'] != $value['dbField'];
                    $flagTypeCompare = $value['sheettypesize'] != $value['dbtypesize'];
                    $flagNullCompare = $value['sheetNull'] != $value['dbNull'];
                    $flagPrimaryCompare = $value['SheetPrimary'] != $value['DbPrimary'];
                    $flagForeginCompare = $value['SheetForeign'] != $value['DbForeign'];
                    if ($flagFieldCompare || $flagTypeCompare || $flagNullCompare || $flagPrimaryCompare || $flagForeginCompare) {
                        unset($arrAll[$key]);
                    }
                }
            }
        }

        //  non-matching

        $totalColumnMatchCount = 0;
        if (!empty($arrParams['tables'])) {
            if ($arrParams['tables'] == "Non-Matching") {
                foreach ($arrAll as $key => $value) {
                    $flagFieldCompare = $value['sheetField'] == $value['dbField'];
                    $flagTypeCompare = $value['sheettypesize'] == $value['dbtypesize'];
                    $flagNullCompare = $value['sheetNull'] == $value['dbNull'];
                    $flagPrimaryCompare = $value['SheetPrimary'] == $value['DbPrimary'];
                    $flagForeginCompare = $value['SheetForeign'] == $value['DbForeign'];
                    if ($flagFieldCompare && $flagTypeCompare && $flagNullCompare && $flagPrimaryCompare && $flagForeginCompare) {
                        unset($arrAll[$key]);
                    }
                }
            }
        }

        //  Action for columns
        //  Actions for tables which are in Sheet and Database
        $arrActionDbSheet = $objDbCompare->getActionDbSheet();
        //  Actions for tables which are in Db but, not in Sheet
        $arrActionDbNoSheet = $objDbCompare->getActionDbNoSheet();
        //  Actions for tables which are in Sheet but, not in Db
        $arrActionNoDbSheet = $objDbCompare->getActionNoDbSheet();

        // unsetting common fields by-default
        if (empty($common_field)) {
            $strPrevious = '';
            $arrCountTemp = [];
//            $countTemp = 1;
            $arrCommonResponse = $arrResult["arrCommonResponse"];
            // inside below part code is for column count per table
            foreach ($arrAll as $key => $value) {
                $arrCommonResponseColumnName = ArrayHelper::getColumn($arrCommonResponse, "str_table_column");
                if (in_array($value["str_table_column"], $arrCommonResponseColumnName)) {
                    unset($arrAll[$key]);
                }

                // column count per table
//                foreach ($value as $subKey => $subValue){
//                    if($value["str_table"] != $strPrevious){
//                        $strPrevious = $value["str_table"];
//                        $arrCountTemp[$strPrevious]++ ;
//                    }else{
//                        $arrCountTemp[$strPrevious] = 0;
//                    }
//                }
            }
        }
        $temp = 0;
        // total column count
        $totalColumnCountPerTable = ArrayHelper::getColumn($arrAll, 'str_table');
        // total sheet column count
        $totalSheetCountPerTable = ArrayHelper::getColumn($arrAll, function($data) {
                    if (!empty($data['sheetField']) && !empty($data['str_table']) ) {
                        return $data['str_table'];
                    }
                });
        // total db column count
        $totalDbCountPerTable = ArrayHelper::getColumn($arrAll, function($data) {
                    if (!empty($data['dbField']) && !empty($data['str_table']) ) {
                        return $data['str_table'];
                    }
                });
        $totalColumnCountPerTable = (array_count_values(array_filter($totalColumnCountPerTable)));
        $totalSheetCountPerTable = (array_count_values(array_filter($totalSheetCountPerTable)));
        $totalDbCountPerTable = (array_count_values(array_filter($totalDbCountPerTable)));
        

        $dataProvider = new ArrayDataProvider([
            'allModels' => $arrAll,
//            'arrMatchTypeToShow'=>$arrResult['arrMatchTypeToShow'],
            'pagination' => false,
        ]);
        $arrCommonOrNot = array_map(function($value) {
            if ((!empty($value["dbField"]) && !empty($value["sheetField"])) && ($value["dbField"] == $value["sheetField"])) {
                return 1;
            }
            return 0;
        }, $arrAll);

        $arrCount = [
            'totalColumnCount' => count($arrAll),
            'totalColumnDbCount' => count(array_filter(ArrayHelper::getColumn($arrAll, "dbField"))),
            'totalColumnSheetCount' => count(array_filter(ArrayHelper::getColumn($arrAll, "sheetField"))),
            'totalColumnMatchCount' => count(array_filter($arrCommonOrNot)),
            'totalColumnNonMatchCount' => count($arrAll) - count(array_filter($arrCommonOrNot)),
            'totalColumnCountPerTable' => $totalColumnCountPerTable,
            'totalSheetCountPerTable' => $totalSheetCountPerTable,
            'totalDbCountPerTable' => $totalDbCountPerTable,
        ];
        return $this->render('table-field-info', [
                    'tables' => true,
                    'arrSheetTitles' => $arrResult['arrSheetTitle'],
                    'dataProvider' => $dataProvider,
                    'arrTablesFields' => $arrResult['arrTablesFields'],
                    'strTableToShow' => $strTableToShow,
                    'arrMatchTypeToShow' => $arrMatchTypeToShow,
                    'sheet_db' => $sheet_db,
                    'arrCount' => $arrCount,
                    'arrpreferenceOption' => $arrpreferenceOption,
                    'preferenceOption' => $preferenceOption,
                    'arrActionDbSheet' => $arrActionDbSheet,
                    'arrActionDbNoSheet' => $arrActionDbNoSheet,
                    'arrActionNoDbSheet' => $arrActionNoDbSheet,
                    'arrTableAction' => $arrTableAction,
                    'common_field' => $common_field,
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionCompareTables() {

        $strTableSearched = NULL;
        $arrParams = Yii::$app->request->queryParams;
        $filename = "commonFile.json";
        $arrTableAction = [];
        $sheet_db = 0;
        $strTableToShow = '';
        $preferenceOption = '';
        $arrKeyToKeep = [];
        $arrAll = [];
        $objDbCompare = new DbCompare();
        $arrResult = $this->compareSheetDb();
        $arrpreferenceOption = $objDbCompare->getComparisonType();
        $arrActionDbSheet = $objDbCompare->getActionDbSheet();
        //  Actions for tables which are in Db but, not in Sheet
        $arrActionDbNoSheet = $objDbCompare->getActionDbNoSheet();
//  Actions for tables which are in Sheet but, not in Db
        $arrActionNoDbSheet = $objDbCompare->getActionNoDbSheet();
        if (!empty($arrParams["sheet_db"])) {
            $sheet_db = $arrParams["sheet_db"];
        }
        if (!empty($arrParams["sheet"])) {
            $sheet = $arrParams["sheet"];
        }
        if (!empty($arrParams['tables'])) {
            $strTableToShow = $arrParams['tables'];
        }
        if (!empty($arrParams['preferenceOption'])) {
            $preferenceOption = $arrParams['preferenceOption'];
        }
        //  getting json data and rendering (transfering) that data to index page
        if (file_exists($filename)) {
            $strJson = file_get_contents($filename);
            $arrTableAction = Json::decode($strJson, true);
            $arrDatabaseDetail = $objDbCompare->getDatabaseName();
            $arrTableAction = $arrTableAction[$arrDatabaseDetail['dbName']]['tables'];
        }
        
        if (!empty($arrResult['arrTables'])) {
            $arrAll = $arrResult['arrTables'];
            $arrSheetsTbl = ArrayHelper::getColumn($arrAll, 'Sheet_Table');
            $arrDbTbl = ArrayHelper::getColumn($arrAll, 'Db_Table');

            //  handling Matching Non Matching Case
            if ($strTableToShow == "Matching") {
                $arrKeyToKeep = array_intersect_assoc($arrSheetsTbl, $arrDbTbl);
                $arrAll = array_intersect_key($arrAll, $arrKeyToKeep);
            }
            if ($strTableToShow == "Non-Matching") {
                $arrKeyToKeep = array_diff_assoc($arrSheetsTbl, $arrDbTbl);
                $arrAll = array_intersect_key($arrAll, $arrKeyToKeep);
            }
        }

        //  filter by column
        //  FILTER  for table names on table column
        if (!empty($arrParams['Table'])) {
            $strTableSearched = $arrParams['Table'];
            foreach ($arrAll as $key => $value) {
                if (strpos($value["Table"], $strTableSearched) === false) {
                    unset($arrAll[$key]);
                }
            }
        }

        //  Choosing data of sheet and comparing with database
        if ($preferenceOption == "sheet_to_db" && $sheet_db == 0) {
            foreach ($arrAll as $keyIndex => $valueSheetTable) {
                if (!$valueSheetTable["Sheet_Table"] == "1") {
                    unset($arrAll[$keyIndex]);
                }
            }
        }

        //  Choosing data of database and comparing with sheet
        if ($preferenceOption == "db_to_sheet" && $sheet_db == 0) {
            foreach ($arrAll as $keyIndex => $valueSheetTable) {
                if (!$valueSheetTable["Db_Table"] == "1") {
                    unset($arrAll[$keyIndex]);
                }
            }
        }

        //  Choosing both data of database and sheet
        //  if preference is left blank (empty) make $arrAll array empty
        if (empty($preferenceOption)) {
            $arrAll = [];
            $arrDbTbl = [];
            $arrSheetsTbl = [];
        }
    //  checking for common and not common tables from database and sheet and filling in $arrCommonOrNot
        $arrCommonOrNot = array_map(function($value) {
            if (!empty($value["Db_Table"]) && !empty($value["Sheet_Table"])) {
                return 1;
            }
            return 0;
        }, $arrAll);

        $arrDbCount = array_map(function($value) {
            if (!empty($value["Db_Table"])) {
                return 1;
            }
            return 0;
        }, $arrAll);

        $arrSheetCount = array_map(function($value) {
            if (!empty($value["Sheet_Table"])) {
                return 1;
            }
            return 0;
        }, $arrAll);

        $arrCount = [
            'totalCount' => count($arrAll),
            'totalDbCount' => count(array_filter($arrDbCount)),
            'totalSheetCount' => count(array_filter($arrSheetCount)),
            'totalMatchCount' => count(array_filter($arrCommonOrNot)),
            'totalNonMatchCount' => count(($arrCommonOrNot)) - count(array_filter($arrCommonOrNot)),
        ];
        $dataProvider = new ArrayDataProvider([
            'allModels' => $arrAll,
//            'pagination' => false,
        ]);
        return $this->render('index', [
                    'tables' => true,
                    'arrSheetTitles' => $arrResult['arrSheetTitle'],
                    'dataProvider' => $dataProvider,
                    'arrMatchTypeToShow' => $arrResult['arrMatchTypeToShow'],
                    'arrTablesFields' => $arrResult['arrTablesFields'],
                    'arrCount' => $arrCount,
                    'arrpreferenceOption' => $arrpreferenceOption,
                    'preferenceOption' => $preferenceOption,
                    'arrTableAction' => $arrTableAction,
                    'arrActionDbSheet' => $arrActionDbSheet,
                    'arrActionDbNoSheet' => $arrActionDbNoSheet,
                    'arrActionNoDbSheet' => $arrActionNoDbSheet,
                    'strTableToShow' => $strTableToShow,
                    'strTableSearched' => $strTableSearched,
                    'sheet_db' => $sheet_db,
                    'sheet' => $sheet,
        ]);
    }

    private function compareSheetDb() {
        //  Used for having unlimited loading time.
        ini_set('max_execution_time', -1);

        $strResponse = [];
        $arrTable = [];
        $arrSheetTitle = [];
        $arrSheetTitle['all'] = 'All';
        $arrFieldNameFoundInDbNotInSheet = [];
        $arrNonExsistingTableInEitherSide = [];
        $arrTableFoundInSheet = [];
        $objDbCompare = new DbCompare();
        $arrParams = Yii::$app->request->queryParams;
        if (!empty($arrParams['sheet'])) {
            $sheet = $arrParams['sheet'];
        }
        if (!empty($arrParams['tableShow'])) {
            $tableShow = $arrParams['tableShow'];
        }

        $field_tbl = "str_table";
        $field_tbl_column = "str_table_column";
        $field_tbl_column_flag = "str_table_column_flag";
        $arrSheets = $objDbCompare->getSheets();


        if (empty($arrSheets)) {
            echo 'No data Found';
        }
//        /*
//         * Common fields
//         */
        foreach ($arrSheets as $sheets) {
            if ("Common Fields" == $sheets->properties->title) {
                $arrSheetTitle[$sheets->properties->title] = $sheets->properties->title;
                $strSheetRange = $sheets->properties->title . '!A2:G';
                $arrResponse = $objDbCompare->getService()->spreadsheets_values->get($objDbCompare->getSheetId(), $strSheetRange);
                $arrCommonFieldData = $arrResponse->values;
                array_unshift($arrCommonFieldData, ["all_table"]);
                $arrCommonFieldData = array_filter($arrCommonFieldData);

                //  $arrCommonFieldData contains all data of sheet    
                // Traversing through the Sheet
                $counterTemp = 0;
                foreach ($arrCommonFieldData as $key => $recData) {

                    // For Setting Empty Fields
                    for ($counter = 0; $counter <= 6; $counter++) {
                        if (empty($recData[$counter])) {
                            $recData[$counter] = '';
                        }
                    }

                    //  filling data into variable by list
                    list($strTableName, $strFieldName, $strFieldType, $strFieldSize, $flagAllowedNull, $strKeyType, $strRemarks) = $recData;
                    $strTableName = trim($strTableName);
                    if (!empty($strTableName)) {
                        $strTempTableName = $strTableName;
                    } else {
                        $arrCommonResponse[$counterTemp]["str_table"] = $strTempTableName;
                        $arrCommonResponse[$counterTemp]["str_table_column"] = $strFieldName;
                        $arrCommonResponse[$counterTemp]["type"] = $strFieldType;
                        $arrCommonResponse[$counterTemp]["size"] = $strFieldSize;
                        $arrCommonResponse[$counterTemp]["null"] = $flagAllowedNull;
                        $arrCommonResponse[$counterTemp]["primary_key"] = $strKeyType;
                        $arrCommonResponse[$counterTemp]["foreign_key"] = $strRemarks;
                        $counterTemp++;
                    }
                }
            }
        }

//    // unsetting dummy name = "all_table" from $arrCommonFieldData 
//        $arrTempCommonFieldData = [];
//        if(($arrCommonFieldData[0])){
//            $arrTempCommonFieldData = array_flip($arrCommonFieldData[0]);
//            if(array_key_exists("all_table", $arrTempCommonFieldData)){
//                unset($arrTempCommonFieldData["all_table"]);
//            }
//        }
//        
//echo "<pre>";            print_r($arrCommonFieldData);exit;
////        echo "<pre."
        /*
         * for all sheets except "Common Sheet"
         */

// Sheet List 
        foreach ($arrSheets as $sheets) {
            //  Getting spreadsheet title
            $arrSheetTitle[$sheets->properties->title] = $sheets->properties->title;
        }

//  Table to Show List
        $arrMatchTypeToShow = $objDbCompare->getActionMatchTypeToShow();
//  Fetching all table names from database
        $arrDatabaseTableName = $objDbCompare->getDatabaseTableNames();

        foreach ($arrSheets as $sheets) {
//             if db to sheet
            if (!empty($arrParams["preferenceOption"]) && $arrParams["preferenceOption"] == "db_to_sheet") {
                $strSheetRange = $sheets->properties->title . '!A2:G';
            } else {
                if (empty($sheet)) {
                    continue;
                }
                if (!empty($sheet)) {
                    if (in_array($sheets->properties->title, array_values($sheet))) {
                        $strSheetRange = $sheets->properties->title . '!A2:G';
                    } else {
                        continue;
                    }
                }
            }
            $arrResponse = $objDbCompare->getService()->spreadsheets_values->get($objDbCompare->getSheetId(), $strSheetRange);
            $arrData = $arrResponse->values;
//            
//echo "<pre>";            print_r($arrData);exit;
//  $arrData contains all data of sheet    
// Traversing through the Sheet
            foreach ($arrData as $key => $recData) {

                // For Setting Empty Fields
                for ($counter = 0; $counter <= 6; $counter++) {
                    if (empty($recData[$counter])) {
                        $recData[$counter] = '';
                    }
                }

                //  filling data into variable by list
                list($strTableName, $strFieldName, $strFieldType, $strFieldSize, $flagAllowedNull, $strKeyType, $strRemarks) = $recData;
                $strTableName = trim($strTableName);
                //  Fetching those table which are common in sheet
                if (!empty($strTableName) && strpos($strTableName, "tbl_") !== FALSE) {
                    $arrTable = Yii::$app->db->getTableSchema($strTableName);
                }
                // Check Table Exists or Not
                if (!empty($strTableName)) {
                    //  Checking wheather table conatins column(s)
                    if (empty($arrTable)) {
                        $arrTableFoundInSheet[] = $strTableName;
                        $arrNonExsistingTableInEitherSide[$strTableName][0] = 'Table "' . $strTableName . '" does not Exists in Database<br>';
                        $currentTblName = '';
                        //        continue;
                    } else {
                        $arrTableFoundInSheet[] = $strTableName;
                        $currentTblName = $strTableName;
                    }
                }
                // !empty
                $strSheetField = '';
                $strDbField = '';

                if ((!empty($currentTblName)) && ((!empty($strFieldName)))) {

                    // Check in Database $strFieldName exists or not
                    if (empty($arrTable->columns[$strFieldName])) {
                        $strSheetField = $strFieldName;
                        $strDbField = '';

                        //continue;
                    } else {
                        $strSheetField = $strFieldName;
                        $strDbField = $strFieldName;
                        $arrFieldNameFoundInDbNotInSheet[$currentTblName][] = $strFieldName;
                        $strResponse[$currentTblName][$strFieldName] = $strFieldName;
                    }

                    // Type Size
                    // Check in Database $strFieldType and $strFieldSize exits or not
                    if (!empty($strFieldType)) {
                        $strFieldType = $objDbCompare->getFieldType($strFieldType, $strFieldSize);
//                        echo "<pre>";print_r($strFieldType);exit;
                    }

                    $strDbType = '';
                    if (!empty($arrTable->columns[$strFieldName]->dbType)) {
                        $strDbType = $arrTable->columns[$strFieldName]->dbType;
                    }

                    $dbNull = '';
                    $sheetNull = '';
                    $strSheetPrimary = '';
                    $strSheetForeign = '';
                    $strDbPrimary = '';
                    $strDbForeign = '';
                    $strDbForeignConstraint = '';
                    //  Checking Not Null
                    if (!empty($arrTable->columns[$strFieldName])) {
                        $dbNull = $arrTable->columns[$strFieldName]->allowNull;
                        if (empty($dbNull)) {
                            $dbNull = 'No';
                        } else {
                            $dbNull = 'Yes';
                        }
                    }
                    if (!empty($flagAllowedNull)) {
                        if (strtolower($flagAllowedNull) == "yes") {
                            $sheetNull = 'Yes';
                        } else {
                            $sheetNull = 'No';
                        }
                    }

                    // Case 1: Sheet-> P, Db-> NP
                    if (!empty($arrTable->columns[$strFieldName])) {
                        // Case : Primary
                        if ($arrTable->columns[$strFieldName]->isPrimaryKey) {
                            $strDbPrimary = 'Yes';
                        }
                    }
                    if (strtolower($strKeyType) == "primary") {
                        $strSheetPrimary = 'Yes';
                    }
                    if (strtolower($strKeyType) == "foreign") {
                        $strSheetForeign = 'Yes';
                    }


                    // check for foreign keys
                    $arrForeignKey = $arrTable->foreignKeys;
                    if (!empty($arrForeignKey) && strtolower($strKeyType) == "foreign") {
                        foreach ($arrForeignKey as $key => $value) {
                            $value = array_map(function($value) {
                                return implode('', array_filter(array_keys($value)));
                            }, $arrForeignKey);

                            if (!in_array(strtolower($strFieldName), $value)) {
                                $strDbForeign = 'No';
                                $strDbForeignConstraint = $key;
                            } else if (strtolower($strKeyType) == "foreign") {
                                $strDbForeign = 'Yes';
                                $strDbForeignConstraint = $key;
                            }
                        }
                    }

                    $strResponse[$currentTblName][$strFieldName . "#DbPrimary"] = $strDbPrimary;
                    $strResponse[$currentTblName][$strFieldName . "#SheetPrimary"] = $strSheetPrimary;
                    $strResponse[$currentTblName][$strFieldName . "#DbForeign"] = $strDbForeign;
                    $strResponse[$currentTblName][$strFieldName . "#DbForeignConstraint"] = $strDbForeignConstraint;
                    $strResponse[$currentTblName][$strFieldName . "#SheetForeign"] = $strSheetForeign;
                    $strResponse[$currentTblName][$strFieldName . "#sheetField"] = $strSheetField;
                    $strResponse[$currentTblName][$strFieldName . "#dbField"] = $strDbField;
                    $strResponse[$currentTblName][$strFieldName . "#dbtypesize"] = $strDbType;
                    $strResponse[$currentTblName][$strFieldName . "#sheettypesize"] = $strFieldType;
                    $strResponse[$currentTblName][$strFieldName . "#sheetNull"] = $sheetNull;
                    $strResponse[$currentTblName][$strFieldName . "#dbNull"] = $dbNull;
                } else {
                    continue;
                }
            }
        }
//  Column in database but not in sheets
        if (!empty($arrFieldNameFoundInDbNotInSheet)) {
            foreach ($arrFieldNameFoundInDbNotInSheet as $key => $value) {
                foreach ($value as $subKey => $subValue) {
                    $arrColumnNames = Yii::$app->db->getTableSchema($key)->columnNames;
                    $arrColumnNotInSheet[$key] = array_diff($arrColumnNames, $value);
                }
            }
        }

//  Iterating only those column which are in database
        if (!empty($arrColumnNotInSheet)) {
            $arrCommonCols = ArrayHelper::getColumn($arrCommonResponse, 'str_table_column', true);


            foreach ($arrColumnNotInSheet as $key => $value) {
                $arrTable = Yii::$app->db->getTableSchema($key);
                foreach ($value as $subKey => $subValue) {
//                     echo '<pre>';                     var_dump(array_key_exists($subValue, array_flip($arrCommonCols));exit;
                    $dbNull = '';
                    $strSheetPrimary = '';
                    $strSheetForeign = '';
                    $strDbPrimary = '';
                    $strDbForeign = '';
                    $strDbForeignConstraint = '';
                    $strDbType = '';
                    if (!array_key_exists($subValue, array_flip($arrCommonCols))) {

                        $strFieldName = $subValue;
//                        echo $subValue."<br />";
//                        $strFieldType = $arrCommonCols[key($subValue)]['sheettypesize'];
                        // Check for  Field Type
                        if (!empty($strFieldType)) {
                            $strFieldType = $objDbCompare->getFieldType($strFieldType, $strFieldSize);
                        }

                        // Check for db Type
                        if (!empty($arrTable->columns[$strFieldName]->dbType)) {
                            $strDbType = $arrTable->columns[$strFieldName]->dbType;
                        }



                        if (!empty($arrTable->columns[$subValue])) {
                            $dbNull = $arrTable->columns[$subValue]->allowNull;
                            if (empty($dbNull)) {
                                $dbNull = 'No';
                            } else {
                                $dbNull = 'Yes';
                            }
                        }

                        // check for Primary keys
                        if (!empty($arrTable->columns[$strFieldName])) {
                            // Case : Primary
//                $strDbPrimary = 'No';
                            if ($arrTable->columns[$strFieldName]->isPrimaryKey) {
                                $strDbPrimary = 'Yes';
                            }
                        }

                        // check for foreign keys
                        $arrForeignKey = $arrTable->foreignKeys;
                        if (!empty($arrForeignKey)) {
                            foreach ($arrForeignKey as $key1 => $value1) {
                                $value = array_map(function($value1) {
                                    return implode('', array_filter(array_keys($value1)));
                                }, $arrForeignKey);

                                if (in_array(strtolower($subValue), $value1)) {
                                    $strDbForeign = 'Yes';
                                    $strDbForeignConstraint = $key1;
                                }
                            }
                        }
                    } else {
                        // Type Size
                        // 
                        // Check in Database $strFieldType and $strFieldSize exits or not
                        $strFieldName = $subValue;

                        $strFieldType = $arrCommonResponse[array_search($strFieldName, $arrCommonCols)]['type'];
                        $strFieldSize = $arrCommonResponse[array_search($strFieldName, $arrCommonCols)]['size'];
                        $flagAllowedNull = $arrCommonResponse[array_search($strFieldName, $arrCommonCols)]['null'];
                        // Check for  Field Type
                        if (!empty($strFieldType)) {
//                            echo "<pre>";            print_r(strtolower($strFieldType) == "smallint");exit;
                            $strFieldType = $objDbCompare->getFieldType($strFieldType, $strFieldSize);
//                             echo "<pre>";            print_r($strFieldType.$strFieldSize);exit;
                        }
//                        echo $strFieldType;exit;
                        // Check for db Type
                        if (!empty($arrTable->columns[$strFieldName]->dbType)) {
                            $strDbType = $arrTable->columns[$strFieldName]->dbType;
                        }

                        // Checking Db Not Null
                        if (!empty($arrTable->columns[$strFieldName])) {
                            $dbNull = $arrTable->columns[$strFieldName]->allowNull;
                            if (empty($dbNull)) {
                                $dbNull = 'No';
                            } else {
                                $dbNull = 'Yes';
                            }
                        }

                        // Checking Sheet Not Null
                        if (!empty($flagAllowedNull)) {
                            if (strtolower($flagAllowedNull) == "yes") {
                                $sheetNull = 'Yes';
                            } else {
                                $sheetNull = 'No';
                            }
                        }

                        // Case 1: Sheet-> P, Db-> NP
                        if (!empty($arrTable->columns[$strFieldName])) {
                            // Case : Primary
                            if ($arrTable->columns[$strFieldName]->isPrimaryKey) {
                                $strDbPrimary = 'Yes';
                            }
                        }
                        if (strtolower($strKeyType) == "primary") {
                            $strSheetPrimary = 'Yes';
                        }
                        if (strtolower($strKeyType) == "foreign") {
                            $strSheetForeign = 'Yes';
                        }


                        // Check for foreign keys
                        $arrForeignKey = $arrTable->foreignKeys;
                        if (!empty($arrForeignKey) && strtolower($strKeyType) == "foreign") {
                            foreach ($arrForeignKey as $key => $value) {
                                $value = array_map(function($value) {
                                    return implode('', array_filter(array_keys($value)));
                                }, $arrForeignKey);

                                if (!in_array(strtolower($strFieldName), $value)) {
                                    $strDbForeign = 'No';
                                    $strDbForeignConstraint = $key;
                                } else if (strtolower($strKeyType) == "foreign") {
                                    $strDbForeign = 'Yes';
                                    $strDbForeignConstraint = $key;
                                }
                            }
                        }
                    }


                    $strResponse[$key][$subValue . "#Field"] = 'No/Not Exists in Sheet';
                    $strResponse[$key][$subValue . "#sheetField"] = $strFieldName;              #
                    $strResponse[$key][$subValue . "#dbField"] = $subValue;
                    $strResponse[$key][$subValue . "#dbtypesize"] = $strDbType;
                    $strResponse[$key][$subValue . "#sheettypesize"] = $strFieldType;           #
                    $strResponse[$key][$subValue . "#sheetNull"] = $sheetNull;                  #
                    $strResponse[$key][$subValue . "#dbNull"] = $dbNull;
                    $strResponse[$key][$subValue . "#DbPrimary"] = $strDbPrimary;
                    $strResponse[$key][$subValue . "#SheetPrimary"] = $strSheetPrimary;
                    $strResponse[$key][$subValue . "#DbForeign"] = $strDbForeign;
                    $strResponse[$key][$subValue . "#DbForeignConstraint"] = $strDbForeignConstraint;
                    $strResponse[$key][$subValue . "#SheetForeign"] = $strSheetForeign;
                }
            }
        }

        $previousKey = '';
        $arrRes = $objDbCompare->processResponse($strResponse);
        $arrTableInBoth = array_unique(array_merge($arrTableFoundInSheet, $arrDatabaseTableName));
        $arrNew = $objDbCompare->getComparedTables($arrTableInBoth, $arrTableFoundInSheet, $arrDatabaseTableName);

        return [
            'arrSheetTitle' => $arrSheetTitle,
            'arrTables' => $arrNew,
            'arrTablesFields' => $arrRes,
            'arrMatchTypeToShow' => $arrMatchTypeToShow,
            'arrCommonResponse' => $arrCommonResponse,
        ];
    }

    /*
     * Action Fiter Form
     */
//    public function actionFilter(){
//        $model = new FilterForm;
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            return $this->render('index', ['model' => $model]);
//        } 
    //        else {
//            return $this->render('entry', ['model' => $model]);
//        }
//    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /*
     * Saving DropDown Response in JSON
     */

    public function actionSaveResponse() {
        if(!Yii::$app->request->isAjax){
            return 'Fail';
        }
        $tableName = '';
        $tableAction = '';
        $strJson = '';
        $arrJson = [];
        $filename = "commonFile.json";
        $objDbCompare = new DbCompare();
        $arrPost = Yii::$app->request->post();
//        echo "<pre>";print_r($arrPost);exit; 
        
        $dbDetails = $objDbCompare->getDatabaseName();
        $db_type = $dbDetails["db_type"];
        $dsn = $dbDetails["dsn"];
        $dbName = $dbDetails["dbName"];
        
        $arrDbJson["dbType"] = $db_type;
        $arrDbJson["dsn"] = $dsn;
        $arrDbJson["name"] = $dbName;
        
        if (empty(filesize($filename))) {
            $arrDbinfo = $arrDbJson;
            $arrTblinfo = [];
            $arrColinfo = [];
        } else {
            $arrDbinfo = $arrDbJson;
            $arrTblinfo = [];
            $arrColinfo = [];
        }
        
        if (!empty($arrPost['tableName'])) {

//        echo $arrPost['tableName'];exit;    
            if (file_exists($filename)) {
                $strJson = file_get_contents($filename);
                $arrJson = Json::decode($strJson, true);
//    ECHO $strJson;exit;
                if (!empty($arrPost['tableName'])) {
                    $tableName = $arrPost['tableName'];
                }

                if (!empty($arrPost['txtAction'])) {
                    $tableAction = $arrPost['txtAction'];
                }

                if (!empty($tableName) && !empty($tableAction)) {
                    $arrJson[$dbName]["tables"][$tableName] = $tableAction;
                }

                if (!empty($tableName) && empty($tableAction)) {
                    unset($arrJson[$dbName]["tables"][$tableName]);
                }

                if (!empty($arrPost['txtAction']) && $arrPost['txtAction'] == "done") {
                    unset($arrJson[$dbName]["tables"][$tableName]);
                }
            }
        } else {
               
            if (file_exists($filename)) {
                $strJson = file_get_contents($filename);
                $arrJson = Json::decode($strJson, true);
                if (!empty($arrPost['tableColumnName'])) {
                    $tableName = $arrPost['tableColumnName'];
                }

                if (!empty($arrPost['txtAction'])) {
                    $tableAction = $arrPost['txtAction'];
                }

                if (!empty($tableName) && !empty($tableAction)) {
                    $arrJson[$dbName]["columns"][$tableName] = $tableAction;
                }

                if (!empty($tableName) && empty($tableAction)) {
                    unset($arrJson[$dbName]["columns"][$tableName]);
                }

                if (!empty($arrPost['txtAction']) && $arrPost['txtAction'] == "done") {
                    unset($arrJson[$dbName]["columns"][$tableName]);
                }
            }
        }
        
        $arrJson[$dbName]["dbinfo"] = $arrDbinfo;
        $strJson = json_encode($arrJson,JSON_PRETTY_PRINT);
        file_put_contents($filename, $strJson);

        return 'Successfully saved';
    }

    /*
     * Table Report
     */

    public function actionTableReport() {
        // Getting request
        $arrParams = Yii::$app->request->queryParams;
        $arrDatabase = [];
        $arrAllDatabaseName = [];
        $fileName = "commonFile.json";
        $arrReport = [];
        $counter = 0;
        //  Calculating counts
        $count_add_to_database = 0;
        $count_modify_in_both = 0;
        $count_add_to_sheet = 0;
        $count_modify_in_database = 0;
        $count_modify_in_sheet = 0;
        $count_remove_from_both = 0;
        $count_remove_from_database = 0;
        $count_remove_from_sheet = 0;
        
    // reading data from json file
        $arrJson = file_get_contents($fileName);
        $arrTableReport = Json::decode($arrJson);
        
    // making all databases array
        $objDbCompare = new DbCompare();
        $arrAllDatabaseName = $objDbCompare->getAllDatabaseNameFromJson($arrTableReport);
        
        if(!empty($arrParams['database'])){
            $arrDatabase = $arrParams['database'];
        }else{
            $arrDatabase = $arrAllDatabaseName;
        }
        
        foreach ($arrDatabase as $key => $value){
            $dbName = $value;
            if (!empty($arrTableReport[$dbName]["tables"])) {
                // making array of json having index (key) table and action array
                foreach ($arrTableReport[$dbName]["tables"] as $key => $value) {
                    $arrReport[$counter]["db"] = $dbName;
                    $arrReport[$counter]["table"] = $key;
                    $arrReport[$counter]["action"] = $value;
                    $counter++;
                }

                foreach ($arrTableReport[$dbName]["tables"] as $tableName => $action) {
                    if ($action == "add_to_database") {
                        $count_add_to_database++;
                    }
                    if ($action == "add_to_sheet") {
                        $count_add_to_sheet++;
                    }
                    if ($action == "modify_in_both") {
                        $count_modify_in_both++;
                    }
                    if ($action == "modify_in_database") {
                        $count_modify_in_database++;
                    }
                    if ($action == "modify_in_sheet") {
                        $count_modify_in_sheet++;
                    }
                    if ($action == "remove_from_both") {
                        $count_remove_from_both++;
                    }
                    if ($action == "remove_from_database") {
                        $count_remove_from_database++;
                    }
                    if ($action == "remove_from_sheet") {
                        $count_remove_from_sheet++;
                    }
                }
            }
        }
        foreach ($arrReport as $key => $value) {
            // filter on table
            if (!empty($arrParams["table"])) {
                if (strpos($value["table"], $arrParams["table"]) === false) {
                    unset($arrReport[$key]);
                }
            }

            // filter on table
            if (!empty($arrParams["action"])) {
                if (strpos($value["action"], $arrParams["action"]) === false) {
                    unset($arrReport[$key]);
                }
            }
        }
        
        $count_total_action_report = $count_add_to_database + $count_modify_in_both + $count_add_to_sheet + $count_modify_in_database + $count_modify_in_sheet + $count_remove_from_both + $count_remove_from_database + $count_remove_from_sheet;

        $arrCount = [
            'count_add_to_database' => $count_add_to_database,
            'count_modify_in_both' => $count_modify_in_both,
            'count_add_to_sheet' => $count_add_to_sheet,
            'count_modify_in_database' => $count_modify_in_database,
            'count_modify_in_sheet' => $count_modify_in_sheet,
            'count_remove_from_both' => $count_remove_from_both,
            'count_remove_from_database' => $count_remove_from_database,
            'count_remove_from_sheet' => $count_remove_from_sheet,
            'count_total_action_report' => $count_total_action_report,
        ];
//echo "<pre>";print_r($arrReport);exit;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $arrReport,
            'pagination' => false,
        ]);

        return $this->render('table-report', [
                    'dataProvider' => $dataProvider,
                    'arrCount' => $arrCount,
                    'arrAllDatabaseName' => $arrAllDatabaseName,
                    'database' => $arrDatabase,
        ]);
    }

    /*
     * Table Column Report
     */

    public function actionTableColumnReport() {
 // Getting request
        $arrParams = Yii::$app->request->queryParams;
        $arrDatabase = [];
        $arrAllDatabaseName = [];
        $fileName = "commonFile.json";
        $arrReport = [];
        $counter = 0;
        //  Calculating counts
        $count_add_to_database = 0;
        $count_modify_in_both = 0;
        $count_add_to_sheet = 0;
        $count_modify_in_database = 0;
        $count_modify_in_sheet = 0;
        $count_remove_from_both = 0;
        $count_remove_from_database = 0;
        $count_remove_from_sheet = 0;
        
    // reading data from json file
        $arrJson = file_get_contents($fileName);
        $arrTableReport = Json::decode($arrJson);
        
    // making all databases array
        $objDbCompare = new DbCompare();
        $arrAllDatabaseName = $objDbCompare->getAllDatabaseNameFromJson($arrTableReport);
        
        if(!empty($arrParams['database'])){
            $arrDatabase = $arrParams['database'];
        }else{
            $arrDatabase = $arrAllDatabaseName;
        }
        
        foreach ($arrDatabase as $key => $value){
            $dbName = $value;
            if (!empty($arrTableReport[$dbName]['columns'])) {
                // making array of json having index (key) table and action array
                foreach ($arrTableReport[$dbName]['columns'] as $key => $value) {
                    $arrTableAndColumn = explode("#", $key);
                    $arrReport[$counter]["db"] = $dbName;
                    $arrReport[$counter]["table"] = $arrTableAndColumn[0];
                    $arrReport[$counter]["column"] = $arrTableAndColumn[1];
                    $arrReport[$counter]["action"] = $value;
                    $counter++;
                }
                foreach ($arrTableReport[$dbName]['columns'] as $tableName => $action) {
                    if ($action == "add_to_database") {
                        $count_add_to_database++;
                    }
                    if ($action == "add_to_sheet") {
                        $count_add_to_sheet++;
                    }
                    if ($action == "modify_in_both") {
                        $count_modify_in_both++;
                    }
                    if ($action == "modify_in_database") {
                        $count_modify_in_database++;
                    }
                    if ($action == "modify_in_sheet") {
                        $count_modify_in_sheet++;
                    }
                    if ($action == "remove_from_both") {
                        $count_remove_from_both++;
                    }
                    if ($action == "remove_from_database") {
                        $count_remove_from_database++;
                    }
                    if ($action == "remove_from_sheet") {
                        $count_remove_from_sheet++;
                    }
                }
            }
        }
        foreach ($arrReport as $key => $value) {
            // filter on table
            if (!empty($arrParams["table"])) {
                if (strpos($value["table"], $arrParams["table"]) === false) {
                    unset($arrReport[$key]);
                }
            }
            // filter on column
            if (!empty($arrParams["column"])) {
                if (strpos($value["column"], $arrParams["column"]) === false) {
                    unset($arrReport[$key]);
                }
            }

            // filter on table
            if (!empty($arrParams["action"])) {
                if (strpos($value["action"], $arrParams["action"]) === false) {
                    unset($arrReport[$key]);
                }
            }
        }
        $count_total_action_report = $count_add_to_database + $count_modify_in_both + $count_add_to_sheet + $count_modify_in_database + $count_modify_in_sheet + $count_remove_from_both + $count_remove_from_database + $count_remove_from_sheet;

        $arrCount = [
            'count_add_to_database' => $count_add_to_database,
            'count_modify_in_both' => $count_modify_in_both,
            'count_add_to_sheet' => $count_add_to_sheet,
            'count_modify_in_database' => $count_modify_in_database,
            'count_modify_in_sheet' => $count_modify_in_sheet,
            'count_remove_from_both' => $count_remove_from_both,
            'count_remove_from_database' => $count_remove_from_database,
            'count_remove_from_sheet' => $count_remove_from_sheet,
            'count_total_action_report' => $count_total_action_report,
        ];
        $dataProvider = new ArrayDataProvider([
            'allModels' => $arrReport,
            'pagination' => false,
        ]);

        return $this->render('table-field-report', [
                    'dataProvider' => $dataProvider,
                    'arrCount' => $arrCount,
                    'arrAllDatabaseName' => $arrAllDatabaseName,
                    'database' => $arrDatabase,
        ]);
    }

}
