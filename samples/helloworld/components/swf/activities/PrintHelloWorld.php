<?php
/**
 * PrintHelloWorld class file.
 * @package app\components\swf\workflows
 * @author Setyo Legowo <setyo@urbanindo.com>
 */

namespace app\components\swf\activities;

/**
 * @package app\components\swf\workflows
 * @author Setyo Legowo <setyo@urbanindo.com>
 */
class PrintHelloWorld extends \UrbanIndo\Yii2\Aws\Swf\Base\Activity
{
    /**
     * @return void
     */
    public function run()
    {
        echo "Hello World from Activity!\n";
        $this->setResultStatus(static::STATUS_COMPLETED);
    }
}
