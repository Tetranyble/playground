<?php

use App\Models\User;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('youtube', function () {

    $yt = new YoutubeDl();
    //$channel = \App\Models\ChannelVideo::where('uuid', 'oDAw7vW7H0c')->first();
    $collection = $yt->setBinPath(config('pensuh.binary.ytdlp'))
        ->download(
            Options::create()

                ->geoByPass()
                ->output('%(id)s.%(ext)s')
                //->noPart(true)
                ->downloadPath(storage_path('app/public/video/downloads'))
                ->url('https://www.youtube.com/watch?v=oDAw7vW7H0c')
        );

    foreach ($collection->getVideos() as $video) {
        if ($video->getError() !== null) {
            echo "Error downloading video: {$video->getError()}.";
        } else {
            return
                \App\Models\ChannelVideo::create([
                    'tag' => $video->getTags(),
                    'repost_count' => $video->getRepostCount(),
                    'resolution' => $video->getResolution(),
                    'playlist' => $video->getPlaylist(),
                    'playlist_id' => $video->getPlaylistId(),
                    'playlist_index' => $video->getPlaylistIndex(),
                    'view_count' => $video->getViewCount(),
                    'duration' => $video->getDuration(),
                    'filename' => $video->getFilename(),
                    'artist' => $video->getArtist(),
                    'user_id' => 1,
                    'uuid' => \Illuminate\Support\Str::uuid()->toString().'-1',
                ]);

        }
    }

    return 'done';
});

Route::get('channel', function (\App\Services\Google\Youtube $youtube) {

    return auth('web')->user()->storeSearch([
        'q' => 'nollywood movies',
        'type' => 'video',
    ]);
})->middleware('auth');
Route::get('channels', function (\App\Services\Google\Youtube $youtube) {

    return $channels = auth('web')->user()->load('channels');
    $user = User::where('email', 'senenerst@gmail.com')->with('channels')->first();
    $channelsUUid = $user->channels->pluck('uuid')->implode(',');

    return $user->storeChannels($channelsUUid);
});

Route::get('upload', function () {
    $user = auth('web')->user();
    $user->upload(\App\Models\ChannelVideo::first());
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
/**
 * Cloud storage Authentication Routes
 * Example Google Drive,
 */
Route::middleware('auth')->prefix('services')->group(function () {
    Route::get('connect/{service}', [\App\Http\Controllers\Api\ServiceAuthenticationController::class, 'connect'])
        ->name('services.connect');

    Route::get('authorization/{service}', [\App\Http\Controllers\Api\ServiceAuthenticationController::class, 'authorization'])
        ->name('services.authorization');

});
Route::get('facebook', function () {
    Api::init(
        config('services.facebook.client_id'),
        config('services.facebook.client_secret'),
        config('services.facebook.token')
    );

    // The Api object is now available through singleton
    $api = Api::instance();

    $account = new AdAccount(null, null, $api);
    //$account->name = 'My account name';
    echo $account->name;
});
Route::get('facebook2', function () {
    $fb = new \Facebook\Facebook([
        'app_id' => config('services.facebook.client_id'),
        'app_secret' => config('services.facebook.client_secret'),
        'default_graph_version' => 'v2.4',
        'default_access_token' => config('services.facebook.token'), // optional
    ]);

    //return $fb->request('GET', '/me')->getResponse();
    $response = $fb->get('/search?q=coffee&type=group&center=37.76,122.427&distance=1000', '504981644943693|gCqVbZsTz2xV9NhPGKD1ln2IIiM');

    return $response;

});
Route::get('firefox', function () {
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
    $driver->get('https://web.facebook.com/search/groups/?q=program');

    $groups = $driver->findElements(WebDriverBy::cssSelector("div[role='article']"));

    $cookies = $driver->executeCustomCommand(
        '/session/:sessionId/cookie',
        'GET'
    );
    Storage::disk('local')->put('cookie.json', json_encode($cookies));

    $groupData = collect($groups)->map(function (RemoteWebElement $group) {

        return [
            //'image' => $group->findElement(WebDriverBy::cssSelector("image[preserveAspectRatio*='slice']"))->getAttribute('xlink:href'),
            'name' => $group->findElement(WebDriverBy::cssSelector("a[role='presentation']"))->getText(),
            'link' => $group->findElement(WebDriverBy::cssSelector("a[role='presentation']"))->getAttribute('href'),
            'subscribers' => $group->findElements(WebDriverBy::cssSelector("span[class*='x1n2onr6']"))[0]->getText(), //x1lliihq x6ikm8r x10wlt62 x1n2onr6
            'description' => $group->findElements(WebDriverBy::cssSelector("span[class*='x1n2onr6']"))[1]->getText(),
            //'description' => $group->findElements(WebDriverBy::cssSelector("span[style*='-webkit-line-clamp: 2; display: -webkit-box;']"))[1]->getText(),
        ];
    })->filter(fn ($group) => preg_match('/[0-9]+K/i', $group['subscribers']));

    $driver->quit();

    return $groupData->values();

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
