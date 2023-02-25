<?php

namespace BoxyBird\Adventure;

class AdventureStep
{
    protected string $key;

    protected $next_step_callback;

    protected $render_step_callback;

    public function __construct(string $key, callable $render_step_callback = null, callable $next_step_callback = null)
    {
        $this->key = $key;
        $this->next_step_callback = $next_step_callback;
        $this->render_step_callback = $render_step_callback;
    }

    public function toArray(): array
    {
        return [
            'key'                  => $this->key,
            'next_step_callback'   => $this->next_step_callback,
            'render_step_callback' => $this->render_step_callback,
        ];
    }
}
