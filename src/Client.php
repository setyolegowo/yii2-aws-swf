<?php
/**
 * Client class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */

namespace UrbanIndo\Yii2\Aws\Swf;

use Yii;
use Aws\Swf\SwfClient;

/**
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */
class Client extends \yii\base\Object implements
    \UrbanIndo\Yii2\Aws\Swf\Interfaces\Client\Starter,
    \UrbanIndo\Yii2\Aws\Swf\Interfaces\Client\Polling
{
    use \UrbanIndo\Yii2\Aws\Swf\Traits\Client\Activity;
    use \UrbanIndo\Yii2\Aws\Swf\Traits\Client\Domain;
    use \UrbanIndo\Yii2\Aws\Swf\Traits\Client\Starter;
    use \UrbanIndo\Yii2\Aws\Swf\Traits\Client\Workflow;

    /**
     * @var array
     */
    public $config;

    /**
     * @var SwfClient
     */
    private $_client;

    /**
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->domainInit();
        $this->workflowInit();
        $this->activityInit();

        $this->_client = SwfClient::factory($this->config);
    }

    /**
     * @return SwfClient
     */
    public function getClient()
    {
        return $this->_client;
    }
}
