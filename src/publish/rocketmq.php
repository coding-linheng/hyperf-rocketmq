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
        'end_point'      => '',  // 设置HTTP接入域名
        'access_id'      => '', //AccessKey 阿里云身份验证，在阿里云服务器管理控制台创建
        'access_key'     => '',  // SecretKey 阿里云身份验证，在阿里云服务器管理控制台创建
        'security_token' => '',  // 来自阿里云的securityToken
        'topic'          => '', // 所属的 Topic
        'instance_id'    => null, // Topic所属实例ID，默认实例为空NULL
        'group_id'       => null, // 分组
        'message_tag'    => [],   // 标签
    ],
];
