<?php

namespace app\modules\counterparties\controllers;

use app\components\ModelPreload;
use app\components\pdf\Pdf;
use app\controllers\BaseController;
use app\models\OpersFms;
use app\modules\communication\services\CommunicationService;
use app\modules\counterparties\models\Counterparties;
use app\modules\counterparties\models\CounterpartiesFms;
use app\modules\counterparties\models\CounterpartiesLog;
use app\modules\counterparties\models\CounterpartiesOpers;
use app\modules\counterparties\models\form\add\communication\FormCounterpartyAddChannel;
use app\modules\counterparties\models\form\add\FormCounterpartyAdd;
use app\modules\counterparties\models\form\edit\FormCounterpartyEdit;
use app\modules\counterparties\models\form\edit\FormCounterpartyEditServices;
use app\modules\counterparties\models\form\edit\FormCounterpartyEditServicesJunctionPoint;
use app\modules\counterparties\models\FormCounterpartyEmployees;
use app\modules\counterparties\models\FormFindCounterparties;
use app\modules\counterparties\models\FormGenerateEvents;
use app\modules\counterparties\models\PaymentAccount;
use app\modules\counterparties\services\CounterpartyDadataService;
use app\modules\counterparties\services\CounterpartyEmployeesService;
use app\modules\counterparties\services\CounterpartyService;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\user\constants\UserCounterpartyTypes;
use app\modules\user\dto\UserCounterpartiesLinkDto;
use app\modules\user\models\UserJunctionPointLink;
use app\modules\user\models\Users;
use app\modules\user\services\UserCounterpartyService;
use app\modules\userside\models\api\ApiSwitch;
use Exception;
use kartik\widgets\ActiveForm;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class CounterpartiesController extends BaseController
{
    public function actionIndex()
    {
        Yii::$app->user->canOrThrow('counterparties.access');

        $model = new FormFindCounterparties();
        $model->load(Yii::$app->request->get(), '');
        $query = $model->find();

        $pager = new Pagination();
        $pager->pageSize = 50;
        $pager->totalCount = $query->count();

        $query->limit($pager->limit);
        $query->offset($pager->offset);

        $counterparties = $query->orderBy(['date_add' => SORT_DESC])->all();

        $can_add = Yii::$app->user->can('counterparties.add');
        $can_delete = Yii::$app->user->can('counterparties.delete');

        $this->view->title = "Контрагенты";
        return $this->render('index', [
            'model'      => $model,
            'can_add'    => $can_add,
            'can_delete' => $can_delete,
            'counterparties' => $counterparties,
            'pager'      => $pager
        ]);
    }

}
