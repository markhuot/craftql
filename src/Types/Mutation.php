<?php

namespace markhuot\CraftQL\Types;

use craft\base\Element;
use GraphQL\Error\UserError;
use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\FieldBehaviors\EntryMutationArguments;

class Mutation extends Schema {

    function boot() {

        $this->addField('helloWorld')
            ->description('A sample mutation. Doesn\'t actually save anything.')
            ->resolve('If this were a real mutation it would have saved to the database.');

        foreach ($this->request->entryTypes()->all('mutate') as $entryType) {
            $this->addField('upsert'.$entryType->getName())
                ->type($entryType)
                ->description('Create or update existing '.$entryType->getName().'.')
                ->use(new EntryMutationArguments);
        }

        if ($this->request->globals()->count() && $this->request->token()->can('mutate:globals')) {
            /** @var \markhuot\CraftQL\Types\Globals $globalSet */
            foreach ($this->request->globals()->all() as $globalSet) {
                $upsertField = $this->addField('upsert'.$globalSet->getName().'Globals')
                    ->type($globalSet)
                    ->addArgumentsByLayoutId($globalSet->getContext()->fieldLayoutId);

                $upsertField->resolve(function ($root, $args) use ($globalSet, $upsertField) {
                        $globalSetElement = $globalSet->getContext();

                        foreach ($args as $handle => &$value) {
                            $callback = $upsertField->getArgument($handle)->getOnSave();
                            if ($callback) {
                                $value = $callback($value);
                            }
                        }

                        $globalSetElement->setFieldValues($args);
                        Craft::$app->getElements()->saveElement($globalSetElement);
                        return $globalSetElement;
                    });
            }
        }

        if ($this->request->token()->can('mutate:users')) {
            $updateUser = $this->addField('upsertUser')
                ->type(User::class)
                ->resolve(function ($root, $args, $context, $info) {
                    $values = $args;

                    if (!empty($args['id'])) {
                        $userId = @$args['id'];
                        unset($values['id']);

                        $user = \craft\elements\User::find()->id($userId)->anyStatus()->one();
                        if (!$user) {
                            throw new UserError('Could not find user '.$userId);
                        }
                    }
                    else {
                        $user = new \craft\elements\User;
                    }

                    foreach (['firstName', 'lastName', 'username', 'email'] as $fieldName) {
                        if (isset($values[$fieldName])) {
                            $user->{$fieldName} = $values[$fieldName];
                            unset($values[$fieldName]);
                        }
                    }

                    $permissions = [];
                    if (!empty($values['permissions'])) {
                        $permissions = $values['permissions'];
                        unset($values['permissions']);
                    }

                    if (!empty($values)) {
                        $user->setFieldValues($values);
                    }

                    $user->setScenario(Element::SCENARIO_LIVE);

                    if (!Craft::$app->elements->saveElement($user)) {
                        if (!empty($user->getErrors())) {
                            foreach ($user->getErrors() as $key => $errors) {
                                foreach ($errors as $error) {
                                    throw new UserError($error);
                                }
                            }
                        }
                    }

                    if (!empty($permissions)) {
                        Craft::$app->getUserPermissions()->saveUserPermissions($user->id, $permissions);
                    }

                    return $user;
                });

            $updateUser->addIntArgument('id');
            $updateUser->addStringArgument('firstName');
            $updateUser->addStringArgument('lastName');
            $updateUser->addStringArgument('username');
            $updateUser->addStringArgument('email');

            if ($this->request->token()->can('mutate:userPermissions')) {
                $updateUser->addStringArgument('permissions')->lists();
            }

            $fieldLayout = Craft::$app->getFields()->getLayoutByType(\craft\elements\User::class);
            $updateUser->addArgumentsByLayoutId($fieldLayout->id);
        }

        // $fields['upsertField'] = [
        //     'type' => \markhuot\CraftQL\Types\Entry::interface($request),
        //     'args' => [
        //         'id' => Type::nonNull(Type::int()),
        //         'json' => Type::nonNull(Type::string()),
        //     ],
        //     'resolve' => function ($root, $args) {
        //         $entry = \craft\elements\Entry::find();
        //         $entry->id($args['id']);
        //         $entry = $entry->first();

        //         $json = json_decode($args['json'], true);
        //         $fieldData = [];
        //         foreach ($json as $fieldName => $value) {
        //             if (in_array($fieldName, ['title'])) {
        //                 $entry->{$fieldName} = $value;
        //             }
        //             else {
        //                 $fieldData[$fieldName] = $value;
        //             }
        //         }

        //         if (!empty($fieldData)) {
        //             $entry->setFieldValues($fieldData);
        //         }

        //         Craft::$app->elements->saveElement($entry);

        //         return $entry;
        //     },
        // ];
    }

}