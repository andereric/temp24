<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $sensors \app\models\Sensors[] */
/* @var $temperatures \app\models\Temperatures[]|null */ // t
/* @var $temperaturesFilter \app\models\filters\TemperaturesFilter */ // t

$this->title = Yii::t('app', 'Temperatuurid'); // Yii:t tõlkimiseks
$sensorName = ArrayHelper::getColumn($sensors, 'sid');

$datapoints = []; // saame kindlad olla, et datapoints on massiiv, mitte null vms
foreach ($temperatures as $temperature) {
    $datapoints[] = ['y' => round($temperature->temperature, $temperaturesFilter->precision), 'label' => $temperature->time];
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

        <!-- Loodud sensorinimedega nupud + perioodinupud + algus ning lõpu vahemike jaoks mõeldud lahtrid (käsitsi valimisiseks) + töötav ChartJS graafik -->

        <?php if ($temperaturesFilter->sid): ?>
            <br>
            <br>
            <label>Vali periood:</label>
            <br>
            <?= Html::a('TÄNA', ['', 'vahemik' => 'tana', 'sid' => $temperaturesFilter->sid], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('EILE', ['', 'vahemik' => 'eile', 'sid' => $temperaturesFilter->sid], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('NÄDAL', ['', 'vahemik' => 'nadal', 'sid' => $temperaturesFilter->sid], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('KUU', ['', 'vahemik' => 'kuu', 'sid' => $temperaturesFilter->sid], ['class' => 'btn btn-primary']) ?>
            <br>
            <br>


        <div class="wrapper">
            <?php
            $form = \yii\widgets\ActiveForm::begin(['method' => 'GET']);
            ?>
            <h2>Manuaalne otsing:</h2>
            <div class="col-sm-3">
                <?php
                echo $form->field($temperaturesFilter, 'startTime')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Vali algusaeg', 'autocomplete' => 'off'],
                'pluginOptions' => [
                'autoclose' => true
                ]
                ])->label(false);
                ?>
            </div>

            <div class="col-sm-3">
                <?php
                echo $form->field($temperaturesFilter, 'endTime')->widget(DateTimePicker::classname(), [
                    'options' => ['placeholder' => 'Vali lõpuaeg', 'autocomplete' => 'off'],
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ])->label(false);
                ?>
            </div>

            <?= Html::button('Vaata', ['class' => 'btn btn-primary', 'type' => 'submit'])?>
            <input type="hidden" name="sid" value="<?= $temperaturesFilter->sid ?>">
            <input type="hidden" name="precision" value="<?= $temperaturesFilter->precision ?>">
            <?php
            \yii\widgets\ActiveForm::end();
            ?>
        </div>


        <br>
        <br>
        <label>Vali täpsus:</label><br>
            <?= Html::a('1', ['', 'precision' => '0', 'sid' => $temperaturesFilter->sid, 'startTime' => $temperaturesFilter->startTime, 'endTime' => $temperaturesFilter->endTime], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('0.1', ['', 'precision' => '1', 'sid' => $temperaturesFilter->sid, 'startTime' => $temperaturesFilter->startTime, 'endTime' => $temperaturesFilter->endTime], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('0.01', ['', 'precision' => '2', 'sid' => $temperaturesFilter->sid, 'startTime' => $temperaturesFilter->startTime, 'endTime' => $temperaturesFilter->endTime], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('0.001', ['', 'precision' => '3', 'sid' => $temperaturesFilter->sid, 'startTime' => $temperaturesFilter->startTime, 'endTime' => $temperaturesFilter->endTime], ['class' => 'btn btn-primary']) ?>

        <?php endif; ?>
        <br><br>




        <br><br>
        <?php if (!$temperaturesFilter->sid): ?>
            <h2>Palun vali sensor</h2>

        <?php elseif ($temperatures): ?> <!--- https://www.php.net/manual/en/control-structures.alternative-syntax.php --->
            <p style="color:seagreen"><strong>Keskmine: </strong><?php echo round($average, $temperaturesFilter->precision); ?> kraadi</p>
            <p style="color:dodgerblue"><strong>Miinimum: </strong><?php echo round($minimum, $temperaturesFilter->precision); ?> kraadi</p>
            <p style="color:red"><strong>Maksimum: </strong><?php echo round($maximum, $temperaturesFilter->precision); ?> kraadi</p>
            <p><strong>Hetkel: </strong><?php echo round($rightNow->temperature, $temperaturesFilter->precision); ?> kraadi</p> <!-- Vajalik oli valida string arrayst -->
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>

        <?php else: ?>s
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
