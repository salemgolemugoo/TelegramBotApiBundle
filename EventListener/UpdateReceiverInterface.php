<?php

namespace Shaygan\TelegramBotApiBundle\EventListener;

use TelegramBot\Api\Types\Update;

/**
 *
 * @author Iman Ghasrfakhri <iman@i-gh.ir>
 */
interface UpdateReceiverInterface
{
    public function handleUpdate(Update $update);
}
