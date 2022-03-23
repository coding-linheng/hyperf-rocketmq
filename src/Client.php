<?php

namespace Codelin\HyperfRocketmq;

use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use MQ\MQClient;

/**
 * @method getProducer(string $instanceId, string $topicName)
 * @method getTransProducer(string $instanceId, string $topicName, string $groupId)
 * @method getConsumer(string $instanceId, string $topicName, string $consumer, string $messageTag)
 * @mixin
 */
class Client
{
    protected ConfigInterface $config;


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }


    public function __call(string $name, array $arguments)
    {
        $client = $this->getClient('default');

        if (method_exists($client, $name)) {
            return call_user_func_array([$client, $name], $arguments);
        }

        throw new \RuntimeException('Method not defined. method:' . $name);
    }

    private function configure(string $name)
    {
        if ($this->config[$name]) {
            throw new \InvalidArgumentException("Configuration does not exist");
        }

        $config = $this->config[$name];

        $client = new MQClient($config['end_point'], $config['access_id'], $config['access_key']);

        return Context::set('hyperf-rocketmq', $client);
    }

    public function getClient(string $name)
    {
        if (!empty(Context::get('hyperf-rocketmq'))) {
            return Context::get('hyperf-rocketmq');
        }

        return $this->configure($name);
    }
}