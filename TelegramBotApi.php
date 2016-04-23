<?php

/**
 * Description of TelegramBotApi
 *
 * @author iman
 */

namespace Shaygan\TelegramBotApiBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TelegramBot\Api\BotApi;

class TelegramBotApi extends BotApi
{

    public function __construct(ContainerInterface $container)
    {
        $token = $container->getParameter('shaygan_telegram_bot_api.config');
        parent::__construct($token['token']);
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

}
