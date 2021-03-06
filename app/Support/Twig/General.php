<?php

namespace FireflyIII\Support\Twig;

use App;
use Carbon\Carbon;
use Config;
use FireflyIII\Models\Account;
use FireflyIII\Models\Transaction;
use Route;
use Session;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * @codeCoverageIgnore
 *
 * Class TwigSupport
 *
 * @package FireflyIII\Support
 */
class General extends Twig_Extension
{


    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function getFilters()
    {
        return [
            $this->formatAmount(),
            $this->formatTransaction(),
            $this->formatAmountPlain(),
            $this->formatJournal(),
            $this->balance(),
            $this->getAccountRole()
        ];

    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            $this->getCurrencyCode(),
            $this->getCurrencySymbol(),
            $this->phpdate(),
            $this->env(),

            $this->activeRouteStrict(),
            $this->activeRoutePartial(),
            $this->activeRoutePartialWhat(),
        ];

    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'FireflyIII\Support\Twig\General';
    }

    /**
     * @return Twig_SimpleFilter
     */
    protected function formatAmount()
    {
        return new Twig_SimpleFilter(
            'formatAmount', function ($string) {
            return App::make('amount')->format($string);
        }, ['is_safe' => ['html']]
        );
    }

    /**
     * @return Twig_SimpleFilter
     */
    protected function formatTransaction()
    {
        return new Twig_SimpleFilter(
            'formatTransaction', function (Transaction $transaction) {
            return App::make('amount')->formatTransaction($transaction);
        }, ['is_safe' => ['html']]
        );
    }

    /**
     * @return Twig_SimpleFilter
     */
    protected function formatAmountPlain()
    {
        return new Twig_SimpleFilter(
            'formatAmountPlain', function ($string) {
            return App::make('amount')->format($string, false);
        }, ['is_safe' => ['html']]
        );
    }

    /**
     * @return Twig_SimpleFilter
     */
    protected function formatJournal()
    {
        return new Twig_SimpleFilter(
            'formatJournal', function ($journal) {
            return App::make('amount')->formatJournal($journal);
        }, ['is_safe' => ['html']]
        );
    }

    /**
     * @return Twig_SimpleFilter
     */
    protected function balance()
    {
        return new Twig_SimpleFilter(
            'balance', function (Account $account = null) {
            if (is_null($account)) {
                return 'NULL';
            }
            $date = Session::get('end', Carbon::now()->endOfMonth());

            return App::make('steam')->balance($account, $date);
        }
        );
    }

    /**
     * @return Twig_SimpleFilter
     */
    protected function getAccountRole()
    {
        return new Twig_SimpleFilter(
            'getAccountRole', function ($name) {
            return Config::get('firefly.accountRoles.' . $name);
        }
        );
    }

    /**
     * @return Twig_SimpleFunction
     */
    protected function getCurrencyCode()
    {
        return new Twig_SimpleFunction(
            'getCurrencyCode', function () {
            return App::make('amount')->getCurrencyCode();
        }
        );
    }

    /**
     * @return Twig_SimpleFunction
     */
    protected function getCurrencySymbol()
    {
        return new Twig_SimpleFunction(
            'getCurrencySymbol', function () {
            return App::make('amount')->getCurrencySymbol();
        }
        );
    }

    /**
     * @return Twig_SimpleFunction
     */
    protected function phpdate()
    {
        return new Twig_SimpleFunction(
            'phpdate', function ($str) {
            return date($str);
        }
        );
    }

    /**
     * @return Twig_SimpleFunction
     */
    protected function env()
    {
        return new Twig_SimpleFunction(
            'env', function ($name, $default) {
            return env($name, $default);
        }
        );
    }

    /**
     * Will return "active" when the current route matches the given argument
     * exactly.
     *
     * @return Twig_SimpleFunction
     */
    protected function activeRouteStrict()
    {
        return new Twig_SimpleFunction(
            'activeRouteStrict', function () {
            $args  = func_get_args();
            $route = $args[0]; // name of the route.

            if (Route::getCurrentRoute()->getName() == $route) {
                return 'active';
            }

            return '';
        }
        );
    }

    /**
     * Will return "active" when a part of the route matches the argument.
     * ie. "accounts" will match "accounts.index".
     *
     * @return Twig_SimpleFunction
     */
    protected function activeRoutePartial()
    {
        return new Twig_SimpleFunction(
            'activeRoutePartial', function () {
            $args  = func_get_args();
            $route = $args[0]; // name of the route.
            if (!(strpos(Route::getCurrentRoute()->getName(), $route) === false)) {
                return 'active';
            }

            return '';
        }
        );
    }

    /**
     * This function will return "active" when the current route matches the first argument (even partly)
     * but, the variable $what has been set and matches the second argument.
     *
     * @return Twig_SimpleFunction
     */
    protected function activeRoutePartialWhat()
    {
        return new Twig_SimpleFunction(
            'activeRoutePartialWhat', function ($context) {
            $args       = func_get_args();
            $route      = $args[1]; // name of the route.
            $what       = $args[2]; // name of the route.
            $activeWhat = isset($context['what']) ? $context['what'] : false;

            if ($what == $activeWhat && !(strpos(Route::getCurrentRoute()->getName(), $route) === false)) {
                return 'active';
            }

            return '';
        }, ['needs_context' => true]
        );
    }

}
