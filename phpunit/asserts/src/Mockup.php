<?php
declare(strict_types=1);

namespace Assert;


class Mockup
{
    private $registr = [];

    public function setFunction(string $name, $value, bool $execute = false)
    {
        if (uopz_set_return($name, $value, $execute)) {
            $this->registr[] = $name;
        }
    }

    public function destroy(): void
    {
        array_map('uopz_unset_return', $this->registr);
    }

    public function __destruct()
    {
        $this->destroy();
    }
}
