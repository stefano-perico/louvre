<?php
/**
 * Created by PhpStorm.
 * User: stefa
 * Date: 03/12/2017
 * Time: 18:27
 */

namespace AppBundle\Services;


class calculerAge
{
    public function age($date)
    {
        $age = date('Y') - date('Y', strtotime($date));
        if (date('md') < date('md', strtotime($date)))
        {
            return $age -1;
        }
        return $age;
    }

}