<?php

namespace app\controllers;

use app\models\Payment;
use app\models\Variant;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{

    /**
    * @inheritdoc
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Variant::find(),
        ]);

        return $this->render('variant/index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFull($id) {
        $model = $this->findModel($id);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $model->calculate(),
        ]);

        return $this->render('variant/full', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Variant model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('variant/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Variant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Variant();

        var_dump(Yii::$app->request->post());die;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('variant/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Variant model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('variant/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Variant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $payments = Payment::find()->where(['variant_id' => $id])->all();
        foreach ($payments as $payment) {
            $payment->delete();
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionClone($id) {
        $model = $this->findModel($id);
        $new_model = new Variant();
        $new_model->attributes = $model->attributes;

        $new_model->created_at = null;
        $new_model->updated_at = null;

        $new_model->save();

        $payments = Payment::find()->where(['variant_id' => $model->id])->all();
        foreach ($payments as $payment) {
            $new_payment = new Payment();
            $new_payment->attributes = $payment->attributes;
            $new_payment->variant_id = $new_model->id;
            $new_payment->save();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Variant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Variant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Variant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
