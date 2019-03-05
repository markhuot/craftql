<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class Globals extends Schema {

    function getName():string {
        return ucfirst($this->context['handle']);
    }

    function boot() {
        $this->addFieldsByLayoutId($this->context['fieldLayoutId']);
    }

    function bootFieldLayouts(): Schema {
        parent::bootFieldLayouts();

        if (empty($this->fields)) {
            $warning = 'The global set, `'.$this->getName().'`, has no fields. This would violate the GraphQL spec so we filled it in with this placeholder.';
            $this->addStringField('empty')->description($warning)->resolve($warning);
        }

        return $this;
    }

}