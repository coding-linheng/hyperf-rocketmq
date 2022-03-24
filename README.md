# qbhy/hyperf-auth

基于阿里云的rocketmq-php-sdk 修改的 hyperf2.2 的 rocket 组件


> 任何问题请加QQ群提问：873213948

## 安装 - install

```bash
$ composer require coding-linheng/hyperf-rocketmq
```

发布配置 vendor:publish

```bash
php bin/hyperf.php vendor:publish coding-linheng/hyperf-rocketmq
```

## 使用

> 以下是伪代码，仅供参考。Auth 注解可以用于类或者方法。

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Codelin\HyperfRocketmq\Client;
use Hyperf\HttpServer\Annotation\AutoController;
use MQ\Exception\MessageNotExistException;
use MQ\Model\Message;
use MQ\Model\TopicMessage;
use MQ\MQConsumer;
use MQ\MQProducer;
use Psr\Container\ContainerInterface;

#[AutoController]
class IndexController extends AbstractController
{
    protected MQProducer $producer;

    protected MQConsumer $consumer;

    public function __construct(ContainerInterface $container)
    {
        $this->producer = $container->get(Client::class)->getProducer();
        $this->consumer = $container->get(Client::class)->getConsumer();
    }


    public function testProducer()
    {
        try {
            for($i = 1; $i <= 8; $i++) {
                $publishMessage = new TopicMessage(
                    "test"// 消息内容
                );
                // 设置属性
                $publishMessage->putProperty("a", $i);
                // 设置分区顺序KEY
                $publishMessage->setShardingKey($i % 2);
                // 设置tag
                $publishMessage->setMessageTag('hyperf-test');
                $result = $this->producer->publishMessage($publishMessage);
                print "Send mq message success. msgId is:" . $result->getMessageId() . ", bodyMD5 is:" . $result->getMessageBodyMD5() . "\n";
            }
        } catch (\Exception $e) {
            print_r($e->getMessage() . "\n");
        }
    }


    public function testConsumer()
    {
        while(True) {
            try {
                // 长轮询消费消息
                // 长轮询表示如果topic没有消息则请求会在服务端挂住3s，3s内如果有消息可以消费则立即返回
                $messages = $this->consumer->consumeMessage(
                    5, // 一次最多消费3条(最多可设置为16条)
                    3 // 长轮询时间3秒（最多可设置为30秒）
                );
            } catch (\Exception $e) {
                if ($e instanceof MessageNotExistException) {
                    // 没有消息可以消费，接着轮询
                    printf("No message, contine long polling!RequestId:%s\n", $e->getRequestId());
                    continue;
                }

                print_r($e->getMessage() . "\n");

                sleep(3);
                continue;
            }

            var_dump($this->consumer->getMessageTag());
            var_dump($messages);
            print "consume finish, messages:\n";

            // 处理业务逻辑
            $receiptHandles = array();
            /** @var Message $message */
            foreach($messages as $message) {
                $receiptHandles[] = $message->getReceiptHandle();
                printf("MessageID:%s TAG:%s BODY:%s \nPublishTime:%d, FirstConsumeTime:%d, \nConsumedTimes:%d, NextConsumeTime:%d,MessageKey:%s\n",
                    $message->getMessageId(), $message->getMessageTag(), $message->getMessageBody(),
                    $message->getPublishTime(), $message->getFirstConsumeTime(), $message->getConsumedTimes(), $message->getNextConsumeTime(),
                    $message->getMessageKey());
                print_r($message->getProperties());
            }

            // $message->getNextConsumeTime()前若不确认消息消费成功，则消息会重复消费
            // 消息句柄有时间戳，同一条消息每次消费拿到的都不一样
            print_r($receiptHandles);
            try {
                $this->consumer->ackMessage($receiptHandles);
            } catch (\Exception $e) {
                if ($e instanceof AckMessageException) {
                    // 某些消息的句柄可能超时了会导致确认不成功
                    printf("Ack Error, RequestId:%s\n", $e->getRequestId());
                    foreach ($e->getAckMessageErrorItems() as $errorItem) {
                        printf("\tReceiptHandle:%s, ErrorCode:%s, ErrorMsg:%s\n", $errorItem->getReceiptHandle(), $errorItem->getErrorCode(), $errorItem->getErrorCode());
                    }
                }
            }
            print "ack finish\n";
        }
    }


    public function testPackage()
    {
        var_dump($this->consumer);
        var_dump($this->producer);
    }
}
```