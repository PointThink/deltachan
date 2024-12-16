#!/bin/sh
cp -r src site-build
rm site-build/internal/config.php
rm site-build/internal/chaninfo.json
rm -rf site-build/uploads
rm -rf site-build/def
touch site-build/first_run
