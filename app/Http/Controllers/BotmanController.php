<?php
namespace App\Http\Controllers;
 
use BotMan\BotMan\BotMan;
use App\Conversations\ExampleConversation;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
 
 
class BotmanController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
 
        $config = [
            // Your driver-specific configuration
            "telegram" => [
               "token" => "5179644162:AAGL4MMkUVuOKJAl3ZjQXSRrnNyFYAecAGg"
            ]
        ];
        $botman = BotManFactory::create($config, new LaravelCache());
 
        $botman->hears('/start|start|mulai|halo|hai', function (BotMan $bot) {
            $user = $bot->getUser();
            $bot->reply('Haii '.$user->getFirstName().', Selamat datang di Restaurant Telegram Bot!. ');
            $bot->startConversation(new ExampleConversation());
        })->stopsConversation();
 
        $botman->listen();
    }
 
}