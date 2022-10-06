<?php

namespace Codelin\HyperfRocketmq;

use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use MQ\Http\HttpClient;
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

    public function getClient(string $name = 'default'): HttpClient
    {
        $client = Context::get($this->getKey($name));
        $this->configure($name);
        if (empty($client)) {
            $client = Context::set($this->getKey($name), make(HttpClient::class, $this->ClientConfig));
        }

        return $client;
    }

    public function getProducer(string $name = 'default'): MQProducer
    {
        $this->setClient($name);
        return make(MQProducer::class, $this->ClientConfig);
    }

    public function getTransProducer(string $name = 'default'): MQTransProducer
    {
        $this->setClient($name);
        return make(MQTransProducer::class, $this->ClientConfig);
    }

    public function getConsumer(string $name = 'default'): MQConsumer
    {
        $this->setClient($name);
        return make(MQConsumer::class, $this->ClientConfig);
    }

    protected function configure(string $name)
    {
        if (!$this->config[$name]) {
            throw new \InvalidArgumentException("Configuration does not exist");
        }

        $this->ClientConfig = $this->config[$name];
    }

    protected function getKey(string $name): string
    {
        return sprintf('rocketmq:%s', $name);
    }

    protected function setClient(string $name)
    {
        $this->ClientConfig['client'] = $this->getClient($name);
    }
}