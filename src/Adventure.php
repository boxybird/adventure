<?php

namespace BoxyBird\Adventure;

use Exception;

class Adventure
{
    protected array $data = [];
    
    protected array $steps = [];
    
    protected array $errors = [];

    protected array $previous_step = [];
    
    protected string $to_step_key;
    
    protected string $current_key;

    public function toStep(string $key): self
    {
        $this->to_step_key = $key;

        return $this;
    }

    public function withErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function withData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function go(): array
    {
        $step = $this->goToStep($this->to_step_key);

        return array_merge($step, [
            'data'    => $this->data,
            'errors'  => $this->errors,
        ]);
    }

    public function getStep(string $key): array
    {
        return $this->goToStep($key);
    }

    public function goToStep(string $key): array
    {
        if (empty($this->steps[$key])) {
            throw new Exception("Step Key not found: '{$key}'");
        }

        $step = $this->steps[$key];

        $this->current_key = $key;

        return $step;
    }

    public function getKey(): string
    {
        return $this->current_key;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getPreviousStep(): array
    {
        return $this->previous_step;
    }

    public function invokeRenderStepCallback(string $key)
    {
        if (empty($this->steps[$key])) {
            throw new Exception("Step Key not found: '{$key}'");
        }

        $step = $this->steps[$key];

        if (!empty($this->previous_step['key']) && $this->previous_step['key'] === $this->current_key) {
            $this->previous_step = [];
        }

        return call_user_func($step['render_step_callback'], $this);
    }

    public function invokeNextStepCallback(string $key, array $request)
    {
        $this->previous_step = [
            'key'     => $key,
            'request' => $request,
        ];

        return call_user_func($this->steps[$key]['next_step_callback'], $this, $request);
    }

    public function addStep(string $key, callable $render_step_callback = null, callable $next_step_callback = null): void
    {
        $this->steps[$key] = (new AdventureStep($key, $render_step_callback, $next_step_callback))->toArray();
    }

    public function addSteps(array $steps): void
    {
        foreach ($steps as $key => $step) {
            $this->addStep($key, $step['render_step_callback'], $step['next_step_callback']);
        }
    }
}
