<?php
/**
 * Controller class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */

namespace UrbanIndo\Yii2\Aws\Swf\Console;

use Yii;
use Aws\Swf\SwfClient;

/**
 * QueueController handles console command for running the queue.
 *
 * To use the controller, update the controllerMap.
 *
 * return [
 *     // ...
 *     'controllerMap' => [
 *         'swf' => 'UrbanIndo\Yii2\Aws\Swf\Console\Controller'
 *     ],
 * ];
 *
 * To run
 *
 * yii swf
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */
class Controller extends \yii\console\Controller
{
    /**
     * @var string|array|Queue the name of the queue component. default to 'queue'.
     */
    public $swfClient = 'swfClient';

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->swfClient = \yii\di\Instance::ensure($this->swfClient, SwfClient::class);
    }

    /**
     * Returns the script path.
     * @return string
     */
    protected function getScriptPath()
    {
        return realpath($_SERVER['argv'][0]);
    }
}
