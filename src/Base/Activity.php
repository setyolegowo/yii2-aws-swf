<?php
/**
 * Activity class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */

namespace UrbanIndo\Yii2\Aws\Swf\Base;

use Yii;
use Aws\Swf\SwfClient;

/**
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.05
 */
abstract class Activity extends \yii\base\Object
{
    const STATUS_CANCELED = 'Canceled';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_FAILED = 'Failed';

    /**
     * @var string
     */
    public $taskToken;

    /**
     * Name of this activity. The value usually equal with inherit class path
     * without contain activity namespace which defined in {@class Client}.
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
     * Activity ID.
     * @var string
     */
    public $id;

    /**
     * Event ID.
     * @var integer
     */
    public $eventId;

    /**
     * @var string
     */
    public $input;

    /**
     * Default value is empty array.
     * @var array
     */
    public $respond = [];

    /**
     * @var string
     */
    private $_resultStatus;

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        if (empty($this->name)) {
            $this->name = get_called_class();
        }
    }

    /**
     * @return void
     */
    abstract public function run();

    /**
     * @param mixed $value Value.
     * @return void
     */
    protected function setResultStatus($value)
    {
        if (!in_array($value, [self::STATUS_CANCELED, self::STATUS_FAILED, self::STATUS_COMPLETED])) {
            throw new \yii\base\InvalidParamException("Error Processing Request", 1);
        }
        $this->_resultStatus = $value;
    }

    /**
     * @return string
     */
    public function getResultStatus()
    {
        return $this->_resultStatus;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getActivityType()
    {
        if (empty($this->name)) {
            throw new \yii\base\InvalidConfigException('$name cannot be empty');
        }

        return [
            'name' => $this->name,
            'version' => $this->version
        ];
    }
}
