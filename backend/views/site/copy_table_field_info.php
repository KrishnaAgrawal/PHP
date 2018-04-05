
<?php

use yii\data\ArrayDataProvider;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
$this->registerCssFile(Yii::$app->request->baseurl . '/css/boostrap-4.css');

$sheet = null;
$tableShow = null;
$action = null;
$arrParams = Yii::$app->request->queryParams;
//if (!empty($arrParams['sheet'])) {
//    $sheet = $arrParams['sheet'];
//}
//if (!empty($arrParams['tableShow'])) {
//    $tableShow = $arrParams['sheet'];
//}
//if (!empty($arrParams['action'])) {
//    $tableShow = $arrParams['action'];
//}
?>

<!--<div class="card pr-0 pl-0">-->
<div class="row">
    <div class="col-sm-3">
            <div class="card-block">
                //<?php
//                $form = ActiveForm::begin(['action' => 'http://localhost/fingertips/backend/web/index.php?r=site%2Fcompare-tables', 'method' => 'get']);
//                echo Html::buttonInput("+");
//                ?>
<!--                <div class="row">
                    <div class="col-sm-6">
                        <label> Select Sheet</label>
                    </div>
                    <div class="col-sm-6">-->
                        //<?php
//                        echo Html::dropDownList("sheet", $sheet, $arrSheetTitles, ['class' => 'form-control','prompt'=>'Select-Sheet']) . "<br />";
//                        ?>
<!--                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label> Select To Show Tables</label>
                    </div>
                    <div class="col-sm-6">-->
                        //<?php
//                        echo Html::dropDownList("tables", $strTableToShow, $arrTableToShow, ['class' => 'form-control','prompt'=>'Select-Choice']) . "<br />";
//                        ?>
<!--                    </div>
                </div>
                <div class="row">
                    <div class="mx-auto col-sm-4">-->
                        //<?php
//                        echo Html::submitButton('Submit');
//                        ?>
<!--                    </div>
                </div>-->
                //<?php
//                $form = ActiveForm::end();
//                ?>
<!--            </div>
        </div>
    <div class=" col-sm-9">
        <div class="card-header">
            <span style="font-size: 1.2em; font-weight: bold;">Tables Summary</span>:<br /><br />
            <span>Total Tables: <?php // $arrCount["totalCount"] ?></span><br />
            <span>Total Tables (Sheet): <?php // $arrCount["totalSheetCount"] ?></span><br />
            <span>Total Tables (Database): <?php // $arrCount["totalDbCount"] ?></span><br />
            <span>Total number of Tables which are common: <?php // $arrCount["totalSheetCount"]  ?></span><br />
        </div>-->



        <!--<div class="card-block">-->

            <?php
