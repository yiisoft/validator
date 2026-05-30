#!/bin/bash

currentDir=$(dirname "$(readlink -f "$0")")

baseConfig="$currentDir/po4a-base.conf"
outputConfig="$currentDir/po4a.conf"
srcDir="$currentDir/guide/en"

cat "$baseConfig" > "$outputConfig"
echo "" >> "$outputConfig"

find "$srcDir" -name "*.md" -type f -print0 | LC_ALL=C sort -z | while IFS= read -r -d '' file; do
    relPath="${file#$srcDir/}"
    potPath=$(echo "$relPath" | tr '/' '_')
    echo "[type: markdown] guide/en/$relPath \$lang:guide/\$lang/$relPath pot=$potPath" >> "$outputConfig"
done

echo "Configuration file generated successfully: $outputConfig"
