<?php

use yii\bootstrap\Html;
use yii\helpers\Html as Html2;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

        //  'compare-tables'
echo 111;
        $form = ActiveForm::begin(['action' => Url::to([$this->context->action->id]), 'method' => 'get']);
echo "Click on the following link : &nbsp;&nbsp;<a href='". $authUrl."' target='_blank'>Click Here</a>";

//print 'Enter verification code: '.'
//    <input type="text" name="authCode"><br>
//    <input type="submit">';

echo "<br />".Html::input("text", $name, $value);

echo "<br />" . Html2::submitButton('Submit', ['class' => 'btn btn-sm btn-primary']);

$form = ActiveForm::end();

?>


