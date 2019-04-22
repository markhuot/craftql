<?php

namespace markhuot\CraftQL\Builders;

use yii\base\Component;
use markhuot\CraftQL\Request;

class BaseBuilder extends Component {

    /**
     * The request used to generate the schema
     *
     * @var \markhuot\CraftQL\Request
     */
    protected $request;

    /**
     * The token used to permission the schema
     *
     * @var \markhuot\CraftQL\Models\Token
     */
    protected $token;

    /**
     * Run the behavior's init methods
     *
     * @return void
     */
    function bootBehaviors() {
        if ($behaviors=$this->getBehaviors()) {
            foreach ($behaviors as $key => $behavior) {
                $this->{"init{$key}"}();
            }
        }
    }

    /**
     * The request that generated this schema
     *
     * @return Request
     */
    function getRequest(): Request {
        return $this->request;
    }

}