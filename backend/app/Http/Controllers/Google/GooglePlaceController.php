<?php

namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GooglePlaceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $response = Http::get(config('services.google.place_url'),[
            'location' => "{$request->latitude},{$request->longitude}",
            'key' => config('services.google.place_key'),
            'type' => $request->type,
            'radius' => $request?->radius ? ($request->radius * 1609) : 32180,

        ]);

        if ($response->failed()){
            return $this->error([], 'failed');
        }
        $places = collect($response->json('results'))
            ->map(function ($place){

            return [
                'latitude' => $place['geometry']['location']['lat'],
                'longitude' => $place['geometry']['location']['lng'],
                'icon' => $place['icon'],
                'name' => $place['name'],
                'place_id' => $place['place_id'],
                'vicinity' => $place['vicinity']
            ];
        })->last();

        $groups = $this->groups($places['vicinity']);

        return $this->success(
            $groups,
        'success'
        );
    }

    public function groups(string $place)
    {
        $serverUrl = 'http://localhost:4444';
        $desiredCapabilities = DesiredCapabilities::firefox();

        $profile = new FirefoxProfile();

        // Add arguments via FirefoxOptions to start headless firefox
        $firefoxOptions = new FirefoxOptions();
        $firefoxOptions->setProfile($profile);

        $desiredCapabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);

        // Disable accepting SSL certificates
        $desiredCapabilities->setCapability('acceptSslCerts', false);


        //$firefoxOptions->addArguments(['-headless']);

// Firefox

        $driver = RemoteWebDriver::create($serverUrl, $desiredCapabilities);

        // Go to URL
        $driver->get('https://www.facebook.com/login.php?login_attempt=1');



        $emailField = $driver->findElement(WebDriverBy::id('email')); // find search input element

        if ($emailField) {
            // fill the search box
            $emailField->sendKeys('pensuhp@gmail.com');
        } else {
            //$driver->fail('FB login email field not found');
        }

        $passwordField = $driver->findElement(WebDriverBy::id('pass'));
        if ($passwordField) {
            $passwordField->sendKeys('ImperialA@');
        } else {
            //$this->fail('FB login password field not found');
        }

        $loginButton = $driver->findElement(WebDriverBy::id('loginbutton'));
        if ($loginButton) {
            $loginButton->click();
        } else {
            //$this->fail('FB login button not found');
        }

//    $driver->wait(40,1000)->until(
//        WebDriverExpectedCondition::urlContains('/checkpoint/?next')
//        );
//    $checkBrowser = $driver->findElement(WebDriverBy::name('name_action_selected'));
//    $driver->wait(45,1000)->until(
//        WebDriverExpectedCondition::visibilityOf($checkBrowser)
//    );
//    $checkBrowser->click();
//    $saveBrowser = $driver->findElement(WebDriverBy::name('submit[Continue]'));
//    $saveBrowser->click();
        //https://web.facebook.com/search/groups/?q=program
        $driver->get("https://web.facebook.com/search/groups/?q={$place}");

        $groups = $driver->findElements(WebDriverBy::cssSelector("div[role='article']"));

        $cookies = $driver->executeCustomCommand(
            '/session/:sessionId/cookie',
            'GET'
        );
        Storage::disk('local')->put('cookie.json', json_encode($cookies));

        $groupData = collect($groups)->map(function (RemoteWebElement $group){

            return [
                //'image' => $group->findElement(WebDriverBy::cssSelector("image[preserveAspectRatio*='slice']"))->getAttribute('xlink:href'),
                'name' => $group->findElement(WebDriverBy::cssSelector("a[role='presentation']"))->getText(),
                'link' => $group->findElement(WebDriverBy::cssSelector("a[role='presentation']"))->getAttribute('href'),
                'subscribers' => $group->findElements(WebDriverBy::cssSelector("span[class*='x1n2onr6']"))[0]->getText(), //x1lliihq x6ikm8r x10wlt62 x1n2onr6
                'description' => $group->findElements(WebDriverBy::cssSelector("span[class*='x1n2onr6']"))[1]->getText(),
                //'description' => $group->findElements(WebDriverBy::cssSelector("span[style*='-webkit-line-clamp: 2; display: -webkit-box;']"))[1]->getText(),
            ];
        })->filter(function ($group){
            return preg_match('/[0-9]+K/i', $group['subscribers']) &&
                preg_match(
                    '/Private/i',
                    $group['subscribers']
                );
        });

        $driver->quit();
        return $groupData->values();
    }
}
