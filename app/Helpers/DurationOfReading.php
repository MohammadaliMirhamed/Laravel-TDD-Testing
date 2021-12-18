<?php

namespace App\Helpers;



class DurationOfReading
{
    protected $timePerWord = 1;
    protected $wordsLenght;
    protected $duration;

    public function setText($text)
    {
        $this->wordsLenght = count(explode(' ', $text));
        $this->duration = $this->timePerWord * $this->wordsLenght;

        return $this;
    }

    public function getDurationPerSec()
    {
        return $this->duration;
    }
    public function getDurationPerMin()
    {
        return $this->duration / 60;
    }
}
