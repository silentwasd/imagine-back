<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class TestConversation extends Conversation
{
    public function start(Nutgram $bot)
    {
        $bot->sendMessage('А ты сосал?');
        $this->next('secondStep');
    }

    public function secondStep(Nutgram $bot)
    {
        $bot->sendMessage('Ага, так я и поверил...');
        $this->end();
    }
}
