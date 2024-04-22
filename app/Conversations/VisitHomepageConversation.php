<?php
namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\config\botman\config;
use App\Http\Controllers\Controller;

class VisitHomepageConversation extends Conversation
{
   /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->say('Hello!');
        return redirect()->route('home');
    }

    /**
     * Redirect the user to the home page.
     */
    public function redirectHomePage()
    {
        // Redirect to the 'home' route using Laravel's Redirect facade
        return Redirect::route('home');
    }
}
