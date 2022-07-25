<?php

namespace App\Http\Controllers;

use App\Botman\OnboardConversation;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Web\WebDriver;
use Illuminate\Http\Request;

class   BotManController extends Controller
{
    public function handle(Request $request)
    {

        $config = [$request];

        DriverManager::loadDriver(WebDriver::class);

        $botman = BotManFactory::create($config,null,$request);



        $botman->listen();

    }
}
