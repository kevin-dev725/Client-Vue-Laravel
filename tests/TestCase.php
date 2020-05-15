<?php

namespace Tests;

use App\Country;
use Geocode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Collection;
use Laravel\Passport\Client;
use League\Fractal\TransformerAbstract;
use Tests\Traits\WithAdmin;
use Tests\Traits\WithTestCoupon;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $testPhoneNumber = '+61491570156';

    protected $client_id;

    protected $client_secret;

    protected $country_id;

    protected $country_name;

    public function setUp()
    {
        parent::setUp();

        $this->withoutMiddleware([
            ThrottleRequests::class
        ]);

        $client = Client::query()
            ->where('password_client', true)
            ->first();
        $this->client_id = $client->id;
        $this->client_secret = $client->secret;
        $country = Country::query()
            ->where('iso_3166_2', 'US')
            ->first();
        $this->country_id = $country->id;
        $this->country_name = $country->name;
    }

    public function setUpTraits()
    {
        Geocode::fake();
        $uses = parent::setUpTraits();
        if (isset($uses[WithTestCoupon::class])) {
            $this->setupTestCoupon();
        }
        if (isset($uses[WithAdmin::class])) {
            $this->setupAdmin();
        }
        return $uses;
    }

    /**
     * @param string $function_name
     * @param string|null $message
     */
    public function assertFunctionExists($function_name, $message = null)
    {
        $this->assertTrue(function_exists($function_name), $message ?: 'Function ' . $function_name . ' does not exist.');
    }

    protected function assertArrayNotHasKeys($keys, $array)
    {
        foreach ($keys as $key) {
            $this->assertArrayNotHasKey($key, $array);
        }
    }

    protected function assertClassUses($useClass, $class, $message = null)
    {
        $this->assertArrayHasKey($useClass, class_uses($class), $message ?: 'Class ' . $class . ' does not use ' . $useClass . '.');
    }

    /**
     * @param      $uri
     * @param      $expectedCount
     * @param null $perPage
     * @return TestResponse|null
     */
    protected function assertApiPaginatesData($uri, $expectedCount, $perPage = null)
    {
        $perPage = $perPage ?: 15;
        $res = null;

        if ($expectedCount > $perPage) {
            $loop = ceil($expectedCount / $perPage);
            for ($page = 1; $page <= $loop; $page++) {
                $count = $page === $loop ?
                    $expectedCount % $perPage :
                    $perPage;

                $res = $this->getJson($uri . (str_contains($uri, '?') ? '' : '?') . 'page=' . $page . '&per_page=' . $perPage)
                    ->assertSuccessful()
                    ->assertJsonCount($count, 'data');
            }
        } else {
            $res = $this->getJson($uri . (str_contains($uri, '?') ? '' : '?') . 'per_page=' . $perPage)
                ->assertSuccessful()
                ->assertJsonCount($expectedCount, 'data');
        }

        return $res;
    }

    /**
     * @param TransformerAbstract $transformerInstance
     * @param Model $modelInstance
     * @param array $includes
     * @param array|null $returnTypes
     * @param bool $assertReturns
     */
    protected function assertHasTransformerIncludes(TransformerAbstract $transformerInstance, $modelInstance, array $includes, array $returnTypes = null, $assertReturns = false)
    {
        for ($i = 0; $i < count($includes); $i++) {
            $include = $includes[$i];
            $this->assertInArray($include, array_merge($transformerInstance->getAvailableIncludes(), $transformerInstance->getDefaultIncludes()), "Cannot find '$include' in includes.");
            $this->assertMethodExists($transformerInstance, $method = 'include' . title_case(camel_case($include)));
            if ($returnTypes) {
                $this->assertInstanceOf($returnTypes[$i], call_user_func_array([$transformerInstance, $method], [$modelInstance]));
            }
            if ($assertReturns) {
                $this->assertTransformerIncludeReturns($transformerInstance, $include, $modelInstance);
            }
        }
    }

    protected function assertInArray($needle, array $haystack, $message = null)
    {
        $this->assertTrue(in_array($needle, $haystack), $message ?: "Cannot find '$needle' in array.");
    }

    protected function assertMethodExists($object, $methodName, $message = null)
    {
        $this->assertTrue(method_exists($object, $methodName), $message ?: 'Method ' . $methodName . ' does not exist.');
    }

    /**
     * @param TransformerAbstract $transformerInstance
     * @param string $include
     * @param Model $model
     * @param Model|Collection $expectedResults
     */
    protected function assertTransformerIncludeReturns(TransformerAbstract $transformerInstance, $include, $model, $expectedResults = null)
    {
        $method = 'include' . title_case(camel_case($include));
        if (!$expectedResults) {
            $this->assertMethodExists($model, $include);
            $expectedResults = $model->$include;
        }
        if ($expectedResults instanceof Collection) {
            $items = call_user_func_array([$transformerInstance, $method], [$model])->getData();
            $i = 0;
            $this->assertEquals(count($expectedResults), count($items));
            foreach ($expectedResults as $expectedResult) {
                $this->assertInstanceOf(get_class($expectedResult), $items[$i]);
                $this->assertEquals($expectedResult->id, $items[$i]->id);
                $i++;
            }
        } else {
            $result = call_user_func_array([$transformerInstance, $method], [$model])->getData();
            $this->assertInstanceOf(get_class($expectedResults), $result);
            $this->assertEquals($expectedResults->id, $result->id);
        }
    }

    protected function assertValidUrl($url, $message = null)
    {
        $this->assertTrue(strpos($url, 'http://') > -1 || strpos($url, 'https://') > -1, $message ?: 'Failed asserting that ' . $url . ' is a valid url.');
    }
}
