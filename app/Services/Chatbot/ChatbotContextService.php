<?php

namespace App\Services\Chatbot;

use App\Models\Auction\Auction;
use App\Models\Payment\Package;
use App\Models\Product\Product;
use App\Models\Product\Category;
use Illuminate\Support\Facades\Http;

class ChatbotContextService
{
    /**
     * Main function that uses NLP to determine intents and fetch corresponding data.
     */
    public function getContextForMessage(string $message, $user = null): string
    {
        $context = "";

        // Send the message to Gemini to analyze and extract intents
        $intents = $this->analyzeIntent($message);

        // Append relevant context based on the extracted intents
        if (in_array('packages', $intents)) {
            $context .= $this->getPackagesContext();
        }

        if (in_array('auctions', $intents)) {
            $context .= $this->getAuctionsContext();
        }

        if (in_array('products', $intents)) {
            $context .= $this->getProductsContext();
        }

        if (in_array('categories', $intents)) {
            $context .= $this->getCategoriesContext();
        }

        if ($user && in_array('account', $intents)) {
            $context .= $this->getUserAccountContext($user);
            $context .= $this->getUnlockCostContext();
        }

        if ($user && in_array('user_auctions', $intents)) {
            $context .= $this->getUserAuctionsContext($user);
        }

        // Return empty context message if no relevant data is found
        return $context ?: "No additional database context available for this specific query.\n";
    }

    /**
     * NLP Intent Classification using Gemini
     * Asks the AI about the user's intent and returns it as an array.
     */
    private function analyzeIntent(string $message): array
    {
        $prompt = "Analyze the following user message and extract their intents.
        You MUST reply with a JSON array ONLY, containing the appropriate keys from this list (Do not write any text or markdown other than the JSON):
        - 'packages': if asking about subscriptions, packages, payments, pricing, or costs.
        - 'auctions': if asking about current live auctions or bidding.
        - 'products': if asking about regular products, scrap materials, or direct purchases.
        - 'categories': if asking about types, categories, or scrap classification.
        - 'account': if asking about their balance, account type, coins, money, or unlock costs.
        - 'user_auctions': if asking about their own auctions, wins, losses, or insurance deposits.

        User Message: '{$message}'";

        $response = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key='. env('GEMINI_API_KEY'), [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text');

            // Clean the response to ensure valid JSON parsing (strip markdown if added by AI)
            $text = str_replace(['```json', '```'], '', trim($text));
            $intents = json_decode(trim($text), true);

            return is_array($intents) ? $intents : [];
        }

        return [];
    }

    // ----------------- Database Context Retrieval Methods -----------------

    private function getPackagesContext(): string
    {
        $packages = Package::where('is_active', true)->get();
        if ($packages->isEmpty()) return "- No packages are currently available.\n";

        $text = "--- Current Packages Information ---\n";
        foreach ($packages as $p) {
            $text .= "- Package {$p->name}: Grants {$p->coins} coins for {$p->price} EGP.\n"; // Change EGP to your currency if needed
        }
        return $text . "\n";
    }

    private function getAuctionsContext(): string
    {
        $auctions = Auction::where('status', 'active')->with('product')->take(5)->get();
        if ($auctions->isEmpty()) return "- No active auctions are currently available.\n";

        $text = "--- Currently Active Auctions ---\n";
        foreach ($auctions as $a) {
            $productName = $a->product ? $a->product->name : 'Unknown Product';
            $text .= "- Auction for: {$productName} (Ends at: {$a->ends_at})\n";
        }
        return $text . "\n";
    }

    private function getProductsContext(): string
    {
        $products = Product::where('status', 'approved')->where('sale_type', 'coins')->take(5)->get();
        if ($products->isEmpty()) return "- No direct-sale products are currently available.\n";

        $text = "--- Products Available for Direct Purchase ---\n";
        foreach ($products as $p) {
            $text .= "- Product: {$p->name} (Available Quantity: {$p->quantity} {$p->unit})\n";
        }
        return $text . "\n";
    }

    private function getCategoriesContext(): string
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->get();

        if ($categories->isEmpty()) return "- No categories are currently registered.\n";

        $text = "--- Available Scrap Categories on the Platform ---\n";
        foreach ($categories as $cat) {
            $text .= "- Main Category: {$cat->name} (Material Priority: {$cat->material_priority})\n";

            if ($cat->children && $cat->children->isNotEmpty()) {
                $childNames = $cat->children->pluck('name')->implode(', ');
                $text .= "  * Subcategories: {$childNames}\n";
            }
        }
        return $text . "\n";
    }

    private function getUserAccountContext($user): string
    {
        $accountType = $user->account_type === 'auction' ? 'Verified Account (Eligible for Auctions)' : 'Standard Account';
        return "--- Current User Account Details ---\n" .
               "- Current Balance: {$user->coins} Coins.\n" .
               "- Account Type: {$accountType}.\n\n";
    }

    private function getUnlockCostContext(): string
    {
        return "--- Product Unlock Cost System (To Contact Sellers) ---\n" .
               "- The cost is not fixed; it is calculated based on 3 factors: Total product price, displayed quantity, and Material Priority.\n" .
               "- The higher the value or rarity of the scrap material, the more coins required to unlock it.\n\n";
    }

    private function getUserAuctionsContext($user): string
    {
        /* Note: Adjust 'winner_id' if your database schema for bids/wins is different */
        $wonAuctions = Auction::where('winner_id', $user->id)->count();

        $text = "--- Current User Auctions Summary ---\n";
        $text .= "- Total auctions won so far: {$wonAuctions} auctions.\n";

        return $text . "\n";
    }
}
