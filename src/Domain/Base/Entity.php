<?php

declare(strict_types=1);

namespace App\Domain\Base;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\MappedSuperclass]
abstract class Entity implements JsonSerializable
{
    protected const DATETIME_FORMAT = \DateTimeInterface::RFC3339;

    #[\ReturnTypeWillChange]
    public function getArrayCopy(): array
    {
        $res = get_object_vars($this);
        foreach ($res as $key => $value) {
            if (is_subclass_of($value, '\Doctrine\Common\Collections\Collection', false)) {
                var_dump($value);
                die();
                $res[$key] = $value->toArray();
                //$res[$key] = [];
            }
        }
        return $res;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        //$data = get_object_vars($this);
        $data = array_filter(get_class_methods($this), function ($k) {
            return str_starts_with($k, 'get') && $k !== 'getArrayCopy';
        });

        $res = [];
        foreach ($data as $method) {
            $key = strtolower(substr($method, 3));
            $value = call_user_func([$this, $method]);
            $res[$key] = $value;

            if (is_resource($value)) {
                $res[$key] = stream_get_contents($value);
                continue;
            }
            if (is_subclass_of($value, 'DateTimeInterface')) {
                $res[$key] = $value->format(self::DATETIME_FORMAT);
                continue;
            }

            if (
                is_subclass_of($value, '\Darsyn\IP\Version\MultiVersionInterface', false)
                && method_exists($value, 'getProtocolAppropriateAddress')
            ) {
                $res[$key] = $value->getProtocolAppropriateAddress();
                continue;
            }

            if (
                is_subclass_of($value, '\Doctrine\Common\Collections\Collection', false)
                && !$value->isEmpty()
            ) {
                $res[$key] = [];
                continue;
            }
            /*
            if (
                is_subclass_of($value, '\Doctrine\Common\Collections\Collection', false)
                && !$value->isEmpty()
            ) {
                $res[$key] = [];
                foreach ($value as $obj) {
                    if (!is_subclass_of($value, 'Common')) {
                        continue;
                    }
                    $res[$key][] = $obj->jsonSerialize();
                }
                continue;
            }
            */

            if (in_array($key, ['createdBy', 'updatedBy', 'deletedBy'])) {
                $res[$key] = $value->getId();
                continue;
            }

            if (is_subclass_of($value, 'Common')) {
                /** @var Common $value */
                $res[$key . 'Id'] = $value->getId();
            }
        }

        return $res;
    }
}
