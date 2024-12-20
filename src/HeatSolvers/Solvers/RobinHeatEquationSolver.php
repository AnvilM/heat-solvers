<?php

declare(strict_types=1);

namespace Anvil\Heat\HeatSolvers\Solvers;

use Anvil\Heat\HeatSolvers\AbstractSolver;
use Exception;


class RobinHeatEquationSolver extends AbstractSolver
{
    private float $envTemp;   // Температура окружающей среды
    private float $hLeft;     // Коэффициент теплопередачи на левой границе
    private float $hRight;    // Коэффициент теплопередачи на правой границе

    public function __construct(
        float $length,
        int $timeSteps,
        int $spaceSteps,
        float $alpha,
        float $envTemp,
        float $hLeft,
        float $hRight
    ) {
        parent::__construct($length, $timeSteps, $spaceSteps, $alpha);

        $this->envTemp = $envTemp;
        $this->hLeft = $hLeft;
        $this->hRight = $hRight;
    }

    /**
     * @throws Exception
     */
    public function solve(): AbstractSolver
    {
        $dx = $this->length / ($this->spaceSteps - 1); // Шаг по пространству
        $dt = 1.0; // Временной шаг (можно изменить)
        $r = $this->alpha * $dt / ($dx * $dx); // Коэффициент разностной схемы

        // Проверка на устойчивость схемы
        if ($r > 0.5) {
            throw new Exception("Схема неустойчива. Уменьшите шаг по времени или увеличьте шаг по пространству.");
        }

        // Итерации по времени
        for ($t = 1; $t < $this->timeSteps; $t++) {
            // Итерации по пространству (без границ)
            for ($i = 1; $i < $this->spaceSteps - 1; $i++) {
                $this->temperature[$t][$i] = $this->temperature[$t - 1][$i] +
                    $r * ($this->temperature[$t - 1][$i - 1] - 2 * $this->temperature[$t - 1][$i] + $this->temperature[$t - 1][$i + 1]);
            }

            // Условия Робина на левой границе
            $this->temperature[$t][0] =
                ($this->temperature[$t - 1][1] - $dx * $this->hLeft * ($this->temperature[$t - 1][0] - $this->envTemp)) /
                (1 + $dx * $this->hLeft);

            // Условия Робина на правой границе
            $this->temperature[$t][$this->spaceSteps - 1] =
                ($this->temperature[$t - 1][$this->spaceSteps - 2] + $dx * $this->hRight * ($this->envTemp - $this->temperature[$t - 1][$this->spaceSteps - 1])) /
                (1 + $dx * $this->hRight);
        }

        return $this;
    }
}