<?php

declare(strict_types=1);

namespace Anvil\Heat\HeatSolvers\Solvers;

use Anvil\Heat\HeatSolvers\AbstractSolver;

final class DirichletHeatEquationSolver extends AbstractSolver
{
    private float $leftTemperature;  // Температура на левой границе
    private float $rightTemperature; // Температура на правой границе

    // Конструктор для инициализации параметров задачи
    public function __construct(
        float $length,
        int $timeSteps,
        int $spaceSteps,
        float $alpha,
        float $leftTemperature,
        float $rightTemperature
    ) {
        parent::__construct($length, $timeSteps, $spaceSteps, $alpha);

        $this->leftTemperature = $leftTemperature;
        $this->rightTemperature = $rightTemperature;
    }

    // Реализация метода решения задачи с граничными условиями Дирихле
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

            // Устанавливаем граничные условия Дирихле
            $this->temperature[$t][0] = $this->leftTemperature;  // Левая граница
            $this->temperature[$t][$this->spaceSteps - 1] = $this->rightTemperature;  // Правая граница
        }

        return $this;
    }
}