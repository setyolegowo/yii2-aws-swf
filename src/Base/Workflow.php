<?php
/**
 * Workflow class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */

namespace UrbanIndo\Yii2\Aws\Swf\Base;

use Yii;
use Aws\Swf\SwfClient;
use Guzzle\Service\Resource\Model as GuzzleModel;

/**
 * Workflow and decider class.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */
abstract class Workflow extends \yii\base\Object
{
    const CHILD_POLICY_TERMINATE = 'TERMINATE';
    const CHILD_POLICY_REQUEST_CANCEL = 'REQUEST_CANCEL';
    const CHILD_POLICY_ABANDON = 'ABANDON';

    /**
     * @var string
     */
    public $taskToken;

    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $version = 1;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $input;

    /**
     * @var string
     */
    public $executionContext;

    /**
     * @var string
     */
    public $childPolicy = self::CHILD_POLICY_ABANDON;

    /**
     * @var WorkflowEvent[]
     */
    public $events = [];

    /**
     * @var array
     */
    public $workflowExecution;

    /**
     * @var integer
     */
    public $previousStartedEventId;

    /**
     * @var integer
     */
    public $startedEventId;

    /**
     * @var array
     */
    private $_decisions = [];

    /**
     * @return DirectedAcylicGraph|null
     * @throws \yii\base\Exception If not overrided yet
     */
    public static function getDirectedAcylicGraph()
    {
        throw new \yii\base\Exception('Not overrided yet');
    }

    /**
     * Initalize.
     * @return void
     */
    public function init()
    {
        parent::init();

        if (empty($this->name)) {
            $this->name = get_called_class();
        }
        if (!static::isVersionValid($this->version)) {
            throw new \yii\base\InvalidParamException('Workflow version is not valid.');
        }
    }

    /**
     * @return boolean
     */
    public static function isVersionValid($version)
    {
        $version; // unused
        return true;
    }

    /**
     * @param array $events Event array.
     * @return void
     */
    public function pushEvents(array $events)
    {
        foreach ($events as $eventArray) {
            $event = new WorkflowEvent($eventArray);
            $this->events[$event->eventId] = $event;
        }
    }

    /**
     * @return void
     */
    public function decide()
    {
        $this->_decisions = [];

        foreach ($this->events as $event) {
            $decision = $this->decideByEvent($event);
            if (!is_null($decision) && $decision instanceof Decision) {
                $this->_decisions[] = $decision->toArray();
            }
        }
    }

    /**
     * @return array
     */
    public function getDecisions()
    {
        return $this->_decisions;
    }

    /**
     * @return Decision|null
     */
    abstract public function decideByEvent(WorkflowEvent $event);

    /**
     * @return array
     */
    public function getWorkflowType()
    {
        if (empty($this->name)) {
            throw new \yii\base\InvalidConfigException('Property name cannot be empty');
        }

        return [
            'name' => $this->name,
            'version' => $this->version
        ];
    }
}
