<?php
use Carbon\Carbon;
use FireflyIII\Models\Category;
use FireflyIII\Repositories\Category\CategoryRepository;
use League\FactoryMuffin\Facade as FactoryMuffin;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * Generated by PHPUnit_SkeletonGenerator on 2015-05-05 at 19:19:32.
 */
class CategoryRepositoryTest extends TestCase
{
    /**
     * @var CategoryRepository
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new CategoryRepository;
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::countJournals
     */
    public function testCountJournals()
    {
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $result   = $this->object->countJournals($category);

        $this->assertEquals(0, $result);
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::destroy
     */
    public function testDestroy()
    {
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $this->object->destroy($category);

        $count = Category::where('id', $category->id)->whereNull('deleted_at')->count();
        $this->assertEquals(0, $count);
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::getCategories
     */
    public function testGetCategories()
    {
        $user          = FactoryMuffin::create('FireflyIII\User');
        $cat1          = FactoryMuffin::create('FireflyIII\Models\Category');
        $cat2          = FactoryMuffin::create('FireflyIII\Models\Category');
        $cat1->name    = 'BBBBB';
        $cat2->name    = 'AAAAA';
        $cat1->user_id = $user->id;
        $cat2->user_id = $user->id;
        $cat1->save();
        $cat2->save();
        $this->be($user);

        $set = $this->object->getCategories();
        $this->assertEquals('AAAAA', $set->first()->name);
        $this->assertCount(2, $set);
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::getCategoriesAndExpensesCorrected
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetCategoriesAndExpensesCorrected()
    {
        $user     = FactoryMuffin::create('FireflyIII\User');
        $type     = FactoryMuffin::create('FireflyIII\Models\TransactionType'); // withdrawal
        $currency = FactoryMuffin::create('FireflyIII\Models\TransactionCurrency'); // something

        // some dates:
        $start   = new Carbon('2014-01-01');
        $end     = new Carbon('2014-01-31');
        $inRange = new Carbon('2014-01-12');

        // generate three categories
        // with ten journals each:
        for ($i = 0; $i < 3; $i++) {
            // category:
            /** @var Category $category */
            $category          = FactoryMuffin::create('FireflyIII\Models\Category');
            $category->user_id = $user->id;
            $category->save();

            for ($j = 0; $j < 10; $j++) {
                $journal                          = FactoryMuffin::create('FireflyIII\Models\TransactionJournal');
                $journal->user_id                 = $user->id;
                $journal->transaction_currency_id = $currency->id;
                $journal->transaction_type_id     = $type->id; // make it a withdrawal
                $journal->date                    = $inRange;
                $journal->save();
                // connect to category:
                $category->transactionjournals()->save($journal);
            }
        }

        $this->be($user);
        $set = $this->object->getCategoriesAndExpensesCorrected($start, $end);
        $this->assertCount(3, $set);
        reset($set);

        // 10 journals of 100 each = 1000.
        $this->assertEquals('1000', current($set)['sum']);
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::getFirstActivityDate
     */
    public function testGetFirstActivityDate()
    {
        $user    = FactoryMuffin::create('FireflyIII\User');
        $journal = FactoryMuffin::create('FireflyIII\Models\TransactionJournal');
        /** @var Category $category */
        $category          = FactoryMuffin::create('FireflyIII\Models\Category');
        $journal->user_id  = $user->id;
        $journal->date     = new Carbon('2015-02-11');
        $category->user_id = $user->id;
        $category->transactionjournals()->save($journal);
        $journal->save();
        $category->save();

        $this->be($user);

        $date = $this->object->getFirstActivityDate($category);
        $this->assertEquals('2015-02-11', $date->format('Y-m-d'));
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::getFirstActivityDate
     */
    public function testGetFirstActivityDateNull()
    {
        /** @var Category $category */
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $this->be($category->user);

        $date = $this->object->getFirstActivityDate($category);
        $this->assertEquals(Carbon::now()->format('Y-m-d'), $date->format('Y-m-d'));
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::getJournals
     */
    public function testGetJournals()
    {

        /** @var Category $category */
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $this->be($category->user);
        $set = $this->object->getJournals($category, 1);

        $this->assertEquals(0, $set->count());
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::getLatestActivity
     */
    public function testGetLatestActivity()
    {
        $user    = FactoryMuffin::create('FireflyIII\User');
        $journal = FactoryMuffin::create('FireflyIII\Models\TransactionJournal');
        /** @var Category $category */
        $category          = FactoryMuffin::create('FireflyIII\Models\Category');
        $journal->user_id  = $user->id;
        $journal->date     = new Carbon('2015-02-11');
        $category->user_id = $user->id;
        $category->transactionjournals()->save($journal);
        $journal->save();
        $category->save();

        $this->be($user);

        $date = $this->object->getLatestActivity($category);
        $this->assertEquals('2015-02-11', $date->format('Y-m-d'));
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::getWithoutCategory
     */
    public function testGetWithoutCategory()
    {
        $user = FactoryMuffin::create('FireflyIII\User');
        $this->be($user);

        $set = $this->object->getWithoutCategory(new Carbon, new Carbon);
        $this->assertCount(0, $set);
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::spentInPeriodCorrected
     * @covers FireflyIII\Repositories\Shared\ComponentRepository::spentInPeriod
     */
    public function testSpentInPeriodSumCorrected()
    {
        $user = FactoryMuffin::create('FireflyIII\User');
        $this->be($user);
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $sum      = $this->object->spentInPeriodCorrected($category, new Carbon, new Carbon, false);

        $this->assertEquals(0, $sum);


    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::spentInPeriodCorrected
     * @covers FireflyIII\Repositories\Shared\ComponentRepository::spentInPeriod
     */
    public function testSpentInPeriodSumCorrectedShared()
    {
        $user = FactoryMuffin::create('FireflyIII\User');
        $this->be($user);
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $sum      = $this->object->spentInPeriodCorrected($category, new Carbon, new Carbon, true);

        $this->assertEquals(0, $sum);


    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::spentOnDaySumCorrected
     */
    public function testSpentOnDaySumCorrected()
    {
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $sum      = $this->object->spentOnDaySumCorrected($category, new Carbon);

        $this->assertEquals(0, $sum);
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::store
     */
    public function testStore()
    {
        $user        = FactoryMuffin::create('FireflyIII\User');
        $data        = [
            'name' => 'New category' . rand(1, 100),
            'user' => $user->id
        ];
        $newCategory = $this->object->store($data);

        $this->assertEquals($data['name'], $newCategory->name);
    }

    /**
     * @covers FireflyIII\Repositories\Category\CategoryRepository::update
     */
    public function testUpdate()
    {
        $category    = FactoryMuffin::create('FireflyIII\Models\Category');
        $data        = [
            'name' => 'New category' . rand(1, 100),
        ];
        $newCategory = $this->object->update($category, $data);

        $this->assertEquals($data['name'], $newCategory->name);
    }

    public function testgetLatestActivityNull()
    {
        /** @var Category $category */
        $category = FactoryMuffin::create('FireflyIII\Models\Category');
        $this->be($category->user);

        $date = $this->object->getLatestActivity($category);
        $this->assertNull($date);
    }
}
