$files = Get-ChildItem -Path resources/views -Filter *.blade.php -Recurse
foreach ($f in $files) {
    $content = Get-Content -Path $f.FullName -Raw
    $ifCount = ([regex]::Matches($content, '@if').Count)
    $elseifCount = ([regex]::Matches($content, '@elseif').Count)
    $elseCount = ([regex]::Matches($content, '@else').Count)
    $endifCount = ([regex]::Matches($content, '@endif').Count)
    if ($ifCount -ne $endifCount) {
        Write-Output "$($f.FullName) : @if=$ifCount @elseif=$elseifCount @else=$elseCount @endif=$endifCount"
    }
}
