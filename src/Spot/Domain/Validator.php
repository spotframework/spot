<?php
namespace Spot\Domain;

interface Validator {
    function validate($domain, array $group = null);
}
