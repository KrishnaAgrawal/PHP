<?php

namespace backend\controllers;

use backend\models\DbCompare;
use common\models\LoginForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
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

        $arrResult = $this->compareSheetDb();
        $arrAll = $arrResult['arrTablesFields'];
//        echo "<pre>";print_r($arrResult);exit;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $arrAll,
//            'arrTableToShow'=>$arrResult['$arrTableToShow'],
            'pagination' => false,
        ]);
        
        return $this->render('table-field-info',[
            'tables' => true,
            'arrSheetTitles'=>$arrResult['arrSheetTitle'],
            'dataProvider' => $dataProvider,
            'arrTablesFields' =>$arrResult['arrTablesFields'],
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
        echo "<pre>";        print_r($arrParams);exit;
        $arrResult = $this->compareSheetDb();
        
        //  Tables That exists sheet(s) ;
//        foreach ($arrResult['arrTables'] as $recKey => $recValue) {
//            if (($recValue["Sheet_Table"] == "No")) {
//                unset($arrResult['arrTables'][$recKey]);
//            }
//        }
//        
        $arrAll = $arrResult['arrTables'];
        
        $arrSheetsTbl = ArrayHelper::getColumn($arrAll, 'Sheet_Table');
        $arrDbTbl = ArrayHelper::getColumn($arrAll, 'Db_Table');
       
//        print_r($arrCount);exit;
        
//  tables to show      
        $strTableToShow = null;
        $arrKeyToKeep = [];
        if(!empty($arrParams['tables'])){
            $strTableToShow = $arrParams['tables'];
            
            
            if($strTableToShow == "Matching"){
                $arrKeyToKeep = array_intersect_assoc($arrSheetsTbl, $arrDbTbl);
                $arrAll = array_intersect_key($arrAll,$arrKeyToKeep);
            }
            if($strTableToShow == "Non-Matching"){
                $arrKeyToKeep = array_diff_assoc($arrSheetsTbl, $arrDbTbl);
                $arrAll = array_intersect_key($arrAll,$arrKeyToKeep);
            }
            
            
        }
                
//  Action Array
//    $arrActionDbSheet = [
//        'remove_from_sheet' => 'Remove from Sheet',
//        'remove_from_database' => 'Remove from Database',
//        'remove_from_both' => 'Remove from Both',
//        'add_in_sheet' => 'Add in Sheet',
//        'add_in_database' => 'Add in Database',
//        'modify_in_sheet' => 'Modify in Sheet',
//        'modify_in_database' => 'Modify in Database',
//        'modify_in_both' => 'Modify in Both',
//    ];
    
    $arrActionDbSheet = [
        'modify_in_both' => 'Modify in Both',
        'modify_in_database' => 'Modify in Database',
        'modify_in_sheet' => 'Modify in Sheet',
        'remove_from_both' => 'Remove from Both',
        'remove_from_database' => 'Remove from Database',
        'remove_from_sheet' => 'Remove from Sheet'
    ];
    
    $arrActionDbNoSheet = [
        'add_to_sheet' => 'Add to Sheet',
        'modify_in_database' => 'Modify in Database',
        'remove_from_database' => 'Remove from Database',
    ];
    
    $arrActionNoDbSheet = [
        'add_to_database' => 'Add to Database',
        'modify_in_sheet' => 'Modify in Sheet',
        'remove_from_sheet' => 'Remove from Sheet',
    ];
    
        $filename = "tableData.json";
        $arrPost = \Yii::$app->request->post();
        
        
        
//        echo "<pre>";print_r($arrPost);exit;
        $arrTableAction = [];
        if(file_exists($filename)){
//            echo "111";
            $strJson = file_get_contents($filename);
            
            $arrTableAction = \yii\helpers\Json::decode($strJson,true);
        }
//  filter by column

    //  FILTER  for table names

        if(!empty($arrParams['Table'])){
            $strTableSearched = $arrParams['Table'];
            foreach ($arrAll as $key => $value){
//                            echo "<pre>";print_r($value["Table"])."<br>********";

                if(strpos($value["Table"], $arrParams['Table']) === false){

                    unset($arrAll[$key]);
                }
            }
        }
        
        
        $arrCommonOrNot = array_map(function($value){
                if($value["Db_Table"] == 1 && $value["Sheet_Table"] == 1){
                    return 1;
                }
                return 0;
            }, $arrAll);
            
        $arrCount = [
            'totalCount' => count($arrAll),
            'totalDbCount' => count(array_filter($arrDbTbl)),
            'totalSheetCount' => count(array_filter($arrSheetsTbl)),
            'totalMatchCount' => count(array_filter($arrCommonOrNot)),
            'totalNonMatchCount' => count(($arrCommonOrNot)) -  count(array_filter($arrCommonOrNot)),
        ];
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $arrAll,
//            'arrTableToShow'=>$arrResult['$arrTableToShow'],
            'pagination' => false,
        ]);
        
        return $this->render('index', [
            'tables' => true,
            'arrSheetTitles'=>$arrResult['arrSheetTitle'],
            'dataProvider' => $dataProvider,
            'arrTableToShow'=>$arrResult['arrTableToShow'],
            'arrTablesFields' =>$arrResult['arrTablesFields'],
            'arrCount' => $arrCount,
            'arrAction' => [],
            'arrTableAction'=>$arrTableAction,
            'arrActionDbSheet' => $arrActionDbSheet,
            'arrActionDbNoSheet' => $arrActionDbNoSheet,
            'arrActionNoDbSheet' => $arrActionNoDbSheet,
            'strTableToShow' => $strTableToShow,
            'strTableSearched' => $strTableSearched,
        ]);
    }

    private function compareSheetDb() {
        //  Used for having unlimited loading time.
        ini_set('max_execution_time', -1);


        $strResponse = [];
        $arrTable = [];
        $arrSheetTitle = [];
        $arrSheetTitle[] = 'All';
        $arrFieldNameFoundInDbNotInSheet = [];
        $arrNonExsistingTableInEitherSide = [];
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
// Sheet List 
        foreach ($arrSheets as $sheets) {
            //  Getting spreadsheet title
            $arrSheetTitle[$sheets->properties->title] = $sheets->properties->title;
        }

//  Table to Show List
        $arrTableToShow = [
            'All'=>"All",
            "Matching" => "Matching",
            "Non-Matching" => "Non-Matching"
            ];

    
    
        
//  Fetching all table names from database
        $arrDatabaseTableName = $objDbCompare->getDatabaseTableNames();


        foreach ($arrSheets as $sheets) {
            if (!empty($sheet) && !empty($strSheetRange)) {
                continue;
            }
            if (!empty($sheet)) {
                $strSheetRange = $sheet . '!A2:G';
            } else {
                $strSheetRange = $sheets->properties->title . '!A2:G';
            }
            $arrResponse = $objDbCompare->getService()->spreadsheets_values->get($objDbCompare->getSheetId(), $strSheetRange);
            $arrData = $arrResponse->values;

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

                //  Fetching those table which are common in sheet
                if (!empty($strTableName)) {
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
        foreach ($arrFieldNameFoundInDbNotInSheet as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $arrColumnNames = Yii::$app->db->getTableSchema($key)->columnNames;
                $arrColumnNotInSheet[$key] = array_diff($arrColumnNames, $value);
            }
        }

//  Iterating only those column which are in database
        foreach ($arrColumnNotInSheet as $key => $value) {
            $arrTable = Yii::$app->db->getTableSchema($key);
            foreach ($value as $subKey => $subValue) {
                $dbNull = '';
                $strSheetPrimary = '';
                $strSheetForeign = '';
                $strDbPrimary = '';
                $strDbForeign = '';
                $strDbForeignConstraint = '';
                if (!empty($arrTable->columns[$subValue])) {
                    $dbNull = $arrTable->columns[$subValue]->allowNull;
                    if (empty($dbNull)) {
                        $dbNull = 'No';
                    } else {
                        $dbNull = 'Yes';
                    }
                }

                // Case 1: Sheet-> P, Db-> NP
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
                $strResponse[$key][$subValue . "#Field"] = 'No/Not Exists in Sheet';
                $strResponse[$key][$subValue . "#sheetField"] = '';
                $strResponse[$key][$subValue . "#dbField"] = $subValue;
                $strResponse[$key][$subValue . "#dbtypesize"] = $arrTable->columns[$subValue]->dbType;
                $strResponse[$key][$subValue . "#sheettypesize"] = '';
                $strResponse[$key][$subValue . "#sheetNull"] = '';
                $strResponse[$key][$subValue . "#dbNull"] = $dbNull;
                $strResponse[$key][$subValue . "#DbPrimary"] = $strDbPrimary;
                $strResponse[$key][$subValue . "#SheetPrimary"] = $strSheetPrimary;
                $strResponse[$key][$subValue . "#DbForeign"] = $strDbForeign;
                $strResponse[$key][$subValue . "#DbForeignConstraint"] = $strDbForeignConstraint;
                $strResponse[$key][$subValue . "#SheetForeign"] = $strSheetForeign;
            }
        }

        $previousKey = '';
        $arrRes = $objDbCompare->processResponse($strResponse);
        $arrTableInBoth = array_unique(array_merge($arrTableFoundInSheet, $arrDatabaseTableName));
        $arrNew = $objDbCompare->getComparedTables($arrTableInBoth, $arrTableFoundInSheet, $arrDatabaseTableName);
        
        
        return [
            'arrSheetTitle'=>$arrSheetTitle,
            'arrTables' =>$arrNew,
            'arrTablesFields' =>$arrRes,
            'arrTableToShow' => $arrTableToShow,
            
           
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
    public  function actionSaveResponse(){
      
        $filename = "tableData.json";
        $arrPost = \Yii::$app->request->post();
        
        
        
//        echo "<pre>";print_r($arrPost);exit;

        if(file_exists($filename)){
//            echo "111";
            $strJson = file_get_contents($filename);
            
            $arrJson = \yii\helpers\Json::decode($strJson,true);
//            echo "<pre>11";print_r($arrJson);
//            echo "$arrJson";print_r($arrJson);
            if(!empty($arrPost['tableName'])){
                $tableName = $arrPost['tableName'];
            }
            
            if(!empty($arrPost['txtAction'])){
                $tableAction = $arrPost['txtAction'];
            }
            
            if(!empty($tableName) && !empty($tableAction)){
                $arrJson[$tableName] = $tableAction;
            }
            
            if(!empty($tableName) && empty($tableAction)){
                 unset($arrJson[$tableName]);
            }
            
        }  
     
        file_put_contents($filename, \yii\helpers\Json::encode($arrJson));
        return 'Successfully saved';
        
    }

}
