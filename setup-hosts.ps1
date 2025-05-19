# PowerShell script to add EvilCorp lab hostnames to Windows hosts file

$hostsFile = "$env:SystemRoot\System32\drivers\etc\hosts"
$entries = @(
    "127.0.0.1 bootcamp.local",
    "127.0.0.1 lab1.evilcorp.local",
    "127.0.0.1 lab2.evilcorp.local",
    "127.0.0.1 lab3.evilcorp.local",
    "127.0.0.1 lab4.evilcorp.local",
    "127.0.0.1 ASTComputers.evilcorp.local",
    "127.0.0.1 acrosstheuniverse.evilcorp.local",
    "127.0.0.1 adminjokes.evilcorp.local",
    "127.0.0.1 artforgery.evilcorp.local",
    "127.0.0.1 grinchcalculator.evilcorp.local",
    "127.0.0.1 grinchserialkiller.evilcorp.local",
    "127.0.0.1 impossible.evilcorp.local",
    "127.0.0.1 receiptexifer.evilcorp.local",
    "127.0.0.1 reindeer.evilcorp.local",
    "127.0.0.1 santascodingworkshop.evilcorp.local",
    "127.0.0.1 santasdisco.evilcorp.local",
    "127.0.0.1 syringe.evilcorp.local",
    "127.0.0.1 wishlistrenderer.evilcorp.local"
)

Write-Host ""
Write-Host "Checking current hosts file entries..."

foreach ($entry in $entries) {
    $hostname = $entry.Split(" ")[1]
    if (Select-String -Path $hostsFile -Pattern $hostname -Quiet) {
        Write-Host "  [OK] $hostname already exists"
    } else {
        Write-Host "  [+] Adding $hostname"
        "`n$entry" | Out-File -FilePath $hostsFile -Encoding utf8 -Append
    }
}

Write-Host ""
Write-Host "Done! You can now access:"
foreach ($entry in $entries) {
    $hostname = $entry.Split(" ")[1]
    Write-Host "  -> http://$hostname"
}