//            if (!empty($tables)) {
//
////$dropdown_field = "dropdown";
////                $form = ActiveForm::begin(['action' => 'http://localhost/fingertips/backend/web/index.php?r=site%2Fcompare-tables','method' => 'get']);
//                echo backend\models\GridEasy::widget(['arrGrid'=>[
//                    'dataProvider' => $dataProvider,
//                    'filterModel' => 'false',
//                    'toolbar' =>  [
////        ['content' => 
////            Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type' => 'button', 'title' => Yii::t('kvgrid', 'Add Book'), 'class' => 'btn btn-success', 'onclick' => 'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
////            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('kvgrid', 'Reset Grid')])
////        ],
//        '{export}',
//        '{toggleData}',
//    ],
//                    
////                'rowOptions' => function ($model) {
////                if ($model['Sheet Table Name'] == $model['Db Table Name']) {
////                    return ['class' => 'bg-success'];
////                }
////                if ($model['Sheet Table Name'] != $model['Db Table Name']) {
////                    return ['class' => 'bg-danger'];
////                }
////            },
//            
//                    'columns' => [
//                        [
//                            'attribute' => 'Table Name',
//                            'header' => 'Table Name',
////                            'filter' => Html::input('text','tableName',$strTableSearched),
//                            
////                            'filterPosition' => GridView::FILTER_POS_FOOTER,
////                            function () {
////                                return Html::buttonInput($label);
////                            }
////                            'layout'=>"{sorter}\n{pager}\n{summary}\n{items}",
////                            ['class' => 'yii\grid\SerialColumn'],
////                            ['class' => 'yii\grid\ActionColumn'],
//                        ],
//                        [
//                            'attribute' => 'Sheet Table Name',
//                            'header' => 'Sheet',
////                            'filter' => Html::input('text','sheetTableName'),
//                            'value' => function($model) {
//                                if ($model['Sheet Table Name'] == '1') {
//                                    return 'Yes';
//                                } else {
//                                    return 'No';
//                                }
//                            },
//                        ],
//                        [
//                            'attribute' => 'Db Table Name',
//                            'header' => 'Database',
////                            'filter' => Html::input('text','dbTableName'),
//                            'value' => function($model) {
//                                if ($model['Db Table Name'] == '1') {
//                                    return 'Yes';
//                                } else {
//                                    return 'No';
//                                }
//                            },
//                        ],
//                        [
//                            'attribute' => '',
//                            'header' => 'Action',
//                            'format' => 'raw',
////                            'filter' => Html::submitButton('Submit'),
//                            'content' => function ($data) use($action, $arrAction) {
//                                return Html::dropDownList("Action", $action, $arrAction, ['class' => 'form-control','prompt'=>'Choose-Action']);
//                            }
//                        ],
//                    ],
//                ]]);
////                    echo Html::submitButton('Submit');
////                    $form = ActiveForm::end();
//            } else 
//            {

    //
    //                $provider = new ArrayDataProvider([
    //                    'allModels' => $arrRes,
    //                    'pagination' => false,
    //                    
    //                ]);
                echo GridView::widget([
                    'dataProvider' => $arrTablesFields,
                    'columns' => [
                        [
                            'attribute' => '',
                            'header' => 'Table',
                            'value' => $field_tbl,
                        ],
                        [
                            'attribute' => '',
                            'header' => 'Column Name',
                            'value' => $field_tbl_column,
                        ],
//        'sheetField',
//        'dbField',
                        [
                            'attribute' => '',
                            'header' => 'Column Found',
//                'filter' => Html::drop
                            'value' => function ($data) {
                                if (!empty($data['sheetField']) && !empty($data['dbField'])) {
                                    if ($data['sheetField'] == $data['dbField']) {
                                        return 'Yes';
                                    }
                                }
                                return 'Column Mismatch';
                            },
                        ],
//        'sheettypesize',
//        'dbtypesize',
                        [
                            'attribute' => '',
                            'header' => 'Type Size',
                            'value' => function ($data) {
                                if (!empty($data['sheettypesize']) && !empty($data['dbtypesize'])) {
                                    if ($data['sheettypesize'] == $data['dbtypesize']) {
                                        return 'Yes';
                                    }
                                }
                                return 'Type/Size Mismatch';
                            },
                        ],
//        'sheetNull',
//        'dbNull',
                        [
                            'attribute' => '',
                            'header' => 'Null',
                            'value' => function ($data) {
                                if (!empty($data['sheetNull']) && !empty($data['dbNull'])) {
                                    if ($data['sheetNull'] == $data['dbNull']) {
                                        return 'Yes';
                                    }
                                }
                                return 'Null Mismatch';
                            },
                        ],
//        'SheetPrimary',
//        'DbPrimary',
                        [
                            'attribute' => '',
                            'header' => 'Primary Key',
                            'value' => function ($data) {
                                if (!empty($data['SheetPrimary']) && !empty($data['DbPrimary'])) {
                                    if ($data['SheetPrimary'] == $data['DbPrimary']) {
                                        return 'Yes';
                                    }
                                    return 'Primary Mismatch';
                                }
                            },
                        ],
//        'SheetForeign',
//        'DbForeign'
                        [
                            'attribute' => '',
                            'header' => 'Foreign Key',
                            'value' => function ($data) {
                                if (!empty($data['SheetForeign']) && !empty($data['DbForeign'])) {
                                    if ($data['SheetForeign'] == $data['DbForeign']) {
                                        return 'Yes';
                                    }
                                    return 'Foreign Mismatch';
                                }
                            },
                        ],
                    ]
                ]);
//            }
            
            ?>
<!--            <script>
                alert(document.getElegetElementByName('tableName').value);
            </script>-->
<!--        </div>
    </div>
</div>-->