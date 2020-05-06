<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Kontakt';
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>
        <p>
            Juhul kui Teil on küsimusi või esineb muid probleeme, võtke meiega ühendust.
        </p>
    <br>
    <br>
        <div class="row">
            <div class="col-lg-5">
                <ul>
                    <strong>e-mail:</strong> siim@siim.ee
                </ul>
                <ul>
                    <strong>telefon:</strong> +37253739771
                </ul>
            </div>
        </div>
</div>
