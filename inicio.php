<?php
session_start();

if ( isset($_SESSION['login']) ) return;

exit ();