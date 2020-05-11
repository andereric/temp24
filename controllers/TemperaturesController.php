<?php

namespace app\controllers;

use app\models\filters\TemperaturesFilter;
use app\models\Sensors;
use Yii;
use app\models\Temperatures;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\debug\models\timeline\Search;
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
    public function actionIndex()
    {
        $temperaturesFilter = new TemperaturesFilter();
        $query = $temperaturesFilter->search(Yii::$app->request->get());

        return $this->render('index', [
            'sensors' => Sensors::find()->all(),
            'temperatures' => $query->all(),
            'average' => $query->average('temperature'),
            'minimum' => $query->min('temperature'),
            'maximum' => $query->max('temperature'),
            'rightNow' => $query->orderBy(['id' => SORT_DESC])->one(),
            'temperaturesFilter' => $temperaturesFilter,
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
