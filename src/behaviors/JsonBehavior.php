<?php

namespace JCIT\behaviors;

use yii\base\Behavior;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\behaviors\TimestampBehavior as YiiTimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class JsonConfigurationBehavior
 * @package JCIT\behaviors
 */
class JsonBehavior extends Behavior
{
    /**
     * Attributes that are in the configuration
     *
     * NOTE: Conflicts are not covered, this behavior takes precedence over own attributes
     *
     * 'jsonAttribute' => [
     *      'attribute' => 'defaultValue'
     * ]
     *
     * @var array
     */
    public $jsonAttributes = [];

    /**
     * @param string $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        foreach ($this->jsonAttributes as $jsonAttribute => $attributes) {
            if (ArrayHelper::keyExists($name, $attributes)) {
                return $this->owner->{$jsonAttribute}[$name] ?? $this->jsonAttributes[$jsonAttribute][$name];
            }
        }

        return parent::__get($name);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        foreach ($this->jsonAttributes as $jsonAttribute => $attributes) {
            if (ArrayHelper::keyExists($name, $attributes)) {
                $jsonAttributeValues = $this->owner->{$jsonAttribute};
                $jsonAttributeValues[$name] = $value;
                $this->owner->{$jsonAttribute} = $jsonAttributeValues;
                return;
            }
        }

        parent::__set($name, $value);
    }

    /**
     * @param string $name
     * @param bool $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if ($checkVars) {
            foreach ($this->jsonAttributes as $jsonAttribute => $attributes) {
                if (ArrayHelper::keyExists($name, $attributes)) {
                    return true;
                }
            }
        }

        return parent::canGetProperty($name, $checkVars);
    }

    /**
     * @param string $name
     * @param bool $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true)
    {
        if ($checkVars) {
            foreach ($this->jsonAttributes as $jsonAttribute => $attributes) {
                if (ArrayHelper::keyExists($name, $attributes)) {
                    return true;
                }
            }
        }

        return parent::canSetProperty($name, $checkVars);
    }

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_INIT => 'initJsonAttributes',
            BaseActiveRecord::EVENT_AFTER_FIND => 'initJsonAttributes'
        ];
    }

    public function init(): void
    {
        parent::init();

        $owner = $this->owner;

        if ($owner instanceof Model) {
            $this->initJsonAttributes();
        }
    }

    public function initJsonAttributes(): void
    {
        $owner = $this->owner;

        foreach($this->jsonAttributes as $jsonAttribute => $attributes) {
            $owner->{$jsonAttribute} = ArrayHelper::merge($owner->{$jsonAttribute}, $attributes);
        }
    }
}

