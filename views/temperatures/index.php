<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;


// muututa = variable
// array [] masiiv

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $sensors \app\models\Sensors[] */
/* @var $temperatures \app\models\Temperatures[]|null */ // t

$this->title = Yii::t('app', 'Temperatuurid'); // Yii:t tõlkimiseks
$sensorName = \yii\helpers\ArrayHelper::getColumn($sensors, 'sid');

$datapoints = []; // saame kindlad olla, et datapoints on massiiv, mitte null vms
foreach ($temperatures as $temperature) {
    $datapoints[] = ['y' => $temperature->temperature, 'label' => $temperature->time];
}

$andmepunktid = Json::encode($datapoints); // php json encode, json =  javascript object notation, võtab sisse array ja teeb selle jsoniks

?>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="temperatures-index">
        <label>Vali sensor:</label><br>
        <?php
        foreach ($sensorName as $sensors) {
            echo Html::a($sensors, ['', 'sid' => $sensors], ['class' => 'btn btn-default']);
        }
        ?>




        <?php if ($sid): ?>
            <br>
            <label>Vali periood:</label>
            <br>
            <?= Html::a('TÄNA', ['', 'vahemik' => 'tana', 'sid' => $sid], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('EILE', ['', 'vahemik' => 'eile', 'sid' => $sid], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('NÄDAL', ['', 'vahemik' => 'nadal', 'sid' => $sid], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('KUU', ['', 'vahemik' => 'kuu', 'sid' => $sid], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <br><br>

            <label>Algus:</label>
            <div id="wrapper">
                <input type="datetime-local" name="lkp" class="form-control" value="">
            </div>
            <label>Lõpp:</label>
            <div id="wrapper">
                <input type="datetime-local" name="lkp" class="form-control" value="">
            </div>

        <?= Html::a('Vaata', ['', 'vahemik' => 'valikvahemik', 'sid' => $sid], ['class' => 'btn btn-primary']) ?>

        <br><br>
        <?php if (!$sid): ?>
            <h2>Palun vali sensor</h2>

        <?php elseif ($temperatures): ?> <!--- https://www.php.net/manual/en/control-structures.alternative-syntax.php --->
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        <?php else: ?>
            <h2>Andmed puuduvad</h2>
        <?php endif; ?>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


    </div>

<?php
$this->registerJs(
    "       window.onload = function () {

            var chart = new CanvasJS.Chart(\"chartContainer\", {
                animationEnabled: false,
                axisX:{
                    valueFormatString: \"DD MMM,YYYY\"
                },
                axisY:{
                    title: \"Temperatuur (C)\",
                    includeZero: false,
                    suffix: \" C\"
                },
                legend:{
                  cursor: \"pointer\",
                  fontSize: 16,
                  itemclick: toggleDataSeries
                },
                toolTip:{
                  shared: true
                },
                data: [{
                    type: \"spline\",
                    yValueFormatString: \"#0.### C\",
                    showInLegend: true,
                    dataPoints: {$andmepunktid}
                    
                }]
            });
            chart.render();

            function toggleDataSeries(e){
                if (typeof(e.dataSeries.visible) === \"undefined\" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                }
                else{
                    e.dataSeries.visible = true;
                }
                chart.render();
            }

        }
    
    
    $('#dropdown').on('change', function sensorsDropdown() {
            document.getElementById('dropdown').submit();
        });",
    \yii\web\View::POS_END,
    'my-button-handler'
);
