<?php

namespace Shaygan\TelegramBotApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use TelegramBot\Api\Exception;

class SetupWebhookCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('telegram:setwebhook')
            ->setDescription('Set webhook for telegram bot')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getContainer()->get('shaygan.telegram_bot_api');
        $config = $this->getContainer()->getParameter('shaygan_telegram_bot_api.config');
        $io = new SymfonyStyle($input, $output);

        if (empty($config['webhook']['domain'])) {
            throw new InvalidArgumentException('"shaygan_telegram_bot_api.webhook.domain" is not set in config.yml');
        }

        $url = sprintf('https://%s%s/telegram-bot/update/%s',
            $config['webhook']['domain'],
            $config['webhook']['path_prefix'],
            $config['token']
        );

        try {
            if ($result = $api->setWebhook($url)) {
                $io->success(sprintf('Webhook set to "%s"', $url));
            } else {
                $io->error($result);
            }
        } catch (Exception $e) {
            $io->error($e->getMessage());
        }
    }
}