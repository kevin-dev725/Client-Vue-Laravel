<?php

namespace App;

use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\DataArraySerializer;

trait SerializesData
{
    /**
     * @param Request|null $request
     * @param ArraySerializer|DataArraySerializer $serializer
     * @param string $include
     * @return array
     */
    public function getSerializedData(Request $request = null, $serializer = null, $include = null)
    {
        if ($serializer === null) {
            $serializer = new ArraySerializer();
        }
        $class = array_last(explode('\\', static::class));
        $transformer = ('App\\Transformers\\' . $class . 'Transformer');
        $resource = new Item($this, new $transformer);
        $manager = new Manager();
        $manager->setSerializer($serializer);
        $includeString = '';
        if ($request && $request->has('include')) {
            $includeString .= $this->cleanIncludeString($request->get('include'));
        }
        $includeString .= ',' . $this->cleanIncludeString($include);
        if ($includeString !== '') {
            $manager->parseIncludes($includeString);
        }
        return $manager->createData($resource)->toArray();
    }

    /**
     * @param $include
     * @return string
     */
    public function cleanIncludeString($include)
    {
        if (!$include) return '';
        return trim(trim($include), ',');
    }
}