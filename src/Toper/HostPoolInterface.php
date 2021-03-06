<?php

namespace Toper;

interface HostPoolInterface
{
    /**
     * @return string
     */
    public function getNext();

    /**
     * @return boolean
     */
    public function hasNext();

    /**
     * @return array
     */
    public function toArray();
}
