<?php

arch('it will not use debugging functions')
    ->skipOnMac()
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
