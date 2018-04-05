<?php

use yii\bootstrap\Html;
use yii\helpers\Html as Html2;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="col-md-3 m-0 p-0 left-block">


    <div class="card p-4 left-section">
        <!-- Get Filter Options !-->


        <?php
        
        $form = ActiveForm::begin(['action' => Url::to(['compare-tables']), 'method' => 'get']);
        ?>
        <!--Preference option-->
        <div class="col-md-12  card card-block mt-2 ml-2 p-1 pl-3">
            <h4> Comparison Type: </h4>
            <div>
                <?php
                echo Html::radioList("preferenceOption", $preferenceOption, $arrpreferenceOption, ['class' => 'h-25', 'prompt' => 'Select-Sheet']) . "";
                ?>
            </div>
        </div>
        <!--Sheet Lists-->
        <div class="col-md-12 sheetsList <?= ($preferenceOption != 'sheet_to_db' ? 'hidden' : '') ?>  card card-block mt-2 ml-2 p-1 pl-3">
            <h4> Select Sheet</h4>
            <div style="max-height:15.3rem;overflow: auto;">
                <?php
                $str = '';
                if (!empty($arrSheetTitles)) {
                    if (in_array("All", $arrSheetTitles)) {
                        $temp = "All";
                        $arrSheetTitles = array_diff($arrSheetTitles, [$temp]);
                        sort($arrSheetTitles);
                        array_unshift($arrSheetTitles, $temp);
                    }
                    if (in_array("Common Fields", $arrSheetTitles)) {
                        $arrSheetTitles = array_flip($arrSheetTitles);
                        unset($arrSheetTitles["Common Fields"]);
                        $arrSheetTitles = array_flip($arrSheetTitles);
                    }
                    foreach ($arrSheetTitles as $sheetTitle) {
                        $selectedSheet = null;
                        if (!empty($sheet) && in_array($sheetTitle, array_values($sheet))) {
                            $selectedSheet = $sheetTitle;
                        }
                        $str .= Html::checkbox("sheet[]", $selectedSheet, ['class' => ($sheetTitle != 'All' ? 'checkAll' : ''), 'label' => $sheetTitle, 'value' => $sheetTitle, 'id' => ($sheetTitle == 'All' ? 'checkAll' : ''), 'labelOptions' => ['class' => 'd-block']]);
                    }
                }
                echo $str;
                ?>
            </div>
        </div>
        <!--Matching and non-matching-->
        <div class="col-md-12 card card-block mt-2 ml-2 p-1 pl-3">
            <h4> Select Matching Type</h4>
            <?php
            if (empty($strTableToShow)) {
                $strTableToShow = "All";
            }
            echo Html::radioList("tables", $strTableToShow, $arrMatchTypeToShow, ['class' => '', 'prompt' => 'Select-Sheet']) . "";
            ?>
        </div>

        <!--checkbox for both table and sheet-->
        <div class="col-md-12 pt-3">
            <?php
            echo Html2::checkbox("sheet_db", $sheet_db, ['label' => "Show Missing Tables Also", 'labelOptions' => ['class' => 'd-block']]);
            ?>
        </div>
        <!--Submit Button-->
        <div class="col-md-12 pull-right">
            <?php
            echo "<br />" . Html2::submitButton('Submit', ['class' => 'btn btn-sm btn-primary']);
            ?>
        </div>
        <?php $form = ActiveForm::end(); ?>
    </div>
</div>