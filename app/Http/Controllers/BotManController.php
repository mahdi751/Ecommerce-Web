<?php
namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Attachments\UrlAttachment;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;



class BotManController extends Controller
{
    /**
     * Handle incoming bot messages.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('hi', function ($botman) {
            $this->listAvailableHears($botman);
        });

        $botman->hears('number of stores', function ($botman) {
            $this->provideStoreInformation($botman);
        });

        $botman->hears('category {category}', function ($botman, $category) {
            $this->checkCategory($botman, $category);
        });

        $botman->hears('products of {store}', function ($botman, $store) {
            $this->getProductsOfStore($botman, $store);
        });

        $botman->hears('product {product}', function ($botman, $product) {
          $this->checkProduct($botman, $product);
      });

        $botman->hears('store {store}', function ($botman, $store) {
          $this->checkStore($botman, $store);
    });

    $botman->hears('redirect me to homepage', function ($botman) {
        $this->redirectToHomepage($botman);
    });

    

        $botman->listen();
    }

    /**
     * Ask for user's name.
     */
    public function askName($botman)
    {
        $botman->ask('Abo samer?', function ($answer) use ($botman) {
            $name = $answer->getText();
            $this->say('hek mnyke ' . $name);
        });
    }

    public function checkStore($botman, $store)
    {
        $storeExists = DB::table('stores')->where('name', $store)->exists();

        $response = $storeExists ? "Yes, the store '{$store}' exists." : "No, the store '{$store}' does not exist.";

        $botman->reply($response);
    }

    /**
     * Provide information about the stores.
     */
    public function provideStoreInformation($botman)
    {
        $stores = DB::table('stores')->select('name')->get();

        $storeCount = $stores->count();
        $response = "There are {$storeCount} stores:\n";

        foreach ($stores as $store) {
            $response .= "{$store->name}\n";
        }

        $botman->reply($response);
    }

    /**
     * Check if the category exists and in which stores it is present.
     */
    public function checkCategory($botman, $category)
    {
        \Log::info("Received category: " . $category);

        $categoryExists = DB::table('categories')->where('title', $category)->exists();

        \Log::info("Category exists: " . ($categoryExists ? 'true' : 'false'));

        if ($categoryExists) {
            $stores = DB::table('categories')
                ->join('stores', 'categories.store_id', '=', 'stores.id')
                ->where('categories.title', $category)
                ->select('stores.name')
                ->get();

            $storeCount = $stores->count();
            $response = "The category '{$category}' exists.\n";
            if ($storeCount > 0) {
                $response .= "It is present in the following stores:\n";
                foreach ($stores as $store) {
                    $response .= "{$store->name}\n";
                }
            } else {
                $response .= "It is not present in any store.";
            }
        } else {
            $response = "The category '{$category}' does not exist.";
        }

        $botman->reply($response);
    }
    /**
     * Get the products of the specified store.
     */
    public function getProductsOfStore($botman, $store)
{
    $products = DB::table('products')
        ->join('categories', 'products.cat_id', '=', 'categories.id')
        ->join('stores', 'categories.store_id', '=', 'stores.id')
        ->where('stores.name', $store)
        ->select('products.title')
        ->get();

    if ($products->count() > 0) {
        $response = "Products available at '{$store}':\n";
        foreach ($products as $product) {
            $response .= "{$product->title}\n";
        }
    } else {
        $response = "No products available at '{$store}'.";
    }

    $botman->reply($response);
}

public function checkProduct($botman, $product)
{
    $productExists = DB::table('products')->where('title', $product)->exists();

    if ($productExists) {
        $stores = DB::table('products')
            ->join('categories', 'products.cat_id', '=', 'categories.id')
            ->join('stores', 'categories.store_id', '=', 'stores.id')
            ->where('products.title', $product)
            ->select('stores.name')
            ->distinct() 
            ->get();

        $storeCount = $stores->count();
        $response = "The product '{$product}' exists.\n";
        if ($storeCount > 0) {
            $response .= "It is present in the following stores:\n";
            foreach ($stores as $store) {
                $response .= "{$store->name}\n";
            }
        } else {
            $response .= "It is not present in any store.";
        }
    } else {
        $response = "The product '{$product}' does not exist.";
    }

    $botman->reply($response);
}

public function redirectToHomepage(BotMan $botman)
{
    $homepageUrl = 'http://127.0.0.1:8000/home';

    $messageText = "Redirecting you to the homepage: $homepageUrl";

    $botman->reply('click <a href="http://127.0.0.1:8000/home">here</a> for answers.');;
}

public function listAvailableHears($botman)
{
    $commands = [
        "1) number of stores",
        "2) category {category}",
        "3) products of {store}",
        "4) product {product}",
        "5) store {store}",
        "6) redirect me to homepage"
    ];

    $questionText = "Here are the available commands you can use:";
    $question = Question::create($questionText)->addButtons(
        collect($commands)->map(function ($command) {
            return Button::create($command)->value($command);
        })->toArray()
    );

    $botman->ask($question, function ($answer) {
    });
}







}
