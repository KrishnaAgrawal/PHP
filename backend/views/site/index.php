<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/bootstrap-4.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/report.css');
yii\web\JqueryAsset::register($this);

$sheet = [];
$action = null;
$sheetToDb = null;

$arrParams = Yii::$app->request->queryParams;
//    check for $arrParams['sheet']
if (!empty($arrParams['sheet'])) {
    $sheet = $arrParams['sheet'];
}
$this->title = 'Database/Sheet Comparison (Table Wise)';

?>

<div class="page-wrapper pt-2">
    <div class="row bg-title p-0">
        <div class="col-md-12 col-md-12">
            <h4 class="page-title">                
                <?= $this->title; ?>
                <?php  if(!empty($preferenceOption)) {?>
                <!--Summary of tables-->
                <div class="pull-right " style="font-size:14px;">
                    <span class="text-info">Matching: <kbd><?= $arrCount["totalMatchCount"]; ?>/<?= $arrCount["totalCount"] ?></kbd></span> |
                    <span class="text-info">Non Matching: <kbd><?= $arrCount["totalNonMatchCount"]; ?>/<?= $arrCount["totalCount"] ?></kbd></span> |
                    <span class="text-info">Tables (Sheet): <kbd><?= $arrCount["totalSheetCount"] ?>/<?= $arrCount["totalCount"] ?></kbd></span> |
                    <span class="text-info">Tables (Database): <kbd><?= $arrCount["totalDbCount"] ?>/<?= $arrCount["totalCount"] ?></kbd></span>
                </div>
                <?php } ?>
            </h4> 
        </div>
    </div>
    
    <div class="content">
        <div class="row">
            <!-- Left Section -->
            <?php echo  $this->context->renderPartial('test',$_params_); ?>  
            <?php
//            Data Provider Code
            if (!empty($dataProvider)) {
                if (empty($arrGridView)) {
                    $arrGridView = [
                        'dataProvider' => $dataProvider,
                        'filterModel' => 'false',
                        'emptyText' => 'Please Select Sheet Type.',
                        'toolbar' => [
                            '{export}',
                            '{toggleData}',
                        ],
//                'rowOptions' => function ($model) {
//                if ($model['Sheet_Table'] == $model['Db_Table']) {
//                    return ['class' => 'bg-success'];
//                }
//                if ($model['Sheet_Table'] != $model['Db_Table']) {
//                    return ['class' => 'bg-danger'];
//                }
//            },
                        'columns' => [
                            [
                                'attribute' => 'Table',
                                'header' => 'Table',
                                'filter' => Html::input('text', 'tableName', $strTableSearched),
//                            'filterPosition' => GridView::FILTER_POS_FOOTER,
//                            function () {
//                                return Html::buttonInput($label);
//                            }
//                            'layout'=>"{sorter}\n{pager}\n{summary}\n{items}",
//                            ['class' => 'yii\grid\SerialColumn'],
//                            ['class' => 'yii\grid\ActionColumn'],
                            ],
                            [
                                'attribute' => 'Db_Table',
                                'header' => 'Database',
//                            'filter' => Html::input('text','dbTableName'),
                                'content' => function($model) {
                                    if ($model['Db_Table']) {
                                        return \backend\models\DbCompare::LABEL_YES;
                                    }
                                    return \backend\models\DbCompare::LABEL_NO;
                                },
                                'visible' => ($preferenceOption == "db_to_sheet") ? true : false,
                            ],
                            [
                                'attribute' => 'Sheet_Table',
                                'header' => 'Sheet',
//                            'filter' => Html::input('text','sheetTableName'),
                                'content' => function($model) {
                                    if ($model['Sheet_Table']) {
                                        return \backend\models\DbCompare::LABEL_YES;
                                    }
                                    return \backend\models\DbCompare::LABEL_NO;
                                },
                            ],
                            [
                                'attribute' => 'Db_Table',
                                'header' => 'Database',
//                            'filter' => Html::input('text','dbTableName'),
                                'content' => function($model) {
                                    if ($model['Db_Table']) {
                                        return \backend\models\DbCompare::LABEL_YES;
                                    }
                                    return \backend\models\DbCompare::LABEL_NO;
                                },
                                'visible' => ($preferenceOption == "db_to_sheet") ? false : true,
                            ],
                            [
                                'attribute' => '',
                                'header' => 'Action',
                                'format' => 'raw',
//                            'filter' => Html::submitButton('Submit'),
                                'content' => function ($data) use($action, $arrActionDbSheet, $arrActionDbNoSheet, $arrActionNoDbSheet, $arrTableAction) {
                                    $temp = $data["Table"];
                                    $arrAction = [];
//                                    echo "<script> x = ".$temp."</script>";
                                    if ($data["Db_Table"] == 1 && $data["Sheet_Table"] == 1) {
                                        $arrAction = $arrActionDbSheet;
                                    }
                                    if ($data["Db_Table"] == 1 && $data["Sheet_Table"] == 0) {
                                        $arrAction = $arrActionDbNoSheet;
                                    }
                                    if ($data["Db_Table"] == 0 && $data["Sheet_Table"] == 1) {
                                        $arrAction = $arrActionNoDbSheet;
                                    }
                                    if (!empty($arrTableAction[$temp])) {
                                        $action = $arrTableAction[$temp];
                                    }
                                    return Html::dropDownList("Action", $action, $arrAction, ['class' => 'form-control', 'prompt' => '', 'onchange' => 'saveResponse("' . $temp . '",this.value)']);
                                }
                            ],
                            [
                                'attribute' => '',
                                'header' => 'Action',
                                'format' => 'raw',
//                            'filter' => Html::submitButton('Submit'),
                                'content' => function ($data) use($action, $arrActionDbSheet, $arrActionDbNoSheet, $arrActionNoDbSheet, $arrTableAction) {
                                    $temp = $data["Table"];
                                    $arrAction = [];
//                                    echo "<script> x = ".$temp."</script>";
                                    if ($data["Db_Table"] == 1 && $data["Sheet_Table"] == 1) {
                                        $arrAction = $arrActionDbSheet;
                                    }
                                    if ($data["Db_Table"] == 1 && $data["Sheet_Table"] == 0) {
                                        $arrAction = $arrActionDbNoSheet;
                                    }
                                    if ($data["Db_Table"] == 0 && $data["Sheet_Table"] == 1) {
                                        $arrAction = $arrActionNoDbSheet;
                                    }
                                    if (!empty($arrTableAction[$temp])) {
                                        $action = $arrTableAction[$temp];
                                    }
                                    if (!empty($action)) {
                                        return $arrAction[$action];
                                    }
                                },
                                'visible' => false
                            ],
                        ],
                    ];

                    if (!empty($showPageSummary)) {
                        $arrGridView['showPageSummary'] = true;
                        $arrGridView['pageSummaryRowOptions'] = ['class' => 'kv-page-summary info'];
                    }
                }
            }
            ?>
            <div class="col-md-9 m-0 p-0 pl-2 pr-2 right-block">            
                <div class="card p-2 right-section">
                    <div class="pull-left">
                        
                        <?php 
                        $filters = (!empty($arrpreferenceOption[$preferenceOption])? implode(", &nbsp;", array_values($sheet)):'');
                        
                        $arrGridView['panelBeforeTemplate'] = '<div class="pull-left">'.$filters.'</div>';?>
                        
                    </div>
                        <?= \backend\models\GridEasy::widget(['arrGrid' => $arrGridView]); ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>


