#!/bin/bash
clear; for i in  `seq -f '%03g' 0 66`; do echo -n "$i " ; (php codeFormatter.php tests/$i-*.in | php -l); done;
