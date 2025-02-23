<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('make:random-order')->daily();
Schedule::command('make:frequency-for-tags')->daily();
