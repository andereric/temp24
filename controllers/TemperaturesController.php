<?php

namespace app\controllers;

use app\models\Sensors;
use Yii;
use app\models\Temperatures;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TemperaturesController implements the CRUD actions for Temperatures model.
 */
class TemperaturesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Temperatures models.
     * @return mixed
     */
    public function actionIndex($sid = null, $vahemik = null, $average = null, $minimum = null, $maximum = null, $rightNow = null) // () hakkab parameetrit getist otsima
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Temperatures::find(),
        ]);



        $sensors = Sensors::find()->all();
        if ($vahemik) {
            if ($vahemik == 'tana') {
                $start = 'CURDATE()';
                $end = 'NOW()';
            } elseif ($vahemik == 'eile') {
                $start = 'DATE_SUB(CURDATE(), INTERVAL 1 DAY) ';
                $end = 'ADDTIME(DATE_SUB(CURDATE(), INTERVAL 1 DAY), "23:59:59")';
            } elseif ($vahemik == 'nadal') {
                $start = 'DATE_SUB(CURDATE(), INTERVAL(WEEKDAY(CURDATE())) DAY)';
                $end = 'NOW()';
            } else {
                $start = 'DATE_FORMAT(NOW() ,\'%Y-%m-01\')';
                $end = 'NOW()';
            }
            $whereClause = Temperatures::find()->where(['sid' => $sid])->andWhere(['between', 'time', new Expression($start), new Expression($end)]);
            $temperatures = $whereClause->all();
            $average = $whereClause->average('temperature');
            $minimum = $whereClause->min('temperature');
            $maximum = $whereClause->max('temperature');
            $rightNow = Temperatures::find()->where(['sid' => $sid])->orderBy(['id' => SORT_DESC])->one();


        } else {
            $whereClauseElse = Temperatures::find()->where(['sid' => $sid]);
            $temperatures = Temperatures::find()->where(['sid' => $sid])->all(); // otsib temperatuuri tabelis sid põhjal kõikide temperatuuride read
            $average = $whereClauseElse->average('temperature');
            $minimum = $whereClauseElse->min('temperature');
            $maximum = $whereClauseElse->max('temperature');
            $rightNow = $whereClauseElse->orderBy(['id' => SORT_DESC])->one();
        };




        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'sensors' => $sensors,
            'temperatures' => $temperatures,
            'sid' => $sid,
            'average' => $average,
            'minimum' => $minimum,
            'maximum' => $maximum,
            'rightNow' => $rightNow,
        ]);
    }

    /**
     * Displays a single Temperatures model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);

    }

    /**
     * Creates a new Temperatures model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Temperatures();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Temperatures model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Temperatures model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Temperatures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Temperatures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Temperatures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
