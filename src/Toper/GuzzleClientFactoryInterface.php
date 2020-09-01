<?php

namespace Toper;

interface GuzzleClientFactoryInterface {

	public function create(): \GuzzleHttp\Client;
}
