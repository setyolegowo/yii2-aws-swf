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
     * @param boolean $autoRepoll Automatically repoll.
     * @return void
     */
    public function actionStartDeciderService($autoRepoll = true)
    {
        while (true) {
            $workflow = null;
            $nextPageToken = null;

            while (true) {
                echo "[INFO] Polling...";
                $retval = $this->swfClient->pollForDecisionTask(array_filter(['nextPageToken' => $nextPageToken]));
                var_dump($retval);
                echo " Done\n";
                if (isset($retval['workflowType'])) {
                    if (is_null($workflow)) {
                        $workflow = $this->swfClient->getWorkflowByGuzzleModel($retval);
                    }
                    $workflow->pushEvents($retval['events']);
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
                var_dump($workflow);
                $this->swfClient->submitDecision($workflow);
            }
            if (!$autoRepoll) {
                break;
            }
        }
    }

    /**
     * Start polling service for single tasklist.
     * @param boolean $autoRepoll Automatically repoll.
     * @return void
     */
    public function actionStartActivityService($autoRepoll = true)
    {
        while (true) {
            echo "[INFO] Polling...";
            $retval = $this->swfClient->pollForActivityTask();
            echo " Done\n";
            if (isset($retval['activityType'])) {
                $activity = $this->swfClient->getActivityByGuzzleModel($retval);
                printf("[INFO] Running activity %s\n", get_class($activity));
                $activity->run();
                $this->swfClient->respondActivity($activity);
            }
            if (!$autoRepoll) {
                break;
            }
        }
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
}
