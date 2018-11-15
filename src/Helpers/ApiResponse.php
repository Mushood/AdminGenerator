<?php

namespace Sleekcube\AdminGenerator\Helpers;

use Sleekcube\AdminGenerator\Interfaces\Mappable;

class ApiResponse
{
    const SUCCESS = [
        'type' => 'success',
        'code' => 200,
    ];

    const EXCEPTION_THROWN = [
        'type' => 'exception',
        'code' => 500,
    ];

    const EXCEPTION_TRANSLATION = [
        'type' => 'translation',
        'code' => 700,
    ];

    /**
     * ApiResponse constructor.
     * @param array $status
     * @param array $errors
     * @param array $data
     */
    public function __construct(
        array $status = self::SUCCESS,
        array $data = [],
        array $errors = [],
        array $paginator = []
    )
    {
        $this->status = $status;
        $this->errors = $errors;
        $this->data = $data;
        $this->paginator = $paginator;
    }

    /**
     * @var array
     */
    protected $status;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $paginator;

    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param array $status
     */
    public function setStatus(array $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data): void
    {
        if (is_array($data)) {
            $this->data = $data;
        }

        if ($data instanceof Mappable) {
            $this->data = [$data];
        }
    }

    /**
     * @return array
     */
    public function getPaginator(): array
    {
        if(count($this->paginator) > 0) {
            unset($this->paginator['data']);
        }

        return $this->paginator;
    }

    /**
     * @param array $paginator
     */
    public function setPaginator(array $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * @param array $mapper
     * @param bool $mapping
     * @return array
     */
    public function response(array $mapper = [], $mapping = true): array
    {
        return [
            'status'    => $this->getStatus(),
            'data'      => $mapping ? DatabaseMapper::mapFromDatabase($mapper, $this->getData()) : $this->getData(),
            'errors'    => $this->getErrors(),
            'paginator' => $this->getPaginator(),
        ];
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function multiResponse(array $data = []): array
    {
        return [
            'status'    => $this->getStatus(),
            'data'      => $this->mapMultiple($data),
            'errors'    => $this->getErrors(),
            'paginator' => $this->getPaginator(),
        ];
    }

    /**
     * @param array $multipleData
     * @return array
     * @throws \Exception
     */
    private function mapMultiple(array $multipleData)
    {
        $mappedData = [];

        foreach ($multipleData as $unit) {
            $this->checkUnit($unit);
            $mapper     = $unit['mapper'];
            $data       = $unit['data'];
            $name       = $unit['name'];
            $mapping    = $unit['mapping'];
            $mappedData[$name] = $mapping ? DatabaseMapper::mapFromDatabase($mapper, $data) : $data;
        }

        return $mappedData;
    }

    /**
     * @param array $unit
     * @throws \Exception
     */
    private function checkUnit(array $unit)
    {
        if(!isset($unit['mapper'])) {
            throw new \Exception('Mapper is not set');
        }

        if(!isset($unit['data'])) {
            throw new \Exception('Data is not set');
        }

        if(!isset($unit['name'])) {
            throw new \Exception('Name is not set');
        }

        if(!isset($unit['mapping'])) {
            throw new \Exception('Mapping is not set');
        }
    }
}
