<?php
/**
 * Starter class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */

namespace UrbanIndo\Yii2\Aws\Swf;

use Yii;
use UrbanIndo\Yii2\Aws\Swf\Base\Workflow;

/**
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */
class Starter extends \yii\base\Object
{
    /**
     * @var string
     */
    public $workflowId;

    /**
     * @var Workflow
     */
    public $workflow;

    /**
     * @var string
     */
    public $input = 'Test';

    /**
     * Execution start to close timeout in second.
     * @var integer
     */
    public $executionStartToCloseTimeout = 300;

    /**
     * Task start to close timeout in second.
     * @var integer
     */
    public $taskStartToCloseTimeout = 300;

    /**
     * @var array
     */
    public $additionalOptions = [];

    /**
     * @var array
     */
    private $_workFlowType;

    /**
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!empty($this->workflow) && $this->workflow instanceof Workflow) {
            $this->_workFlowType = $this->workflow->getWorkflowType();
        } else {
            throw new \yii\base\InvalidParamException('Property workflow must instance of Workflow');
        }
        if (empty($this->workflowId)) {
            $this->workflowId = uniqid('', true);
        }
    }

    /**
     * @return Guzzle\Service\Resource\Model
     */
    public function getParams()
    {
        return array_merge($this->additionalOptions, [
            'workflowId' => $this->workflowId,
            'workflowType' => $this->_workFlowType,
            'input' => $this->input,
            'executionStartToCloseTimeout' => $this->executionStartToCloseTimeout,
            'taskStartToCloseTimeout' => $this->taskStartToCloseTimeout,
        ]);
    }
}
