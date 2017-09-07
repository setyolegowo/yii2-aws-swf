<?php
/**
 * Decision class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.06
 */

namespace UrbanIndo\Yii2\Aws\Swf\Base;

use Yii;

/**
 * Decision wrapper for decider.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.06
 */
class Decision extends \yii\base\Object
{
    const DECISION_CANCEL_TIMER = 1;
    const DECISION_CANCEL_WORKFLOW_EXECUTION = 2;
    const DECISION_COMPLETE_WORKFLOW_EXECUTION = 3;
    const DECISION_CONTINUE_AS_NEW_WORKFLOW_EXECUTION = 4;
    const DECISION_FAIL_WORKFLOW_EXECUTION = 5;
    const DECISION_RECORD_MARKER = 6;
    const DECISION_REQUEST_CANCEL_ACTIVITY_TASK = 7;
    const DECISION_REQUEST_CANCEL_EXTERNAL_WORKFLOW_EXECUTION = 8;
    const DECISION_SCHEDULE_ACTIVITY_TASK = 9;
    const DECISION_SCHEDULE_LAMBDA_FUNCTION = 10;
    const DECISION_SIGNAL_EXTERNAL_WORKFLOW_EXECUTION = 11;
    const DECISION_STARTCHILD_WORKFLOW_EXECUTION = 12;
    const DECISION_START_TIMER = 13;

    /**
     * @var array
     */
    public $decisionAttributes = [];

    /**
     * @var integer
     */
    private $_decision;

    /**
     * @return Decision
     */
    public static function factory($decision, array $decisionAttributes, array $config = [])
    {
        if (!in_array($decision, array_keys(static::getDecisionTypeList()))) {
            throw new \yii\base\InvalidParamException('Paremeter decision must be one of listed in decision list');
        }

        $decisionClassName = static::getDecisionTypeList()[$decision];
        $class = "UrbanIndo\\Yii2\\Aws\\Swf\\Decisions\\{$decisionClassName}";

        if (class_exists($class)) {
            return new $class($decision, array_merge($config, ['decisionAttributes' => $decisionAttributes]));
        }

        return new self($decision, array_merge($config, ['decisionAttributes' => $decisionAttributes]));
    }

    /**
     * @return void
     */
    public function __construct($decision, array $config = [])
    {
        parent::__construct($config);
        $this->_decision = $decision;
    }

    /**
     * @return integer
     */
    public function getDecision()
    {
        return $this->_decision;
    }

    /**
     * @return string
     */
    public function getDecisionType()
    {
        return static::getDecisionTypeList()[$this->decision];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $decisionType = $this->getDecisionType();
        $decisionAttributeKey = lcfirst($decisionType) . 'DecisionAttributes';

        return [
            'decisionType' => $this->getDecisionType(),
            $decisionAttributeKey => array_merge(static::defaultDecisionAttributes(), $this->decisionAttributes),
        ];
    }

    /**
     * @return array
     */
    protected static function defaultDecisionAttributes()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDecisionTypeList()
    {
        return [
            self::DECISION_SCHEDULE_ACTIVITY_TASK => 'ScheduleActivityTask',
            self::DECISION_REQUEST_CANCEL_ACTIVITY_TASK => 'RequestCancelActivityTask',
            self::DECISION_COMPLETE_WORKFLOW_EXECUTION => 'CompleteWorkflowExecution',
            self::DECISION_FAIL_WORKFLOW_EXECUTION => 'FailWorkflowExecution',
            self::DECISION_CANCEL_WORKFLOW_EXECUTION => 'CancelWorkflowExecution',
            self::DECISION_CONTINUE_AS_NEW_WORKFLOW_EXECUTION => 'ContinueAsNewWorkflowExecution',
            self::DECISION_RECORD_MARKER => 'RecordMarker',
            self::DECISION_START_TIMER => 'StartTimer',
            self::DECISION_CANCEL_TIMER => 'CancelTimer',
            self::DECISION_SIGNAL_EXTERNAL_WORKFLOW_EXECUTION => 'SignalExternalWorkflowExecution',
            self::DECISION_REQUEST_CANCEL_EXTERNAL_WORKFLOW_EXECUTION => 'RequestCancelExternalWorkflowExecution',
            self::DECISION_STARTCHILD_WORKFLOW_EXECUTION => 'StartChildWorkflowExecution',
            self::DECISION_SCHEDULE_LAMBDA_FUNCTION => 'ScheduleLambdaFunction',
        ];
    }
}
