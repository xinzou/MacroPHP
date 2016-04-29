<?php
/**
 * User: macro
 * Date: 16-4-29
 * Time: 上午10:35
 */
include_once './config/config.php';

class Test extends \Boot\BootTask
{
    private $shard_id = 1;

    public function run()
    {
        $em = $this->getDbInstance('entityManager', 'db1');
        $shardManager = new \Doctrine\DBAL\Sharding\PoolingShardManager($em->getConnection());
        $shardManager->selectGlobal();
        $shardManager->selectShard($this->shard_id);
        $query = $em->createQuery("SELECT admin FROM Admin\\Entity\\Admin admin ORDER BY admin.id ASC")->setFirstResult(0)->setMaxResults(100);
        $query->setResultCacheLifetime(3600);
        $query->setResultCacheId('admin_index_query_result' . $this->shard_id);
        $data = new \Doctrine\ORM\Tools\Pagination\Paginator($query, true);
        $count = count($data);
        print_r($count);
    }
}

$test = new Test();
$test->run();