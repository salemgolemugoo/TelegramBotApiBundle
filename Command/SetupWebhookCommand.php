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
        $container = $this->getContainer();
        $api = $container->get('shaygan.telegram_bot_api');
        $config = $container->getParameter('shaygan_telegram_bot_api.config');
        $io = new SymfonyStyle($input, $output);
        $file = null;

        if (empty($config['webhook']['domain'])) {
            throw new InvalidArgumentException('"shaygan_telegram_bot_api.webhook.domain" is not set in config.yml');
        }

        if (!empty($config['certificate'])) {
            $rootDir = $container->getParameter('kernel.root_dir');
            $filePath = $rootDir . $config['certificate'];

            if (!file_exists($filePath)) {
                throw new InvalidArgumentException(sprintf('SSL certificate file was not found "%s"', $filePath));
            }

            $file = new \CURLFile($filePath, 'plain/text', 'certificate.pem');
        }

        $url = sprintf('https://%s%s/telegram-bot/update/%s',
            $config['webhook']['domain'],
            $config['webhook']['path_prefix'],
            $config['token']
        );

        try {
            if ($result = $api->setWebhook($url, $file)) {
                $io->success(sprintf('Webhook set to "%s"', $url));

                if (!empty($config['certificate'])) {
                    $io->success('Certificate uploaded');
                }
            } else {
                $io->error($result);
            }
        } catch (Exception $e) {
            $io->error($e->getMessage());
        }
    }
}