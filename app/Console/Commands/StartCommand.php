<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = "start";

    /**
     * @var string Command Description
     */
    protected string $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        Log::info("Start command received");
        // This will send a message using sendMessage method
        $this->replyWithMessage(['text' => 'Hello! Welcome to our Telegram Bot']);

        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // This will prepare a list of available commands
        $commands = $this->getTelegram()->getCommands();

        // Build the list
        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        // Reply with the commands list
        $this->replyWithMessage(['text' => $response]);
    }
}