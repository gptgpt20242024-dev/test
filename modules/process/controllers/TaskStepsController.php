<?php
/**
 * Created by PhpStorm.
 * User: caHek
 * Date: 11.07.2016
 * Time: 18:47
 */

namespace app\modules\process\controllers;


use app\components\Str;
use app\controllers\BaseController;
use app\models\Opers;
use app\modules\process\components\HelperPreload;
use app\modules\process\constants\IdentifierCompleteErrors;
use app\modules\process\models\FormReq3Remark;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3FunctionBase;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemIdentifierComments;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_opers\Req3TaskOperOnline;
use app\modules\process\models\template_steps\Req3TemplateStepRule2Functions;
use app\modules\process\models\template_steps\Req3TemplateStepRuleFunctions;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use app\modules\process\services\ProcessTaskService;
use Exception;
use Throwable;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class TaskStepsController extends BaseController
{
    public function actionHistory($task_id)
    {
        $task = Req3Tasks::find()->id($task_id)->one();
        if (!$task) throw new NotFoundHttpException("Ничего не найдено");
        if (!$task->isAccessView(Yii::$app->user->identity)) throw new ForbiddenHttpException("Нет доступа");

        $items = [];

        $before_full_info_transitions = false;

        foreach ($task->step_history as $item) {
            $items_step = [];

            //проверяем если в предыдущем итеме истории была информация о переходе то всё ок иначе добавим переход
            if (!$before_full_info_transitions) {
                $items[] = [
                    'time'     => strtotime($item->start_date),
                    'type'     => 'transition',
                    'priority' => 4,
                    'item'     => $item
                ];
            }
            $before_full_info_transitions = false;
            $data = $item->getDataArray();

            //информация о шаге
            $item_step = [
                'time'     => strtotime($item->start_date),
                'type'     => 'step',
                'priority' => 5,
                'item'     => $item,
                'online'   => []
            ];
            if (empty($item->end_date)) {
                $data['online'] = Req3TaskOperOnline::find()
                    ->select(new Expression("SUM(online_seconds) seconds"))
                    ->andWhere(['task_id' => $task->id, 'step_id' => $task->step_id])
                    ->groupBy(['oper_id'])
                    ->indexBy('oper_id')
                    ->column();
            }
            if (isset($data['online'])) {
                foreach ($data['online'] as $oper_id => $seconds) {
                    if (is_array($seconds)) {
                        $oper_id = $seconds['oper_id'];
                        $seconds = $seconds['seconds'];
                    }
                    if (!isset($item_step['online'][$oper_id])) {
                        $item_step['online'][$oper_id] = 0;
                    }
                    $item_step['online'][$oper_id] += $seconds;
                }
            }
            $items[] = $item_step;

            //детальные переходы
            if (isset($data['transitions'])) {
                foreach ($data['transitions'] as $transition) {
                    if (isset($transition['time_start'])) {
                        if (array_key_exists('rule2_id', $transition)) {
                            $transition['from_step_id'] = $item->step_id;
                            $items_step[] = [
                                'time'     => $transition['time_start'],
                                'type'     => 'rule2_detail',
                                'priority' => 4,
                                'item'     => $transition
                            ];
                        } else {
                            $items_step[] = [
                                'time'     => $transition['time_start'],
                                'type'     => 'transition_detail',
                                'priority' => 4,
                                'item'     => $transition
                            ];
                        }
                        $before_full_info_transitions = true;
                    }
                }
            }

            //информационные сообщения
            if (isset($data['info'])) {
                foreach ($data['info'] as $info) {
                    $items_step[] = [
                        'time'     => $info['time_start'],
                        'type'     => 'info',
                        'priority' => 0,
                        'item'     => $info
                    ];
                }
            }


            //информационные сообщения
            if (isset($data['link'])) {
                foreach ($data['link'] as $link) {
                    $items_step[] = [
                        'time'     => $link['time_start'],
                        'type'     => 'link',
                        'priority' => 0,
                        'item'     => $link
                    ];
                }
            }

            //ф-ции
            if (isset($data['functions'])) {
                foreach ($data['functions'] as $i => $function) {
                    $function_item = [
                        'time'     => $function['time_start'],
                        'type'     => 'function',
                        'priority' => 2,
                        'n'        => $i,
                        'item'     => $function
                    ];
                    //------------------------------------------
                    //поддержка старых задач
                    //если не было детального перехода то надо сдвинуть фции к информации о выходе из шага
                    if (!$before_full_info_transitions) {
                        if (($function['type'] ?? Req3FunctionBase::TYPE_NEXT_STEP) == Req3FunctionBase::TYPE_NEXT_STEP && $item->end_date != null) {
                            $function_item['time'] = strtotime($item->end_date);//костыль, что бы фции шли после информации о нажатии на переход
                        }
                    }
                    //------------------------------------------
                    $items_step[] = $function_item;
                }
            }

            //данные
            if (isset($data['data_change'])) {
                foreach ($data['data_change'] as $data_item) {
                    $time = $data_item['time'] ?? strtotime($item->start_date);
                    $items_step[] = [
                        'time'     => $time,
                        'type'     => 'data',
                        'priority' => 3,
                        'item'     => $data_item
                    ];
                }
            }

            usort($items_step, function ($a, $b) {
                if ($a['time'] != $b['time']) return $a['time'] <=> $b['time'];
                if ($a['priority'] != $b['priority']) return $a['priority'] <=> $b['priority'];
                if (isset($a['n']) && isset($b['n'])) return $a['n'] <=> $b['n'];
                return 0;
            });
            foreach ($items_step as $item_step) {
                $items[] = $item_step;
            }
        }


        $this->view->params['breadcrumbs'][] = ['label' => "Шаблоны", 'url' => ['/process/templates/index']];
        $this->view->params['breadcrumbs'][] = ['label' => "Задачи", 'url' => ['/process/task/index']];
        $this->view->params['breadcrumbs'][] = ['label' => Str::compactOverflow($task->name, 60), 'url' => ['/process/task/view', 'id' => $task->id]];
        $this->view->params['breadcrumbs'][] = "Движение";
        $this->view->title = "{$task->name} - Движение";
        return $this->render('history', [
            'task'  => $task,
            'items' => $items,
        ]);
    }

    public function actionSetStep($id, $step_id)
    {
        $task = Req3Tasks::find()->id($id)->one();
        if (!$task) throw new NotFoundHttpException("Ничего не найдено");
        if (!$task->isAccessActionNextStep(Yii::$app->user->identity)) throw new ForbiddenHttpException("Нет доступа");
        if (!$task->step->isDeviation()) throw new ForbiddenHttpException("Только с шага отклонение/отклонение архитектора можно ставить произвольный шаг");
        if ($task->active_chat) throw new ForbiddenHttpException("Запрещено двигать задачу пока есть активный чат.");
        if ($task->step->block_by_oper_id ?? null) {
            throw new ForbiddenHttpException("Вы не можете что либо делать на этом шаге т.к. " . Opers::getFioOrFioDeletedHtml($task->step, 'blockOper', 'block_by_oper_id') . " заблокировал шаг");
        }

        $step = Req3TemplateSteps::find()->id($step_id)->one();
        if (!$step) throw new NotFoundHttpException("Не найден шаг");
        if ($step->version_id != $task->version_id) throw new BadRequestHttpException("Шаг не относится к версии ");

        $identifier_from_deviation_to_close = Yii::$app->controller->module->params['identifier_from_deviation_to_close'] ?? null;
        $identifier_from_deviation_to_other = Yii::$app->controller->module->params['identifier_from_deviation_to_other'] ?? null;


        $identifier_name = false;
        $identifier = null;
        if ($step->is_last == 1) {
            $identifier_name = $identifier_from_deviation_to_close;
        } else {
            if (!$task->isDeviationJobComplete()) {
                $identifier_name = $identifier_from_deviation_to_other;
            }
        }

        if ($identifier_name !== false) {
            $identifier = Req3Identifiers::find()->andWhere(['identifier' => $identifier_name])->andWhere(['is', 'version_id', null])->one();
            if (!$identifier) {
                throw new ServerErrorHttpException("В конфиге не указан идентификатор ухода с отклонения");
            }
        }

        if (Yii::$app->request->isPost) {
            try {
                if ($identifier) {
                    $data = Yii::$app->request->post("Req3TasksDataItems", []);
                    Req3TasksDataItems::preLoadFiles($data, 'Req3TasksDataItems');
                    $errors = [];
                    $data = Req3TasksDataItems::loadCreateObject($data, $task, Yii::$app->user->id, $errors, true);
                    if (count($errors) > 0) {
                        throw new Exception(implode(", ", $errors));
                    }

                    $check = [];
                    foreach ($data as $value) {
                        if (!isset($check[$value->identifier_id])) {
                            $check[$value->identifier_id] = true;
                            if (!$value->isWorkRaterConfirmed(Yii::$app->user->id)) {
                                throw new Exception("Для заполнения идентификатора, ознакомьтесь со стандартом и регламентом, и если Вам все понятно - нажмите кнопку 'Обучился'");
                            }
                        }
                    }
                    if (!Req3TasksDataItems::isFillData($identifier, $data, $task)) {
                        throw new Exception("Не все данные заполнены (" . $identifier->name . ")");
                    }
                    $task->setData($data);
                }

                try {
                    $task->toStep(Yii::$app->user->id, $step_id);
                } catch (Throwable $e) {
                    Yii::warning("Ошибка при выполнении перехода в бп (задача {$task->id}) (" . $e->getMessage() . ").");
                    throw $e;
                }

                return $this->redirect(['task/view', 'id' => $id]);

            } catch (Throwable $e) {
                $task->addError('other', $e->getMessage());
            }
        }

        $this->view->params['breadcrumbs'][] = ['label' => "Шаблоны", 'url' => ['/process/templates/index']];
        $this->view->params['breadcrumbs'][] = ['label' => "Задачи", 'url' => ['/process/task/index']];
        $this->view->params['breadcrumbs'][] = ['label' => Str::compactOverflow($task->name, 60), 'url' => ['/process/task/view', 'id' => $task->id]];
        $this->view->params['breadcrumbs'][] = "Перевод из Отклонения";
        $this->view->title = "Перевод из Отклонения - {$task->name}";

        return $this->render('set-step', [
            'task'       => $task,
            'step'       => $step,
            'identifier' => $identifier,
        ]);
    }

    public function actionNextStep($id)
    {
        $rule2Id = Yii::$app->request->get('rule2_id');
        $task = Req3Tasks::find()->id($id)->one();
        if (!$task) throw new NotFoundHttpException("Не найдена задача");
        if (!$task->isAccessActionNextStep(Yii::$app->user->identity)) throw new ForbiddenHttpException("Нет доступа");
        if ($task->active_chat) throw new ForbiddenHttpException("Запрещено двигать задачу пока есть активный чат.");
        if ($task->step->block_by_oper_id ?? null) {
            throw new ForbiddenHttpException("Вы не можете что либо делать на этом шаге т.к. " . Opers::getFioOrFioDeletedHtml($task->step, 'blockOper', 'block_by_oper_id') . " заблокировал шаг");
        }

        //если прилетели решения по переходу их можно сразу сохранить без проверок ограничений по задаче чтоб не потерять данные
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post($task->formName(), []);
            if (isset($data['function_solves'])) {
                $task->addSolves($data['function_solves'], Yii::$app->user->id);
            }
        }

        $taskService = Yii::$container->get(ProcessTaskService::class);
        $data = $taskService->processRule2($task, $task->step_id);
        if ($data->isUpdateTask) {
            Yii::$app->session->addFlash("toast", "Во время выполнения запроса сработала автоматика, страница была обновлена.");
            return $this->redirect(['/process/task/view', 'id' => $task->id]);
        }

        $rule = $data->rule;
        $identifiers = $data->identifiers;
        /** @var Req3TemplateStepRule2Functions[] $functionsNext */
        $functionsNext = $data->functionsNext;

        $notComplete = $taskService->checkIdentifierComplete($task, $identifiers);
        if (isset($notComplete[IdentifierCompleteErrors::ERROR_COMPLETE_FILL])) {
            throw new BadRequestHttpException("Не все данные заполнены (" . implode(", ", ArrayHelper::getColumn($notComplete[IdentifierCompleteErrors::ERROR_COMPLETE_FILL], 'name')) . ")");
        }
        if (isset($not_complete[IdentifierCompleteErrors::ERROR_COMPLETE_REMARKS])) {
            throw new BadRequestHttpException("Не все улучшения исправлены (" . implode(", ", ArrayHelper::getColumn($notComplete[IdentifierCompleteErrors::ERROR_COMPLETE_REMARKS], 'name')) . ")");
        }

        if ($rule) {
            if ($rule->id != $rule2Id) {
                Yii::$app->session->addFlash("toast", "То что у вас на экране не соответствует реальным данным, страница будет обновлена, повторите переход если еще актуально.");
                return $this->redirect(['/process/task/view', 'id' => $id]);
            }


            $isLimit = $rule->isExceededTransitions($task);
            $toStepId = $rule->to_step_id;
            $toStep = $rule->toStep;
            if ($isLimit) {
                if (!empty($rule->to_step_limit_id)) {
                    $toStepId = $rule->to_step_limit_id;
                    $toStep = $rule->toStepLimit;
                } else {
                    throw new ForbiddenHttpException("Достигнут лимит перехода по маршруту.");
                }
            }

            if (!$rule->isAccessAction(Yii::$app->user->identity, $task)) {
                throw new ForbiddenHttpException("Маршрут ограничен.");
            }

            $endToEndTimeData = [];
            foreach ($functionsNext as $function) {
                $solve = $function->getSomethingSolve($task, Yii::$app->user->id, $endToEndTimeData);
                if (isset($solve['correct']) && !$solve['correct']) {
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'error'       => 1,
                            'view'        => $this->render('solve_function/_solve', [
                                'task'     => $task,
                                'function' => $function,
                                'solve'    => $solve,
                            ]),
                            'is_solvable' => $solve['is_solvable'] ?? false
                        ];
                    } else {
                        throw new BadRequestHttpException("Ошибка в ф-ции {$function->getFunctionName()}.");
                    }
                }
            }

            //если это подтверждение на переход
            if (Yii::$app->request->isPost) {
                if (Yii::$app->request->post('confirm')) {
                    try {
                        if ($isLimit) {
                            $task->addLogHistoryInfoText("У маршрута был достигнут лимит переходов, двигаем задачу на специальный шаг.");
                        }
                        $task->toStep(Yii::$app->user->id, $toStepId, $rule, $functionsNext, true, false, $data);
                    } catch (Throwable $e) {
                        Yii::warning("Ошибка при выполнении перехода в бп (задача {$task->id}) (" . $e->getMessage() . ").");
                        throw new ServerErrorHttpException($e->getMessage());
                    }
                    return $this->redirect(['task/view', 'id' => $id]);
                }
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'ok'   => 1,
                    'view' => $this->renderAjax('next_step', [
                        'task'        => $task,
                        'rule'        => $rule,
                        'toStep' => $toStep,
                        'identifiers' => [],
                        'functions'   => $functionsNext,
                    ]),
                ];
            } else {
                return $this->redirect(['task/view', 'id' => $id]);
            }
        } else {
            throw new NotFoundHttpException("Не доступен ни один маршрут в условиях 2.1.");
        }
    }

    public function actionStartFunction($id, $function_id)
    {
        $task = Req3Tasks::find()->id($id)->one();
        if (!$task) throw new NotFoundHttpException("Не найдена задача");
        if (!$task->isAccessActionNextStep(Yii::$app->user->identity)) throw new ForbiddenHttpException("Нет доступа");
        if ($task->step->block_by_oper_id ?? null) {
            throw new ForbiddenHttpException("Вы не можете что либо делать на этом шаге т.к. " . Opers::getFioOrFioDeletedHtml($task->step, 'blockOper', 'block_by_oper_id') . " заблокировал шаг");
        }

        $taskService = Yii::$container->get(ProcessTaskService::class);
        $ruleData = $taskService->processRule2($task, $task->step_id);
        if ($ruleData->isUpdateTask) {
            Yii::$app->session->addFlash("toast", "Во время выполнения запроса сработала автоматика, страница была обновлена.");
            return $this->redirect(['/process/task/view', 'id' => $task->id]);
        }

        $function = $ruleData->getFunction($function_id);
        if (!$function) {
            throw new NotFoundHttpException("В маршруте нет такой ф-ции обновите страницу.");
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post($task->formName(), []);
            if (isset($data['function_solves'])) {
                $task->addSolves($data['function_solves'], Yii::$app->user->id);
            }
        }

        //если это подтверждение на запуск
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (isset($data['confirm'])) {
                if (!($function->start($task, Yii::$app->user->id))) {
                    throw new ServerErrorHttpException(implode(", ", $task->getFirstErrors()));
                } else {
                    return $this->redirect(['task/view', 'id' => $id]);
                }
            }
        }

        $solve = $function->getSomethingSolve($task, Yii::$app->user->id);
        if (isset($solve['correct']) && !$solve['correct']) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'error'       => 1,
                    'view'        => $this->render('solve_function/_solve', [
                        'task'     => $task,
                        'function' => $function,
                        'solve'    => $solve,
                    ]),
                    'is_solvable' => $solve['is_solvable'] ?? false
                ];
            } else {
                throw new BadRequestHttpException("Ошибка в ф-ции {$function->getFunctionName()}.");
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'ok'   => 1,
            'view' => $this->renderAjax('start_function', [
                'task'     => $task,
                'function' => $function,
            ]),
        ];
    }

    public function actionAjaxViewMain($task_id)
    {
        $task = Req3Tasks::find()->id($task_id)->one();
        if (!$task) throw new NotFoundHttpException("Не найдена задача");
        if (!$task->isAccessView(Yii::$app->user->identity)) throw new ForbiddenHttpException("Нет доступа");

        HelperPreload::preload([$task], [HelperPreload::PRELOAD_VIEW_DATA]);
        $processService = Yii::$container->get(ProcessTaskService::class);
        $taskData = $processService->getData($task);
        $ruleData = $processService->processRule2($task, $task->step_id);
        if ($ruleData->isUpdateTask) {
            Yii::$app->session->addFlash("toast", "Во время выполнения запроса сработала автоматика, страница была обновлена.");
            return $this->redirect(['/process/task/view', 'id' => $task->id]);
        }

        $ruleData->identifiers = $processService->checkIdentifierFillOrFilter($ruleData->identifiers, $taskData);
        HelperPreload::preload([$task], [HelperPreload::PRELOAD_VIEW_DATA], [$task->id => $ruleData->identifiers]);

        $notCompleteIdentifiers = $processService->checkIdentifierComplete($task, $ruleData->identifiers);

        return $this->render('header', [
            'task'                   => $task,
            'ruleData'               => $ruleData,
            'notCompleteIdentifiers' => $notCompleteIdentifiers,
        ]);
    }

    public function actionAjaxViewHeader($task_id)
    {
        $task = Req3Tasks::find()->id($task_id)->one();
        if (!$task) throw new NotFoundHttpException("Не найдена задача");
        if (!$task->isAccessView(Yii::$app->user->identity)) throw new ForbiddenHttpException("Нет доступа");

        $data_check = Req3TasksDataItemIdentifierComments::getIdentifiersAndWorksCheckWork();
        $task_check_step = $data_check['step_ids'][$task->step_id] ?? null;
        $task_check_work = $data_check['work_rated_ids'][$task->step->work_rated_id ?? null] ?? null;

        return $this->render('/task/view/header', [
            'task'            => $task,
            'task_check_step' => $task_check_step,
            'task_check_work' => $task_check_work,
        ]);
    }

    public function actionAjaxViewAddRemark($task_id, $step_id)
    {
        $task = Req3Tasks::find()->id($task_id)->one();
        if (!$task) throw new NotFoundHttpException("Не найдена задача");

        $step = Req3TemplateSteps::find()->id($step_id)->one();
        if (!$step) throw new NotFoundHttpException("Не найден шаг");

        if (!$task->canAddRemark(Yii::$app->user->identity, $step)) {
            throw new ForbiddenHttpException("Вы не можете добавлять улучшение");
        }

        $remark = new FormReq3Remark(Yii::$app->user->id, $task, $step);

        return $this->render('add-remark', [
            'task'   => $task,
            'step'   => $step,
            'remark' => $remark,
        ]);
    }

    public function actionAjaxSaveRemark($task_id, $step_id)
    {
        $task = Req3Tasks::find()->id($task_id)->one();
        if (!$task) throw new NotFoundHttpException("Не найдена задача");

        $step = Req3TemplateSteps::find()->id($step_id)->one();
        if (!$step) throw new NotFoundHttpException("Не найден идентификатор");

        if (!$task->canAddRemark(Yii::$app->user->identity, $step)) {
            throw new ForbiddenHttpException("Вы не можете добавлять улучшение");
        }

        $remark = new FormReq3Remark(Yii::$app->user->identity, $task, $step);
        if ($remark->load(Yii::$app->request->post())) {
            if ($remark->start()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['ok' => 1];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['ok' => 0, 'error' => implode(", ", $remark->getFirstErrors())];
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['ok' => 0, 'error' => "Нет данных"];
        }
    }

}