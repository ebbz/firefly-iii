<?php namespace FireflyIII\Http\Controllers\Auth;

use FireflyIII\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

/**
 * Class PasswordController
 *
 * @codeCoverageIgnore
 * @package FireflyIII\Http\Controllers\Auth
 */
class PasswordController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;


    protected $redirectPath = '/';

    /**
     * Create a new password controller instance.
     *
     * @codeCoverageIgnore
     *
     */
    public function __construct()
    {

        $this->middleware('guest');
    }

}
