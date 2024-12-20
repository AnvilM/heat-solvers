<?php

declare(strict_types=1);

namespace Anvil\Heat\HeatSolvers\Solvers;

use Anvil\Heat\HeatSolvers\AbstractSolver;

final class NeumannHeatEquationSolver extends AbstractSolver
{
    private float $leftFlux;  // Тепловой поток на левой границе
    private float $rightFlux; // Тепловой поток на правой границе

    // Конструктор для инициализации параметров задачи
    public function __construct(
        float $length,
        int $timeSteps,
        int $spaceSteps,
        float $alpha,
        float $leftFlux,
        float $rightFlux
    ) {
        parent::__construct($length, $timeSteps, $spaceSteps, $alpha);

        $this->leftFlux = $leftFlux;
        $this->rightFlux = $rightFlux;
    }

    // Реализация метода решения задачи с граничными условиями Неймана
    public function solve(): AbstractSolver
    {
        $dx = $this->length / ($this->spaceSteps - 1);  // Шаг по пространству
        $dt = 1.0;  // Временной шаг (для простоты, можно настроить в зависимости от задач)

        // Процесс итерации по времени
        for ($t = 1; $t < $this->timeSteps; $t++) {
            // Процесс итерации по пространству
            for ($i = 1; $i < $this->spaceSteps - 1; $i++) {
                // Применяем схему разностного метода для теплопроводности
                $this->temperature[$t][$i] = $this->temperature[$t - 1][$i] +
                    $this->alpha * $dt / ($dx * $dx) * (
                        $this->temperature[$t - 1][$i - 1] - 2 * $this->temperature[$t - 1][$i] + $this->temperature[$t - 1][$i + 1]
                    );
            }

            // Устанавливаем граничные условия Неймана
            // На левой границе (производная по x = q_left / dx)
            $this->temperature[$t][0] = $this->temperature[$t - 1][1] + $this->leftFlux * $dx;

            // На правой границе (производная по x = q_right / dx)
            $this->temperature[$t][$this->spaceSteps - 1] = $this->temperature[$t - 1][$this->spaceSteps - 2] + $this->rightFlux * $dx;
        }

        return $this;
    }
}
