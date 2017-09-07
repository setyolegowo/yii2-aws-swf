<?php
/**
 * HelloWorld class file.
 * @package app\components\swf\workflows
 * @author Setyo Legowo <setyo@urbanindo.com>
 */

namespace app\components\swf\workflows;

use Yii;
use UrbanIndo\Yii2\Aws\Swf\Base\Decision;
use UrbanIndo\Yii2\Aws\Swf\Base\WorkflowEvent;

/**
 * @package app\components\swf\workflows
 * @author Setyo Legowo <setyo@urbanindo.com>
 */
class HelloWorld extends \UrbanIndo\Yii2\Aws\Swf\Base\Workflow
{
    /**
     * @param WorkflowEvent $event Event.
     * @return Decision|null
     */
    public function decideByEvent(WorkflowEvent $event)
    {
        if ($event->eventType == 'DecisionTaskStarted' && $event->eventId == 3) {
            $activity = Yii::$app->swfClient->getActivityByName('PrintHelloWorld');

            return Decision::factory(Decision::DECISION_SCHEDULE_ACTIVITY_TASK, [
                'activityType' => $activity->getActivityType(),
                'activityId' => '1',
            ]);
        }
    }
}
