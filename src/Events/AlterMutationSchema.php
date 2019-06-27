<?php

namespace markhuot\CraftQL\Events;

use markhuot\CraftQL\Builders\Schema;
use yii\base\Event;

/**
 * Class AlterMutationSchema
 *
 * @author  Ether Creative
 * @package markhuot\CraftQL\Events
 */
class AlterMutationSchema extends Event
{

	const EVENT = 'craftQlAlterMutationSchema';

	/**
	 * The schema to build
	 *
	 * @var Schema
	 */
	public $mutation;

}
