<?php
use FireflyIII\Models\Account;
use FireflyIII\Models\Bill;
use FireflyIII\Models\Budget;
use FireflyIII\Models\Category;
use FireflyIII\Models\LimitRepetition;
use FireflyIII\Models\PiggyBank;
use FireflyIII\Models\Tag;
use FireflyIII\Models\TransactionCurrency;
use FireflyIII\Models\TransactionJournal;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


// models
Route::bind(
    'account',
    function ($value) {
        if (Auth::check()) {
            $object = Account::leftJoin('account_types', 'account_types.id', '=', 'accounts.account_type_id')
                             ->where('account_types.editable', 1)
                             ->where('accounts.id', $value)
                             ->where('user_id', Auth::user()->id)
                             ->first(['accounts.*']);
            if ($object) {
                return $object;
            }
        }
        throw new NotFoundHttpException;
    }
);

Route::bind(
    'tj', function ($value) {
    if (Auth::check()) {
        $object = TransactionJournal::where('id', $value)->where('user_id', Auth::user()->id)->first();
        if ($object) {
            return $object;
        }
    }

    throw new NotFoundHttpException;
}
);

Route::bind(
    'currency', function ($value) {
    if (Auth::check()) {
        $object = TransactionCurrency::find($value);
        if ($object) {
            return $object;
        }
    }
    throw new NotFoundHttpException;
}
);

Route::bind(
    'bill', function ($value) {
    if (Auth::check()) {
        $object = Bill::where('id', $value)->where('user_id', Auth::user()->id)->first();
        if ($object) {
            return $object;
        }
    }

    throw new NotFoundHttpException;
}
);

Route::bind(
    'budget', function ($value) {
    if (Auth::check()) {
        $object = Budget::where('id', $value)->where('user_id', Auth::user()->id)->first();
        if ($object) {
            return $object;
        }
    }

    throw new NotFoundHttpException;
}
);

Route::bind(
    'limitrepetition', function ($value) {
    if (Auth::check()) {
        $object = LimitRepetition::where('limit_repetitions.id', $value)
                                 ->leftjoin('budget_limits', 'budget_limits.id', '=', 'limit_repetitions.budget_limit_id')
                                 ->leftJoin('budgets', 'budgets.id', '=', 'budget_limits.budget_id')
                                 ->where('budgets.user_id', Auth::user()->id)
                                 ->first(['limit_repetitions.*']);
        if ($object) {
            return $object;
        }
    }

    throw new NotFoundHttpException;
}
);

Route::bind(
    'piggyBank', function ($value) {
    if (Auth::check()) {
        $object = PiggyBank::where('piggy_banks.id', $value)
                           ->leftJoin('accounts', 'accounts.id', '=', 'piggy_banks.account_id')
                           ->where('accounts.user_id', Auth::user()->id)
                           ->first(['piggy_banks.*']);
        if ($object) {
            return $object;
        }
    }

    throw new NotFoundHttpException;
}
);

Route::bind(
    'category', function ($value) {
    if (Auth::check()) {
        $object = Category::where('id', $value)->where('user_id', Auth::user()->id)->first();
        if ($object) {
            return $object;
        }
    }

    throw new NotFoundHttpException;
}
);

Route::bind(
    'tag', function ($value) {
    if (Auth::check()) {
        $object = Tag::where('id', $value)->where('user_id', Auth::user()->id)->first();
        if ($object) {
            return $object;
        }
    }

    throw new NotFoundHttpException;
}
);


/**
 * Auth\AuthController
 */
Route::get('/register', ['uses' => 'Auth\AuthController@getRegister', 'as' => 'register']);

Route::controllers(
    [
        'auth'     => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]
);


