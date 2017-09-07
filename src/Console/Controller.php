<?php
/**
 * Controller class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */

namespace UrbanIndo\Yii2\Aws\Swf\Console;

use Yii;
use UrbanIndo\Yii2\Aws\Swf\Client as SwfClient;

/**
 * Controller handles console command for running the SWF.
 *
 * To use the controller, update the controllerMap.
 *
 * return [
 *     // ...
 *     'controllerMap' => [
 *         'swf' => 'UrbanIndo\Yii2\Aws\Swf\Console\Controller'
 *     ],
 * ];
 *
 * To run
 *
 * yii swf
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */
class Controller extends \yii\console\Controller
{
    /**
     * @var string|array|Queue the name of the queue component. default to 'queue'.
     */
    public $swfClient = 'swfClient';

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->swfClient = \yii\di\Instance::ensure($this->swfClient, SwfClient::class);
    }

    /**
     * Start polling service for decider.
     * @return void
     */
    public function actionStartDeciderService()
    {
        while (true) {
            $workflow = null;
            $nextPageToken = null;

            while (true) {
                echo "[INFO] Polling...";
                $retval = $this->swfClient->pollForDecisionTask(array_filter(['nextPageToken' => $nextPageToken]));
                echo " Done\n";
                if (isset($retval['workflowType'])) {
                    if (is_null($workflow)) {
                        $workflow = $this->swfClient->getWorkflowByGuzzleModel($retval);
                    } else {
                        $workflow->pushEvents($retval['events']);
                    }
                }
                if (isset($retval['nextPageToken'])) {
                    $nextPageToken = $retval['nextPageToken'];
                    echo "[INFO] Repoll using previous token...";
                } else {
                    break;
                }
            }

            if (!is_null($workflow)) {
                printf("[INFO] Running workflow %s\n", get_class($workflow));
                $workflow->decide();
                $this->swfClient->submitDecision($workflow);
            }
        }
    }

    /**
     * Start polling service for single tasklist.
     * @return void
     */
    public function actionStartWorkflowService()
    {
        $retval = $this->swfClient->pollForActivityTask();
        var_dump($retval);
    }

    /**
     * Register workflow.
     * @param string $workflowName   Workflow name.
     * @param array  $workflowParams Workflow params.
     * @return void
     */
    public function actionStartWorkflow($workflowName, array $workflowParams = [], array $starterParams = [])
    {
        $workflow = $this->swfClient->getWorkflowByName($workflowName, $workflowParams);
        $starter = new \UrbanIndo\Yii2\Aws\Swf\Starter(array_merge($starterParams, [
            'workflow' => $workflow,
        ]));

        var_dump($this->swfClient->startWorkflow($starter));
    }

    /**
     * @param \Guzzle\Service\Resource\Model $model Model.
     * @return Workflow
     */
    protected function getWorkflowFromDeciderReturnVal(\Guzzle\Service\Resource\Model $model)
    {
        $workflowType = $model['workflowType'];
        return $this->swfClient->getWorkflowByName($workflowType['name']);
    }
}
