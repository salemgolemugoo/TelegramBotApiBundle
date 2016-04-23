<?php

namespace Shaygan\TelegramBotApiBundle\Controller;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use TelegramBot\Api\Types\Update;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function updateAction($secret, Request $request)
    {
        $config = $this->getParameter("shaygan_telegram_bot_api.config");

        if (!$request->isMethod(Request::METHOD_POST)) {
            throw new MethodNotAllowedHttpException(['POST']);
        }

        // Check that token is correct
        if ($secret != $config['token']) {
            throw new AccessDeniedHttpException('Invalid token');
        }

        if (empty($config['webhook']['update_receiver'])) {
            throw new InvalidArgumentException('"webhook.update_receiver" is not valid service name');
        }

        $content = $request->getContent();
        $data = json_decode($content, true);

        $updateReceiver = $this->container->get($config['webhook']['update_receiver']);
        $updateReceiver->handleUpdate(Update::fromResponse($data));

        return new Response();
    }
}
