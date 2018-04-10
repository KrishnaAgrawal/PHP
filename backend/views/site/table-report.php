<?php

use backend\models\GridEasy;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerCssFile(Yii::$app->request->baseUrl . '/css/bootstrap-4.css');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/report.css');
$this->title = 'Table Action Report';
?>

<div class="page-wrapper pt-2">
    <div class="row bg-title" style="margin-left: 0px;">
        <div class="col-md-12">
            <h4 class="page-title">                
                <?= $this->title; ?>

            </h4>  
            <!--Summary of tables-->
            <?php if (!empty($dataProvider)) { ?>
                <div class="col-2 pull-right" style="font-size:14px; margin-top: -3.3%">
                    <span class="text-danger row" style="margin-bottom: -19px"><div class="col-11">Remove from Both: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_remove_from_both"]; ?></kbd></div></span><br />
                    <span class="text-danger row" style="margin-bottom: -19px"><div class="col-11">Remove from Database: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_remove_from_database"]; ?></kbd></div></span><br />
                    <span class="text-danger row" style="margin-bottom: -19px"><div class="col-11">Remove from Sheet: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_remove_from_sheet"]; ?></kbd></div></span>
                </div>
                <div class="col-2 pull-right" style="font-size:14px; margin-top: -3.3%">
                    <span class="text-warning row" style="margin-bottom: -19px"><div class="col-11">Modify in Both: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_modify_in_both"]; ?></kbd></div></span><br />
                    <span class="text-warning row" style="margin-bottom: -19px"><div class="col-11">Modify in Database: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_modify_in_database"]; ?></kbd></div></span><br />
                    <span class="text-warning row" style="margin-bottom: -19px"><div class="col-11">Modify in Sheet: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_modify_in_sheet"]; ?></kbd></div></span><br />
                </div>
                <div class="col-2 pull-right" style="font-size:14px; margin-top: -3.3%">
                    <span class="text-success font-weight-bold row" style="margin-bottom: -19px"><div class="col-11">Total Actions: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_total_action_report"] ?></kbd></div></span><br />
                    <span class="text-primary row" style="margin-bottom: -19px"><div class="col-11">Add to Database: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_add_to_database"]; ?></kbd></div></span><br />
                    <span class="text-primary row" style="margin-bottom: -19px"><div class="col-11">Add to Sheet: </div><div class="" style="margin-left: -32px;"><kbd><?= $arrCount["count_add_to_sheet"]; ?></kbd></div></span><br />
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <!-- Left Section -->
            
            <?php 
                echo $this->context->renderPartial('left-report-filter',$_params_);
            ?>
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
                            'attribute' => "db",
                            'header' => 'Database',
                            'vAlign' => 'middle',
                            'group' => true,
                            'visible' => function($data){
                                if(!empty('database')){
                                    return  false;
                                }
                            }
                        ],
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
            ?>
            <div class="col-md-9 m-0 p-0 pl-2 mt-5 right-block">            
                <div class="card p-2 right-section">
                    <?php 
//                        echo "<pre>";print_r($arrGridView);exit;
                        echo GridEasy::widget(['arrGrid' => $arrGridView]);
                    ?>
                </div>
            </div>
        </div>
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
<?php
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