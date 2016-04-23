<?php

namespace Shaygan\TelegramBotApiBundle\Controller;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use TelegramBot\Api\Types\Update;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function updateAction($secret, Request $request)
    {
        $config = $this->getParameter("shaygan_telegram_bot_api.config");
        $data = [];

        // Check that token is correct
        if ($secret == $config['token']) {
            $content = $request->getContent();

            if (!empty($content)) {
                $data = json_decode($content);
            }

            if (empty($config['webhook']['update_receiver'])) {
                throw new InvalidArgumentException("'webhook.update_receiver' is not valud service name", 0);
            }

            $updateReceiver = $this->container->get($config['webhook']['update_receiver']);
            $updateReceiver->handleUpdate(Update::fromResponse($data));

            return new Response();
        } else {
            throw new AccessDeniedHttpException('Invalid token');
        }
    }
}
