<?php

namespace App\Telegram\Commands;

class RandomTagCommand extends RandomCommand
{
    protected string $command = 'random {tag}';

    protected ?string $description = 'Get random image with tags (multiple with space).';
}
