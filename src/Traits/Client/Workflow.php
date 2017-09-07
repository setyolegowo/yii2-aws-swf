<?php
/**
 * Workflow trait file.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */

namespace UrbanIndo\Yii2\Aws\Swf\Traits\Client;

use Guzzle\Service\Resource\Model as GuzzleModel;
use UrbanIndo\Yii2\Aws\Swf\Interfaces\Client\Polling as PollingInterface;
use UrbanIndo\Yii2\Aws\Swf\Base\Workflow as BaseWorkflow;

/**
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */
trait Workflow
{
    /**
     * @var array
     */
    public $defaultTaskList = ['name' => 'main'];

    /**
     * @var string
     */
    public $workflowNamespace = '';

    /**
     * @var array
     */
    public $workflowClassMap = [];

    /**
     * @return void
     */
    private function workflowInit()
    {
        if (empty($this->workflowNamespace)) {
            Yii::warning('Workflow namespace undefined');
        }
        $this->workflowNamespace = str_replace('/', '\\', $this->workflowNamespace);
    }

    /**
     * @param BaseWorkflow $workflow Workflow object.
     * @param array        $config   Additional config.
     * @return mixed
     */
    public function registerWorkflow(BaseWorkflow $workflow, array $config = [])
    {
        $wokflowType = $workflow->getWorkflowType();
        $name = $wokflowType['name'];
        $version = $wokflowType['version'];

        return $this->client->registerWorkflowType(array_filter(array_merge(
            [
                'description' => $workflow->description,
                'defaultTaskList' => $this->defaultTaskList,
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
     * @param array $config Additional config.
     * @return mixed
     */
    public function pollForDecisionTask(array $config = [])
    {
        $params = array_merge(
            [
                'taskList' => $this->defaultTaskList,
                'maximumPageSize' => PollingInterface::DEFAULT_DECISION_POLLING_MAX_PAGE,
            ],
            $config,
            [
                'domain' => $this->domain,
            ]
        );

        return $this->client->pollForDecisionTask($params);
    }

    /**
     * @param BaseWorkflow $workflow Workflor.
     * @return mixed
     */
    public function submitDecision(BaseWorkflow $workflow)
    {
        $decisions = $workflow->getDecisions();
        if (empty($decisions)) {
            Yii::info('No decision has been return');
            return;
        }

        return $this->client->respondDecisionTaskCompleted([
            'taskToken' => $workflow->taskToken,
            'decisions' => $decisions,
            'executionContext' => $workflow->executionContext,
        ]);
    }

    /**
     * @param string $name           Workflow name.
     * @param array  $workflowParams Workflow params.
     * @return BaseWorkflow
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
     * @return BaseWorkflow
     */
    public function getWorkflowByGuzzleModel(GuzzleModel $model)
    {
        return $this->getWorkflowByName($model['workflowType']['name'], [
            'version' => $model['workflowType']['version'],
            'taskToken' => $model['taskToken'],
            'workflowExecution' => $model['workflowExecution'],
        ]);
    }
}
