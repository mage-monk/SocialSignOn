<?php

declare(strict_types=1);

namespace MageMonk\SocialSignOn\Api\Customer;

interface SsoLoginManagementInterface
{
   /**
    * @param string $email
    * @return bool
    */
    public function login(string $email): bool;
}
