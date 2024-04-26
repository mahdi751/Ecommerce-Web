<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\Store;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{
    public function SendEmails()
    {
        $store_id = session('current_store_id');
        $store = Store::find($store_id);

        $storeEmail = $store->email;

        $subscribers = Subscriber::where('store_id', $store_id)->get();

        foreach ($subscribers as $subscriber) {
            Log::info('email sent from store to: ' . $subscriber->email);
            $user = User::where('email', $subscriber->email)->first();
            //dd($user);
            $emailContent = "Hello {$user->name}\n\n";
            $emailContent .= "We're excited to inform you about a new event for bidding that has just been added!\n\n";
            $emailContent .= "Visit our website to participate in the bidding and seize the opportunity!\n\n";
            $emailContent .= "http://127.0.0.1:8000/home    Click here to go to our website\n\n";
            $emailContent .= "Best regards,\n\n";
            $emailContent .= $store->name." Store Team";

            Mail::raw($emailContent, function ($message) use ($subscriber, $storeEmail) {
                $message->to($subscriber->email)->from($storeEmail)->subject('New Event for Bidding!');

            });
        }

        return redirect()->back()->with('success', 'Emails sent successfully');
    }



    public function SaveSubscribe(Request $request)
    {
        $userEmail = auth()->user()->email;

        $store_id = Memory::where('storeId', '>', 0)
                   ->where('userId', auth()->id())
                   ->orderBy('id', 'desc')
                   ->value('storeId');

        $existingSubscriber = Subscriber::where('email', $userEmail)->where('store_id', $store_id)->first();

        if ($existingSubscriber) {
            return redirect()->back()->with('success', 'You are already subscribed!');
        }

        $subscriber = new Subscriber();
        $subscriber->email = $userEmail;
        $subscriber->store_id = $store_id;
        $subscriber->save();

        $this->SendThankyouEmail($userEmail);

        return redirect()->back()->with('success', 'You have subscribed successfully. Check your Inbox!');
    }

    public function SendThankyouEmail($email)
    {
        $store_id = Memory::where('storeId', '>', 0)
                   ->where('userId', auth()->id())
                   ->orderBy('id', 'desc')
                   ->value('storeId');
        $store = Store::find($store_id);

        $storeEmail = $store->email;

        $user = User::where('email', $email)->first();

        if ($user) {
            $emailContent = "Hello {$user->name},\n\n";
            $emailContent .= "Thank you for subscribing to our newsletter!\n\n";
            $emailContent .= "We're excited that you joined our team! Any new events will be delivered for you!\n\n";
            $emailContent .= "http://127.0.0.1:8000/home    Click here to go to our website\n\n";
            $emailContent .= "Best regards,\n\n";
            $emailContent .= $store->name . " Store Team";

            Mail::raw($emailContent, function ($message) use ($email, $storeEmail) {
                $message->to($email)->from($storeEmail)->subject('Thank You for Subscribing!');
            });
        } else {
            return redirect()->back()->with('error', 'User with provided email does not exist');
        }

        return redirect()->back()->with('success', 'Thank you email sent successfully');
    }

}
