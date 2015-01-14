<?php

$email=$_GET['e'];
echo sha1(strtolower($email));