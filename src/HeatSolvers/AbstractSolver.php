<?php

declare(strict_types=1);

namespace Anvil\Heat\HeatSolvers;

abstract class AbstractSolver
{
    protected float $length;          // Длина стержня
    protected int $timeSteps;         // Число временных шагов
    protected int $spaceSteps;        // Число пространственных шагов
    protected float $alpha;           // Коэффициент теплопроводности
    protected array $temperature;     // Матрица температуры

    // Конструктор для инициализации параметров задачи
    public function __construct(float $length, int $timeSteps, int $spaceSteps, float $alpha)
    {
        $this->length = $length;
        $this->timeSteps = $timeSteps;
        $this->spaceSteps = $spaceSteps;
        $this->alpha = $alpha;

        // Инициализация температурного распределения
        $this->initializeTemperature();
    }

    // Инициализация температурного распределения (по умолчанию все температуры 0)
    private function initializeTemperature(): void
    {
        $this->temperature = array_fill(0, $this->timeSteps, array_fill(0, $this->spaceSteps, 0));
    }

    // Абстрактный метод для решения задачи
    abstract public function solve(): AbstractSolver;

    // Метод для получения таблицы температуры
    public function getTemperatureTable(): array
    {
        return $this->temperature;
    }
}