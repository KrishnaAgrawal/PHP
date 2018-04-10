<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


?>


            <div class="col-md-3 m-0 p-0 mt-5 left-block">
                <div class="card p-4 left-section">
                    
                    <!-- Get Filter Options !-->
                    <?php
                        $form = ActiveForm::begin(['action' => Url::to(['table-report']), 'method' => 'get']);
                    ?>
                    <!--Preference option-->
                    <div class="col-md-12  card card-block mt-2 ml-2 p-1 pl-3">
                        <h4> Select Database: </h4>
                        <div>
                            <?php
                            $str = '';
                            if (!empty($arrAllDatabaseName)) {
                                foreach ($arrAllDatabaseName as $databaseTitle) {
                                    $selectedDatabase = null;
                                    if (!empty($database) && in_array($databaseTitle, array_values($database))) {
                                        $selectedDatabase = $databaseTitle;
                                    }
                                    if (empty($databaseTitle)) {
                                        $databaseTitle = null;
                                    }
                                    $str .= Html::checkbox("database[]", $selectedDatabase, ['class' => ($databaseTitle != 'All' ? 'checkAll' : ''), 'label' => $databaseTitle, 'value' => $databaseTitle, 'id' => ($databaseTitle == 'All' ? 'checkAll' : ''), 'labelOptions' => ['class' => 'd-block']]);
                                }
                            }
                            echo $str;
                            ?>
                        </div>
                    </div>

                    <!--Submit Button-->
                    <div class="col-md-12 pull-right">
                        <?php
                        echo "<br />" . Html::submitButton('Submit', ['class' => 'btn btn-sm btn-primary']);
                        ?>
                    </div>

                    <?php $form = ActiveForm::end(); ?>
                </div>
            </div>
