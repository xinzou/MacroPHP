<?php
namespace controller;

use boot\Bootstrap;
use Doctrine\Common\EventArgs;
use Entity\Actor;
use event\TestEvent;
use subscriber\TestEventSubscriber;
use Zend\Permissions\Rbac\Rbac;
use Zend\Validator\EmailAddress;

class Hello extends Controller
{

    protected $hooks = array(
        "the.hook.name" => array(
            array(),
        ),
        "slim.after.dispatch" => array(array("black", "red")),
    );

    protected function registerHooks()
    {
        foreach ($this->hooks as $key => $val) {
            $this->app->hook($key, function () use ($val) {
                $data['name'] = "jack";
                echo $data['name'];
                echo "registerHooks";
            });
        }
    }

    public function index()
    {
        $this->sessionContainer->pageNum11 = 10;
        $validator = new EmailAddress();
        $name = array(
            'name' => "macro",
        );
        $this->applyHook("the.hook.name", $name);
        $this->render('/home/index.twig', array(
            'somevar' => $name['name'],
        ));
        $eventArgs = new EventArgs();
        $eventArgs->obj = array(1, 2, 3, 4, 5, 6);
        // $testEvent = new TestEvent(Bootstrap::getEntityManager()->getEventManager());
        $eventSubscriber = new TestEventSubscriber();
        $test = new TestEvent(Bootstrap::getEvm());
        $test->preFoo($eventArgs);
        Bootstrap::getEvm()->addEventSubscriber($eventSubscriber);
        Bootstrap::getEvm()->dispatchEvent(TestEvent::preFoo, $eventArgs);
        //   print_r(Bootstrap::getEvm()->getListeners());
    }

    public function show()
    {
        /*
        $client = new Client();
        $response = $client->get("http://guzzlephp.org"); */
        $this->sessionContainer->pageNum = 10;
        $this->sessionContainer->limit = "Macro chen";
        $this->sessionContainer->parans = "AAAAAAAAa";
        echo $this->sessionContainer->parans;
        print_r($this->param('name'));
    }

    public function test()
    {
        print_r($this->app->router()
            ->getCurrentRoute()
            ->getParams());
        print_r($this->sessionContainer->pageNum);
    }

    public function addItem()
    {
        $this->app->applyHook('aaa');
        print_r(get_class_methods($this->app));
        $em = Bootstrap::getEntityManager();
        /* $conn = $em->getConnection(); */
        $actor = new Actor();
        $actor->setFirstName('macro');
        $actor->setLastName("bbb");
        $eventArgs = new EventArgs();
        $eventArgs->obj = $actor;
        // $testEvent = new TestEvent(Bootstrap::getEntityManager()->getEventManager());
        $eventSubscriber = new TestEventSubscriber();
        Bootstrap::getEntityManager()->getEventManager()->addEventSubscriber($eventSubscriber);
        Bootstrap::getEntityManager()->getEventManager()->dispatchEvent(TestEvent::preFoo, $eventArgs);

        $em->persist($actor);
        $em->flush($actor);

        /*
         * $metadata = $em->getClassMetadata(get_class($actor));
         * $tableName = $metadata->getQuotedTableName($conn);
         * echo $tableName;
         * print_r($metadata->getTableName());
         * $results = $conn->query("select * from " . $tableName);
         * print_r($metadata->getAssociationMappings());
         */
        /*
     * $actor->setFirstName("zhao");
     * $actor->setLastName("haha");
     * $em->persist($actor);
     * $em->flush($actor );
     */
    }

    public function admin()
    {
        if ($this->app->response->getStatus() == 404) {
            check_login();
        }
    }

    public function rbac()
    {
        $rbac = new Rbac();
        $rbac->addRole("foo");
        var_dump($rbac->hasRole('foo'));
        echo "rbac";
    }

    public function rbac1()
    {
        print_r($this->params);
        echo "rbac1";
    }

    public function login()
    {
        echo "Login....";
    }
}