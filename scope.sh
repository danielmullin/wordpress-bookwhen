#!/usr/bin/env bash
 
current=$PWD

dependencies=('guzzlehttp/guzzle' 'php-http/discovery' 'psr/http-client' 'psr/http-message' 'inshore/bookwhen')
 
namespaces=('GuzzleHttp' 'Http\Discovery' 'Psr\Http\Client' 'Psr\Http\Message' 'InShore\Bookwhen')
 
#Http\Discovery\Psr18ClientDiscovery

for ((i = 0; i < ${#dependencies[@]}; ++i)); do
  output_dir="$current/inshore-bookwhen/includes/Vendor/${namespaces[$i]//\\/\/}"
  vendor/bin/php-scoper add-prefix \
    --force \
    --output-dir="$output_dir" \
    --prefix="InShore\Bookwhen\Vendor" \
    --working-dir="$current/vendor/${dependencies[$i]}/src"
done

vendor/bin/php-scoper add-prefix \
    --force \
    --output-dir="$current/inshore-bookwhen/includes/Vendor/Respect/Validation" \
    --prefix="InShore\Bookwhen\Vendor" \
    --working-dir="$current/vendor/respect/validation/library"
