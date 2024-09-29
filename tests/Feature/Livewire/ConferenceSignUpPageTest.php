<?php

use App\Livewire\ConferenceSignUpPage;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ConferenceSignUpPage::class)
        ->assertStatus(200);
});
