#!/usr/bin/env bash
 
current=$PWD

dependencies=('inshore/bookwhen')
 
namespaces=('InShore\Bookwhen')
 
for ((i = 0; i < ${#dependencies[@]}; ++i)); do
  output_dir="$current/inshore-bookwhen/includes/Vendor/${namespaces[$i]//\\/\/}"
  vendor/bin/php-scoper add-prefix \
    --force \
    --output-dir="$output_dir" \
    --prefix="InShore\Bookwhen\Vendor" \
    --working-dir="$current/vendor/${dependencies[$i]}/src"
done
