
<!--    <head>
        <link data-require="bootstrap-css@*" data-semver="3.2.0" rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <script data-require="jquery@*" data-semver="2.1.1" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script data-require="bootstrap@*" data-semver="3.2.0" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.js"></script>
    </head>-->

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/bootstrap-4.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/report.css');
yii\web\JqueryAsset::register($this);

$sheet = [];
$action = null;
$sheetToDb = null;
if (empty($strTableToShow)) {
    $strTableToShow = "All";
}
$arrParams = Yii::$app->request->queryParams;
//    check for $arrParams['sheet']
if (!empty($arrParams['sheet'])) {
    $sheet = $arrParams['sheet'];
}
$totalSheetCountPerTable = 0;
if (!empty($arrCount["totalSheetCountPerTable"])) {
    $totalSheetCountPerTable = $arrCount["totalSheetCountPerTable"];
}
$totalDbCountPerTable = 0;
if (!empty($arrCount["totalDbCountPerTable"])) {
    $totalDbCountPerTable = $arrCount["totalDbCountPerTable"];
}
$this->title = 'Database/Sheet Comparison (Column Wise)';
?>

<div class="page-wrapper pt-2">
    <div class="row bg-title" style="margin-left: 0px;">
        <div class="col-md-12">
            <h4 class="page-title">                
                <?= $this->title; ?>
                <!--Summary of tables-->
                <?php  if(!empty($preferenceOption)) {?>
                    <div class="pull-right " style="font-size:14px;">
                        <span class="text-info">Matching: <kbd><?= $arrCount["totalColumnMatchCount"]; ?>/<?= $arrCount["totalColumnCount"] ?></kbd></span> |
                        <span class="text-info">Non Matching: <kbd><?= $arrCount["totalColumnNonMatchCount"]; ?>/<?= $arrCount["totalColumnCount"] ?></kbd></span> |
                        <span class="text-info">Columns (Sheet): <kbd><?= $arrCount["totalColumnSheetCount"] ?>/<?= $arrCount["totalColumnCount"] ?></kbd></span> |
                        <span class="text-info">Columns (Database): <kbd><?= $arrCount["totalColumnDbCount"] ?>/<?= $arrCount["totalColumnCount"] ?></kbd></span>
                    </div>
                <?php } ?>
            </h4> 
        </div>
    </div>
    <div class="content">
        <div class="row">
             <!--Left Section--> 
            <?php 
                echo $this->context->renderPartial('left-compare-filter',$_params_);
            ?>
            
            <?php
//            Data Provider Code
            $field_tbl = "str_table";
            $field_tbl_column = "str_table_column";
            $field_tbl_column_flag = "str_table_column_flag";
