<?php

namespace Samim\FilamentToolkit;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentToolkitServiceProvider extends PackageServiceProvider
{


    public function configurePackage(Package $package): void
    {
        $package->name('filament-toolkit')
            ->hasViews();
    }
}
