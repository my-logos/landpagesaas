# Script to remove duplicate CSS selectors, keeping the first occurrence

$inputFile = "main.css"
$outputFile = "main-clean.css"

$content = Get-Content $inputFile -Raw -Encoding UTF8

# Track seen selectors
$seenSelectors = @{}
$lines = $content -split "`n"
$output = @()
$inComment = $false
$currentSelector = ""
$currentBlock = @()
$braceCount = 0

for ($i = 0; $i -lt $lines.Count; $i++) {
    $line = $lines[$i]
    $trimmed = $line.Trim()
    
    # Skip empty lines and comments
    if ($trimmed -eq "" -or $trimmed.StartsWith("/*") -or $trimmed.StartsWith("*")) {
        $output += $line
        continue
    }
    
    # Check for CSS selector (starts with . or : or * or html or body)
    if ($trimmed -match "^(\.|:root|html|body|\*|@media|@keyframes)") {
        # Extract selector name (before {)
        if ($trimmed -match "^([^{]+)\s*\{") {
            $selectorName = $matches[1].Trim()
            
            # Check if we've seen this exact selector before
            if ($seenSelectors.ContainsKey($selectorName)) {
                # Skip this duplicate - find the closing brace
                $braceCount = 1
                $j = $i + 1
                while ($j -lt $lines.Count -and $braceCount -gt 0) {
                    $nextLine = $lines[$j]
                    $braceCount += ($nextLine.ToCharArray() | Where-Object { $_ -eq '{' }).Count
                    $braceCount -= ($nextLine.ToCharArray() | Where-Object { $_ -eq '}' }).Count
                    $j++
                }
                $i = $j - 1
                continue
            } else {
                # First time seeing this selector - mark it and add to output
                $seenSelectors[$selectorName] = $true
                $output += $line
            }
        } else {
            $output += $line
        }
    } else {
        $output += $line
    }
}

# Write cleaned content
$output -join "`n" | Out-File -FilePath $outputFile -Encoding UTF8 -NoNewline

Write-Host "Cleaned CSS written to $outputFile"
Write-Host "Original size: $((Get-Item $inputFile).Length / 1KB) KB"
Write-Host "Cleaned size: $((Get-Item $outputFile).Length / 1KB) KB"
