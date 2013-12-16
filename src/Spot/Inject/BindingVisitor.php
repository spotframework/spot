<?php
namespace Spot\Inject;

interface BindingVisitor {
    function visit(Binding $binding);
}
