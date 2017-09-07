<?php
/**
 * Activity trait file.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */

namespace UrbanIndo\Yii2\Aws\Swf\Traits\Client;

use Guzzle\Service\Resource\Model as GuzzleModel;
use UrbanIndo\Yii2\Aws\Swf\Base\Activity as BaseActivity;

/**
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */
trait Activity
{
    /**
     * @var string
     */
    public $activityNamespace = '';

    /**
     * @var array
     */
    public $activityClassMap = [];

    /**
     * @return void
     */
    public function activityInit()
    {
        if (empty($this->activityNamespace)) {
            Yii::warning('Activity namespace undefined');
        }
        $this->activityNamespace = str_replace('/', '\\', $this->activityNamespace);
    }

    /**
     * @param BaseActivity $activity Activity object.
     * @param array        $config   Additional config.
     * @return mixed
     */
    public function registerActivity(BaseActivity $activity, array $config = [])
    {
        $activityType = $activity->getActivityType();
        $name = $activityType['name'];
        $version = $activityType['version'];

        return $this->client->registerActivityType(array_filter(array_merge(
            [
                'description' => $activity->description,
                'defaultTaskList' => $this->defaultTaskList,
            ],
            $config,
            [
                'domain' => $this->domain,
                'name' => $name,
                'version' => $version,
        ])));
    }

    /**
     * @param array $config Additional config.
     * @return mixed
     */
    public function pollForActivityTask(array $config = [])
    {
        $params = array_merge(
            [
                'taskList' => $this->defaultTaskList,
            ],
            $config,
            [
                'domain' => $this->domain,
            ]
        );

        return $this->client->pollForActivityTask($params);
    }

    /**
     * @param BaseActivity $activity Activity.
     * @return mixed
     */
    public function respondActivity(BaseActivity $activity)
    {
        $command = 'respondActivityTask' . $activity->resultStatus;
        return $this->client->{$command}(array_merge(
            $activity->respond,
            [
                'taskToken' => $activity->taskToken
            ]
        ));
    }

    /**
     * @param string $name           Activity name.
     * @param array  $activityParams Activity params.
     * @return Activity
     */
    public function getActivityByName($name, array $activityParams = [])
    {
        if (count($this->activityClassMap) > 0 && in_array($name, array_keys($this->activityClassMap))) {
            $className = $this->activityClassMap[$name];
        } elseif (strlen($this->activityNamespace) > 0) {
            $className = "{$this->activityNamespace}\\${name}";
        } else {
            $className = $name;
        }

        return new $className(array_merge([
            'name' => $name,
        ], $activityParams));
    }

    /**
     * @param GuzzleModel $model Guzzle model.
     * @return Workflow
     */
    public function getActivityByGuzzleModel(GuzzleModel $model)
    {
        return $this->getActivityByName($model['activityType']['name'], [
            'version' => $model['activityType']['version'],
            'taskToken' => $model['taskToken'],
            'workflowExecution' => $model['workflowExecution'],
            'id' => $model['activityId'],
            'eventId' => $model['startedEventId'],
        ]);
    }
}
