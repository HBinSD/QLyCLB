<?php

interface DAOInterface {
    public function insert(object $t): bool;

    public function update(object $t): bool;

    public function delete(object $t): bool;

    /**
     * @return array<object>
     */
    public function selectAll(): array;

    public function selectByID(object $t): ?object;

    public function selectByCondition(string $condition): array;
}