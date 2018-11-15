<?php

namespace App\Generator;

class GeneratorFactory implements GeneratorFactoryInterface
{
    public function makeGenerator($type, $table, $directory, $extension)
    {
        switch ($type) {
            case "model":
                $generator = new ModelGenerator($table, $directory, $extension);
                break;

            case "request":
                $generator = new RequestGenerator($table, $directory, $extension);
                break;

            case "controller":
                $generator = new ControllerGenerator($table, $directory, $extension);
                break;

            case "transformer":
                $generator = new TransformerGenerator($table, $directory, $extension);
                break;

            case "view":
                $generator = new ViewGenerator($table, $directory, $extension);
                break;

            case "route":
                $generator = new RouteGenerator($table, $directory, $extension);
                break;

            case "route_js":
                $generator = new RouteJSGenerator($table, $directory, $extension);
                break;

            default:
                throw new \Exception("Invalid type");
        }

        return $generator;
    }
}
