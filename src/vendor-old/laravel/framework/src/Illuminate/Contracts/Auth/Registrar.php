<?php

namespace Illuminate\Contracts\Auth;

interface Registrar
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data);

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function create(array $data);
}
