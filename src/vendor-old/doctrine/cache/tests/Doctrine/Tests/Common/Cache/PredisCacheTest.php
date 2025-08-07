<?php

namespace Doctrine\Tests\Common\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\PredisCache;
use Predis\Client;
use Predis\Connection\ConnectionException;

class PredisCacheTest extends CacheTest
{
    private $client;

    protected function setUp()
    {
        if (! class_exists('Predis\Client')) {
            $this->markTestSkipped('Predis\Client is missing. Make sure to "composer install" to have all dev dependencies.');
        }

        $this->client = new Client;

        try {
            $this->client->connect();
        } catch (ConnectionException $e) {
            $this->markTestSkipped('Cannot connect to Redis because of: '.$e);
        }
    }

    public function test_hit_misses_stats_are_provided()
    {
        $cache = $this->_getCacheDriver();
        $stats = $cache->getStats();

        $this->assertNotNull($stats[Cache::STATS_HITS]);
        $this->assertNotNull($stats[Cache::STATS_MISSES]);
    }

    /**
     * @return PredisCache
     */
    protected function _getCacheDriver()
    {
        return new PredisCache($this->client);
    }

    /**
     * {@inheritDoc}
     *
     * @dataProvider provideDataToCache
     */
    public function test_set_contains_fetch_delete($value)
    {
        if ($value === []) {
            $this->markTestIncomplete(
                'Predis currently doesn\'t support saving empty array values. '
                .'See https://github.com/nrk/predis/issues/241'
            );
        }

        parent::testSetContainsFetchDelete($value);
    }

    /**
     * {@inheritDoc}
     *
     * @dataProvider provideDataToCache
     */
    public function test_update_existing_entry($value)
    {
        if ($value === []) {
            $this->markTestIncomplete(
                'Predis currently doesn\'t support saving empty array values. '
                .'See https://github.com/nrk/predis/issues/241'
            );
        }

        parent::testUpdateExistingEntry($value);
    }

    public function test_allows_generic_predis_client()
    {
        /* @var $predisClient \Predis\ClientInterface */
        $predisClient = $this->getMock('Predis\\ClientInterface');

        $this->assertInstanceOf('Doctrine\\Common\\Cache\\PredisCache', new PredisCache($predisClient));
    }
}
