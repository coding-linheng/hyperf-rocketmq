<?php

declare(strict_types=1);
/**
 * This file is part of coding-linheng/hyperf-rocketmq.
 *
 * @link     https://github.com/coding-linheng/hyperf-rocketmq
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  494020937@qq.com
 * @license  https://github.com/coding-linheng/hyperf-rocketmq/master/LICENSE
 */


return [
    'default' => [
        'endPoint'      => '',  // 设置HTTP接入域名
        'accessId'      => '', //AccessKey 阿里云身份验证，在阿里云服务器管理控制台创建
        'accessKey'     => '',  // SecretKey 阿里云身份验证，在阿里云服务器管理控制台创建
        'securityToken' => '',  // 来自阿里云的securityToken
        'topicName'     => '', // 所属的 Topic
        'instanceId'    => null, // Topic所属实例ID，默认实例为空NULL
        'consumer'      => '', // consumer
        'groupId'       => '', // 分组
        'messageTag'    => '',   // 标签
    ],
];