<?php // echo "Hey Boy";exit;   ?>
<style>
    label{
        /*color : #337ab7;*/
        font-size: 15px;
        font-weight: normal;
    }
    td {
        white-space: nowrap;
    }
    .form-control{
        padding: 0.3rem;
    }
  .chip {
    display: inline-block;
    height: 3rem;
    font-size: 14px;
    /* line-height: 50px; */
    border-radius: 25px;
    background-color: #c1c1c1;
}
</style>

<script>

    function saveResponse(table, txtAction) {

        var arrDataToStore = {
            'tableName': table,
            'txtAction': txtAction
        };
        $.ajax({
            method: "POST",
            url: url,
            data: arrDataToStore
        }).done(function (msg) {
            alert(msg);
        });

    }
</script>
<?php
$url = "var url = '" . \yii\helpers\Url::to(['save-response']) . "';";
$this->registerJs($url, \yii\web\View::POS_HEAD);


$script = <<<JS
        $('input[name="preferenceOption"]').click(function(){
            if($(this).val() == 'sheet_to_db'){
                $('.sheetsList').removeClass('hidden');
            }else{
                $('.sheetsList').addClass('hidden');
            }
        });
        
        $('#checkAll').click(function () {
        if ($(this).is(':checked')) {
            $("input[type='checkbox'].checkAll:checkbox").prop('checked', true);
        } else {
            $("input[type='checkbox'].checkAll:checkbox").prop('checked', false);
        }
    });

    $("input[type='checkbox'].checkAll").change(function () {
        var a = $("input[type='checkbox'].checkAll");
        if (a.length == a.filter(":checked").length) {
            $('#checkAll').prop('checked', true);
        } else {
            $('#checkAll').prop('checked', false);
        }
    });    
JS;
$this->registerJs($script);
?>