<?php

namespace Sarfraznawaz2005\Meter\Charts;

use Sarfraznawaz2005\Meter\Models\MeterModel;
use Sarfraznawaz2005\Meter\Monitors\QueryMonitor;
use Sarfraznawaz2005\Meter\Type;

class QueriesTimeChart extends Chart
{
    /**
     * Sets options for chart.
     *
     * @return void
     */
    protected function setOptions()
    {
        $this->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'title' => [
                'display' => true,
                'text' => ['Average: ' . round(collect($this->getValues())->average()) . 'ms'],
            ],
            'legend' => false,
            'scales' => [
                'yAxes' => [[
                    'ticks' => [
                        'beginAtZero' => true
                    ],
                    'scaleLabel' => [
                        'display' => true,
                        'labelString' => 'Time (ms)'
                    ]
                ]],
                'xAxes' => [[
                    'display' => false,
                    //'type' => 'time',
                    'time' => [
                        'displayFormats' => ['hour' => 'MMM D hA'],
                    ],
                    'ticks' => [
                        'beginAtZero' => true,
                        'autoSkip' => true,
                        'autoSkipPadding' => 30,
                        'maxRotation' => 0,
                    ],
                    'gridLines' => ['offsetGridLines' => true],
                    'offset' => true,
                ]]
            ],
        ], true);
    }

    /**
     * Sets data for chart.
     *
     * @param MeterModel $model
     * @return void
     */
    protected function setData(MeterModel $model)
    {
        foreach ($model->type(Type::QUERY)->filtered()->orderBy('id', 'asc')->get() as $item) {
            if (isset($item->content['time'])) {
                $this->data[(string)$item->created_at] = $item->content['time'];
            }
        }
    }

    /**
     * Gets labels for chart.
     *
     * @return mixed
     */
    protected function getLabels(): array
    {
        return array_keys($this->data);
    }

    /**
     * Gets values for chart.
     *
     * @return mixed
     */
    protected function getValues(): array
    {
        return array_values($this->data);
    }

    /**
     * Generates and returns chart
     *
     * @return void
     */
    protected function setDataSet()
    {
        $type = config('meter.monitors.' . QueryMonitor::class . '.graph_type', 'bar');
        $color = config('meter.monitors.' . QueryMonitor::class . '.graph_color', 'rgb(255, 99, 132)');

        $this->dataset('Time', $type, $this->getValues())
            ->color($color)
            ->options([
                'pointRadius' => 1,
                'fill' => true,
                'lineTension' => 0,
                'borderWidth' => 1
            ])
            ->backgroundcolor($color);
    }

}