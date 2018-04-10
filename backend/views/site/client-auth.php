<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = 'Configure Sheet and Credentials';
if(!empty($msg)){
    common\widgets\Alert::widget();
}
?>
<div class="page-wrapper pt-2">
    <div class="row bg-title" style="margin-left: 0px;">
        <div class="col-md-12">
            <h3 class="page-title text-bold ">                
                <?= $this->title; ?>
            </h3>
        </div>
    </div>
    
    <div class="content">
        <div class="row">
            <?php
                //  'client auth'
                $form = ActiveForm::begin(['action' => Url::to([$this->context->action->id]), 'method' => 'post']);
            ?>
        </div>
    </div>
    
    <div class="col-md-12" style="margin-top: 5%; margin-left: 8%; font-size: 1.1em; ">
        <div class="col-md-3"></div>
        <div class="col-md-6">
        
            <?php
                echo "Click on the following link : &nbsp;&nbsp;<a href='". $authUrl."' target='_blank'>Click Here</a>&nbsp;";
//                echo "<span class='glyphicon glyphicon-info-sign'></span>";
                $content = "<span class='text-danger' style='font-size: 1.4em; margin-left: -4%'>On clicking you will be redirected to next page and you have to authenticate yourself.</span>";
                echo '<span class="glyphicon glyphicon-info-sign" style="font-size: 1em;" data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="' . $content . '" data-original-title="" title=""></span>';
//echo "<span class='text-danger' style='font-size: 0.8em; margin-left: -4%'>(on clicking you will be redirected to next page and you have to authenticate yourself)</span>";
                
            ?>
        </div>
        <div class="col-md-3"></div>
    </div>
    
    <div class="col-md-12" style="margin-top: 0.5%; margin-left: 9%; font-size: 1.1em; ">
        <div class="col-md-2"></div>
        <div class="col-md-6">
            <?php
                echo "<br />".Html::textInput("code", $value, ['id' => 'code', 'style' => 'width: 500px; height: 30px;', 'placeholder'=> "Enter the Code",])."&nbsp";
                $content = "<span class='text-danger' style='font-size: 1.4em; margin-left: -4%'>Enter the code copied which you have got from above link.</span>";
                echo '<span class="glyphicon glyphicon-info-sign" style="font-size: 1em;" data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="' . $content . '" data-original-title="" title=""></span>';
//                echo "<span class='text-danger' style='font-size: 0.8em; margin-left: 13%'>(enter the code copied which you have got from above link)</span>"
            ?>
        </div>
        <div class="col-md-4"></div>
    </div>
    
    <div class="col-md-12" style="margin-top: 0.5%; margin-left: 9%; font-size: 1.1em; ">
        <div class="col-md-2"></div>
        <div class="col-md-6">
            <?php
                echo "<br />".Html::textInput("sheetID", $value, ['id' => 'code', 'style' => 'width: 500px; height: 30px;', 'placeholder'=> "Enter the SpreedSheet ID",])."&nbsp";
                $content = "<span class='text-danger' style='font-size: 1.4em; margin-left: -4%'>Copy the blue text from the URL as: https://docs.google.com/spreadsheets/<br />d/<span class='text-info'>spreadsheetId</span>/edit#gid=sheetId.</span>";
                echo '<span class="glyphicon glyphicon-info-sign" style="font-size: 1em;" data-trigger="hover" data-html="true" data-container="body" data-toggle="popover" data-placement="right" data-content="' . $content . '" data-original-title="" title=""></span>';
//                echo "<span class='text-danger' style='font-size: 0.8em; margin-left: 13%'>(enter the code copied which you have got from above link)</span>"
            ?>
        </div>
        <div class="col-md-4"></div>
    </div>
    
    
    <div class="col-md-12" style="margin-top: 0.5%; margin-left: 9%; font-size: 1.1em; ">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <?php
                echo "<br />" . Html::submitButton('Submit', ['class' => 'btn btn-primary btn-lg']);
            ?>
        </div>
        <div class="col-md-4"></div>
    </div>
    
    <?php
        $form = ActiveForm::end();
    ?>
</div>
        
<?php
        





?>

<style>

::-webkit-input-placeholder {
   color: #808080;
   text-align: center;

}

:-moz-placeholder {
   color: #808080;
   text-align: center;

}
</style>


