<?php
/**
 * Class SwfController
 * @package app\controllers
 * @author Setyo Legowo <setyo@urbanindo.com>
 */

namespace app\controllers;

/**
 * Class SwfController
 * @package app\controllers
 * @author Setyo Legowo <setyo@urbanindo.com>
 */
class SwfController extends \UrbanIndo\Yii2\Aws\Swf\Console\Controller
{
    /**
     * Register domain.
     * @return void
     */
    public function actionRegisterDomain()
    {
        $this->swfClient->registerDomain([
            'description' => 'Test',
            'workflowExecutionRetentionPeriodInDays' => 7,
        ]);
    }

    /**
     * Register workflow.
     * @param string $name           Workflow name.
     * @param array  $workflowParams Workflow params.
     * @return void
     */
    public function actionRegisterWorkflow($name, array $workflowParams = [])
    {
        $workflow = $this->swfClient->getWorkflowByName($name, $workflowParams);
        var_dump($this->swfClient->registerWorkflow($workflow));
    }

    /**
     * Register workflow.
     * @param string $name           Activity name.
     * @param array  $activityParams Activity params.
     * @return void
     */
    public function actionRegisterActivity($name, array $activityParams = [])
    {
        $activity = $this->swfClient->getActivityByName($name, $activityParams);
        var_dump($this->swfClient->registerActivity($activity));
    }
}
