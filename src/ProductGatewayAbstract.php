<?php

abstract class ProductGatewayAbstract
{
    abstract public function getAll(): array;
    abstract public function create(array $data): string;
    abstract public function massDelete(array $ids) : int;
}