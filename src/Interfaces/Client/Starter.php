<?php
/**
 * Starter interface file.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */

namespace UrbanIndo\Yii2\Aws\Swf\Interfaces\Client;

/**
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */
interface Starter
{
    const DEFAULT_STARTER_EXECUTION_START_TO_CLOSE_TIMEOUT = 86400; // 1 day in second
    const DEFAULT_STARTER_TASK_START_TO_CLOSE_TIMEOUT = 'NONE';
}
