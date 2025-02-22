<?php

namespace App\Telegram\InlineMenus;

use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class TestMenu extends InlineMenu
{
    public function start(Nutgram $bot)
    {
        $this->menuText('Куда сам сядешь?')
             ->addButtonRow(InlineKeyboardButton::make('Левый', callback_data: 'left@sitOn'))
             ->addButtonRow(InlineKeyboardButton::make('Правый', callback_data: 'right@sitOn'))
             ->showMenu();
    }

    public function sitOn(Nutgram $bot)
    {
        $side = $bot->callbackQuery()->data;
        $this->menuText("Выбрал: $side")
             ->showMenu();
    }
}
