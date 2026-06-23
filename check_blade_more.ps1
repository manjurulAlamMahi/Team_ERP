$pairs = @(
    @{s='@if'; e='@endif'},
    @{s='@can'; e='@endcan'},
    @{s='@canany'; e='@endcanany'},
    @{s='@section'; e='@endsection'},
    @{s='@push'; e='@endpush'},
    @{s='@isset'; e='@endisset'},
    @{s='@auth'; e='@endauth'},
    @{s='@guest'; e='@endguest'},
    @{s='@unless'; e='@endunless'}
)

$files = Get-ChildItem -Path resources/views -Filter *.blade.php -Recurse
foreach ($f in $files) {
    $content = Get-Content -Path $f.FullName -Raw
    $issues = @()
    foreach ($p in $pairs) {
        $sCount = ([regex]::Matches($content, [regex]::Escape($p.s)).Count)
        $eCount = ([regex]::Matches($content, [regex]::Escape($p.e)).Count)
        if ($sCount -ne $eCount) {
            $issues += "$($p.s)=$sCount $($p.e)=$eCount"
        }
    }
    if ($issues.Count -gt 0) {
        Write-Output "$($f.FullName) : $($issues -join '; ')"
    }
}
