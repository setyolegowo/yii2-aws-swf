<?php
/**
 * Domain trait file.
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */

namespace UrbanIndo\Yii2\Aws\Swf\Traits\Client;

/**
 * @author Setyo Legowo <gw.tio145@gmail.com>
 * @since 2017.09.07
 */
trait Domain
{
    /**
     * @var string
     */
    public $domain;

    /**
     * @return void
     */
    private function domainInit()
    {
        if (empty($this->domain)) {
            throw new \yii\base\InvalidConfigException('Domain name cannot be empty');
        }
    }

    /**
     * @param array $config Config.
     * @return mixed
     */
    public function registerDomain(array $config = [])
    {
        return $this->_client->registerDomain(array_merge($config, [
            'name' => $this->domain,
        ]));
    }
}