//        echo "<pre>";print_r($dataProvider);exit;

            $arrGridView = [
                'dataProvider' => $dataProvider,
                'filterModel' => 'false',
                'emptyText' => 'Please Select Sheet.',
                'toolbar' => [
                    '{export}',
                    '{toggleData}',
                ],
                'columns' => [
                    [
                        'attribute' => $field_tbl,
                        'header' => 'Table',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:7%'],
//                        'contentOptions' => ['style' => 'width:9%'],
                        'group' => true,
                        'vAlign' => 'middle',
                        'content' => function($data, $totalSheetCountPerTable, $totalDbCountPerTable, $field_tbl) use($field_tbl, $totalSheetCountPerTable, $totalDbCountPerTable) {
                            $str = '';
//                            echo "<pre>";
//                            print_r($totalSheetCountPerTable);
//                            print_r($data[$field_tbl]);
//                            print_r($field_tbl);
//                            print_r($data);
//                            print_r($totalDbCountPerTable);
//                            exit;
                            
                            $str .= '<span class="tableText" data-trigger="hover"'
                                    . ' data-html="true" data-container="body" data-toggle="popover" data-placement="right"'
                                    . ' data-content="Sheet column Count: ' . $totalSheetCountPerTable[$data[$field_tbl]] . ',<br />'
                                    . 'Database column Count: ' . $totalDbCountPerTable[$data[$field_tbl]] . '" '
                                    . 'data-original-title="" title="">' . $data[$field_tbl] . '</span>';
                            return $str;
                        },
                    ],
                    [
                        'attribute' => $field_tbl_column,
                        'header' => 'Column Name',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:7%'],
//                        'contentOptions' => ['class' => 'text-center'],
//                        'option' => ['class' => 'text-center'],
//                            'value' => $field_tbl_column,
                    ],
//        'sheetField',
//        'dbField',
                    [
                        'attribute' => '',
                        'header' => 'Column Exist',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:9%'],
                        'contentOptions' => ['class' => 'text-center'],
//                'filter' => Html::drop
                        'content' => function ($data) {
                            $str = '';
                            if (!empty($data['sheetField']) && !empty($data['dbField'])) {
                                if ($data['sheetField'] == $data['dbField']) {
                                    $str .= '<span data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="Column exists in both" data-original-title="" title="">' . \backend\models\DbCompare::LABEL_YES . '</span>';
                                }
                            } else {
                                $str .= '<span data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="'.(empty($data['sheetField'])?"Column does not exists in sheet":(empty($data['dbField'])?"Column does not exists in database":"")).'" data-original-title="" title="">' . \backend\models\DbCompare::LABEL_NO . '</span>';
                            }

                            return $str;
                        },
                    ],
//        'sheettypesize',
//        'dbtypesize',
                    [
                        'attribute' => '',
                        'header' => 'Type Size',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:6%'],
                        'contentOptions' => ['class' => 'text-center'],
                        'vAlign' => 'middle',
                        'filter'=> '<input class="form-control " name="" value="" type="hidden">',
                        'content' => function ($data) {
                            $str = '';
                            $keySheet = 'sheettypesize';
                            $keyDb = 'dbtypesize';
//                            $str = '<span class="text-success">'.$data['sheettypesize'].'</span> <br>'.'<span class="text-danger">'. $data['dbtypesize'].'</span>';
                            $label =  \backend\models\DbCompare::LABEL_NO ;
                            if ((!empty($data[$keySheet]) && !empty($data[$keyDb])) && ($data[$keySheet] == $data[$keyDb])  ) {
                                $label =  \backend\models\DbCompare::LABEL_YES ;
                            } 
                            $str .= '<span data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="Sheet: ' . (!empty($data[$keySheet])?$data[$keySheet]:'--') . ',<br />Database: ' . (!empty($data[$keyDb])?$data[$keyDb]:'--') . '" data-original-title="" title="">' . $label . '</span>';
                            return $str;
                        },
                    ],
//        'sheetNull',
//        'dbNull',
                    [
                        'attribute' => '',
                        'header' => 'Null',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:3%'],
                        'contentOptions' => ['class' => 'text-center'],
                        'content' => function ($data) {
                            $str = '';
                            $keySheet = 'sheetNull';
                            $keyDb = 'dbNull';
//                            $str = '<span class="text-success">'.$data['sheettypesize'].'</span> <br>'.'<span class="text-danger">'. $data['dbtypesize'].'</span>';
                            $label =  \backend\models\DbCompare::LABEL_NO ;
                            if ((!empty($data[$keySheet]) && !empty($data[$keyDb])) && (strtolower($data[$keySheet]) == strtolower($data[$keyDb]))  ) {
                                $label =  \backend\models\DbCompare::LABEL_YES ;
                            } 
                            $str .= '<span data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="Sheet: ' . (!empty($data[$keySheet])?$data[$keySheet]:'--') . ',<br />Database: ' . (!empty($data[$keyDb])?$data[$keyDb]:'--') . '" data-original-title="" title="">' . $label . '</span>';
                            return $str;
                        },
                    ],
//        'SheetPrimary',
//        'DbPrimary',
                    [
                        'attribute' => '',
                        'header' => 'Primary Key',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:9%'],
                        'contentOptions' => ['class' => 'text-center'],
                        'content' => function ($data) {
                            $content = 'Sheet: ' . $data["SheetPrimary"] . ',<br />Database: ' . $data["DbPrimary"];
                            if (!empty($data['SheetPrimary']) && !empty($data['DbPrimary'])) {
                                if ($data['SheetPrimary'] == $data['DbPrimary']) {
                                    return '<span   data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="' . $content . '" data-original-title="" title="">' . \backend\models\DbCompare::LABEL_YES . '</span>';
                                }
                                return '<span data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="' . $content . '" data-original-title="" title="">' . \backend\models\DbCompare::LABEL_NO . '</span>';
                            }
                            return "--";
                        },
                    ],
//        'SheetForeign',
//        'DbForeign'
                    [
                        'attribute' => '',
                        'header' => 'Foreign Key',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:9%'],
                        'contentOptions' => ['class' => 'text-center'],
                        'content' => function ($data) {
                            if (!empty($data['SheetForeign']) && !empty($data['DbForeign'])) {
                                if ($data['SheetForeign'] == $data['DbForeign']) {
                                    return '<span data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="Sheet: ' . $data["SheetForeign"] . ',<br />Database: ' . $data["DbForeign"] . '" data-original-title="" title="">' . \backend\models\DbCompare::LABEL_YES . '</span>';
                                }
                                return '<span data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="Sheet: ' . $data["SheetForeign"] . ',<br />Database: ' . $data["DbForeign"] . '" data-original-title="" title="">' . \backend\models\DbCompare::LABEL_NO . '</span>';
                            }
                            return "--";
                        },
                    ],
                    [
                        'attribute' => '',
                        'header' => 'Action',
                        'headerOptions' => ['class' => 'text-center','style' => 'width:16%'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'raw',
                        //                            'filter' => Html::submitButton('Submit'),
                        'content' => function ($data) use($action, $arrActionDbSheet, $arrActionDbNoSheet, $arrActionNoDbSheet, $arrTableAction) {
                            $temp = $data["str_table"] . "#" . $data["str_table_column"];
                            $arrAction = [];
                            //                                    echo "<script> x = ".$temp."</script>";
                            if (!empty($data["dbField"]) && !empty($data["sheetField"])) {
                                $arrAction = $arrActionDbSheet;
                            }
                            if (!empty($data["dbField"]) && empty($data["sheetField"])) {
                                $arrAction = $arrActionDbNoSheet;
                            }
                            if (empty($data["dbField"]) && !empty($data["sheetField"])) {
                                $arrAction = $arrActionNoDbSheet;
                            }
                            if (!empty($arrTableAction[$temp])) {
                                $action = $arrTableAction[$temp];
                            }
                            return Html::dropDownList("Action", $action, $arrAction, ['class' => 'form-control', 'prompt' => '', 'onchange' => 'saveResponseColumn("' . $temp . '",this.value)']);
                        }
                    ],
                ],
            ];

            if (!empty($showPageSummary)) {
                $arrGridView['showPageSummary'] = true;
                $arrGridView['pageSummaryRowOptions'] = ['class' => 'kv-page-summary info'];
            }
            ?>
            <div class="col-md-9 m-0 p-0 pl-2 pr-2 right-block">            
                <div class="card p-2 right-section">
                    <div class="pull-left">

                        <?php
                        $filters = (!empty($sheet) && $preferenceOption == "sheet_to_db" ? '<div class="<!--chip p-2 text-bold">' . implode(", &nbsp;", array_values($sheet)) . '</div>' : '(none)');

                        $arrGridView['panelBeforeTemplate'] = '<div class="text-success text-bold">Filtered on Sheet</div> <div class="pull-left">' . $filters . '</div>';
                        ?>

                    </div>
                    <?php echo  \backend\models\GridEasy::widget(['arrGrid' => $arrGridView]); ?>
                    <?php // \kartik\grid\GridView::widget($arrGridView); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php // echo "Hey Boy";exit;     ?>
<style>
    label{
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
    .label-danger,.label-success{
        cursor: pointer;
    }
    .tableText{
        cursor: pointer;
    }

    /* Styles go here */

</style>

<script>
//
//    function saveResponse(table, txtAction) {
//
//        var arrDataToStore = {
//            'tableName': table,
//            'txtAction': txtAction
//        };
//        $.ajax({
//            method: "POST",
//            url: url,
//            data: arrDataToStore
//        }).done(function (msg) {
//            alert(msg);
//        });
//
//    }

    function saveResponseColumn(tableColumn, txtAction) {

        var arrDataToStore = {
            'tableColumnName': tableColumn,
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

    // Code goes here



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