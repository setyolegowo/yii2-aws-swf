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
    public $input;

    /**
     * @var array
     */
    public $additionalOptions = [];

    /**
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (empty($this->workflow) || !($this->workflow instanceof Workflow)) {
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
            'workflowType' => $this->workflow->getWorkflowType(),
            'input' => $this->input,
        ]);
    }
}
