<?php
namespace event;

use Doctrine\Common\EventManager;
use Doctrine\Common\EventArgs;

class TestEvent
{

    const preFoo = "preFoo";

    const postFoo = "postFoo";

    private $_evm;

    public $preFooInvoked = false;

    public $postFooInvoked = false;

    public function __construct(EventManager $evm)
    {
        $evm->addEventListener(array(
            self::preFoo,
            self::postFoo
        ), $this);
    }

    public function preFoo(EventArgs $e)
    {
        $obj = $e->obj;
        $obj->setFirstName('mmm');
        $this->preFooInvoked = true;
        echo "1111<br/>";
    }

    public function postFoo(EventArgs $e)
    {
        $this->postFooInvoked = true;
        echo "2222";
    }
}

?>