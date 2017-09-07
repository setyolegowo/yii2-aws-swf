<?php
/**
 * DirectedAcylicGraph class file.
 *
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */

namespace UrbanIndo\Yii2\Aws\Swf\Base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @see http://users.monash.edu/~lloyd/tildeAlgDS/Graph/DAG/
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */
class DirectedAcylicGraph extends \yii\base\Object
{
    /**
     * @var array
     */
    protected $dagArray = [];

    /**
     * @return void
     */
    public function __construct(array $submitDagArray, array $config = [])
    {
        parent::__construct($config);

        $this->dagArray = self::sortArray($submitDagArray);
    }

    /**
     * @param array $submitDagArray Submit DAG array.
     * @return array
     */
    private static function sortArray(array $submitDagArray)
    {
        $sort = [];

        foreach ($submitDagArray as $key => $value) {
            if (empty($value)) {
                throw new \yii\base\InvalidValueException('Value cannot be empty');
            }
            if (!is_array($value)) {
                throw new \yii\base\InvalidValueException('Value type must be array');
            }

            if (!isset($sort[$key])) {
                $sort[$key] = [];
            }
            if (ArrayHelper::isAssociative($value)) {
                $sort = ArrayHelper::merge($sort, static::sortArray($value));
            } else {
                $sort[$key] = array_merge($sort[$key], $value);
            }
        }
    }
}
