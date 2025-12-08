<?php

declare(strict_types=1);

namespace App\Service\Settings\Tasks;

use App\Service\Settings\AbstractWebDevConfigService;

class TaskConfigService extends AbstractWebDevConfigService implements TaskConfigServiceInterface
{
    /**
     * Get all tasks
     */
    public function getTasks(): array
    {
        $config = $this->getConfig();

        return $config['tasks'] ?? [];
    }

    /**
     * Get a specific task by key
     */
    public function getTask(string $key): ?array
    {
        $tasks = $this->getTasks();

        return $tasks[$key] ?? null;
    }

    /**
     * Create a new task
     */
    public function createTask(string $key, array $data): void
    {
        $this->validateTaskData($data);

        if ($this->config === null) {
            $this->loadConfig();
        }

        if (isset($this->config['tasks'][$key])) {
            throw new \InvalidArgumentException("Task with key '{$key}' already exists");
        }

        if (!isset($this->config['tasks'])) {
            $this->config['tasks'] = [];
        }

        $this->config['tasks'][$key] = $data;
        $this->saveConfig();
    }

    /**
     * Update an existing task
     */
    public function updateTask(string $key, array $data): void
    {
        $this->validateTaskData($data);

        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['tasks'][$key])) {
            throw new \InvalidArgumentException("Task with key '{$key}' does not exist");
        }

        $this->config['tasks'][$key] = array_merge($this->config['tasks'][$key], $data);
        $this->saveConfig();
    }

    /**
     * Delete a task
     */
    public function deleteTask(string $key): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['tasks'][$key])) {
            throw new \InvalidArgumentException("Task with key '{$key}' does not exist");
        }

        unset($this->config['tasks'][$key]);
        $this->saveConfig();
    }

    /**
     * Validate task data
     */
    private function validateTaskData(array $data): void
    {
        if (!isset($data['name']) || empty($data['name'])) {
            throw new \InvalidArgumentException("Task field 'name' is required");
        }

        $validHooks = ['init', 'create', 'start'];
        foreach ($data as $key => $value) {
            if (\in_array($key, $validHooks) && !\is_array($value)) {
                throw new \InvalidArgumentException("Task hook '{$key}' must be an array of commands");
            }
        }
    }
}
