<?php

namespace Fjord\Chart\Loader\Concerns;

trait HasGoal
{
    /**
     * Get daily goal.
     *
     * @return integer|null
     */
    protected function getDailyGoal()
    {
        if ($this->shoudDailyGoalBeShown()) {
            return $this->config->dailyGoal;
        }
    }

    /**
     * Determine if daily goal should be shown.
     *
     * @return boolean
     */
    protected function shoudDailyGoalBeShown()
    {
        return $this->getShowGoal();
    }

    /**
     * Get show goal config.
     *
     * @return array
     */
    protected function getShowGoalConfig()
    {
        return [
            'today' => false,
            'yesterday' => false,
            'last7days' => true,
            'thisweek' => true,
            'last30days' => true,
            'thismonth' => true
        ];
    }
}