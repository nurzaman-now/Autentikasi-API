<?php

namespace App\Helpers;

enum UserRoles: string
{
  case ADMIN = 'admin';
  case USER = 'user';
}
