<?php

/*
|--------------------------------------------------------------------------
| PHP Display Errors Configuration
|--------------------------------------------------------------------------
|
| Since Laravel intercepts and displays all errors with a detailed stack
| trace, we can turn off the display_errors ini directive. However, you
| may want to enable this option if you ever run into a dreaded white
| screen of death, as it can provide some clues.
|
*/

ini_set('display_errors', 'On');

/*
|--------------------------------------------------------------------------
| Laravel Configuration Loader
|--------------------------------------------------------------------------
|
| The Laravel configuration loader is responsible for returning an array
| of configuration options for a given bundle and file. By default, we
| use the files provided with Laravel; however, you are free to use
| your own storage mechanism for configuration arrays.
|
*/

define("SPLIT0", "------------------");
define("SPLIT",  "==================");


define("TIANCHAO_RATE", 0.1);   // 天朝无抵押贷款利率
define("CREDIT_RATE", 0.06);    // 信用卡贷款利率
define("CREDIT_MONTHS", 24);    // 信用卡分期付款时间
define("P2P_RATE", 0.5);        // P2P贷款利率
define("PROFILE_VERSION", 3);   // PROFILE内核版本

Laravel\Event::listen(Laravel\Config::loader, function($bundle, $file)
{
	return Laravel\Config::file($bundle, $file);
});

/*
|--------------------------------------------------------------------------
| Register Class Aliases
|--------------------------------------------------------------------------
|
| Aliases allow you to use classes without always specifying their fully
| namespaced path. This is convenient for working with any library that
| makes a heavy use of namespace for class organization. Here we will
| simply register the configured class aliases.
|
*/

$aliases = Laravel\Config::get('application.aliases');

Laravel\Autoloader::$aliases = $aliases;

/*
|--------------------------------------------------------------------------
| Auto-Loader Mappings
|--------------------------------------------------------------------------
|
| Registering a mapping couldn't be easier. Just pass an array of class
| to path maps into the "map" function of Autoloader. Then, when you
| want to use that class, just use it. It's simple!
|
*/

Autoloader::map(array(
	'Base_Controller' => path('app').'controllers/base.php',
    // libraries
    'CSVReader' => path('app').'libraries/CSVReader.php',
    'BaseMachine' => path('app').'libraries/baseMachine.php',
    'LinearMachine' => path('app').'libraries/linearMachine.php',
    'HomeMachine' => path('app').'libraries/homeMachine.php',
    // tools
    'EstimateHouseMachine' => path('app').'libraries/tool/estimateHouseMachine.php',
    'LoanMachine' => path('app').'libraries/tool/loanMachine.php',
    'WhetherMachine' => path('app').'libraries/tool/whetherMachine.php',
    // Game
    'GameMachine' =>  path('app').'libraries/game/gameMachine.php',
    'BegginerGameMachine' =>  path('app').'libraries/game/beginnerGameMachine.php',
    // Game_State
    'State' =>  path('app').'libraries/game/state.php',
    'AccidentState' =>  path('app').'libraries/game/state/AccidentState.php',
    'AssetsHandleState' =>  path('app').'libraries/game/state/AssetsHandleState.php',
    'AssetsListState' =>  path('app').'libraries/game/state/AssetsListState.php',
    'AssetsRefundState' =>  path('app').'libraries/game/state/AssetsRefundState.php',
    'BaseState' =>  path('app').'libraries/game/state/BaseState.php',
    'HouseBaseState' =>  path('app').'libraries/game/state/HouseBaseState.php',
    'ShopBaseState' =>  path('app').'libraries/game/state/ShopBaseState.php',
    'LicaiChanceState' =>  path('app').'libraries/game/state/LicaiChanceState.php',
    'LotteryState' =>  path('app').'libraries/game/state/LotteryState.php',
    'CertificateChanceState' =>  path('app').'libraries/game/state/CertificateChanceState.php',
    'OtherBaseState' =>  path('app').'libraries/game/state/OtherBaseState.php',
    'HouseChanceState' =>  path('app').'libraries/game/state/HouseChanceState.php',
    'HouseChanceLoanState' =>  path('app').'libraries/game/state/HouseChanceLoanState.php',
    'ShopChanceState' =>  path('app').'libraries/game/state/ShopChanceState.php',
    'ShopChanceLoanState' =>  path('app').'libraries/game/state/ShopChanceLoanState.php',
    'CharactersState' =>  path('app').'libraries/game/state/CharactersState.php',
    'CityState' =>  path('app').'libraries/game/state/CityState.php',
    'EndState' =>  path('app').'libraries/game/state/EndState.php',
    'SucceedState' =>  path('app').'libraries/game/state/SucceedState.php',
    'NoneState' =>  path('app').'libraries/game/state/NoneState.php',
    'SaleState' =>  path('app').'libraries/game/state/SaleState.php',
    'StepState' =>  path('app').'libraries/game/state/StepState.php',
    'MessageState' => path('app').'libraries/game/state/MessageState.php',
    'MeState' =>  path('app').'libraries/game/state/MeState.php',
    'MeEstateState' =>  path('app').'libraries/game/state/MeEstateState.php',
    'MeLoanState' =>  path('app').'libraries/game/state/MeLoanState.php',
    'MeLicaiState' =>  path('app').'libraries/game/state/MeLicaiState.php',
    'LoanState' => path('app').'libraries/game/state/LoanState.php',
    'LoanListState' => path('app').'libraries/game/state/LoanListState.php',
    'LoanRefundState' => path('app').'libraries/game/state/LoanRefundState.php',
    'StockChanceState' => path('app').'libraries/game/state/StockChanceState.php',
    'StockListState' => path('app').'libraries/game/state/StockListState.php',
    'StockSaleState' => path('app').'libraries/game/state/StockSaleState.php',
    //Game_Profile
    'UserProfile' => path('app').'libraries/game/profile/UserProfile.php',
    'RealEstateProfile' => path('app').'libraries/game/profile/RealEstateProfile.php',
    'AssetProfile' =>  path('app').'libraries/game/profile/AssetProfile.php',
    'HouseProfile' =>  path('app').'libraries/game/profile/HouseProfile.php',
    'ShopProfile' =>  path('app').'libraries/game/profile/ShopProfile.php',
    'LoanProfile' =>  path('app').'libraries/game/profile/LoanProfile.php',
    'LicaiProfile' =>  path('app').'libraries/game/profile/LicaiProfile.php',
    // Market
    'StocksMarket' => path('app').'libraries/game/market/StocksMarket.php',
    'Macro' => path('app').'libraries/game/macro/macro.php',
));

