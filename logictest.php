<?php
/**
 * Created by PhpStorm.
 * User: himu
 * Date: 24/11/19
 * Time: 7:12 PM
 */
for($i=1;$i<=100;$i++){
    print ($i % 5 == 0 ? ($i % 3 == 0 ? "foobar,": "bar,"):( $i % 3 == 0 ? "foo,": $i.","));
}