Route::group(
    ['middleware' => ['auth', 'range']], function () {

    /**
     * Home Controller
     */
    Route::get('/', ['uses' => 'HomeController@index', 'as' => 'index']);
    Route::get('/home', ['uses' => 'HomeController@index', 'as' => 'home']);
    Route::post('/daterange', ['uses' => 'HomeController@dateRange', 'as' => 'daterange']);
    Route::get('/flush', ['uses' => 'HomeController@flush', 'as' => 'flush']);
    /**
     * Account Controller
     */
    Route::get('/accounts/{what}', ['uses' => 'AccountController@index', 'as' => 'accounts.index'])->where('what', 'revenue|asset|expense');
    Route::get('/accounts/create/{what}', ['uses' => 'AccountController@create', 'as' => 'accounts.create'])->where('what', 'revenue|asset|expense');
    Route::get('/accounts/edit/{account}', ['uses' => 'AccountController@edit', 'as' => 'accounts.edit']);
    Route::get('/accounts/delete/{account}', ['uses' => 'AccountController@delete', 'as' => 'accounts.delete']);
    Route::get('/accounts/show/{account}/{view?}', ['uses' => 'AccountController@show', 'as' => 'accounts.show']);

    Route::post('/accounts/store', ['uses' => 'AccountController@store', 'as' => 'accounts.store']);
    Route::post('/accounts/update/{account}', ['uses' => 'AccountController@update', 'as' => 'accounts.update']);
    Route::post('/accounts/destroy/{account}', ['uses' => 'AccountController@destroy', 'as' => 'accounts.destroy']);

    /**
     * Bills Controller
     */
    Route::get('/bills', ['uses' => 'BillController@index', 'as' => 'bills.index']);
    Route::get('/bills/rescan/{bill}', ['uses' => 'BillController@rescan', 'as' => 'bills.rescan']); # rescan for matching.
    Route::get('/bills/create', ['uses' => 'BillController@create', 'as' => 'bills.create']);
    Route::get('/bills/edit/{bill}', ['uses' => 'BillController@edit', 'as' => 'bills.edit']);
    Route::get('/bills/delete/{bill}', ['uses' => 'BillController@delete', 'as' => 'bills.delete']);
    Route::get('/bills/show/{bill}', ['uses' => 'BillController@show', 'as' => 'bills.show']);
    Route::post('/bills/store', ['uses' => 'BillController@store', 'as' => 'bills.store']);
    Route::post('/bills/update/{bill}', ['uses' => 'BillController@update', 'as' => 'bills.update']);
    Route::post('/bills/destroy/{bill}', ['uses' => 'BillController@destroy', 'as' => 'bills.destroy']);

    /**
     * Budget Controller
     */
    Route::get('/budgets', ['uses' => 'BudgetController@index', 'as' => 'budgets.index']);
    Route::get('/budgets/income', ['uses' => 'BudgetController@updateIncome', 'as' => 'budgets.income']); # extra.
    Route::get('/budgets/create', ['uses' => 'BudgetController@create', 'as' => 'budgets.create']);
    Route::get('/budgets/edit/{budget}', ['uses' => 'BudgetController@edit', 'as' => 'budgets.edit']);
    Route::get('/budgets/delete/{budget}', ['uses' => 'BudgetController@delete', 'as' => 'budgets.delete']);
    Route::get('/budgets/show/{budget}/{limitrepetition?}', ['uses' => 'BudgetController@show', 'as' => 'budgets.show']);
    Route::get('/budgets/list/noBudget', ['uses' => 'BudgetController@noBudget', 'as' => 'budgets.noBudget']);
    Route::post('/budgets/income', ['uses' => 'BudgetController@postUpdateIncome', 'as' => 'budgets.postIncome']);
    Route::post('/budgets/store', ['uses' => 'BudgetController@store', 'as' => 'budgets.store']);
    Route::post('/budgets/update/{budget}', ['uses' => 'BudgetController@update', 'as' => 'budgets.update']);
    Route::post('/budgets/destroy/{budget}', ['uses' => 'BudgetController@destroy', 'as' => 'budgets.destroy']);
    Route::post('budgets/amount/{budget}', ['uses' => 'BudgetController@amount']);

    /**
     * Category Controller
     */
    Route::get('/categories', ['uses' => 'CategoryController@index', 'as' => 'categories.index']);
    Route::get('/categories/create', ['uses' => 'CategoryController@create', 'as' => 'categories.create']);
    Route::get('/categories/edit/{category}', ['uses' => 'CategoryController@edit', 'as' => 'categories.edit']);
    Route::get('/categories/delete/{category}', ['uses' => 'CategoryController@delete', 'as' => 'categories.delete']);
    Route::get('/categories/show/{category}', ['uses' => 'CategoryController@show', 'as' => 'categories.show']);
    Route::get('/categories/list/noCategory', ['uses' => 'CategoryController@noCategory', 'as' => 'categories.noCategory']);
    Route::post('/categories/store', ['uses' => 'CategoryController@store', 'as' => 'categories.store']);
    Route::post('/categories/update/{category}', ['uses' => 'CategoryController@update', 'as' => 'categories.update']);
    Route::post('/categories/destroy/{category}', ['uses' => 'CategoryController@destroy', 'as' => 'categories.destroy']);

    /**
     * Currency Controller
     */
    Route::get('/currency', ['uses' => 'CurrencyController@index', 'as' => 'currency.index']);
    Route::get('/currency/create', ['uses' => 'CurrencyController@create', 'as' => 'currency.create']);
    Route::get('/currency/edit/{currency}', ['uses' => 'CurrencyController@edit', 'as' => 'currency.edit']);
    Route::get('/currency/delete/{currency}', ['uses' => 'CurrencyController@delete', 'as' => 'currency.delete']);
    Route::get('/currency/default/{currency}', ['uses' => 'CurrencyController@defaultCurrency', 'as' => 'currency.default']);
    Route::post('/currency/store', ['uses' => 'CurrencyController@store', 'as' => 'currency.store']);
    Route::post('/currency/update/{currency}', ['uses' => 'CurrencyController@update', 'as' => 'currency.update']);
    Route::post('/currency/destroy/{currency}', ['uses' => 'CurrencyController@destroy', 'as' => 'currency.destroy']);


    /**
     * ALL CHART Controllers
     */
    // accounts:
    Route::get('/chart/account/frontpage', ['uses' => 'Chart\AccountController@frontpage']);
    Route::get('/chart/account/month/{year}/{month}/{shared?}', ['uses' => 'Chart\AccountController@all'])->where(
        ['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}', 'shared' => 'shared']
    );
    Route::get('/chart/account/{account}', ['uses' => 'Chart\AccountController@single']);


    // bills:
    Route::get('/chart/bill/frontpage', ['uses' => 'Chart\BillController@frontpage']);
    Route::get('/chart/bill/{bill}', ['uses' => 'Chart\BillController@single']);

    // budgets:
    Route::get('/chart/budget/frontpage', ['uses' => 'Chart\BudgetController@frontpage']);
    Route::get('/chart/budget/year/{year}/{shared?}', ['uses' => 'Chart\BudgetController@year'])->where(['year' => '[0-9]{4}', 'shared' => 'shared']);
    Route::get('/chart/budget/{budget}/{limitrepetition}', ['uses' => 'Chart\BudgetController@budgetLimit']);
    Route::get('/chart/budget/{budget}', ['uses' => 'Chart\BudgetController@budget']);

    // categories:
    Route::get('/chart/category/frontpage', ['uses' => 'Chart\CategoryController@frontpage']);
    Route::get('/chart/category/year/{year}/{shared?}', ['uses' => 'Chart\CategoryController@year'])->where(['year' => '[0-9]{4}', 'shared' => 'shared']);
    Route::get('/chart/category/{category}/month', ['uses' => 'Chart\CategoryController@month']); // should be period.
    Route::get('/chart/category/{category}/all', ['uses' => 'Chart\CategoryController@all']);

    // piggy banks:
    Route::get('/chart/piggyBank/{piggyBank}', ['uses' => 'Chart\PiggyBankController@history']);

    // reports:
    Route::get('/chart/report/in-out/{year}/{shared?}', ['uses' => 'Chart\ReportController@yearInOut'])->where(['year' => '[0-9]{4}', 'shared' => 'shared']);
    Route::get('/chart/report/in-out-sum/{year}/{shared?}', ['uses' => 'Chart\ReportController@yearInOutSummarized'])->where(
        ['year' => '[0-9]{4}', 'shared' => 'shared']
    );


    /**
     * Help Controller
     */
    Route::get('/help/{route}', ['uses' => 'HelpController@show', 'as' => 'help.show']);

    /**
     * JSON Controller
     */
    Route::get('/json/expense-accounts', ['uses' => 'JsonController@expenseAccounts', 'as' => 'json.expense-accounts']);
    Route::get('/json/revenue-accounts', ['uses' => 'JsonController@revenueAccounts', 'as' => 'json.revenue-accounts']);
    Route::get('/json/categories', ['uses' => 'JsonController@categories', 'as' => 'json.categories']);
    Route::get('/json/tags', ['uses' => 'JsonController@tags', 'as' => 'json.tags']);
    Route::get('/json/box/in', ['uses' => 'JsonController@boxIn', 'as' => 'json.box.in']);
    Route::get('/json/box/out', ['uses' => 'JsonController@boxOut', 'as' => 'json.box.out']);
    Route::get('/json/box/bills-unpaid', ['uses' => 'JsonController@boxBillsUnpaid', 'as' => 'json.box.paid']);
    Route::get('/json/box/bills-paid', ['uses' => 'JsonController@boxBillsPaid', 'as' => 'json.box.unpaid']);
    Route::get('/json/transaction-journals/{what}', 'JsonController@transactionJournals');

    /**
     * New user Controller
     */
    Route::get('/new-user', ['uses' => 'NewUserController@index', 'as' => 'new-user.index']);
    Route::post('/new-user/submit', ['uses' => 'NewUserController@submit', 'as' => 'new-user.submit']);

    /**
     * Piggy Bank Controller
     */
    Route::get('/piggy-banks', ['uses' => 'PiggyBankController@index', 'as' => 'piggy-banks.index']);
    Route::get('/piggy-banks/add/{piggyBank}', ['uses' => 'PiggyBankController@add', 'as' => 'piggy-banks.addMoney']); # add money
    Route::get('/piggy-banks/remove/{piggyBank}', ['uses' => 'PiggyBankController@remove', 'as' => 'piggy-banks.removeMoney']); #remove money
    Route::get('/piggy-banks/create', ['uses' => 'PiggyBankController@create', 'as' => 'piggy-banks.create']);
    Route::get('/piggy-banks/edit/{piggyBank}', ['uses' => 'PiggyBankController@edit', 'as' => 'piggy-banks.edit']);
    Route::get('/piggy-banks/delete/{piggyBank}', ['uses' => 'PiggyBankController@delete', 'as' => 'piggy-banks.delete']);
    Route::get('/piggy-banks/show/{piggyBank}', ['uses' => 'PiggyBankController@show', 'as' => 'piggy-banks.show']);
    Route::post('/piggy-banks/store', ['uses' => 'PiggyBankController@store', 'as' => 'piggy-banks.store']);
    Route::post('/piggy-banks/update/{piggyBank}', ['uses' => 'PiggyBankController@update', 'as' => 'piggy-banks.update']);
    Route::post('/piggy-banks/destroy/{piggyBank}', ['uses' => 'PiggyBankController@destroy', 'as' => 'piggy-banks.destroy']);
    Route::post('/piggy-banks/add/{piggyBank}', ['uses' => 'PiggyBankController@postAdd', 'as' => 'piggy-banks.add']); # add money
    Route::post('/piggy-banks/remove/{piggyBank}', ['uses' => 'PiggyBankController@postRemove', 'as' => 'piggy-banks.remove']); # remove money.
    Route::post('/piggy-banks/sort', ['uses' => 'PiggyBankController@order', 'as' => 'piggy-banks.order']);

    /**
     * Preferences Controller
     */
    Route::get('/preferences', ['uses' => 'PreferencesController@index', 'as' => 'preferences']);
    Route::post('/preferences', ['uses' => 'PreferencesController@postIndex']);

    /**
     * Profile Controller
     */
    Route::get('/profile', ['uses' => 'ProfileController@index', 'as' => 'profile']);
    Route::get('/profile/change-password', ['uses' => 'ProfileController@changePassword', 'as' => 'profile.change-password']);
    Route::get('/profile/delete-account', ['uses' => 'ProfileController@deleteAccount', 'as' => 'profile.delete-account']);
    Route::post('/profile/delete-account', ['uses' => 'ProfileController@postDeleteAccount', 'as' => 'delete-account-post']);
    Route::post('/profile/change-password', ['uses' => 'ProfileController@postChangePassword', 'as' => 'change-password-post']);

    /**
     * Report Controller
     */
    Route::get('/reports', ['uses' => 'ReportController@index', 'as' => 'reports.index']);
    Route::get('/reports/{year}/{shared?}', ['uses' => 'ReportController@year', 'as' => 'reports.year'])->where(['year' => '[0-9]{4}', 'shared' => 'shared']);
    Route::get('/reports/{year}/{month}/{shared?}', ['uses' => 'ReportController@month', 'as' => 'reports.month'])->where(
        ['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}', 'shared' => 'shared']
    );

    // pop ups for budget report:

    /**
     * Search Controller
     */
    Route::get('/search', ['uses' => 'SearchController@index', 'as' => 'search']);

    /**
     * Tag Controller
     */
    Route::get('/tags', ['uses' => 'TagController@index', 'as' => 'tags.index']);
    Route::get('/tags/create', ['uses' => 'TagController@create', 'as' => 'tags.create']);
    Route::get('/tags/show/{tag}', ['uses' => 'TagController@show', 'as' => 'tags.show']);
    Route::get('/tags/edit/{tag}', ['uses' => 'TagController@edit', 'as' => 'tags.edit']);
    Route::get('/tags/delete/{tag}', ['uses' => 'TagController@delete', 'as' => 'tags.delete']);

    Route::post('/tags/store', ['uses' => 'TagController@store', 'as' => 'tags.store']);
    Route::post('/tags/update/{tag}', ['uses' => 'TagController@update', 'as' => 'tags.update']);
    Route::post('/tags/destroy/{tag}', ['uses' => 'TagController@destroy', 'as' => 'tags.destroy']);

    Route::post('/tags/hideTagHelp/{state}', ['uses' => 'TagController@hideTagHelp', 'as' => 'tags.hideTagHelp']);


    /**
     * Transaction Controller
     */
    Route::get('/transactions/{what}', ['uses' => 'TransactionController@index', 'as' => 'transactions.index'])->where(
        ['what' => 'expenses|revenue|withdrawal|deposit|transfer|transfers']
    );
    Route::get('/transactions/create/{what}', ['uses' => 'TransactionController@create', 'as' => 'transactions.create'])->where(
        ['what' => 'expenses|revenue|withdrawal|deposit|transfer|transfers']
    );
    Route::get('/transaction/edit/{tj}', ['uses' => 'TransactionController@edit', 'as' => 'transactions.edit']);
    Route::get('/transaction/delete/{tj}', ['uses' => 'TransactionController@delete', 'as' => 'transactions.delete']);
    Route::get('/transaction/show/{tj}', ['uses' => 'TransactionController@show', 'as' => 'transactions.show']);
    // transaction controller:
    Route::post('/transactions/store/{what}', ['uses' => 'TransactionController@store', 'as' => 'transactions.store'])->where(
        ['what' => 'expenses|revenue|withdrawal|deposit|transfer|transfers']
    );
    Route::post('/transaction/update/{tj}', ['uses' => 'TransactionController@update', 'as' => 'transactions.update']);
    Route::post('/transaction/destroy/{tj}', ['uses' => 'TransactionController@destroy', 'as' => 'transactions.destroy']);
    Route::post('/transaction/reorder', ['uses' => 'TransactionController@reorder', 'as' => 'transactions.reorder']);

    /**
     * Auth\Auth Controller
     */
    Route::get('/logout', ['uses' => 'Auth\AuthController@getLogout', 'as' => 'logout']);


}
);

