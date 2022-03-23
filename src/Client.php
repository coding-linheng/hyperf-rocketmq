<?php

namespace Codelin\HyperfRocketmq;

use Hyperf\Contract\ConfigInterface;
use MQ\MQClient;
use MQ\MQConsumer;
use MQ\MQProducer;
use MQ\MQTransProducer;

class Client
{
    protected array $config;

    protected array $ClientConfig;


    public function __construct(ConfigInterface $config)
    {
        $config = $config->get('rocketmq');
        if (empty($config)) {
            throw new \RuntimeException('Please publish rocketmq configuration');
        }
        $this->config = $config;
    }

    public function getClient(string $name = 'default'): MQClient
    {
        $this->configure($name);
        return make(MQClient::class, $this->ClientConfig);
    }

    public function getProducer(string $name = 'default'): MQProducer
    {
        $client = $this->getClient($name);
        return make(MQProducer::class, $this->ClientConfig);
    }

    public function getTransProducer(string $name = 'default'): MQTransProducer
    {
        $client = $this->getClient($name);
        return make(MQTransProducer::class, $this->ClientConfig);
    }

    public function getConsumer(string $name = 'default'): MQConsumer
    {
        $client = $this->getClient($name);
        return make(MQConsumer::class, $this->ClientConfig);
    }

    private function configure(string $name)
    {
        if (!$this->config[$name]) {
            throw new \InvalidArgumentException("Configuration does not exist");
        }

        $this->ClientConfig = $this->config[$name];
    }


}