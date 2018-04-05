<?php

use backend\models\GridEasy;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/bootstrap-4.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/report.css');
$this->title = 'Table Action Report';
?>

<div class="page-wrapper pt-2">
    <div class="row bg-title p-0">
        <div class="col-md-12 col-md-12">
            <h4 class="page-title">                
                <?= $this->title; ?>

            </h4>  
            <!--Summary of tables-->
            <div class="col-md-3 pull-right" style="font-size:14px; margin-top: -3%">
                <span class="text-danger"><div class="col-md-8">Remove from Both: </div><div class="col-md-4"><kbd><?= $arrCount["count_remove_from_both"]; ?></kbd></div></span><br />
                <span class="text-danger"><div class="col-md-8">Remove from Database: </div><div class="col-md-4"><kbd><?= $arrCount["count_remove_from_database"]; ?></kbd></div></span><br />
                <span class="text-danger"><div class="col-md-8">Remove from Sheet: </div><div class="col-md-4"><kbd><?= $arrCount["count_remove_from_sheet"]; ?></kbd></div></span>
            </div>
            <div class="col-md-3 pull-right" style="font-size:14px; margin-top: -3%">
                <span class="text-warning"><div class="col-md-7">Modify in Both: </div><div class="col-md-5"><kbd><?= $arrCount["count_modify_in_both"]; ?></kbd></div></span><br />
                <span class="text-warning"><div class="col-md-7">Modify in Database: </div><div class="col-md-5"><kbd><?= $arrCount["count_modify_in_database"]; ?></kbd></div></span><br />
                <span class="text-warning"><div class="col-md-7">Modify in Sheet: </div><div class="col-md-5"><kbd><?= $arrCount["count_modify_in_sheet"]; ?></kbd></div></span><br />
            </div>
            <div class="col-md-3 pull-right" style="font-size:14px; margin-top: -3%">
                <span class="text-success font-weight-bold"><div class="col-md-6">Total Actions: </div><div class="col-md-6"><kbd><?= $arrCount["count_total_action_report"] ?></kbd></div></span><br />
                <span class="text-primary"><div class="col-md-6">Add to Database: </div><div class="col-md-6"><kbd><?= $arrCount["count_add_to_database"]; ?></kbd></div></span><br />
                <span class="text-primary"><div class="col-md-6">Add to Sheet: </div><div class="col-md-6"><kbd><?= $arrCount["count_add_to_sheet"]; ?></kbd></div></span><br />
            </div>
        </div>
    </div>
    <div class="content pt-4 pr-3">
        <!--<div class="row">-->
        <!-- Left Section -->
        <!--<div class="col-md-3 m-0 p-0 left-block">-->
        <!--                <div class="card p-4 left-section">
                             Get Filter Options !
        
        
        <?php
//                    $form = ActiveForm::begin(['action' => \yii\helpers\Url::to(['index']), 'method' => 'get']);
        ?>
                            Preference option
                            <div class="col-md-12  card card-block mt-2 ml-2 p-1 pl-3">
                                <label> Comparison Type: </label>
                                <div>
        <?php
//                            echo \yii\bootstrap\Html::radioList("preferenceOption", $preferenceOption, $arrpreferenceOption, ['class' => 'h-25', 'prompt' => 'Select-Sheet']) . "";
        ?>
                                </div>
                            </div>
                            
                            Sheet Lists
                            <div class="col-md-12 sheetsList <?php // echo ($preferenceOption != 'sheet_to_db' ? 'hidden' : '')   ?>  card card-block mt-2 ml-2 p-1 pl-3">
                                <label> Select Sheet</label>
                                <div style="max-height:15.3rem;overflow: auto;">
        <?php
//                            $str = '';
//                            foreach ($arrSheetTitles as $sheetTitle) {
//                                $selectedSheet = null;
//                                if (!empty($sheet) && in_array($sheetTitle, array_values($sheet))) {
//                                    $selectedSheet = $sheetTitle;
//                                }
//                                $str .= Html::checkbox("sheet[]", $selectedSheet, ['class' => ($sheetTitle != 'All' ? 'checkAll' : ''), 'label' => $sheetTitle, 'value' => $sheetTitle, 'id' => ($sheetTitle == 'All' ? 'checkAll' : ''), 'labelOptions' => ['class' => 'd-block']]);
//                            }
//                            echo $str;
        ?>
                                </div>
                            </div>
                            
                            Matching and non-matching
                            <div class="col-md-12 card card-block mt-2 ml-2 p-1 pl-3">
                                <label > Select Matching Type</label>
        <?php
//                        echo \yii\bootstrap\Html::radioList("tables", $strTableToShow, $arrMatchTypeToShow, ['class' => '', 'prompt' => 'Select-Sheet']) . "";
        ?>
                            </div>
        
                            checkbox for both table and sheet
                            <div class="col-md-12 pt-3">
        <?php
//                        echo Html::checkbox("sheet_db", $sheet_db, ['label' => "Show Missing Tables Also", 'labelOptions' => ['class' => 'd-block']]);
        ?>
                            </div>
                            
                            Submit Button
                            <div class="col-md-12 pull-right">
        <?php
//                        echo "<br />" . Html::submitButton('Submit', ['class' => 'btn btn-sm btn-primary']);
        ?>
                            </div>
                            
        <?php // $form = ActiveForm::end();  ?>
                        </div>-->
        <!--</div>-->
        <?php
        $arrGridView = [
            'dataProvider' => $dataProvider,
            'filterModel' => 'false',
            'emptyText' => 'No record(s) to show.',
            'toolbar' => [
                '{export}',
                '{toggleData}',
            ],
            'columns' => [
                [
                    'attribute' => "table",
                    'header' => 'Table',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => "action",
                    'header' => 'Action',
                    'vAlign' => 'middle',
                    'content' => function($data) {
                        $arrConcat = [];
                        $arrConcat = explode("_", $data["action"]);
                        return ucfirst($arrConcat[0]) . " " . $arrConcat[1] . " " . $arrConcat[2];
                    },
                ],
            ],
        ];

//    if (!empty($showPageSummary)) {
//        $arrGridView['showPageSummary'] = true;
//        $arrGridView['pageSummaryRowOptions'] = ['class' => 'kv-page-summary info'];
//    }
        ?>
        <div class="col-md-12 m-0 p-0 pl-2 pr-2 ">            
            <div class="card p-2">
                <!--<div class="pull-left">-->

                <?php
//                        $filters = (!empty($sheet) && $preferenceOption == "sheet_to_db" ? '<div class="chip p-2 text-bold">' . implode(",", array_values($sheet)) . '</div>' : '');
//
//                        $arrGridView['panelBeforeTemplate'] = '<div class="pull-left">' . $filters . '</div>';
//                        
                ?>

                <!--</div>-->
                <?= GridEasy::widget(['arrGrid' => $arrGridView]); ?>
            </div>
        </div>
        <!--</div>-->
    </div>
</div>
<style>
    kbd {
        background-color: lightblue;
        color: black;            
        font-weight: bold;
        font-size: 1em;
    }
</style>
