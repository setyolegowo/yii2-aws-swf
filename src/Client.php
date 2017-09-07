<?php
/**
 * Client class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */

namespace UrbanIndo\Yii2\Aws\Swf;

use Yii;
use Aws\Swf\SwfClient;
use Guzzle\Service\Resource\Model as GuzzleModel;
use UrbanIndo\Yii2\Aws\Swf\Base\Activity;
use UrbanIndo\Yii2\Aws\Swf\Base\Workflow;

/**
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */
class Client extends \yii\base\Object
{
    /**
     * @var string
     */
    public $domain;

    /**
     * @var array
     */
    public $taskList = ['name' => 'main'];

    /**
     * @var array
     */
    public $config;

    /**
     * @var string
     */
    public $workflowNamespace = '';

    /**
     * @var array
     */
    public $workflowClassMap = [];

    /**
     * @var string
     */
    public $activityNamespace = '';

    /**
     * @var array
     */
    public $activityClassMap = [];

    /**
     * @var SwfClient
     */
    private $_client;

    /**
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (empty($this->domain)) {
            throw new \yii\base\InvalidConfigException('Domain name cannot be empty');
        }
        if (empty($this->workflowNamespace)) {
            Yii::warning('Workflow namespace undefined');
        }
        $this->workflowNamespace = str_replace('/', '\\', $this->workflowNamespace);

        if (empty($this->activityNamespace)) {
            Yii::warning('Activity namespace undefined');
        }
        $this->activityNamespace = str_replace('/', '\\', $this->activityNamespace);

        $this->_client = SwfClient::factory($this->config);
    }

    /**
     * @return SwfClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @param array $config Config.
     * @return mixed
     */
    public function registerDomain(array $config = [])
    {
        return $this->_client->registerDomain(array_merge($config, [
            'name' => $this->domain,
        ]));
    }

    /**
     * @param Workflow $workflow Workflow object.
     * @param array    $config   Additional config.
     * @return mixed
     */
    public function registerWorkflow(Workflow $workflow, array $config = [])
    {
        $wokflowType = $workflow->getWorkflowType();
        $name = $wokflowType['name'];
        $version = $wokflowType['version'];

        return $this->_client->registerWorkflowType(array_filter(array_merge(
            [
                'description' => $workflow->description,
                'defaultTaskList' => $this->taskList,
                'defaultChildPolicy' => $workflow->childPolicy,
            ],
            $config,
            [
                'domain' => $this->domain,
                'name' => $name,
                'version' => $version,
            ]
        )));
    }

    /**
     * @param Activity $activity Activity object.
     * @param array    $config   Additional config.
     * @return mixed
     */
    public function registerActivity(Activity $activity, array $config = [])
    {
        $activityType = $activity->getActivityType();
        $name = $activityType['name'];
        $version = $activityType['version'];

        return $this->_client->registerActivityType(array_filter(array_merge(
            [
                'description' => $activity->description,
                'defaultTaskList' => $this->taskList,
            ],
            $config,
            [
                'domain' => $this->domain,
                'name' => $name,
                'version' => $version,
        ])));
    }

    /**
     * @param array $config Additional config.
     * @return mixed
     */
    public function pollForDecisionTask(array $config = [])
    {
        return $this->_client->pollForDecisionTask(array_merge(
            [
                'taskList' => $this->taskList,
                'maximumPageSize' => 3,
            ],
            $config,
            [
                'domain' => $this->domain,
            ]
        ));
    }

    /**
     * @param array $config Additional config.
     * @return mixed
     */
    public function pollForActivityTask(array $config = [])
    {
        return $this->_client->pollForActivityTask(array_merge(
            [
                'taskList' => $this->taskList,
            ],
            $config,
            [
                'domain' => $this->domain,
            ]
        ));
    }

    /**
     * @param string $name      Method name.
     * @param mixed  $arguments Arguments.
     * @return mixed
     */
    public function startWorkflow(Starter $starter)
    {
        return $this->_client->startWorkflowExecution(array_merge(
            [
                'taskList' => $this->taskList,
            ],
            $starter->getParams(),
            [
                'domain' => $this->domain,
            ]
        ));
    }

    /**
     * @param Workflow $workflow Workflor.
     * @return mixed
     */
    public function submitDecision(Workflow $workflow)
    {
        $decisions = $workflow->getDecisions();
        if (empty($decisions)) {
            echo "[INFO] Has NO decision\n";
            Yii::info('No decision has been return');
            return;
        }
        echo "[INFO] Has decision\n";

        return $this->_client->respondDecisionTaskCompleted([
            'taskToken' => $workflow->taskToken,
            'decisions' => $decisions,
            'executionContext' => $workflow->executionContext,
        ]);
    }

    /**
     * @param Activity $activity Activity.
     * @return mixed
     */
    public function respondActivity(Activity $activity)
    {
        $command = 'respondActivityTask' . $activity->resultStatus;
        return $this->_client->{$command}(array_merge(
            $activity->respond,
            [
                'taskToken' => $activity->taskToken
            ]
        ));
    }

    /**
     * @param string $name           Workflow name.
     * @param array  $workflowParams Workflow params.
     * @return Workflow
     */
    public function getWorkflowByName($name, array $workflowParams = [])
    {
        if (count($this->workflowClassMap) > 0 && in_array($name, array_keys($this->workflowClassMap))) {
            $className = $this->workflowClassMap[$name];
        } elseif (strlen($this->workflowNamespace) > 0) {
            $className = "{$this->workflowNamespace}\\${name}";
        } else {
            $className = $name;
        }

        return new $className(array_merge([
            'name' => $name,
        ], $workflowParams));
    }

    /**
     * @param GuzzleModel $model Guzzle model.
     * @return Workflow
     */
    public function getWorkflowByGuzzleModel(GuzzleModel $model)
    {
        $workflow = $this->getWorkflowByName($model['workflowType']['name'], [
            'taskToken' => $model['taskToken'],
        ]);
        $workflow->pushEvents($model['events']);

        return $workflow;
    }

    /**
     * @param string $name           Activity name.
     * @param array  $activityParams Activity params.
     * @return Activity
     */
    public function getActivityByName($name, array $activityParams = [])
    {
        if (count($this->activityClassMap) > 0 && in_array($name, array_keys($this->activityClassMap))) {
            $className = $this->activityClassMap[$name];
        } elseif (strlen($this->activityNamespace) > 0) {
            $className = "{$this->activityNamespace}\\${name}";
        } else {
            $className = $name;
        }

        return new $className(array_merge([
            'name' => $name,
        ], $activityParams));
    }
}
