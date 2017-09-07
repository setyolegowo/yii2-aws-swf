<?php
/**
 * Starter trait file.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */

namespace UrbanIndo\Yii2\Aws\Swf\Traits\Client;

use UrbanIndo\Yii2\Aws\Swf\Starter as StarterClass;
use UrbanIndo\Yii2\Aws\Swf\Interfaces\Client\Starter as StarterInterface;

/**
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */
trait Starter
{
    /**
     * @var array
     */
    public $starterDefaultParams = [];

    /**
     * @param StarterClass $starter Starter class.
     * @return mixed
     */
    public function startWorkflow(StarterClass $starter)
    {
        $params = array_merge(
            $this->getStarterDefaultParams(),
            [
                'taskList' => $this->defaultTaskList,
            ],
            $starter->getParams(),
            [
                'domain' => $this->domain,
            ]
        );

        return $this->client->startWorkflowExecution($params);
    }

    /**
     * @return array
     */
    private function getStarterDefaultParams()
    {
        return array_merge([
            // Execution start to close timeout in second.
            'executionStartToCloseTimeout' => StarterInterface::DEFAULT_STARTER_EXECUTION_START_TO_CLOSE_TIMEOUT,
            // Task start to close timeout in second.
            'taskStartToCloseTimeout' => StarterInterface::DEFAULT_STARTER_EXECUTION_START_TO_CLOSE_TIMEOUT
        ], $this->starterDefaultParams);
    }
}
