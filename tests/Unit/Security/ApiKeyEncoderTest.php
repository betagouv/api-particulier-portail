<?php

namespace App\Tests\Unit\Security;

use App\Security\ApiKeyEncoder;
use PHPUnit\Framework\TestCase;

class PasswordEncoderTest extends TestCase
{
    public function testEncodingIsCompliantWithLegacy()
    {
        $encoder = new ApiKeyEncoder();

        $encodedApiKey = "5f6c070b2f2126b39cb8c72cd05e99588c189a7ef689d7f7c5a0d1ed337068e525485469aa007f7202335774150474875a9ac28d5b6f433d103dcd4e25fc48e4";
        $plainApiKey = "f25ba6ce8729ae6c3dd2493337a999de8d5c94b05dd5d775b306f18477621b29d9df626355b1bd011baf27749beb20a1";

        $this->assertEquals($encodedApiKey, $encoder->encodeString($plainApiKey));
    }
}
