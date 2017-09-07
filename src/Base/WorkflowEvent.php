<?php
/**
 * WorkflowEvent class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.06
 */

namespace UrbanIndo\Yii2\Aws\Swf\Base;

/**
 * Event of the workflow.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.06
 */
class WorkflowEvent extends \yii\base\Object
{
    /**
     * @var string
     */
    private $_eventType;

    /**
     * @var float
     */
    private $_eventTimestamp;

    /**
     * @var integer
     */
    private $_eventId;

    /**
     * @var array
     */
    private $_eventAttributes = [];

    /**
     * @param array $event Event.
     */
    public function __construct(array $event)
    {
        $this->_eventId = $event['eventId'];
        $this->_eventType = $event['eventType'];
        $this->_eventTimestamp = $event['eventTimestamp'];

        $keyAttribute = $this->createKeyAttribute();
        $this->_eventAttributes = $event[$keyAttribute];

        parent::__construct(array_diff_key(
            $event,
            array_flip(['eventId', 'eventType', 'eventTimestamp', $keyAttribute])
        ));
    }

    /**
     * @return string
     */
    private function createKeyAttribute()
    {
        return lcfirst($this->_eventType) . 'EventAttributes';
    }

    /**
     * @return integer
     */
    public function getEventId()
    {
        return $this->_eventId;
    }

    /**
     * @return float
     */
    public function getEventTimestamp()
    {
        return $this->_eventTimestamp;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->_eventType;
    }

    /**
     * @return array
     */
    public function getEventAttributes()
    {
        return $this->_eventAttributes;
    }
}
