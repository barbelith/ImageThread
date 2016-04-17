<?php

namespace AppBundle\Export;

interface ExporterInterface
{
    public function prepare();

    public function save($path);
}