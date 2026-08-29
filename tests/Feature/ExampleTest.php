<?php

it('returns a successful response for the homepage', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