/*
|--------------------------------------------------------------------------
| Auto-Loader Directories
|--------------------------------------------------------------------------
|
| The Laravel auto-loader can search directories for files using the PSR-0
| naming convention. This convention basically organizes classes by using
| the class namespace to indicate the directory structure.
|
*/

Autoloader::directories(array(
	path('app').'models',
	path('app').'libraries',
));

/*
|--------------------------------------------------------------------------
| Laravel View Loader
|--------------------------------------------------------------------------
|
| The Laravel view loader is responsible for returning the full file path
| for the given bundle and view. Of course, a default implementation is
| provided to load views according to typical Laravel conventions but
| you may change this to customize how your views are organized.
|
*/

Event::listen(View::loader, function($bundle, $view)
{
	return View::file($bundle, $view, Bundle::path($bundle).'views');
});

/*
|--------------------------------------------------------------------------
| Laravel Language Loader
|--------------------------------------------------------------------------
|
| The Laravel language loader is responsible for returning the array of
| language lines for a given bundle, language, and "file". A default
| implementation has been provided which uses the default language
| directories included with Laravel.
|
*/

Event::listen(Lang::loader, function($bundle, $language, $file)
{
	return Lang::file($bundle, $language, $file);
});

/*
|--------------------------------------------------------------------------
| Attach The Laravel Profiler
|--------------------------------------------------------------------------
|
| If the profiler is enabled, we will attach it to the Laravel events
| for both queries and logs. This allows the profiler to intercept
| any of the queries or logs performed by the application.
|
*/

if (Config::get('application.profiler'))
{
	Profiler::attach();
}

/*
|--------------------------------------------------------------------------
| Enable The Blade View Engine
|--------------------------------------------------------------------------
|
| The Blade view engine provides a clean, beautiful templating language
| for your application, including syntax for echoing data and all of
| the typical PHP control structures. We'll simply enable it here.
|
*/

Blade::sharpen();

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
|
| We need to set the default timezone for the application. This controls
| the timezone that will be used by any of the date methods and classes
| utilized by Laravel or your application. The timezone may be set in
| your application configuration file.
|
*/

date_default_timezone_set(Config::get('application.timezone'));

/*
|--------------------------------------------------------------------------
| Start / Load The User Session
|--------------------------------------------------------------------------
|
| Sessions allow the web, which is stateless, to simulate state. In other
| words, sessions allow you to store information about the current user
| and state of your application. Here we'll just fire up the session
| if a session driver has been configured.
|
*/

if ( ! Request::cli() and Config::get('session.driver') !== '')
{
	Session::load();
}