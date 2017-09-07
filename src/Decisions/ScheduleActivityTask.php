<?php
/**
 * ScheduleActivityTask class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */

namespace UrbanIndo\Yii2\Aws\Swf\Decisions;

use Yii;

/**
 * Decision for ScheduleActivityTask.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */
class ScheduleActivityTask extends \UrbanIndo\Yii2\Aws\Swf\Base\Decision
{
    /**
     * @return array
     */
    protected static function defaultDecisionAttributes()
    {
        return [
            'scheduleToStartTimeout' => 'NONE',
            'scheduleToCloseTimeout' => 'NONE',
            'startToCloseTimeout' => 'NONE',
            'heartbeatTimeout' => 'NONE',
        ];
    }
}
