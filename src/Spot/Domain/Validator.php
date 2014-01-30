<?php
namespace Spot\Domain;

interface Validator {
    function validate($entity, array $group = null);
}
