<?php
namespace Spot\Inject\Impl;

interface Binding {
    function getKey();

    function accept(BindingVisitor $visitor);
}